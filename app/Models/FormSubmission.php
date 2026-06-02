<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    protected $fillable = [
        'form_id', 'name', 'email', 'data', 'ip_address',
        'user_agent', 'is_read',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    public function form()
    {
        return $this->belongsTo(FormCms::class, 'form_id');
    }

    public function scopeUnread(Builder $query)
    {
        return $query->where('is_read', false);
    }
}
