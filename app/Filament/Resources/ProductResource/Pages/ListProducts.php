<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('cart')
                ->label(fn () =>
                    'Cart (' . app(CartService::class)->count() . ')'
                )
                ->icon('heroicon-o-shopping-cart')
                ->url(
                    fn () => route('filament.admin.pages.cart')
                ),
        ];
    }
}
