<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PageCms extends Model
{
    protected $table = 'pages';

    protected $fillable = [
        'title', 'slug', 'content', 'excerpt', 'meta_title',
        'meta_description', 'featured_image', 'is_published',
        'published_at', 'sort_order', 'visibility', 'visible_user_ids',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'visibility' => 'string',
        'visible_user_ids' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (PageCms $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }

    /**
     * Get content blocks for this page from page_contents table.
     * Uses prefix 'cms:{slug}' as the page identifier.
     */
    public function blocks()
    {
        return $this->hasMany(PageContent::class, 'page')
            ->where('page', 'cms:' . $this->slug);
    }

    /**
     * Get a specific content block value.
     */
    public function block(string $key, $default = '')
    {
        $pageKey = 'cms:' . $this->slug;
        return pageContent($pageKey, $key, $default);
    }

    /**
     * Get a JSON-decoded content block.
     */
    public function blockJson(string $key, array $default = []): array
    {
        $pageKey = 'cms:' . $this->slug;
        return pageContentJson($pageKey, $key, $default);
    }

    /**
     * Get all blocks as key => value array.
     */
    public function allBlocks(): array
    {
        $pageKey = 'cms:' . $this->slug;
        $contents = PageContent::where('page', $pageKey)->get();
        $blocks = [];
        foreach ($contents as $content) {
            $blocks[$content->key] = $content->getContentValue();
        }
        return $blocks;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where('published_at', '<=', now());
    }

    public function isVisibleTo(?User $user): bool
    {
        if ($this->visibility === 'public' || empty($this->visibility)) {
            return true;
        }

        if (! $user) {
            return false;
        }

        if ($this->visibility === 'logged_in') {
            return true;
        }

        if ($this->visibility === 'specific_users') {
            $ids = $this->visible_user_ids ?? [];
            return in_array($user->id, $ids, true);
        }

        return true;
    }

    /**
     * Normalize a rich-content value into a valid Tiptap document (JSON string).
     * Plain HTML/text passes through untouched; fragmented JSON nodes are
     * wrapped into a proper doc so Filament's RichEditor and the renderer
     * never choke on legacy or malformed payloads.
     */
    public static function normalizeRichContent(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $trimmed = trim($value);
        if (! str_starts_with($trimmed, '{') && ! str_starts_with($trimmed, '[')) {
            return $value;
        }

        $decoded = json_decode($trimmed, true);
        if (! is_array($decoded)) {
            return $value;
        }

        if (($decoded['type'] ?? null) === 'doc'
            && isset($decoded['content'])
            && is_array($decoded['content'])) {
            return json_encode($decoded);
        }

        $content = array_is_list($decoded) ? $decoded : [$decoded];
        try {
            return json_encode((new \Tiptap\Editor([new \Tiptap\Extensions\StarterKit()]))
                ->setContent(['type' => 'doc', 'content' => $content])
                ->getDocument());
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getRouteKeyName(): string
    {
       // return 'slug';
        return 'id';
    }
}
