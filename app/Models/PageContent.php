<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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

        // For richtext/wysiwyg, Tiptap JSON is converted to HTML using the same
        // extension set the editor writes with, so underline, links, tables,
        // highlights etc. survive. Raw HTML is passed through untouched.
        if (in_array($this->type, ['richtext', 'wysiwyg'], true) && filled($this->value)) {
            return PageCms::renderRichContent($this->value);
        }

        return $this->value;
    }

    /**
     * Replace every content block belonging to a page with the given set.
     *
     * `page` + `key` is unique, so this runs inside a transaction and skips
     * duplicate/blank keys. Without both, one duplicated key aborted the
     * insert halfway through and left the page with deleted or half-written
     * content, which is what made saving look like it did nothing.
     *
     * @param  array<int|string, array<string, mixed>>  $blocks
     */
    public static function syncForPage(string $page, array $blocks): void
    {
        $rows = [];

        foreach ($blocks as $block) {
            if (! is_array($block)) {
                continue;
            }

            $key = trim((string) ($block['key'] ?? ''));

            if ($key === '' || array_key_exists($key, $rows)) {
                continue;
            }

            // The WYSIWYG editor keeps its own state so that opening a page can
            // never rewrite a plain text or HTML block into a Tiptap document.
            $value = ($block['type'] ?? null) === 'wysiwyg'
                ? ($block['wysiwyg_value'] ?? $block['value'] ?? null)
                : ($block['value'] ?? null);

            $rows[$key] = [
                'page' => $page,
                'key' => $key,
                'value' => static::normalizeBlockValue($value),
                'type' => static::normalizeBlockType($block),
            ];
        }

        DB::transaction(function () use ($page, $rows): void {
            static::where('page', $page)->delete();

            foreach ($rows as $row) {
                static::create($row);
            }
        });

        static::clearCache($page);
    }

    /**
     * The rich editor hands back an array on some paths and a string on
     * others — normalise both to something the longText column can hold.
     */
    protected static function normalizeBlockValue(mixed $value): ?string
    {
        if (is_array($value)) {
            return json_encode($value);
        }

        if ($value === null) {
            return null;
        }

        return (string) $value;
    }

    /**
     * The section class is packed into the `type` column as
     * `html_section:<class>`, which is what the front end reads.
     *
     * @param  array<string, mixed>  $block
     */
    protected static function normalizeBlockType(array $block): string
    {
        $type = (string) ($block['type'] ?? 'text');

        if ($type !== 'html_section') {
            return $type;
        }

        $class = $block['section_class'] ?? 'section-white';

        if ($class === 'custom') {
            $class = $block['section_class_custom'] ?: 'section-white';
        }

        return 'html_section:' . $class;
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
