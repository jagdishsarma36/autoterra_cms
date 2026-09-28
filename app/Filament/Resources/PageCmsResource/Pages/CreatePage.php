<?php

namespace App\Filament\Resources\PageCmsResource\Pages;

use App\Filament\Resources\PageCmsResource;
use App\Models\PageContent;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageCmsResource::class;

    protected function afterCreate(): void
    {
        $this->saveBlocks();
    }

    protected function saveBlocks(): void
    {
        PageContent::syncForPage(
            'cms:' . $this->record->slug,
            $this->data['content_blocks'] ?? [],
        );
    }
}