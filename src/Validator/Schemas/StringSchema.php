<?php

namespace Hexlet\Validator\Schemas;

class StringSchema extends BaseSchema
{
    private ?string $contains = null;
    private ?int $minLength = null;

    public function contains(string $substring): self
    {
        $this->contains = $substring;
        return $this;
    }

    public function minLength(int $length): self
    {
        $this->minLength = $length;
        return $this;
    }

    public function isValid($value): bool
    {
        // Check required
        if ($this->required && ($value === null || $value === '')) {
            return false;
        }

        // If value is null and not required, it's valid
        if ($value === null) {
            return true;
        }

        // Check if value is string
        if (!is_string($value)) {
            return false;
        }

        // Check empty string when required
        if ($this->required && $value === '') {
            return false;
        }

        // Check minLength
        if ($this->minLength !== null && strlen($value) < $this->minLength) {
            return false;
        }

        // Check contains
        if ($this->contains !== null && strpos($value, $this->contains) === false) {
            return false;
        }

        return true;
    }
}
