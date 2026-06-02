<?php

namespace App\Filament\Resources;

use App\Models\FormCms;
use App\Models\FormField;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class FormCmsResource extends Resource
{
    protected static ?string $model = FormCms::class;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Form';
    protected static ?string $pluralModelLabel = 'Forms';

    public static function getNavigationItems(): array
    {
        return [parent::getNavigationItems()[0]->label('Forms')];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'CMS';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-check';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->searchable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('fields_count')->counts('fields')->label('Fields'),
                Tables\Columns\TextColumn::make('submissions_count')->counts('submissions')->label('Submissions'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M j, Y'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Form Details')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) => filled($state) && $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->unique(FormCms::class, 'slug', ignoreRecord: true)
                            ->maxLength(255),
                        Toggle::make('is_active')->default(true),
                        TextInput::make('submit_button_text')->default('Submit')->maxLength(100),
                        TextInput::make('notification_email')
                            ->label('Send submissions to')
                            ->email()
                            ->helperText('Leave empty to disable email notifications'),
                    ]),
                    Textarea::make('description')->rows(2)->columnSpanFull(),
                    TextInput::make('success_message')
                        ->default('Thank you! Your submission has been received.')
                        ->maxLength(500)
                        ->columnSpanFull(),
                ]),

            Section::make('Form Fields')
                ->description('Drag to reorder. Use simple names like "name", "email", "message".')
                ->schema([
                    Repeater::make('fields')
                        ->schema([
                            Grid::make(12)->schema([
                                TextInput::make('label')
                                    ->label('Label')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(4),
                                TextInput::make('name')
                                    ->label('Field Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('machine name')
                                    ->columnSpan(3),
                                Select::make('type')
                                    ->options(FormField::types())
                                    ->required()
                                    ->default('text')
                                    ->live()
                                    ->columnSpan(2),
                                Toggle::make('is_required')
                                    ->label('Required')
                                    ->default(false)
                                    ->columnSpan(1),
                                Toggle::make('width')
                                    ->label('Full Width')
                                    ->default(true)
                                    ->columnSpan(2)
                                    ->dehydrated(fn ($state) => $state ? 100 : 50),
                            ]),
                            Grid::make(12)->schema([
                                TextInput::make('placeholder')
                                    ->label('Placeholder')
                                    ->maxLength(255)
                                    ->columnSpan(5),
                                TextInput::make('help_text')
                                    ->label('Help Text')
                                    ->maxLength(255)
                                    ->columnSpan(4),
                                TextInput::make('sort_order')
                                    ->label('Order')
                                    ->numeric()
                                    ->default(0)
                                    ->columnSpan(3),
                            ]),
                            Textarea::make('options')
                                ->label('Options (one per line)')
                                ->rows(4)
                                ->columnSpanFull()
                                ->visible(fn ($get) => in_array($get('type'), ['select', 'radio', 'checkbox']))
                                ->helperText('One option per line'),
                        ])
                        ->columns(1)
                        ->addActionLabel('Add field')
                        ->defaultItems(0)
                        ->reorderable(),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\FormCmsResource\Pages\ListForms::route('/'),
            'create' => \App\Filament\Resources\FormCmsResource\Pages\CreateForm::route('/create'),
            'edit' => \App\Filament\Resources\FormCmsResource\Pages\EditForm::route('/{record}/edit'),
        ];
    }
}
