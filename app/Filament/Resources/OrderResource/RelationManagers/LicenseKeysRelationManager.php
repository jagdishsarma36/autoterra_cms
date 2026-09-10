<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LicenseKeysRelationManager extends RelationManager
{
    protected static string $relationship = 'licenseKeys';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('license_key')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable(),
                Tables\Columns\TextColumn::make('license_key')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime('M j, Y'),
                Tables\Columns\TextColumn::make('activations_count')
                    ->label('Activations'),
                Tables\Columns\TextColumn::make('max_activations'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->form(fn (): array => static::getLicenseFormSchema()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form(fn (): array => static::getLicenseFormSchema()),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getLicenseFormSchema(): array
    {
        return [
            Forms\Components\Select::make('product_id')
                ->label('Product')
                ->relationship('product', 'name')
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('license_key')
                ->required()
                ->maxLength(255),
            Forms\Components\DatePicker::make('expires_at')
                ->required(),
            Forms\Components\TextInput::make('max_activations')
                ->numeric()
                ->default(1)
                ->minValue(1),
        ];
    }
}