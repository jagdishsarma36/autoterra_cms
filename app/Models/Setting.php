<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting) return $default;
            if ($setting->type === 'boolean') return $setting->value === '1';
            return $setting->value;
        });
    }

    public static function set(string $key, $value, string $type = 'boolean'): void
    {
        static::updateOrCreate(['key' => $key], [
            'value' => $value,
            'type' => $type,
        ]);
        Cache::forget("setting.{$key}");
    }
}
