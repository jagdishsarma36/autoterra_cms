<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PageContent extends Model
{
    protected $fillable = ['page', 'key', 'value', 'type'];

    protected function casts(): array
    {
        return [];
    }

    /**
     * Get content value, decoded from JSON if type is json.
     */
    public function getContentValue()
    {
        if ($this->type === 'json') {
            return json_decode($this->value, true) ?? [];
        }
        // For richtext, convert tiptap JSON to HTML for frontend rendering
        if ($this->type === 'richtext' && !empty($this->value)) {
            $decoded = json_decode($this->value, true);
            if (is_array($decoded) && isset($decoded['type'])) {
                try {
                    $editor = new \Tiptap\Editor([new \Tiptap\Extensions\StarterKit()]);
                    return $editor->setContent($decoded)->getHtml();
                } catch (\Exception $e) {
                    return $this->value;
                }
            }
            return $this->value;
        }
        return $this->value;
    }

    /**
     * Helper: get page content with caching.
     */
    public static function get(string $page, string $key, $default = '')
    {
        $cacheKey = "page_content.{$page}.{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($page, $key, $default) {
            $record = static::where('page', $page)->where('key', $key)->first();
            if (!$record) return $default;

            $value = $record->getContentValue();
            return $value !== null && $value !== '' ? $value : $default;
        });
    }

    /**
     * Helper: get JSON-decoded content.
     */
    public static function getJson(string $page, string $key, array $default = []): array
    {
        $value = static::get($page, $key, $default);
        return is_array($value) ? $value : $default;
    }

    /**
     * Clear cache for a page.
     */
    public static function clearCache(string $page): void
    {
        $contents = static::where('page', $page)->get();
        foreach ($contents as $content) {
            Cache::forget("page_content.{$page}.{$content->key}");
        }
    }

    /**
     * Clear all page content cache.
     */
    public static function clearAllCache(): void
    {
        $pages = static::distinct()->pluck('page');
        foreach ($pages as $page) {
            static::clearCache($page);
        }
    }
}
