<?php

namespace Hexlet\Validator\Schemas;

use Hexlet\Validator\Validator;

class NumberSchema extends BaseSchema
{
    private ?array $range = null;

    public function __construct(?Validator $validator = null)
    {
        parent::__construct($validator ?? new Validator(), 'number');
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

    protected function runCustomTests(int $value): bool
    {
        foreach ($this->customTests as $test) {
            $validator = $this->validator->getCustomValidator($this->type, $test['name']);
            if ($validator !== null) {
                if (!call_user_func($validator, $value, ...$test['args'])) {
                    return false;
                }
            }
        }
        return true;
    }

    public function test(string $name, int ...$args): self
    {
        $this->customTests[] = ['name' => $name, 'args' => $args];
        return $this;
    }
}
