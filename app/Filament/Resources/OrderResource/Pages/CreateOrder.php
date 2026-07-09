<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\LicenseKey;
use App\Models\Setting;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function afterCreate(): void
    {
        $order = $this->record;

        if ($order->status !== 'paid') {
            return;
        }

        if (Setting::get('license_key_mode', 'auto') === 'manual') {
            return;
        }

        LicenseKey::create([
            'user_id' => $order->user_id,
            'product_id' => $order->product_id,
            'order_id' => $order->id,
            'license_key' => Str::uuid()->toString(),
            'activated_at' => now(),
            'expires_at' => now()->addDays(termDays($order->term)),
            'is_active' => true,
            'max_activations' => 1,
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return OrderResource::getUrl('view', ['record' => $this->record]);
    }
}
