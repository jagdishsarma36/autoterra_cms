<?php
namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
    
    protected function afterCreate(): void
    {
        Mail::to($this->record->user->email)->send(new OrderConfirmation($this->record));
    }
}