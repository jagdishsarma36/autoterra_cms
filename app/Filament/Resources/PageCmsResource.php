<?php

namespace App\Filament\Resources;

use App\Models\PageCms;
use App\Models\PageContent;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageCmsResource extends Resource
{
    protected static ?string $model = PageCms::class;
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $modelLabel = 'Page';
    protected static ?string $pluralModelLabel = 'Pages';

    public static function getNavigationItems(): array
    {
        return [parent::getNavigationItems()[0]->label('Pages')];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'CMS';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->searchable(),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
                Tables\Columns\TextColumn::make('published_at')->dateTime('M j, Y')->sortable(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M j, Y'),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('viewPage')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => '/' . $record->slug)
                    ->openUrlInNewTab(),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Page Details')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('title')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, $set) {
                                    if (filled($state)) {
                                        $set('slug', Str::slug($state));
                                    }
                                }),
                            TextInput::make('slug')
                                ->unique(PageCms::class, 'slug', ignoreRecord: true)
                                ->maxLength(255)
                                ->helperText('Use slashes for nested URLs, e.g. test/my-page'),
                            Toggle::make('is_published')->default(true),
                            DatePicker::make('published_at'),
                            TextInput::make('sort_order')->numeric()->default(0),
                        ]),
                        TextInput::make('featured_image')
                            ->label('Featured Image URL')
                            ->maxLength(500),
                        Textarea::make('excerpt')->rows(3)->columnSpanFull(),
                    ]),

                Section::make('Content Blocks')
                    ->description('Type any key that exists on other pages (e.g. hero.heading, hero.button_primary_text) — it will auto-fill from the existing value. Or create new blocks manually.')
                    ->schema([
                        Repeater::make('content_blocks')
                            ->schema([
                                Grid::make(12)->schema([
                                    TextInput::make('key')
                                        ->label('Key')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('hero.heading')
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state, $set, $get) {
                                            if (!$state) return;
                                            $existing = PageContent::where('key', $state)->first();
                                            if ($existing && !$get('value')) {
                                                $set('type', $existing->type);
                                                $set('value', $existing->value);
                                            }
                                        })
                                        ->columnSpan(5),
                                    Select::make('type')
                                        ->options([
                                            'text' => 'Text',
                                            'html_inline' => 'HTML (inline)',
                                            'richtext' => 'Rich Text',
                                            'json' => 'JSON',
                                            'html' => 'HTML Block',
                                            'html_section' => 'HTML Block + Section',
                                        ])
                                        ->required()
                                        ->default('richtext')
                                        ->live()
                                        ->columnSpan(2),
                                    Placeholder::make('exists_badge')
                                        ->label(' ')
                                        ->content(function ($get) {
                                            $key = $get('key');
                                            if (!$key) return '';
                                            $existing = PageContent::where('key', $key)->first();
                                            if ($existing) {
                                                return "✓ Imported from: {$existing->page}";
                                            }
                                            return '+ New block';
                                        })
                                        ->columnSpan(5),
                                ]),
                                Textarea::make('value')
                                    ->label('Value')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->visible(fn ($get) => $get('type') === 'text'),
                                Textarea::make('value')
                                    ->label('HTML Content')
                                    ->rows(4)
                                    ->columnSpanFull()
                                    ->visible(fn ($get) => $get('type') === 'html_inline')
                                    ->helperText('Inline HTML tags: <br>, <span>, <strong>, <em>, <a>, <sup>, <sub> — renders as-is.'),
                                Textarea::make('value')
                                    ->label('HTML Content')
                                    ->rows(8)
                                    ->columnSpanFull()
                                    ->visible(fn ($get) => $get('type') === 'richtext')
                                    ->helperText('Use HTML tags: h2, h3, p, strong, em, ul, li, a, blockquote'),
                                Textarea::make('value')
                                    ->label('JSON Value')
                                    ->rows(8)
                                    ->columnSpanFull()
                                    ->visible(fn ($get) => $get('type') === 'json')
                                    ->helperText('Valid JSON — arrays or objects'),
                                Textarea::make('value')
                                    ->label('HTML Block')
                                    ->rows(12)
                                    ->columnSpanFull()
                                    ->visible(fn ($get) => $get('type') === 'html')
                                    ->helperText('Paste raw HTML. Renders as-is, no wrapper.'),
                                Textarea::make('value')
                                    ->label('HTML Block')
                                    ->rows(12)
                                    ->columnSpanFull()
                                    ->visible(fn ($get) => str_starts_with((string) $get('type'), 'html_section'))
                                    ->helperText('Paste raw HTML. Wraps in a <section> tag with the class you choose below.'),
                                Select::make('section_class')
                                    ->label('Section Class')
                                    ->options([
                                        'section-white' => 'section-white',
                                        'section-light' => 'section-light',
                                        'section-dark' => 'section-dark',
                                        'custom' => 'Custom...',
                                    ])
                                    ->default('section-white')
                                    ->live()
                                    ->visible(fn ($get) => str_starts_with((string) $get('type'), 'html_section'))
                                    ->columnSpan(2),
                                TextInput::make('section_class_custom')
                                    ->label('Custom Section Class')
                                    ->placeholder('e.g. section-white my-custom-class')
                                    ->visible(fn ($get) => str_starts_with((string) $get('type'), 'html_section') && ($get('section_class') ?? '') === 'custom')
                                    ->columnSpan(2),
                            ])
                            ->columns(1)
                            ->addActionLabel('Add block')
                            ->defaultItems(0)
                            ->reorderable(),
                    ]),

                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(255),
                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(2),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PageCmsResource\Pages\ListPages::route('/'),
            'create' => \App\Filament\Resources\PageCmsResource\Pages\CreatePage::route('/create'),
            'edit' => \App\Filament\Resources\PageCmsResource\Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
