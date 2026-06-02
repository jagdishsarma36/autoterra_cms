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
        $pageKey = 'cms:' . $this->record->slug;
        $blocks = $this->data['content_blocks'] ?? [];

        PageContent::where('page', $pageKey)->delete();

        foreach ($blocks as $block) {
            if (empty($block['key'])) continue;
            $value = $block['value'] ?? '';
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $type = $block['type'] ?? 'text';
            if ($type === 'html_section') {
                $class = $block['section_class'] ?? 'section-white';
                if ($class === 'custom') {
                    $class = $block['section_class_custom'] ?: 'section-white';
                }
                $type = 'html_section:' . $class;
            }
            PageContent::create([
                'page' => $pageKey,
                'key' => $block['key'],
                'value' => $value,
                'type' => $type,
            ]);
        }
    }
}
