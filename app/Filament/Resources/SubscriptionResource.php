<?php

namespace App\Filament\Resources;

use App\Models\Subscription;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;
    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationItems(): array
    {
        return [parent::getNavigationItems()[0]->label('Subscriptions')];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Commerce';
    }

    public static function getNavigationSort(): ?int
    {
        return 15;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-arrow-path';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('user.name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('product.name')->sortable(),
                Tables\Columns\TextColumn::make('term'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'cancelled' => 'danger',
                        'expired' => 'gray',
                        'paused' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->currency === 'INR'
                            ? '₹' . number_format($state, 0)
                            : '$' . number_format($state, 2)
                    ),
                Tables\Columns\TextColumn::make('current_period_end')->dateTime('M j, Y'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M j, Y'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'Active', 'cancelled' => 'Cancelled', 'expired' => 'Expired', 'paused' => 'Paused']),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Subscription Details')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->required(),
                        Select::make('term')
                            ->options([
                                'daily' => 'Daily',
                                'weekly' => 'Weekly',
                                '3mo' => '3 Months',
                                '6mo' => '6 Months',
                                '1yr' => '1 Year',
                                '3yr' => '3 Years',
                                '5yr' => '5 Years',
                            ])
                            ->required()
                            ->native(false),
                        Select::make('status')
                            ->options(['active' => 'Active', 'cancelled' => 'Cancelled', 'expired' => 'Expired', 'paused' => 'Paused'])
                            ->required(),
                        TextInput::make('amount')
                            ->label('Amount (paise/cents)')
                            ->numeric()
                            ->required(),
                        TextInput::make('currency')
                            ->required(),
                    ]),
                ]),
            Section::make('Razorpay Details')
                ->schema([
                    TextInput::make('razorpay_subscription_id')->readonly(),
                    TextInput::make('razorpay_plan_id')->readonly(),
                    DateTimePicker::make('current_period_start'),
                    DateTimePicker::make('current_period_end'),
                ])->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\SubscriptionResource\Pages\ListSubscriptions::route('/'),
            'view' => \App\Filament\Resources\SubscriptionResource\Pages\ViewSubscription::route('/{record}'),
            'edit' => \App\Filament\Resources\SubscriptionResource\Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
