<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderItems';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable(),
                Tables\Columns\TextColumn::make('term')
                    ->badge(),
                Tables\Columns\TextColumn::make('quantity')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price_inr')
                    ->label('Unit Price (₹)')
                    ->state(fn (mixed $record): mixed => $record->unit_price_inr)
                    ->formatStateUsing(fn ($state) => chr(36) . number_format($state / 100, 2)),
                Tables\Columns\TextColumn::make('gst_amount')
                    ->label('GST (₹)')
                    ->state(fn (mixed $record): mixed => $record->gst_amount)
                    ->formatStateUsing(fn ($state) => chr(36) . number_format($state / 100, 2)),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total (₹)')
                    ->state(fn (mixed $record): mixed => $record->total_amount)
                    ->formatStateUsing(fn ($state) => chr(36) . number_format($state / 100, 2)),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->form(fn (): array => static::getItemFormSchema()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form(fn (): array => static::getItemFormSchema()),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getItemFormSchema(): array
    {
        return [
            Forms\Components\Select::make('product_id')
                ->label('Product')
                ->relationship('product', 'name')
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('term')
                ->required(),
            Forms\Components\TextInput::make('quantity')
                ->numeric()
                ->default(1)
                ->required(),
            Forms\Components\TextInput::make('unit_price_inr')
                ->label('Unit Price (₹)')
                ->numeric()
                ->formatStateUsing(fn (?int $state): mixed => $state ? $state / 100 : null)
                ->dehydrateStateUsing(fn ($state): int => (int) round((float) ($state ?? 0) * 100)),
            Forms\Components\TextInput::make('gst_amount')
                ->label('GST (₹)')
                ->numeric()
                ->formatStateUsing(fn (?int $state): mixed => $state ? $state / 100 : null)
                ->dehydrateStateUsing(fn ($state): int => (int) round((float) ($state ?? 0) * 100)),
            Forms\Components\TextInput::make('total_amount')
                ->label('Total (₹)')
                ->numeric()
                ->formatStateUsing(fn (?int $state): mixed => $state ? $state / 100 : null)
                ->dehydrateStateUsing(fn ($state): int => (int) round((float) ($state ?? 0) * 100)),
        ];
    }
}