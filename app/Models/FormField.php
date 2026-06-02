<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    protected $fillable = [
        'form_id', 'label', 'name', 'type', 'is_required',
        'placeholder', 'help_text', 'options', 'sort_order', 'width',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
        'sort_order' => 'integer',
        'width' => 'integer',
    ];

    public function form()
    {
        return $this->belongsTo(FormCms::class, 'form_id');
    }

    public static function types(): array
    {
        return [
            'text' => 'Text Input',
            'email' => 'Email',
            'number' => 'Number',
            'phone' => 'Phone',
            'textarea' => 'Text Area',
            'select' => 'Dropdown Select',
            'radio' => 'Radio Buttons',
            'checkbox' => 'Checkboxes',
            'date' => 'Date',
            'time' => 'Time',
            'file' => 'File Upload',
            'hidden' => 'Hidden Field',
        ];
    }
}
