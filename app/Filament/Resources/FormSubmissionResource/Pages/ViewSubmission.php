<?php

namespace App\Filament\Resources\FormSubmissionResource\Pages;

use App\Filament\Resources\FormSubmissionResource;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;

class ViewSubmission extends ViewRecord
{
    protected static string $resource = FormSubmissionResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Submission Details')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('form.name')->label('Form'),
                        \Filament\Infolists\Components\TextEntry::make('name'),
                        \Filament\Infolists\Components\TextEntry::make('email'),
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
