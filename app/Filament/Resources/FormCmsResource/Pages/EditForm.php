<?php

namespace App\Filament\Resources\FormCmsResource\Pages;

use App\Filament\Resources\FormCmsResource;
use App\Models\FormField;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditForm extends EditRecord
{
    protected static string $resource = FormCmsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('viewForm')
                ->label('View Form')
                ->icon('heroicon-o-eye')
                ->url(fn () => '/form/' . $this->record->slug)
                ->openUrlInNewTab(),
            Actions\Action::make('submissions')
                ->label('Submissions')
                ->icon('heroicon-o-inbox')
                ->url(fn () => '/admin/form-submissions?filters[form_id]=' . $this->record->id)
                ->openUrlInNewTab(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $fields = FormField::where('form_id', $data['id'])
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($field) => [
                'label' => $field->label,
                'name' => $field->name,
                'type' => $field->type,
                'is_required' => $field->is_required,
                'placeholder' => $field->placeholder,
                'help_text' => $field->help_text,
                'options' => $field->options ? implode("\n", $field->options) : '',
                'sort_order' => $field->sort_order,
                'width' => $field->width,
            ])
            ->toArray();

        $data['fields'] = $fields;
        return $data;
    }

    protected function afterSave(): void
    {
        $this->saveFields();
    }

    protected function saveFields(): void
    {
        FormField::where('form_id', $this->record->id)->delete();

        $fields = $this->data['fields'] ?? [];
        foreach ($fields as $index => $field) {
            if (empty($field['label'])) continue;
            $name = $field['name'] ?? Str::slug($field['label']);
            FormField::create([
                'form_id' => $this->record->id,
                'label' => $field['label'],
                'name' => $name,
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
