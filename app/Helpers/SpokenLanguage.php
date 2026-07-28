<?php

namespace App\Helpers;

class SpokenLanguage
{
    public static function options(): array
    {
        return config('spoken_languages', []);
    }

    public static function allowedValues(): array
    {
        return array_keys(self::options());
    }

    /**
     * Parse stored JSON / array / comma string → list of allowed language labels.
     */
    public static function parse($value): array
    {
        if (empty($value)) {
            return [];
        }

        if (is_array($value)) {
            $items = $value;
        } else {
            $decoded = json_decode((string) $value, true);
            if (is_array($decoded)) {
                $items = $decoded;
            } else {
                $items = array_map('trim', explode(',', (string) $value));
            }
        }

        $allowed = self::allowedValues();
        $result = [];
        foreach ($items as $item) {
            $item = trim((string) $item);
            if ($item === '' || !in_array($item, $allowed, true)) {
                continue;
            }
            if (!in_array($item, $result, true)) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Normalize request input → JSON string for DB (or null).
     */
    public static function fromRequest($input): ?string
    {
        $parsed = self::parse($input);
        if (empty($parsed)) {
            return null;
        }

        return json_encode($parsed, JSON_UNESCAPED_UNICODE);
    }

    public static function display(?string $value, string $fallback = 'Tiếng Việt, English'): string
    {
        $parsed = self::parse($value);
        if (empty($parsed)) {
            return $fallback;
        }

        return implode(', ', $parsed);
    }
}
