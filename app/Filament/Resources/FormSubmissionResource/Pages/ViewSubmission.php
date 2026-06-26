<?php

namespace App\Filament\Resources\FormSubmissionResource\Pages;

use App\Filament\Resources\FormSubmissionResource;
use App\Models\FormSubmission;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;

class ViewSubmission extends ViewRecord
{
    protected static string $resource = FormSubmissionResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (!$this->record->is_read) {
            $this->record->update(['is_read' => true]);
        }
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Submission Details')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('form.name')->label('Form'),
                        \Filament\Infolists\Components\TextEntry::make('data.first_name')
                            ->label('First Name')
                            ->getStateUsing(fn (FormSubmission $record): string =>
                                $record->data['first_name'] ?? $record->data['name'] ?? $record->name ?? '-'
                            ),
                        \Filament\Infolists\Components\TextEntry::make('data.email')
                            ->label('Email')
                            ->getStateUsing(fn (FormSubmission $record): string =>
                                $record->data['email'] ?? $record->email ?? '-'
                            ),
                        \Filament\Infolists\Components\TextEntry::make('created_at')->dateTime('M j, Y g:i A'),
                        \Filament\Infolists\Components\TextEntry::make('ip_address'),
                    ]),
                \Filament\Schemas\Components\Section::make('Submitted Data')
                    ->schema(fn ($record) => collect($record->data ?? [])->map(fn ($value, $key) =>
                        \Filament\Infolists\Components\TextEntry::make("data.{$key}")
                            ->label(ucwords(str_replace('_', ' ', $key)))
                    )->toArray()),
            ]);
    }
}
