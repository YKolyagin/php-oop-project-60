<?php

namespace Hexlet\Validator;
class Validator
{
    public function string(): StringSchema
    {
        return new StringSchema();
    }
}
