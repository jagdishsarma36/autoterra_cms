<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestOrdersWidget extends TableWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Latest Orders';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::with(['user', 'product'])->latest())
            ->paginated([5])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable(),
                Tables\Columns\TextColumn::make('term')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '1yr' => 'success',
                        '3yr' => 'info',
                        '5yr' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state): string => '₹' . number_format($state / 100))
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->label('View')
                    ->url(fn ($record): string => "/admin/orders/{$record->id}/view")
                    ->icon('heroicon-o-eye')
                    ->color('gray'),
            ])
            ->poll('60s');
    }
}
