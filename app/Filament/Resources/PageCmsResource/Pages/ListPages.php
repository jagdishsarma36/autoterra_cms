<?php

namespace App\Filament\Resources\PageCmsResource\Pages;

use App\Filament\Resources\PageCmsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPages extends ListRecords
{
    protected static string $resource = PageCmsResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
