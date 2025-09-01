<?php

namespace Hexlet\Validator\Schemas;

use Hexlet\Validator\Validator;

abstract class BaseSchema
{
    protected bool $required = false;
    protected Validator $validator;
    protected string $type;
    protected array $customTests = [];

    public function __construct(Validator $validator, string $type)
    {
        $this->validator = $validator;
        $this->type = $type;
    }

    public function required(): self
    {
        $this->required = true;
        return $this;
    }
    
    public function test(string $name, ...$args): self
    {
        $this->customTests[] = ['name' => $name, 'args' => $args];
        return $this;
    }
    
    protected function runCustomTests($value): bool
    {
        foreach ($this->customTests as $test) {
            $validator = $this->validator->getCustomValidator($this->type, $test['name']);
            if ($validator) {
                if (!call_user_func($validator, $value, ...$test['args'])) {
                    return false;
                }
            }
        }
        return true;
    }

    abstract public function isValid($value): bool;
}