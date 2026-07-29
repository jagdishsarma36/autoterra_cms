<?php

namespace App\Filament\Resources\PageCmsResource\Pages;

use App\Filament\Resources\PageCmsResource;
use App\Models\PageContent;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageCmsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('viewPage')
                ->label('View Page')
                ->icon('heroicon-o-eye')
                ->url(fn () => '/' . $this->record->slug)
                ->openUrlInNewTab(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $pageKey = 'cms:' . $data['slug'];
        $blocks = PageContent::where('page', $pageKey)
            ->orderBy('id')
            ->get()
            ->map(function ($block) {
                $type = $block->type;
                $sectionClass = null;
                // Decode html_section:white → type=html_section, section_class=white
                if (str_starts_with($type, 'html_section:')) {
                    $sectionClass = substr($type, 13); // after "html_section:"
                    $type = 'html_section';
                }
                return [
                    'key' => $block->key,
                    'type' => $type,
                    'value' => $block->value,
                    'section_class' => $sectionClass,
                    'section_class_custom' => null,
                ];
            })
            ->toArray();

        // Pre-fill section_class and section_class_custom for each block
        foreach ($blocks as &$block) {
            if ($block['section_class'] && !in_array($block['section_class'], ['section-white', 'section-light', 'section-dark'])) {
                $block['section_class_custom'] = $block['section_class'];
                $block['section_class'] = 'custom';
            }
        }

        $data['content_blocks'] = $blocks;
        return $data;
    }

    protected function afterSave(): void
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
            // RichEditor returns array (tiptap JSON), store as JSON
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $type = $block['type'] ?? 'text';
            // Encode section class into type for html_section blocks
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

        PageContent::clearCache($pageKey);
    }
}
