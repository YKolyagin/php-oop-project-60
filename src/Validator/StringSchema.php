<?php

namespace Hexlet\Validator;

class StringSchema
{
    private bool $isRequired = false;
    private ?int $minLength = null;
    private ?string $contains = null;

    public function required(): self
    {
        $this->isRequired = true;
        return $this;
    }

    public function minLength(int $length): self
    {
        $this->minLength = $length;
        return $this;
    }

    public function contains(string $substring): self
    {
        $this->contains = $substring;
        return $this;
    }

    public function isValid(?string $value): bool
    {
        // Проверка required
        if ($this->isRequired && ($value === null || $value === '')) {
            return false;
        }

        // Если значение null и не требуется, то валидно
        if ($value === null) {
            return true;
        }

        // Проверка minLength
        if ($this->minLength !== null && strlen($value) < $this->minLength) {
            return false;
        }

        // Проверка contains
        if ($this->contains !== null && strpos($value, $this->contains) === false) {
            return false;
        }

        return true;
    }
}
