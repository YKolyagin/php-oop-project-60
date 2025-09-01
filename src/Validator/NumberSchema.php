<?php

namespace Hexlet\Validator;

class NumberSchema
{
    private bool $isRequired = false;
    private bool $isPositive = false;
    private ?array $range = null;

    public function required(): self
    {
        $this->isRequired = true;
        return $this;
    }

    public function positive(): self
    {
        $this->isPositive = true;
        return $this;
    }

    public function range(int $min, int $max): self
    {
        $this->range = ['min' => $min, 'max' => $max];
        return $this;
    }

    public function isValid(?int $value): bool
    {
        // Check required
        if ($this->isRequired && $value === null) {
            return false;
        }

        // If value is null and not required, it's valid
        if ($value === null) {
            return true;
        }

        // Check positive
        if ($this->isPositive && $value <= 0) {
            return false;
        }

        // Check range
        if ($this->range !== null && ($value < $this->range['min'] || $value > $this->range['max'])) {
            return false;
        }

        return true;
    }
}
