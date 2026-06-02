<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Resources\MediaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditMedia extends EditRecord
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('copyUrl')
                ->label('Copy URL')
                ->icon('heroicon-o-clipboard')
                ->color('gray')
                ->modalHeading('Copy Media URL')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalContent(fn () => view('filament.modals.copy-media-url', ['url' => $this->record->url])),
            Actions\Action::make('view')
                ->label('View')
                ->icon('heroicon-o-eye')
                ->url(fn () => $this->record->url)
                ->openUrlInNewTab(),
        ];
    }
}
