<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use App\Services\RazorpayService;
use Filament\Actions;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
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
        ];
    }
}
