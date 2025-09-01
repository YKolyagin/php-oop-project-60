<?php

namespace Hexlet\Validator\Schemas;

class ArraySchema extends BaseSchema
{
    private ?int $size = null;
    private array $shapeSchemas = [];

    public function sizeof(int $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function shape(array $schemas): self
    {
        $this->shapeSchemas = $schemas;
        return $this;
    }

    public function isValid($value): bool
    {
        // Check required
        if ($this->required && ($value === null || !is_array($value))) {
            return false;
        }

        // If value is null and not required, it's valid
        if ($value === null) {
            return true;
        }

        // Check if value is array
        if (!is_array($value)) {
            return false;
        }

        // Check size
        if ($this->size !== null && count($value) !== $this->size) {
            return false;
        }

        // Check shape validation
        foreach ($this->shapeSchemas as $key => $schema) {
            // If key exists in the value array, validate it
            if (array_key_exists($key, $value)) {
                if (!$schema->isValid($value[$key])) {
                    return false;
                }
            } else {
                // If key doesn't exist, check if the schema is required
                // We need to check if the schema requires the value
                if (property_exists($schema, 'required') && $schema->required) {
                    return false;
                }
            }
        }

        return true;
    }
}
