<?php

namespace App\Filament\Widgets;

use App\Models\QuoteRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentQuoteRequestsWidget extends TableWidget
{
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Recent Quote Requests';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return '';
        return $table
            ->query(QuoteRequest::latest())
            ->paginated([5])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company'),
                Tables\Columns\TextColumn::make('product'),
                Tables\Columns\TextColumn::make('seats')
                    ->label('Seats'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'contacted' => 'info',
                        'closed' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->label('View')
                    ->url(fn ($record): string => "/admin/quote-requests/{$record->id}/edit")
                    ->icon('heroicon-o-eye')
                    ->color('gray'),
            ])
            ->poll('60s');
    }
}
