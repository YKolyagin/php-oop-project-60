<?php

namespace Hexlet\Validator\Schemas;

class ArraySchema extends BaseSchema
{
    private ?int $size = null;

    public function sizeof(int $size): self
    {
        $this->size = $size;
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

        return true;
    }
}
