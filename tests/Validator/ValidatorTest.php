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

    public function testArrayValidation(): void
    {
        $v = new Validator();
        $schema = $v->array();

        // По умолчанию null является валидным
        $this->assertTrue($schema->isValid(null));

        // Тест required
        $schema->required();
        $this->assertFalse($schema->isValid(null));
        $this->assertTrue($schema->isValid([]));
        $this->assertTrue($schema->isValid(['hexlet']));
    }

    public function testArraySizeof(): void
    {
        $v = new Validator();
        $schema = $v->array();

        // Без required null по-прежнему валиден
        $this->assertTrue($schema->isValid(null));

        // Проверка sizeof
        $schema->sizeof(2);
        $this->assertFalse($schema->isValid(['hexlet'])); // только 1 элемент
        $this->assertTrue($schema->isValid(['hexlet', 'code-basics'])); // 2 элемента
        $this->assertFalse($schema->isValid(['hexlet', 'code-basics', 'test'])); // 3 элемента

        // Проверка с required
        $schema->required();
        $this->assertFalse($schema->isValid(null)); // теперь null не валиден
        $this->assertFalse($schema->isValid([])); // пустой массив
        $this->assertFalse($schema->isValid(['hexlet'])); // 1 элемент
        $this->assertTrue($schema->isValid(['hexlet', 'code-basics'])); // 2 элемента
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    public function testShapeValidation(): void
    {
        $schema = $this->validator->array();

        // Позволяет описывать валидацию для ключей массива
        $schema->shape([
            'name' => $this->validator->string()->required(),
            'age' => $this->validator->number()->positive(),
        ]);

        // Valid cases
        $this->assertTrue($schema->isValid(['name' => 'kolya', 'age' => 100]));
        $this->assertTrue($schema->isValid(['name' => 'maya', 'age' => null]));

        // Invalid cases
        $this->assertFalse($schema->isValid(['name' => '', 'age' => null]));
        $this->assertFalse($schema->isValid(['name' => 'ada', 'age' => -5]));
    }

    public function testShapeWithMissingKeys(): void
    {
        $schema = $this->validator->array();

        $schema->shape([
            'name' => $this->validator->string()->required(),
            'age' => $this->validator->number()->positive(),
        ]);

        // Missing required field 'name'
        $this->assertFalse($schema->isValid(['age' => 25]));

        // Missing optional field 'age' - should be valid
        $this->assertTrue($schema->isValid(['name' => 'john']));
    }

    public function testShapeWithExtraKeys(): void
    {
        $schema = $this->validator->array();

        $schema->shape([
            'name' => $this->validator->string()->required(),
        ]);

        // Extra keys should not affect validation
        $this->assertTrue($schema->isValid(['name' => 'john', 'extra' => 'value']));
    }

    public function testShapeWithEmptyArray(): void
    {
        $schema = $this->validator->array();

        $schema->shape([
            'name' => $this->validator->string()->required(),
        ]);

        // Empty array doesn't have required 'name' key
        $this->assertFalse($schema->isValid([]));
    }

    public function testShapeWithoutDefinedSchemas(): void
    {
        $schema = $this->validator->array();

        // No shape defined, should work as before
        $this->assertTrue($schema->isValid(['any', 'array']));
    }

    public function testCustomStringValidator(): void
    {
        $v = new Validator();

        $fn = fn($value, $start) => strpos($value, $start) === 0;
        $v->addValidator('string', 'startWith', $fn);

        $schema = $v->string()->test('startWith', 'H');
        $this->assertFalse($schema->isValid('exlet'));
        $this->assertTrue($schema->isValid('Hexlet'));

        // Test with non-existent validator (should pass)
        $schema2 = $v->string()->test('nonExistent', 'H');
        $this->assertTrue($schema2->isValid('exlet'));
        $this->assertTrue($schema2->isValid('Hexlet'));
    }

    public function testCustomNumberValidator(): void
    {
        $v = new Validator();

        $fn = fn($value, $min) => $value >= $min;
        $v->addValidator('number', 'min', $fn);

        $schema = $v->number()->test('min', 5);
        $this->assertFalse($schema->isValid(4));
        $this->assertTrue($schema->isValid(6));
        $this->assertTrue($schema->isValid(5)); // boundary test

        // Test with non-existent validator (should pass)
        $schema2 = $v->number()->test('nonExistent', 5);
        $this->assertTrue($schema2->isValid(4));
        $this->assertTrue($schema2->isValid(6));
    }

    public function testMultipleCustomValidators(): void
    {
        $v = new Validator();

        $v->addValidator('string', 'startWith', fn($value, $start) => strpos($value, $start) === 0);
        $v->addValidator('string', 'endWith', fn($value, $end) => substr($value, -strlen($end)) === $end);

        $schema = $v->string()->test('startWith', 'H')->test('endWith', 't');
        $this->assertFalse($schema->isValid('Hexlety'));
        $this->assertFalse($schema->isValid('YexleH'));
        $this->assertTrue($schema->isValid('Hexlet'));
    }
}