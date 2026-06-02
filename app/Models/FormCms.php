<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class FormCms extends Model
{
    protected $table = 'forms';

    protected $fillable = [
        'name', 'slug', 'description', 'submit_button_text',
        'success_message', 'redirect_url', 'notification_email',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::creating(function (FormCms $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function fields()
    {
        return $this->hasMany(FormField::class, 'form_id')->orderBy('sort_order');
    }

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class, 'form_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
