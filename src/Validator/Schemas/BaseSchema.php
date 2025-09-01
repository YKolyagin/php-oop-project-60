<?php

namespace Hexlet\Validator\Schemas;

abstract class BaseSchema
{
    protected bool $required = false;

    public function required(): self
    {
        $this->required = true;
        return $this;
    }

    abstract public function isValid($value): bool;
}
