<?php

namespace App\Filament\Resources;

use App\Models\Media;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $modelLabel = 'Media';
    protected static ?string $pluralModelLabel = 'Media Library';

    public static function getNavigationItems(): array
    {
        return [parent::getNavigationItems()[0]->label('Media Library')];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'CMS';
    }

    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-photo';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\ImageColumn::make('path')
                    ->disk('public')
                    ->label('Preview')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => match(true) {
                        $record->isVideo() => 'https://cdn.jsdelivr.net/npm/@tabler/icons@latest/icons/video.svg',
                        $record->isDocument() => 'https://cdn.jsdelivr.net/npm/@tabler/icons@latest/icons/file-text.svg',
                        $record->isArchive() => 'https://cdn.jsdelivr.net/npm/@tabler/icons@latest/icons/zip.svg',
                        default => 'https://cdn.jsdelivr.net/npm/@tabler/icons@latest/icons/photo.svg',
                    })
                    ->limit(50),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('alt')
                    ->searchable()
                    ->label('Alt Text')
                    ->limit(30),
                Tables\Columns\TextColumn::make('mime_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match(true) {
                        str_starts_with($state, 'image/') => 'success',
                        str_starts_with($state, 'video/') => 'warning',
                        str_contains($state, 'pdf') => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('human_size')
                    ->label('Size')
                    ->sortable(),
                Tables\Columns\TextColumn::make('folder')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('folder')
                    ->options(fn () => Media::distinct()->pluck('folder', 'folder')->toArray())
                    ->placeholder('All Folders'),
                Tables\Filters\SelectFilter::make('mime_type')
                    ->label('Type')
                    ->options([
                        'image/' => 'Images',
                        'video/' => 'Videos',
                        'application/pdf' => 'PDFs',
                        'application/msword' => 'Word Docs',
                        'application/vnd.ms-excel' => 'Excel',
                        'application/zip' => 'Archives',
                        'text/csv' => 'CSV',
                    ])
                    ->placeholder('All Types'),
            ])
            ->actions([
                Action::make('copyUrl')
                    ->label('Copy URL')
                    ->icon('heroicon-o-clipboard')
                    ->color('gray')
                    ->modalHeading('Copy Media URL')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn ($record) => view('filament.modals.copy-media-url', ['url' => $record->url])),
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => $record->url)
                    ->openUrlInNewTab(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Upload')
                    ->schema([
                        \Filament\Forms\Components\View::make('filament.components.direct-upload')
                            ->columnSpanFull(),
                    ]),

                Section::make('Preview')
                    ->schema([
                        Placeholder::make('preview')
                            ->label(' ')
                            ->content(function ($get) {
                                $path = $get('path');
                                if (!$path) return 'No file uploaded yet.';
                                $url = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
                                $mime = $get('mime_type') ?? '';
                                if (str_starts_with($mime, 'image/')) {
                                    return '<img src="' . $url . '" style="max-width:100%;max-height:300px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);">';
                                }
                                if (str_starts_with($mime, 'video/')) {
                                    return '<video src="' . $url . '" controls style="max-width:100%;max-height:300px;border-radius:8px;"></video>';
                                }
                                return '<a href="' . $url . '" target="_blank" style="color:var(--cyan);font-weight:600;">' . $url . '</a>';
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('Media Details')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('alt')
                                ->label('Alt Text')
                                ->maxLength(255)
                                ->helperText('Descriptive text for screen readers and SEO'),
                            TextInput::make('title')
                                ->label('Title')
                                ->maxLength(255)
                                ->helperText('Shown on hover or in media details'),
                            TextInput::make('folder')
                                ->default('/')
                                ->maxLength(255)
                                ->helperText('Organize media into folders (e.g. /blog, /products)'),
                        ]),
                        Textarea::make('caption')
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('File Info')
                    ->schema([
                        Grid::make(4)->schema([
                            Placeholder::make('info_file_name')
                                ->label('File Name')
                                ->content(fn ($get) => $get('file_name') ?? '-'),
                            Placeholder::make('info_mime')
                                ->label('MIME Type')
                                ->content(fn ($get) => $get('mime_type') ?? '-'),
                            Placeholder::make('info_size')
                                ->label('Size')
                                ->content(fn ($get) => $get('human_size') ?? '-'),
                            Placeholder::make('info_disk')
                                ->label('Disk')
                                ->content(fn ($get) => $get('disk') ?? '-'),
                        ]),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\MediaResource\Pages\ListMedia::route('/'),
            'create' => \App\Filament\Resources\MediaResource\Pages\CreateMedia::route('/create'),
            'edit' => \App\Filament\Resources\MediaResource\Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}
