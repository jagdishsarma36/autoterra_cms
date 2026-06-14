<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use App\Models\LicenseKey;
use App\Models\Setting;
use App\Services\RazorpayService;
use Filament\Actions;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewSubscription extends ViewRecord
{
    protected static string $resource = SubscriptionResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->record->invoices = [];

        if ($this->record->razorpay_subscription_id) {
            $razorpay = app(RazorpayService::class);
            $result = $razorpay->fetchInvoices($this->record->razorpay_subscription_id);

            if (!isset($result['error'])) {
                $this->record->invoices = $result['items'] ?? [];
            }
        }
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->record)
            ->columns(1)
            ->schema([
                Section::make('Subscription Details')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('product.name')->label('Product'),
                            TextEntry::make('term')->label('Term'),
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge(fn (string $state): string => match ($state) {
                                    'active' => 'success',
                                    'cancelled' => 'danger',
                                    'expired' => 'gray',
                                    'paused' => 'warning',
                                    default => 'gray',
                                }),
                            TextEntry::make('created_at')->dateTime('M j, Y g:i A')->label('Created'),
                            TextEntry::make('current_period_start')->dateTime('M j, Y')->label('Period Start'),
                            TextEntry::make('current_period_end')->dateTime('M j, Y')->label('Next Billing'),
                        ]),
                    ]),

                Section::make('Payment Details')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('amount')
                                ->label('Amount')
                                ->formatStateUsing(fn ($state, $record) =>
                                    $record->currency === 'INR'
                                        ? '₹' . number_format($state, 0) . ' / ' . $record->term
                                        : '$' . number_format($state, 2) . ' / ' . $record->term
                                ),
                            TextEntry::make('currency')->label('Currency'),
                            TextEntry::make('razorpay_subscription_id')->label('Razorpay Sub ID')->copyable(),
                            TextEntry::make('razorpay_plan_id')->label('Razorpay Plan ID')->copyable(),
                            TextEntry::make('user.name')->label('User'),
                            TextEntry::make('user.email')->label('Email'),
                        ]),
                    ]),

                Section::make('Invoice History')
                    ->columnSpanFull()
                    ->schema([
                        \Filament\Infolists\Components\ViewEntry::make('invoices')
                            ->view('filament.infolists.components.invoice-table')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn () => "/dashboard/subscriptions/{$this->record->id}/print")
                ->openUrlInNewTab(),
            $this->getLicenseKeyAction(),
        ];
    }

    protected function getLicenseKeyAction(): Actions\Action
    {
        $record = $this->record;
        $license = LicenseKey::where('user_id', $record->user_id)
            ->where('product_id', $record->product_id)
            ->where('order_id', null)
            ->first();

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
                        'order_id' => null,
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
            });
    }
}
