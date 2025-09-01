<?php

namespace Hexlet\Validator;

use Hexlet\Validator\Schemas\ArraySchema;
use Hexlet\Validator\Schemas\StringSchema;
use Hexlet\Validator\Schemas\NumberSchema;

class Validator
{
    private array $customValidators = [];

    public function string(): StringSchema
    {
        return new StringSchema($this);
    }

    public function number(): NumberSchema
    {
        return new NumberSchema($this);
    }

    public function array(): ArraySchema
    {
        return new ArraySchema($this);
    }
    
    public function addValidator(string $type, string $name, callable $validator): void
    {
        $this->customValidators[$type][$name] = $validator;
    }
    
    public function getCustomValidator(string $type, string $name): ?callable
    {
        return $this->customValidators[$type][$name] ?? null;
    }
}