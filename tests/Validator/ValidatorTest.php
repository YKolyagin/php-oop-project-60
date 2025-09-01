<?php

namespace Tests\Validator;

use Hexlet\Validator\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    private Validator $validator;

    public function testStringValidation(): void
    {
        $v = new Validator();
        $schema = $v->string();
        $schema2 = $v->string();

        self::assertTrue($schema->isValid(''));
        self::assertTrue($schema->isValid(null));
        self::assertTrue($schema->isValid('what does the fox say'));

        $schema->required();
        self::assertTrue($schema2->isValid('')); // Другая схема
        self::assertFalse($schema->isValid(null));
        self::assertFalse($schema->isValid(''));
        self::assertTrue($schema->isValid('hexlet'));
    }

    public function testContains(): void
    {
        $v = new Validator();
        $schema = $v->string();

        self::assertTrue($schema->contains('what')->isValid('what does the fox say'));
        self::assertFalse($schema->contains('whatthe')->isValid('what does the fox say'));
    }

    public function testMinLength(): void
    {
        $v = new Validator();

        self::assertTrue($v->string()->minLength(10)->minLength(5)->isValid('Hexlet'));
        self::assertFalse($v->string()->minLength(10)->isValid('Hexlet'));
    }

    public function testCombinedValidators(): void
    {
        $v = new Validator();
        $schema = $v->string()->required()->minLength(5)->contains('test');

        self::assertFalse($schema->isValid(''));
        self::assertFalse($schema->isValid(null));
        self::assertFalse($schema->isValid('test')); // слишком короткая строка
        self::assertFalse($schema->isValid('short')); // не содержит 'test'
        self::assertTrue($schema->isValid('this is a test string'));
    }

    public function testNumberValidation(): void
    {
        $v = new Validator();
        $schema = $v->number();

        self::assertTrue($schema->isValid(null));

        $schema->required();
        self::assertFalse($schema->isValid(null));
        self::assertTrue($schema->isValid(7));
    }

    public function testNumberPositive(): void
    {
        $v = new Validator();
        $schema = $v->number();

        self::assertTrue($schema->positive()->isValid(10));
        self::assertFalse($schema->positive()->isValid(-5));
        self::assertFalse($schema->positive()->isValid(0)); // Zero is not positive
    }

    public function testNumberRange(): void
    {
        $v = new Validator();
        $schema = $v->number();

        $schema->range(-5, 5);
        self::assertFalse($schema->isValid(-10)); // Below range
        self::assertFalse($schema->isValid(10));  // Above range
        self::assertTrue($schema->isValid(5));    // At upper boundary
        self::assertTrue($schema->isValid(-5));   // At lower boundary
        self::assertTrue($schema->isValid(0));    // Within range
    }

    public function testCombinedNumberValidators(): void
    {
        $v = new Validator();
        $schema = $v->number()->required()->positive()->range(1, 100);

        self::assertFalse($schema->isValid(null));  // Required fails
        self::assertFalse($schema->isValid(-5));    // Positive fails
        self::assertFalse($schema->isValid(0));     // Positive fails
        self::assertFalse($schema->isValid(150));   // Range fails
        self::assertTrue($schema->isValid(50));     // All pass
    }

    public function testArrayValidation(): void
    {
        $v = new Validator();
        $schema = $v->array();

        self::assertTrue($schema->isValid(null));

        $schema->required();
        self::assertFalse($schema->isValid(null));
        self::assertTrue($schema->isValid([]));
        self::assertTrue($schema->isValid(['hexlet']));
    }

    public function testArraySizeof(): void
    {
        $v = new Validator();
        $schema = $v->array();

        self::assertTrue($schema->isValid(null));

        $schema->sizeof(2);
        self::assertFalse($schema->isValid(['hexlet'])); // только 1 элемент
        self::assertTrue($schema->isValid(['hexlet', 'code-basics'])); // 2 элемента
        self::assertFalse($schema->isValid(['hexlet', 'code-basics', 'test'])); // 3 элемента

        $schema->required();
        self::assertFalse($schema->isValid(null)); // теперь null не валиден
        self::assertFalse($schema->isValid([])); // пустой массив
        self::assertFalse($schema->isValid(['hexlet'])); // 1 элемент
        self::assertTrue($schema->isValid(['hexlet', 'code-basics'])); // 2 элемента
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    public function testShapeValidation(): void
    {
        $schema = $this->validator->array();

        $schema->shape([
            'name' => $this->validator->string()->required(),
            'age' => $this->validator->number()->positive(),
        ]);

        self::assertTrue($schema->isValid(['name' => 'kolya', 'age' => 100]));
        self::assertTrue($schema->isValid(['name' => 'maya', 'age' => null]));

        self::assertFalse($schema->isValid(['name' => '', 'age' => null]));
        self::assertFalse($schema->isValid(['name' => 'ada', 'age' => -5]));
    }

    public function testShapeWithMissingKeys(): void
    {
        $schema = $this->validator->array();

        $schema->shape([
            'name' => $this->validator->string()->required(),
            'age' => $this->validator->number()->positive(),
        ]);

        self::assertFalse($schema->isValid(['age' => 25]));

        self::assertTrue($schema->isValid(['name' => 'john']));
    }

    public function testShapeWithExtraKeys(): void
    {
        $schema = $this->validator->array();

        $schema->shape([
            'name' => $this->validator->string()->required(),
        ]);

        self::assertTrue($schema->isValid(['name' => 'john', 'extra' => 'value']));
    }

    public function testShapeWithEmptyArray(): void
    {
        $schema = $this->validator->array();

        $schema->shape([
            'name' => $this->validator->string()->required(),
        ]);

        self::assertFalse($schema->isValid([]));
    }

    public function testShapeWithoutDefinedSchemas(): void
    {
        $schema = $this->validator->array();

        self::assertTrue($schema->isValid(['any', 'array']));
    }

    public function testCustomStringValidator(): void
    {
        $v = new Validator();

        $fn = fn($value, $start) => strpos($value, $start) === 0;
        $v->addValidator('string', 'startWith', $fn);

        $schema = $v->string()->test('startWith', 'H');
        self::assertFalse($schema->isValid('exlet'));
        self::assertTrue($schema->isValid('Hexlet'));

        $schema2 = $v->string()->test('nonExistent', 'H');
        self::assertTrue($schema2->isValid('exlet'));
        self::assertTrue($schema2->isValid('Hexlet'));
    }

    public function testCustomNumberValidator(): void
    {
        $v = new Validator();

        $fn = fn($value, $min) => $value >= $min;
        $v->addValidator('number', 'min', $fn);

        $schema = $v->number()->test('min', 5);
        self::assertFalse($schema->isValid(4));
        self::assertTrue($schema->isValid(6));
        self::assertTrue($schema->isValid(5)); // boundary test

        $schema2 = $v->number()->test('nonExistent', 5);
        self::assertTrue($schema2->isValid(4));
        self::assertTrue($schema2->isValid(6));
    }

    public function testMultipleCustomValidators(): void
    {
        $v = new Validator();

        $v->addValidator('string', 'startWith', fn($value, $start) => strpos($value, $start) === 0);
        $v->addValidator('string', 'endWith', fn($value, $end) => substr($value, -strlen($end)) === $end);

        $schema = $v->string()->test('startWith', 'H')->test('endWith', 't');
        self::assertFalse($schema->isValid('Hexlety'));
        self::assertFalse($schema->isValid('YexleH'));
        self::assertTrue($schema->isValid('Hexlet'));
    }
}
