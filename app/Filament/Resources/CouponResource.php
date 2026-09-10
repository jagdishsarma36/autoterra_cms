<?php

namespace App\Filament\Resources;

use App\Models\Coupon;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;
    protected static ?string $recordTitleAttribute = 'code';

    public static function getNavigationItems(): array
    {
        return [parent::getNavigationItems()[0]->label('Coupons')];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Commerce';
    }

    public static function getNavigationSort(): ?int
    {
        return 12;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-ticket';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->badge(fn (string $state): string => match ($state) {
                        'percentage' => 'info',
                        'fixed' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->type === 'percentage'
                            ? $state . '%'
                            : '₹' . number_format($state / 100, 0)
                    ),
                Tables\Columns\TextColumn::make('used_count')
                    ->label('Used')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_uses')
                    ->label('Max Uses')
                    ->formatStateUsing(fn ($state) => $state ?? '∞'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('starts_at')->dateTime('M j, Y'),
                Tables\Columns\TextColumn::make('expires_at')->dateTime('M j, Y'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M j, Y'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(['percentage' => 'Percentage', 'fixed' => 'Fixed']),
                Tables\Filters\TernaryFilter::make('is_active'),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Coupon Details')
                ->schema([
                    Grid::make(2)->schema([
                        \Filament\Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        \Filament\Forms\Components\Select::make('type')
                            ->options(['percentage' => 'Percentage', 'fixed' => 'Fixed Amount'])
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('value')
                            ->label('Value')
                            ->helperText(fn ($get) => $get('type') === 'percentage' ? 'Enter percentage (e.g., 20 for 20% off)' : 'Enter amount in paise (e.g., 50000 for ₹500 off)')
                            ->numeric()
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('min_order_amount')
                            ->label('Minimum Order (paise)')
                            ->numeric()
                            ->nullable(),
                        \Filament\Forms\Components\TextInput::make('max_uses')
                            ->numeric()
                            ->nullable(),
                        \Filament\Forms\Components\TextInput::make('used_count')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ]),
                ]),
            Section::make('Description & Restrictions')
                ->schema([
                    \Filament\Forms\Components\Textarea::make('description')
                        ->nullable()
                        ->rows(2),
                    \Filament\Forms\Components\TagsInput::make('product_ids')
                        ->label('Restrict to Product IDs')
                        ->placeholder('Add product ID')
                        ->helperText('Leave empty to apply to all products.')
                        ->separator(','),
                    Grid::make(2)->schema([
                        \Filament\Forms\Components\Toggle::make('is_active')
                            ->default(true),
                        \Filament\Forms\Components\DateTimePicker::make('starts_at')
                            ->nullable(),
                        \Filament\Forms\Components\DateTimePicker::make('expires_at')
                            ->nullable(),
                    ]),
                ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\CouponResource\Pages\ListCoupons::route('/'),
            'create' => \App\Filament\Resources\CouponResource\Pages\CreateCoupon::route('/create'),
            'edit' => \App\Filament\Resources\CouponResource\Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
