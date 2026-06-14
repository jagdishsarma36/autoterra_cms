<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\LicenseKey;
use App\Models\Setting;
use Filament\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

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

    protected function getLicenseKeyAction(): Actions\Action
    {
        $record = $this->record;
        $license = LicenseKey::where('order_id', $record->id)->first();

        return Actions\Action::make('manageLicenseKey')
            ->label($license ? 'Edit License Key' : 'Add License Key')
            ->icon($license ? 'heroicon-o-key' : 'heroicon-o-plus-circle')
            ->color($license ? 'warning' : 'success')
            ->modalHeading($license ? 'Edit License Key' : 'Add License Key')
            ->modalSubmitActionLabel($license ? 'Update' : 'Save')
            ->form([
                Section::make('License Key Details')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('license_key')
                                ->label('License Key')
                                ->required()
                                ->maxLength(255)
                                ->default($license?->license_key),
                            DatePicker::make('expires_at')
                                ->label('Expiry Date')
                                ->required()
                                ->default($license?->expires_at),
                        ]),
                    ]),
            ])
            ->action(function (array $data) use ($record, $license): void {
                if (Setting::get('license_key_mode', 'auto') !== 'manual') {
                    Notification::make()
                        ->title('License key generation is set to automatic')
                        ->warning()
                        ->send();
                    return;
                }

                if ($license) {
                    $license->update([
                        'license_key' => $data['license_key'],
                        'expires_at' => $data['expires_at'],
                    ]);
                } else {
                    LicenseKey::create([
                        'user_id' => $record->user_id,
                        'product_id' => $record->product_id,
                        'order_id' => $record->id,
                        'license_key' => $data['license_key'],
                        'activated_at' => now(),
                        'expires_at' => $data['expires_at'],
                        'is_active' => true,
                        'max_activations' => 1,
                    ]);
                }

                Notification::make()
                    ->title('License key saved')
                    ->success()
                    ->send();

                $this->refreshFormData(['licenseKeys']);
            });
    }
}
