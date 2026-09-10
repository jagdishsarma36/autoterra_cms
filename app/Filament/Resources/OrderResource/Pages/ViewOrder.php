<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\LicenseKey;
use App\Models\Order;
use App\Models\Setting;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Collection;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function mountRecord(int|string $recordId): void
    {
        parent::mountRecord($recordId);
        $this->record->load(['orderItems.product']);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('printInvoice')
                ->label('Print Invoice')
                ->icon('heroicon-o-printer')
                ->url(fn () => '/admin/invoices/' . $this->record->id . '/print')
                ->openUrlInNewTab(),
            $this->getLicenseKeyAction(),
        ];
    }

    protected function orderProducts(Order $record): Collection
    {
        if ($record->orderItems->isNotEmpty()) {
            return $record->orderItems
                ->pluck('product')
                ->filter()
                ->unique('id')
                ->values();
        }

        return $record->product ? collect([$record->product]) : collect();
    }

    protected function getLicenseKeyAction(): Actions\Action
    {
        $record = $this->record;
        $products = $this->orderProducts($record);
        $existing = LicenseKey::where('order_id', $record->id)->get()->keyBy('product_id');

        return Actions\Action::make('manageLicenseKey')
            ->label('License Keys')
            ->icon('heroicon-o-key')
            ->color('warning')
            ->modalHeading('Manage License Keys')
            ->modalSubmitActionLabel('Save License Keys')
            ->form([
                Repeater::make('licenses')
                    ->label('License Keys')
                    ->schema([
                        Hidden::make('product_id'),
                        TextInput::make('product_name')
                            ->label('Product')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('license_key')
                            ->label('License Key')
                            ->required()
                            ->maxLength(255),
                        DatePicker::make('expires_at')
                            ->label('Expiry Date')
                            ->required(),
                        TextInput::make('max_activations')
                            ->label('Max Activations')
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                    ])
                    ->columns(2)
                    ->addable(false)
                    ->reorderable(false)
                    ->deletable(true)
                    ->default(fn () => $products->map(fn ($product) => [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'license_key' => $existing->get($product->id)?->license_key ?? '',
                        'expires_at' => $existing->get($product->id)?->expires_at?->format('Y-m-d'),
                        'max_activations' => (string) ($existing->get($product->id)?->max_activations ?? 1),
                    ])->all()),
            ])
            ->action(function (array $data) use ($record): void {
                if (Setting::get('license_key_mode', 'auto') !== 'manual') {
                    Notification::make()
                        ->title('License key generation is set to automatic')
                        ->warning()
                        ->send();
                    return;
                }

                $rows = $data['licenses'] ?? [];
                $updated = 0;
                $created = 0;

                foreach ($rows as $row) {
                    $productId = (int) ($row['product_id'] ?? 0);
                    if (! $productId) {
                        continue;
                    }

                    $license = LicenseKey::where('order_id', $record->id)
                        ->where('product_id', $productId)
                        ->first();

                    if ($license) {
                        $license->update([
                            'license_key' => $row['license_key'],
                            'expires_at' => $row['expires_at'],
                            'max_activations' => (int) ($row['max_activations'] ?? 1),
                        ]);
                        $updated++;
                    } else {
                        LicenseKey::create([
                            'user_id' => $record->user_id,
                            'product_id' => $productId,
                            'order_id' => $record->id,
                            'license_key' => $row['license_key'],
                            'activated_at' => now(),
                            'expires_at' => $row['expires_at'],
                            'is_active' => true,
                            'max_activations' => (int) ($row['max_activations'] ?? 1),
                        ]);
                        $created++;
                    }
                }

                Notification::make()
                    ->title("{$created} license key" . ($created === 1 ? '' : 's') . ' created, ' .
                        "{$updated} updated")
                    ->success()
                    ->send();
            });
    }
}