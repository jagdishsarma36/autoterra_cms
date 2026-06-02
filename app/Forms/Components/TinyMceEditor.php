<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class TinyMceEditor extends Field
{
    protected string $view = 'forms.components.tiny-mce-editor';

    protected int|float|null $height = 400;

    public function height(int|float|null $height): static
    {
        $this->height = $height;
        return $this;
    }

    public function getHeight(): int|float|null
    {
        return $this->height;
    }
}
