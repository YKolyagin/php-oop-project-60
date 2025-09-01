<?php

namespace Hexlet\Validator\Schemas;

use Hexlet\Validator\Validator;

abstract class BaseSchema
{
    protected ?int $minLength = null;
    protected bool $isPositive = false;
    protected bool $required = false;
    protected Validator $validator;
    protected string $type;
    protected array $customTests = [];

    public function __construct(Validator $validator, string $type)
    {
        $this->validator = $validator;
        $this->type = $type;
    }

    /**
     * @return static
     */
    public function required()
    {
        $this->required = true;
        return $this;
    }
}
