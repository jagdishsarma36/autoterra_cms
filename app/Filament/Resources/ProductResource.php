<?php

namespace App\Filament\Resources;

use App\Models\Product;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationItems(): array
    {
        return [parent::getNavigationItems()[0]->label('Products')];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Commerce';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cube';
    }

    public static function termOptions(): array
    {
        return [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            '3mo' => '3 Months',
            '6mo' => '6 Months',
            '1yr' => '1 Year',
            '3yr' => '3 Years',
            '5yr' => '5 Years',
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('slug')->searchable(),
                Tables\Columns\TextColumn::make('tier')
                    ->badge(fn (string $state): string => match ($state) {
                        'basic' => 'gray',
                        'pro' => 'info',
                        'advanced' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('prices_count')->counts('prices')->label('Prices'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M j, Y'),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('tier')
                    ->options(['basic' => 'Basic', 'pro' => 'Professional', 'advanced' => 'Advanced']),
                Tables\Filters\TernaryFilter::make('is_active'),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Product Details')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->required()
                            ->unique(Product::class, 'slug', ignoreRecord: true)
                            ->maxLength(255),
                        Select::make('tier')
                            ->options([
                                'basic' => 'Basic',
                                'pro' => 'Professional',
                                'advanced' => 'Advanced',
                            ])
                            ->required(),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->default(true),
                    ]),
                    Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            Section::make('Pricing (per term)')
                ->schema([
                    Repeater::make('prices')
                        ->relationship('prices')
                        ->schema([
                            Grid::make(4)->schema([
                                Select::make('term')
                                    ->options(static::termOptions())
                                    ->required()
                                    ->native(false),
                                TextInput::make('price_inr')
                                    ->label('Price INR (paise)')
                                    ->numeric()
                                    ->helperText('e.g. 600000 = ₹6,000'),
                                TextInput::make('price_usd')
                                    ->label('Price USD (cents)')
                                    ->numeric()
                                    ->helperText('e.g. 8800 = $88'),
                                Toggle::make('is_active')
                                    ->default(true),
                            ]),
                        ])
                        ->columns(1)
                        ->addActionLabel('Add price tier')
                        ->defaultItems(0),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ProductResource\Pages\ListProducts::route('/'),
            'create' => \App\Filament\Resources\ProductResource\Pages\CreateProduct::route('/create'),
            'edit' => \App\Filament\Resources\ProductResource\Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
