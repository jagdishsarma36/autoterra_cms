<?php

namespace App\Models;

use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Throwable;

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
     *
     * The Tiptap schema comes from Filament's own RichContentRenderer so the
     * full node/mark set is used. A bare StarterKit only knows about bold,
     * italic, code and strike, so everything else the editor can produce
     * (underline, links, highlights, tables, sub/superscript, text
     * alignment, ...) would be silently dropped on every round trip.
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
            return $trimmed;
        }

        $content = array_is_list($decoded) ? $decoded : [$decoded];

        try {
            $document = RichContentRenderer::make(['type' => 'doc', 'content' => $content])->toArray();
        } catch (Throwable) {
            return null;
        }

        return blank($document) ? null : json_encode($document);
    }

    /**
     * Turn a stored rich-content value into HTML for the front end.
     *
     * Tiptap documents are re-serialized with the editor's own extension set so
     * no formatting is lost. Raw HTML (what the legacy `richtext` blocks and
     * the `html*` blocks store) is returned untouched so existing pages keep
     * rendering exactly as before.
     */
    public static function renderRichContent(?string $value): string
    {
        if (blank($value)) {
            return '';
        }

        $trimmed = trim($value);

        if (! str_starts_with($trimmed, '{') && ! str_starts_with($trimmed, '[')) {
            return $value;
        }

        $normalized = static::normalizeRichContent($value);

        if (blank($normalized)) {
            return $value;
        }

        try {
            return RichContentRenderer::make($normalized)->toUnsafeHtml();
        } catch (Throwable) {
            return $normalized;
        }
    }

    public function getRouteKeyName(): string
    {
       // return 'slug';
        return 'id';
    }
}
