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
        'published_at', 'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
