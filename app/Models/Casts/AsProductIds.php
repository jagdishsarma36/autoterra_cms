<?php

namespace App\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class AsProductIds implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): array
    {
        if (is_array($value)) {
            return array_values($value);
        }

        if ($value === null || trim((string) $value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return array_values(array_filter(array_map('trim', $decoded)));
        }

        return array_values(array_filter(array_map('trim', explode(',', (string) $value))));
    }

    public function set($model, string $key, $value, array $attributes): string
    {
        $ids = is_array($value) ? $value : explode(',', (string) $value);

        return json_encode(array_values(array_filter(array_map('trim', $ids))));
    }
}