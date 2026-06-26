<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestCustomersWidget extends TableWidget
{
    protected static ?int $sort = 3;
    protected static ?string $heading = 'Latest Customers';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(User::where('role', 'user')->latest())
            ->paginated([5])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Orders')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->label('View')
                    ->url(fn ($record): string => "/admin/users/{$record->id}")
                    ->icon('heroicon-o-eye')
                    ->color('gray'),
            ])
            ->poll('60s');
    }
}
