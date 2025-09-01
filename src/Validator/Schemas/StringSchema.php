<?php

namespace Hexlet\Validator\Schemas;

use Hexlet\Validator\Validator;

class StringSchema extends BaseSchema
{
    private ?string $contains = null;

    public function __construct(?Validator $validator = null)
    {
        parent::__construct($validator ?? new Validator(), 'string');
    }

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

    public function isValid(?string $value): bool
    {
        if ($this->required && ($value === null || $value === '')) {
            return false;
        }

        if ($value === null) {
            return true;
        }

        if ($this->required && $value === '') {
            return false;
        }

        if ($this->minLength !== null && strlen($value) < $this->minLength) {
            return false;
        }

        if ($this->contains !== null && strpos($value, $this->contains) === false) {
            return false;
        }

        if (!$this->runCustomTests($value)) {
            return false;
        }

        return true;
    }

    protected function runCustomTests(string $value): bool
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

    public function test(string $name, string ...$args): self
    {
        $this->customTests[] = ['name' => $name, 'args' => $args];
        return $this;
    }
}
