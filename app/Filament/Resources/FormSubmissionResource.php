<?php

namespace App\Filament\Resources;

use App\Models\FormSubmission;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FormSubmissionResource extends Resource
{
    protected static ?string $model = FormSubmission::class;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Submission';
    protected static ?string $pluralModelLabel = 'Submissions';

    public static function getNavigationItems(): array
    {
        return [parent::getNavigationItems()[0]->label('Submissions')];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'CMS';
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-inbox';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('form.name')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M j, Y g:i A')->sortable(),
                Tables\Columns\IconColumn::make('is_read')->boolean(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('form_id')
                    ->label('Form')
                    ->relationship('form', 'name', fn ($query) => $query->orderBy('name')),
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('Read status'),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function getRelationManagers(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\FormSubmissionResource\Pages\ListSubmissions::route('/'),
            'view' => \App\Filament\Resources\FormSubmissionResource\Pages\ViewSubmission::route('/{record}'),
        ];
    }
}
