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
}
