<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\StateCasts\RichEditorStateCast;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;

/**
 * A state cast that defers the expensive Tiptap parse until a block's editor
 * is actually visible.
 *
 * Filament's schema hydration runs the `set()` cast on every component in a
 * repeater, hidden or not, and dehydration runs `get()` on every dehydrated
 * component. For a page with dozens of Content Blocks that meant dozens of
 * full Tiptap editor instantiations per request — hundreds of megabytes for
 * big pages — even though at most one editor can be on screen at a time.
 *
 * Closed blocks pass their raw value straight through untouched, so opening a
 * page costs almost nothing per block and existing content is never
 * re-serialized (it renders byte-for-byte as stored).
 */
class GatedRichEditorStateCast implements StateCast
{
    protected RichEditorStateCast $inner;

    public function __construct(protected RichEditor $component)
    {
        $this->inner = new RichEditorStateCast($this->component);
    }

    public function get(mixed $state): mixed
    {
        return $this->component->isVisible()
            ? $this->inner->get($state)
            : $state;
    }

    public function set(mixed $state): mixed
    {
        return $this->component->isVisible()
            ? $this->inner->set($state)
            : $state;
    }
}