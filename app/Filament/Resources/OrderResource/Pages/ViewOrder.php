<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
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
        ];
    }
}
