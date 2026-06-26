<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\Widget;

class BlogLinkWidget extends Widget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected string $view = 'filament.widgets.blog-link';

    protected function getViewData(): array
    {
        return [
            'postCount' => Post::published()->count(),
        ];
    }
}
