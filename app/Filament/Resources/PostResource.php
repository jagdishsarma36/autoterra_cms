<?php

namespace App\Filament\Resources;

use App\Models\Post;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $modelLabel = 'Blog Post';
    protected static ?string $pluralModelLabel = 'Blog Posts';

    public static function getNavigationItems(): array
    {
        return [parent::getNavigationItems()[0]->label('Blog Posts')];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'CMS';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-pencil-square';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->searchable(),
                Tables\Columns\TextColumn::make('category'),
                Tables\Columns\TextColumn::make('author_name'),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
                Tables\Columns\TextColumn::make('published_at')->dateTime('M j, Y')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M j, Y'),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published'),
                Tables\Filters\SelectFilter::make('category')
                    ->options(fn () => Post::distinct()->pluck('category', 'category')->filter()->toArray()),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Post Details')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('title')->required()->maxLength(255),
                        TextInput::make('slug')->unique(Post::class, 'slug', ignoreRecord: true)->maxLength(255),
                        TextInput::make('author_name')->maxLength(255),
                        TextInput::make('category')->maxLength(255),
                        Toggle::make('is_published')->default(false),
                        DatePicker::make('published_at'),
                    ]),
                    TextInput::make('featured_image')->label('Featured Image URL')->maxLength(500),
                    Textarea::make('excerpt')->rows(3)->columnSpanFull(),
                    TagsInput::make('tags')->columnSpanFull(),
                ]),
            Section::make('Content')
                ->schema([
                    RichEditor::make('content')->columnSpanFull(),
                ]),
            Section::make('SEO')
                ->schema([
                    TextInput::make('meta_title')->label('Meta Title')->maxLength(255),
                    Textarea::make('meta_description')->label('Meta Description')->rows(2),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PostResource\Pages\ListPosts::route('/'),
            'create' => \App\Filament\Resources\PostResource\Pages\CreatePost::route('/create'),
            'edit' => \App\Filament\Resources\PostResource\Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
