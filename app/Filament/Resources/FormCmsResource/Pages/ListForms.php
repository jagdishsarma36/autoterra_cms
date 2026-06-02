<?php

namespace App\Filament\Resources\FormCmsResource\Pages;

use App\Filament\Resources\FormCmsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListForms extends ListRecords
{
    protected static string $resource = FormCmsResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
