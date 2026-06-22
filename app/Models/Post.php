<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'excerpt', 'meta_title',
        'meta_description', 'featured_image', 'author_name',
        'category', 'tags', 'is_published', 'published_at', 'sort_order',
        'views_count',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'tags' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Post $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where('published_at', '<=', now());
    }

    public function scopePopular(Builder $query, int $limit = 5): Builder
    {
        return $query->published()
            ->orderByDesc('views_count')
            ->limit($limit);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('content', 'like', "%{$term}%")
              ->orWhere('excerpt', 'like', "%{$term}%");
        });
    }

    public function scopeWithTag(Builder $query, string $tag): Builder
    {
        return $query->where('tags', 'like', '%"'.$tag.'"%');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
