<?php

namespace Hexlet\Validator\Schemas;

use Hexlet\Validator\Validator;

class ArraySchema extends BaseSchema
{
    private ?int $size = null;
    private array $shapeSchemas = [];

    public function __construct(?Validator $validator = null)
    {
        parent::__construct($validator ?? new Validator(), 'array');
    }

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

    public function isValid(?array $value): bool
    {
        if ($this->required && ($value === null || !is_array($value))) {
            return false;
        }

        if ($value === null) {
            return true;
        }

        if (!is_array($value)) {
            return false;
        }

        if ($this->size !== null && count($value) !== $this->size) {
            return false;
        }

        foreach ($this->shapeSchemas as $key => $schema) {
            if (array_key_exists($key, $value)) {
                if (!$schema->isValid($value[$key])) {
                    return false;
                }
            } else {
                if (property_exists($schema, 'required') && $schema->required) {
                    return false;
                }
            }
        }

        if (!$this->runCustomTests($value)) {
            return false;
        }

        return true;
    }
}
