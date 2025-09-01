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

    public function test(string $name, array ...$args): self
    {
        $this->customTests[] = ['name' => $name, 'args' => $args];
        return $this;
    }

    protected function runCustomTests(mixed $value): bool
    {
        foreach ($this->customTests as $test) {
            $validator = $this->validator->getCustomValidator($this->type, $test['name']);
            if (!empty($validator)) {
                if (!call_user_func($validator, $value, ...$test['args'])) {
                    return false;
                }
            }
        }
        return true;
    }
}
