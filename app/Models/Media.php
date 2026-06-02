<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'name', 'file_name', 'mime_type', 'size', 'disk', 'path',
        'alt', 'title', 'caption', 'description', 'folder',
    ];

    protected $appends = ['url', 'human_size'];

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isVideo(): bool
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    public function isDocument(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv',
        ]);
    }

    public function isArchive(): bool
    {
        return in_array($this->mime_type, [
            'application/zip',
            'application/x-zip-compressed',
            'application/x-rar-compressed',
            'application/gzip',
            'application/x-7z-compressed',
        ]);
    }

    public function deleteFile(): bool
    {
        if ($this->path && Storage::disk($this->disk)->exists($this->path)) {
            return Storage::disk($this->disk)->delete($this->path);
        }
        return true;
    }

    protected static function booted(): void
    {
        static::deleted(function (Media $media) {
            $media->deleteFile();
        });
    }
}
