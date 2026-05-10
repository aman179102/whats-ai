<?php

namespace App\Core;

class Input
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public static function sanitize(string $value): string
    {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }

    public static function phone(string $value): string
    {
        return preg_replace('/[^0-9+]/', '', $value);
    }

    public static function number(mixed $value): int
    {
        return max(0, (int) $value);
    }
}
