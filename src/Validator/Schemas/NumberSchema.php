<?php

namespace Hexlet\Validator\Schemas;

use Hexlet\Validator\Validator;

class NumberSchema extends BaseSchema
{
    private bool $isPositive = false;
    private ?array $range = null;

    public function __construct(?Validator $validator = null)
    {
        parent::__construct($validator ?? new Validator(), 'number');
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
        if ($this->required && $value === null) {
            return false;
        }

        if ($value === null) {
            return true;
        }

        if (!is_numeric($value)) {
            return false;
        }

        if ($this->isPositive && $value <= 0) {
            return false;
        }

        if ($this->range !== null && ($value < $this->range['min'] || $value > $this->range['max'])) {
            return false;
        }

        if (!$this->runCustomTests($value)) {
            return false;
        }

        return true;
    }
}
