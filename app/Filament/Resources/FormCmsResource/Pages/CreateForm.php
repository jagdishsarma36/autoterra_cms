<?php

namespace App\Filament\Resources\FormCmsResource\Pages;

use App\Filament\Resources\FormCmsResource;
use App\Models\FormField;
use Filament\Resources\Pages\CreateRecord;

class CreateForm extends CreateRecord
{
    protected static string $resource = FormCmsResource::class;

    protected function afterCreate(): void
    {
        $this->saveFields();
    }

    protected function saveFields(): void
    {
        $fields = $this->data['fields'] ?? [];
        foreach ($fields as $index => $field) {
            if (empty($field['label'])) continue;
            if (empty($field['name'])) {
                $field['name'] = Str::slug($field['label']);
            }
            FormField::create([
                'form_id' => $this->record->id,
                'label' => $field['label'],
                'name' => $field['name'],
                'type' => $field['type'] ?? 'text',
                'is_required' => $field['is_required'] ?? false,
                'placeholder' => $field['placeholder'] ?? null,
                'help_text' => $field['help_text'] ?? null,
                'options' => $this->parseOptions($field['options'] ?? null, $field['type'] ?? 'text'),
                'sort_order' => $field['sort_order'] ?? $index,
                'width' => $field['width'] ?? 100,
            ]);
        }
    }

    protected function parseOptions($value, string $type): ?array
    {
        if (empty($value) || !in_array($type, ['select', 'radio', 'checkbox'])) {
            return null;
        }
        return array_filter(array_map('trim', explode("\n", $value)));
    }
}
