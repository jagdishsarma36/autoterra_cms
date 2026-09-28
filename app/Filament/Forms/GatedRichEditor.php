<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\StateCasts\RichEditorStateCast;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;

class GatedRichEditor extends RichEditor
{
    /**
     * The stock cast builds a full Tiptap editor for every repeater item on
     * every page load, hidden or not, which is a big chunk of PHP memory per
     * block and the reason large Content Block pages hung the browser.
     *
     * This cast only parses a block when its editor is actually visible, i.e.
     * exactly the one block whose "Edit content" switch is on.
     *
     * @return array<StateCast>
     */
    public function getDefaultStateCasts(): array
    {
        return [new GatedRichEditorStateCast($this)];
    }

    /**
     * Construct the delegate cast exactly the way Filament does, so the two
     * stay in lock-step with the framework's own state handling.
     */
    public function makeStateCast(): RichEditorStateCast
    {
        return new RichEditorStateCast($this);
    }
}