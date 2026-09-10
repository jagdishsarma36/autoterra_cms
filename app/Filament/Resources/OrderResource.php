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
                Tables\Columns\TextColumn::make('products')
                    ->label('Products')
                    ->state(fn (Order $record): string =>
                        $record->orderItems->isNotEmpty()
                            ? $record->orderItems
                                ->filter(fn ($item) => (bool) $item->product)
                                ->map(fn ($item) => $item->product->name . ($item->quantity > 1 ? ' ×' . $item->quantity : ''))
                                ->unique()
                                ->implode(', ')
                            : ($record->product?->name ?? '—')
                    )
                    ->wrap()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('term')->label('Term'),
                Tables\Columns\TextColumn::make('currency'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->currency === 'INR'
                            ? '₹' . number_format($state / 100, 0)
                            : '$' . number_format($state / 100, 2)
                    ),
                Tables\Columns\TextColumn::make('coupon_code')->label('Coupon'),
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
            ->modifyQueryUsing(fn ($query) => $query->with(['product', 'orderItems.product']))
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
                            ->nullable(),
                        \Filament\Forms\Components\TextInput::make('term')
                            ->required(),
                        \Filament\Forms\Components\Select::make('currency')
                            ->options(['INR' => 'INR', 'USD' => 'USD'])
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->prefix(fn (\Filament\Forms\Components\TextInput $component) => ($component->getRecord()?->currency ?? 'INR') === 'INR' ? '₹' : chr(36))
                            ->formatStateUsing(fn (string $state): float => round((float) $state / 100, 2))
                            ->dehydrateStateUsing(fn (string $state): int => (int) round((float) $state * 100))
                            ->numeric()
                            ->required()
                            ->helperText('Enter amount in rupees. Stored as paise (multiply by 100).'),
                        \Filament\Forms\Components\TextInput::make('gst_amount')
                            ->label('GST')
                            ->prefix(fn (\Filament\Forms\Components\TextInput $component) => ($component->getRecord()?->currency ?? 'INR') === 'INR' ? '₹' : chr(36))
                            ->formatStateUsing(fn (string $state): float => round((float) $state / 100, 2))
                            ->dehydrateStateUsing(fn (string $state): int => (int) round((float) $state * 100))
                            ->numeric()
                            ->helperText('Enter GST in rupees. Stored as paise (multiply by 100).'),
                        \Filament\Forms\Components\TextInput::make('total_amount')
                            ->label('Total')
                            ->prefix(fn (\Filament\Forms\Components\TextInput $component) => ($component->getRecord()?->currency ?? 'INR') === 'INR' ? '₹' : chr(36))
                            ->formatStateUsing(fn (string $state): float => round((float) $state / 100, 2))
                            ->dehydrateStateUsing(fn (string $state): int => (int) round((float) $state * 100))
                            ->numeric()
                            ->required()
                            ->helperText('Enter total in rupees. Stored as paise (multiply by 100).'),
                        \Filament\Forms\Components\TextInput::make('discount_amount')
                            ->label('Discount')
                            ->prefix(fn (\Filament\Forms\Components\TextInput $component) => ($component->getRecord()?->currency ?? 'INR') === 'INR' ? '₹' : chr(36))
                            ->formatStateUsing(fn (string $state): float => round((float) $state / 100, 2))
                            ->dehydrateStateUsing(fn (string $state): int => (int) round((float) $state * 100))
                            ->numeric()
                            ->default(0)
                            ->helperText('Enter discount in rupees. Stored as paise (multiply by 100).'),
                        \Filament\Forms\Components\TextInput::make('coupon_code')
                            ->label('Coupon Code')
                            ->nullable(),
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
        return [
            \App\Filament\Resources\OrderResource\RelationManagers\OrderItemsRelationManager::class,
            \App\Filament\Resources\OrderResource\RelationManagers\LicenseKeysRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\OrderResource\Pages\ListOrders::route('/'),
            'create' => \App\Filament\Resources\OrderResource\Pages\CreateOrder::route('/create'),
            'view' => \App\Filament\Resources\OrderResource\Pages\ViewOrder::route('/{record}'),
            'edit' => \App\Filament\Resources\OrderResource\Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
