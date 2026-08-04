<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use Filament\Resources\Pages\CreateRecord;
use App\Mail\SubscriptionConfirmation;

class CreateSubscription extends CreateRecord
{
    protected static string $resource = SubscriptionResource::class;
}