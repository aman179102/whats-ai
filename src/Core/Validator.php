<?php

namespace App\Core;

class Validator
{
    private array $errors = [];

    public function required(string $field, mixed $value): static
    {
        if (empty($value) && $value !== '0') {
            $this->errors[$field][] = "$field is required";
        }
        return $this;
    }

    public function phone(string $field, mixed $value): static
    {
        if (!empty($value) && !preg_match('/^\+?[0-9]{7,15}$/', $value)) {
            $this->errors[$field][] = "$field must be a valid phone number";
        }
        return $this;
    }

    public function maxLength(string $field, mixed $value, int $max): static
    {
        if (strlen($value ?? '') > $max) {
            $this->errors[$field][] = "$field must not exceed $max characters";
        }
        return $this;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): string
    {
        $all = array_values($this->errors);
        return $all[0][0] ?? '';
    }
}
