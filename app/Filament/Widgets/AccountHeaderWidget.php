<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\Widget;

class AccountHeaderWidget extends Widget
{
    protected string $view = 'filament.widgets.account-header';

    protected function getViewData(): array
    {
        return [
            'latestPost' => Post::published()->latest('published_at')->first(),
        ];
    }
}
