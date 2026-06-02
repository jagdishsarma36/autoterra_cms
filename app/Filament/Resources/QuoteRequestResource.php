<?php

namespace App\Filament\Resources;

use App\Models\QuoteRequest;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuoteRequestResource extends Resource
{
    protected static ?string $model = QuoteRequest::class;
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationItems(): array
    {
        return [parent::getNavigationItems()[0]->label('Quote Requests')];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Commerce';
    }

    public static function getNavigationSort(): ?int
    {
        return 25;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('company'),
                Tables\Columns\TextColumn::make('country'),
                Tables\Columns\TextColumn::make('product'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'contacted' => 'info',
                        'closed' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M j, Y'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'contacted' => 'Contacted', 'closed' => 'Closed']),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Contact Details')
                ->schema([
                    Grid::make(2)->schema([
                        \Filament\Forms\Components\TextInput::make('name')->required(),
                        \Filament\Forms\Components\TextInput::make('email')->email()->required(),
                        \Filament\Forms\Components\TextInput::make('company'),
                        \Filament\Forms\Components\TextInput::make('country')->required(),
                        \Filament\Forms\Components\TextInput::make('product'),
                        \Filament\Forms\Components\TextInput::make('term'),
                        \Filament\Forms\Components\TextInput::make('seats'),
                        \Filament\Forms\Components\Select::make('status')
                            ->options(['pending' => 'Pending', 'contacted' => 'Contacted', 'closed' => 'Closed'])
                            ->required(),
                    ]),
                    \Filament\Forms\Components\Textarea::make('message')->rows(3)->columnSpanFull(),
                    \Filament\Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull()->label('Admin Notes'),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\QuoteRequestResource\Pages\ListQuoteRequests::route('/'),
            'view' => \App\Filament\Resources\QuoteRequestResource\Pages\ViewQuoteRequest::route('/{record}'),
            'edit' => \App\Filament\Resources\QuoteRequestResource\Pages\EditQuoteRequest::route('/{record}/edit'),
        ];
    }
}
