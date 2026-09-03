<?php

namespace App\Filament\Pages;

use App\Services\CartService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Cart extends Page
{
    protected static ?string $navigationLabel = 'Cart';

    protected static ?string $title = 'Shopping Cart';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.cart';

    public array $cart = [];

    public function mount(): void
    {
        $this->refreshCart();
    }

    public function refreshCart(): void
    {
        $this->cart = app(CartService::class)->get();
    }

    public function increment(string $key): void
    {
        app(CartService::class)->increment($key);

        $this->refreshCart();
    }

    public function decrement(string $key): void
    {
        app(CartService::class)->decrement($key);

        $this->refreshCart();
    }

    public function remove(string $key): void
    {
        app(CartService::class)->remove($key);

        $this->refreshCart();

        Notification::make()
            ->title('Item removed')
            ->success()
            ->send();
    }

    public function clearCart(): void
    {
        app(CartService::class)->clear();

        $this->refreshCart();

        Notification::make()
            ->title('Cart cleared')
            ->success()
            ->send();
    }

    public function getCartCount(): int
    {
        return app(CartService::class)->count();
    }

    public function getSubtotal(): float
    {
        return app(CartService::class)->subtotal();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearCart')
                ->label('Clear Cart')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn () => $this->clearCart()),
        ];
    }
}