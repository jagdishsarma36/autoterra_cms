<?php

namespace App\Filament\Resources;

use App\Models\Order;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationItems(): array
    {
        return [parent::getNavigationItems()[0]->label('Orders')];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Commerce';
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-shopping-cart';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('user.name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('product.name')->sortable(),
                Tables\Columns\TextColumn::make('term')->label('Term'),
                Tables\Columns\TextColumn::make('currency'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->currency === 'INR'
                            ? '₹' . number_format($state, 0)
                            : '$' . number_format($state, 2)
                    ),
                Tables\Columns\TextColumn::make('status')
                    ->badge(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('razorpay_order_id')->limit(20),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M j, Y'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed', 'refunded' => 'Refunded']),
                Tables\Filters\SelectFilter::make('currency')
                    ->options(['INR' => 'INR', 'USD' => 'USD']),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Order Details')
                ->schema([
                    Grid::make(2)->schema([
                        \Filament\Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        \Filament\Forms\Components\Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('term')
                            ->required(),
                        \Filament\Forms\Components\Select::make('currency')
                            ->options(['INR' => 'INR', 'USD' => 'USD'])
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('amount')
                            ->label('Amount (paise/cents)')
                            ->numeric()
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('gst_amount')
                            ->label('GST (paise)')
                            ->numeric(),
                        \Filament\Forms\Components\TextInput::make('total_amount')
                            ->label('Total (paise/cents)')
                            ->numeric()
                            ->required(),
                        \Filament\Forms\Components\Select::make('status')
                            ->options(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed', 'refunded' => 'Refunded'])
                            ->required(),
                        \Filament\Forms\Components\Select::make('billing_mode')
                            ->options(['upfront' => 'Upfront', 'monthly' => 'Monthly']),
                    ]),
                ]),
            Section::make('Payment Details')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('razorpay_order_id')->readonly(),
                    \Filament\Forms\Components\TextInput::make('razorpay_payment_id')->readonly(),
                ])->columns(2),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\OrderResource\Pages\ListOrders::route('/'),
            'view' => \App\Filament\Resources\OrderResource\Pages\ViewOrder::route('/{record}'),
            'edit' => \App\Filament\Resources\OrderResource\Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
