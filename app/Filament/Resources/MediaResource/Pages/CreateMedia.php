<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Resources\MediaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['path'])) {
            $path = $data['path'];
            if (is_array($path)) {
                $path = $path[0] ?? '';
            }
            $data['path'] = $path;
            $data['file_name'] = basename($path);
            $data['mime_type'] = mime_content_type(storage_path('app/public/' . $path)) ?? 'application/octet-stream';
            $data['size'] = filesize(storage_path('app/public/' . $path)) ?? 0;
            $data['disk'] = 'public';
        }
        return $data;
    }
}
