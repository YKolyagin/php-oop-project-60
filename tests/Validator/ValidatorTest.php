<?php

namespace Tests\Validator;

use Hexlet\Validator\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testStringValidation(): void
    {
        $v = new Validator();
        $schema = $v->string();
        $schema2 = $v->string();

        // По умолчанию все значения валидны
        $this->assertTrue($schema->isValid(''));
        $this->assertTrue($schema->isValid(null));
        $this->assertTrue($schema->isValid('what does the fox say'));

        // После вызова required()
        $schema->required();
        $this->assertTrue($schema2->isValid('')); // Другая схема
        $this->assertFalse($schema->isValid(null));
        $this->assertFalse($schema->isValid(''));
        $this->assertTrue($schema->isValid('hexlet'));
    }

    public function testContains(): void
    {
        $v = new Validator();
        $schema = $v->string();

        $this->assertTrue($schema->contains('what')->isValid('what does the fox say'));
        $this->assertFalse($schema->contains('whatthe')->isValid('what does the fox say'));
    }

    public function testMinLength(): void
    {
        $v = new Validator();

        // Последний вызов minLength имеет приоритет
        $this->assertTrue($v->string()->minLength(10)->minLength(5)->isValid('Hexlet'));
        $this->assertFalse($v->string()->minLength(10)->isValid('Hexlet'));
    }

    public function testCombinedValidators(): void
    {
        $v = new Validator();
        $schema = $v->string()->required()->minLength(5)->contains('test');

        $this->assertFalse($schema->isValid(''));
        $this->assertFalse($schema->isValid(null));
        $this->assertFalse($schema->isValid('test')); // слишком короткая строка
        $this->assertFalse($schema->isValid('short')); // не содержит 'test'
        $this->assertTrue($schema->isValid('this is a test string'));
    }
}
