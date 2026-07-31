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
        Mail::to('shas.sarma@gmail.com')->send(new OrderConfirmation($this->record));
    }
}