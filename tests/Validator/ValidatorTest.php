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

    public function testNumberValidation(): void
    {
        $v = new Validator();
        $schema = $v->number();

        // By default null is valid
        $this->assertTrue($schema->isValid(null));

        // Test required
        $schema->required();
        $this->assertFalse($schema->isValid(null));
        $this->assertTrue($schema->isValid(7));
    }

    public function testNumberPositive(): void
    {
        $v = new Validator();
        $schema = $v->number();

        $this->assertTrue($schema->positive()->isValid(10));
        $this->assertFalse($schema->positive()->isValid(-5));
        $this->assertFalse($schema->positive()->isValid(0)); // Zero is not positive
    }

    public function testNumberRange(): void
    {
        $v = new Validator();
        $schema = $v->number();

        $schema->range(-5, 5);
        $this->assertFalse($schema->isValid(-10)); // Below range
        $this->assertFalse($schema->isValid(10));  // Above range
        $this->assertTrue($schema->isValid(5));    // At upper boundary
        $this->assertTrue($schema->isValid(-5));   // At lower boundary
        $this->assertTrue($schema->isValid(0));    // Within range
    }

    public function testCombinedNumberValidators(): void
    {
        $v = new Validator();
        $schema = $v->number()->required()->positive()->range(1, 100);

        $this->assertFalse($schema->isValid(null));  // Required fails
        $this->assertFalse($schema->isValid(-5));    // Positive fails
        $this->assertFalse($schema->isValid(0));     // Positive fails
        $this->assertFalse($schema->isValid(150));   // Range fails
        $this->assertTrue($schema->isValid(50));     // All pass
    }
}
