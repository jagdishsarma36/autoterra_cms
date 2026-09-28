<?php

namespace App\Filament\Resources;


use App\Filament\Forms\GatedRichEditor;
use App\Models\PageCms;
use App\Models\PageContent;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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

    /**
     * Every content block on the site, loaded at most once per request and
     * used to resolve the "imported from another page" lookups.
     *
     * @var array<string, array{page: string, key: string, type: string, value: string}>|null
     */
    protected static ?array $blockIndex = null;

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
                Tables\Columns\TextColumn::make('visibility')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'logged_in' => 'info',
                        'specific_users' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'logged_in' => 'Logged in',
                        'specific_users' => 'Specific users',
                        default => 'Public',
                    }),
                Tables\Columns\TextColumn::make('published_at')->dateTime('M j, Y')->sortable(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M j, Y'),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published'),
                Tables\Filters\SelectFilter::make('visibility')
                    ->options([
                        'public' => 'Public',
                        'logged_in' => 'Logged-in users only',
                        'specific_users' => 'Specific users only',
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('viewPage')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => '/' . $record->slug)
                    ->openUrlInNewTab(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
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

                Section::make('Visibility')
                    ->description('Control who can view this page on the public site. Admin preview is always available.')
                    ->schema([
                        Select::make('visibility')
                            ->label('Visibility')
                            ->options([
                                'public' => 'Public',
                                'logged_in' => 'Logged-in users only',
                                'specific_users' => 'Specific users only',
                            ])
                            ->default('public')
                            ->required()
                            ->live(),
                        Select::make('visible_user_ids')
                            ->label('Visible to these users')
                            ->multiple()
                            ->searchable()
                            ->options(fn () => \App\Models\User::orderBy('email')->pluck('email', 'id'))
                            ->visible(fn ($get) => $get('visibility') === 'specific_users')
                            ->columnSpanFull()
                            ->helperText('Only these logged-in users will be able to view the page.'),
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

                Section::make('Content Blocks')
                    ->columnSpanFull()
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
                                            if (! $state) {
                                                return;
                                            }

                                            $existing = static::existingBlock($state);

                                            if (! $existing) {
                                                return;
                                            }

                                            // Never clobber content the user has
                                            // already typed into this block.
                                            if (filled($get('value')) || filled($get('wysiwyg_value'))) {
                                                return;
                                            }

                                            // Section-class blocks are stored in
                                            // the `type` column with a suffix
                                            // (e.g. `wysiwyg:section-dark`), so
                                            // split it back into the base type
                                            // plus the class controls.
                                            $existingType = $existing['type'];
                                            $sectionClass = null;

                                            if (str_starts_with($existingType, 'wysiwyg:')) {
                                                $sectionClass = substr($existingType, 8);
                                                $existingType = 'wysiwyg';
                                            } elseif (str_starts_with($existingType, 'html_section:')) {
                                                $sectionClass = substr($existingType, 13);
                                                $existingType = 'html_section';
                                            }

                                            $set('type', $existingType);

                                            if ($sectionClass !== null) {
                                                if (in_array($sectionClass, ['section-white', 'section-light', 'section-dark'], true)) {
                                                    $set('section_class', $sectionClass);
                                                } else {
                                                    $set('section_class', 'custom');
                                                    $set('section_class_custom', $sectionClass);
                                                }
                                            }

                                            if ($existingType === 'wysiwyg') {
                                                // The editor needs HTML, not a
                                                // Tiptap JSON string — its JS
                                                // parses strings as HTML and
                                                // would show the raw JSON.
                                                $set('wysiwyg_value', PageCms::renderRichContent($existing['value']));
                                                $set('is_editing', true);

                                                return;
                                            }

                                            $set('value', $existing['value']);
                                        })
                                        ->columnSpan(5),
                                    Select::make('type')
                                        ->options([
                                            'text' => 'Text',
                                            'html_inline' => 'HTML (inline)',
                                            'richtext' => 'Rich Text',
                                            'wysiwyg' => 'WYSIWYG Editor',
                                            'json' => 'JSON',
                                            'html' => 'HTML Block',
                                            'html_section' => 'HTML Block + Section',
                                        ])
                                        ->required()
                                        ->default('richtext')
                                        ->live()
                                        ->afterStateUpdated(function ($state, $get, $set) {
                                            // The editor and the plain textarea keep
                                            // separate state, so carry the content
                                            // across when the type changes instead
                                            // of silently dropping it.
                                            if ($state === 'wysiwyg') {
                                                if (blank($get('wysiwyg_value')) && filled($get('value'))) {
                                                    $set('wysiwyg_value', PageCms::renderRichContent($get('value')));
                                                }

                                                // A block that already holds content
                                                // opens its editor straight away; a
                                                // blank one stays closed until "Edit
                                                // content" is switched on.
                                                $set('is_editing', filled($get('wysiwyg_value')));

                                                return;
                                            }

                                            if (blank($get('value')) && filled($get('wysiwyg_value'))) {
                                                $set('value', PageCms::renderRichContent($get('wysiwyg_value')));
                                            }

                                            if ($state === 'html_section' && blank($get('section_class'))) {
                                                $set('section_class', 'section-white');
                                            }
                                        })
                                        ->columnSpan(2),
                                    Placeholder::make('exists_badge')
                                        ->label(' ')
                                        ->content(function ($get) {
                                            $key = $get('key');

                                            if (! $key) {
                                                return '+ New block';
                                            }

                                            $existing = static::existingBlock($key);

                                            return $existing
                                                ? "✓ Imported from: {$existing['page']}"
                                                : '+ New block';
                                        })
                                        ->columnSpan(5),
                                ]),

                                // One textarea for every non-WYSIWYG type. These used
                                // to be six separate components per block that all
                                // shared the `value` state path, which multiplied the
                                // markup (and PHP work) of every page by six.
                                Textarea::make('value')
                                    ->label(fn ($get) => match ($get('type')) {
                                        'html_inline' => 'HTML Content',
                                        'richtext' => 'HTML Content',
                                        'json' => 'JSON Value',
                                        'html', 'html_section' => 'HTML Block',
                                        default => 'Value',
                                    })
                                    ->rows(fn ($get) => match ($get('type')) {
                                        'text' => 3,
                                        'html_inline' => 4,
                                        'richtext' => 8,
                                        'json' => 8,
                                        'html', 'html_section' => 12,
                                        default => 4,
                                    })
                                    ->helperText(fn ($get) => match ($get('type')) {
                                        'html_inline' => 'Inline HTML tags: <br>, <span>, <strong>, <em>, <a>, <sup>, <sub> — renders as-is.',
                                        'richtext' => 'Use HTML tags: h2, h3, p, strong, em, ul, li, a, blockquote',
                                        'json' => 'Valid JSON — arrays or objects',
                                        'html' => 'Paste raw HTML. Renders as-is, no wrapper.',
                                        'html_section' => 'Paste raw HTML. Wraps in a <section> tag with the class you choose below.',
                                        default => null,
                                    })
                                    ->visible(fn ($get) => $get('type') !== 'wysiwyg')
                                    ->dehydrated(fn ($get) => $get('type') !== 'wysiwyg')
                                    ->columnSpanFull(),

                                // The WYSIWYG editor is expensive: it ships a Tiptap
                                // instance, a full toolbar, and runs a PHP Tiptap
                                // parse on every render. Rendering one per block is
                                // what made big pages exhaust PHP's memory limit and
                                // hang the browser, so only the block whose switch is
                                // on gets an editor; the rest show a text preview.
                                Toggle::make('is_editing')
                                    ->label('Edit content')
                                    ->helperText('Turn this on to load the WYSIWYG editor for this block. Only one block needs it at a time.')
                                    ->live()
                                    ->default(false)
                                    ->dehydrated(false)
                                    ->visible(fn ($get) => $get('type') === 'wysiwyg')
                                    ->columnSpanFull(),

                                Placeholder::make('value_summary')
                                    ->label('Content')
                                    ->visible(fn ($get) => $get('type') === 'wysiwyg' && ! (bool) $get('is_editing'))
                                    ->content(fn ($get) => static::summariseValue($get('wysiwyg_value')))
                                    ->columnSpanFull(),

                                // NOTE: this deliberately does NOT share the `value`
                                // state path with the textarea above. A schema fill
                                // runs every field's state cast, hidden or not, so a
                                // RichEditor sitting on `value` rewrote every plain
                                // text / HTML block into a Tiptap document every
                                // time the page was opened, and saved it back.
                                GatedRichEditor::make('wysiwyg_value')
                                    ->label('WYSIWYG Content')
                                    ->visible(fn ($get) => $get('type') === 'wysiwyg' && (bool) $get('is_editing'))
                                    ->dehydrated(fn ($get) => $get('type') === 'wysiwyg')
                                    ->placeholder('Start typing…')
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'underline',
                                        'undo',
                                    ])
                                    ->helperText('WYSIWYG editor. Saved as HTML — legacy Tiptap JSON is converted to HTML when you open a block, so the editor never shows raw JSON.'),

                                Select::make('section_class')
                                    ->label('Section Class')
                                    ->options([
                                        'section-white' => 'section-white',
                                        'section-light' => 'section-light',
                                        'section-dark' => 'section-dark',
                                        'custom' => 'Custom...',
                                    ])
                                    // html_section always wraps in a <section>
                                    // (section-white is its historical default);
                                    // WYSIWYG blocks stay unwrapped unless a
                                    // class is picked, so they default to none.
                                    ->default(fn ($get): ?string => str_starts_with((string) $get('type'), 'html_section') ? 'section-white' : null)
                                    ->helperText('Wraps the block in a <section class="section …">. WYSIWYG blocks render without a wrapper if no class is chosen.')
                                    ->live()
                                    ->visible(fn ($get) => str_starts_with((string) $get('type'), 'html_section') || $get('type') === 'wysiwyg')
                                    ->columnSpan(2),
                                TextInput::make('section_class_custom')
                                    ->label('Custom Section Class')
                                    ->placeholder('e.g. section-white my-custom-class')
                                    ->visible(fn ($get) => (str_starts_with((string) $get('type'), 'html_section') || $get('type') === 'wysiwyg') && ($get('section_class') ?? '') === 'custom')
                                    ->columnSpan(2),
                            ])
                            ->itemLabel(fn (array $state): ?string => filled($state['key'] ?? null)
                                ? $state['key'] . ($state['type'] ?? '' ? ' · ' . $state['type'] : '')
                                : null)
                            ->columns(1)
                            ->addActionLabel('Add block')
                            ->defaultItems(0)
                            ->reorderable(),
                    ]),
            ]);
    }

    /**
     * Look up an existing content block by key, memoised for the duration of a
     * single render. The form used to run one query per block on every render,
     * which is a lot of pointless round trips on a page with 50+ blocks.
     *
     * @return array{page: string, key: string, type: string, value: string}|null
     */
    protected static function existingBlock(string $key): ?array
    {
        $index = static::$blockIndex ??= PageContent::query()
            ->get(['page', 'key', 'type', 'value'])
            ->keyBy('key')
            ->map(fn (PageContent $block): array => $block->only(['page', 'key', 'type', 'value']))
            ->all();

        return $index[$key] ?? null;
    }

    /**
     * A short, plain-text preview of a block value, shown instead of the
     * editor while a WYSIWYG block is closed. Understands both raw HTML and
     * Tiptap JSON documents.
     */
    protected static function summariseValue(mixed $value): string
    {
        $text = static::plainText($value);

        if ($text === '') {
            return '<em>Empty — turn on “Edit content” to start writing.</em>';
        }

        return e(Str::limit($text, 140));
    }

    protected static function plainText(mixed $value): string
    {
        if (blank($value)) {
            return '';
        }

        if (is_array($value)) {
            $value = json_encode($value);
        }

        $trimmed = trim((string) $value);

        if (str_starts_with($trimmed, '{') || str_starts_with($trimmed, '[')) {
            $decoded = json_decode($trimmed, true);

            if (is_array($decoded)) {
                return static::collectNodeText($decoded);
            }
        }

        return trim(html_entity_decode(strip_tags($trimmed)));
    }

    /**
     * @param  array<mixed>  $node
     */
    protected static function collectNodeText(array $node): string
    {
        $text = isset($node['text']) && is_string($node['text']) ? $node['text'] . ' ' : '';

        foreach ($node['content'] ?? [] as $child) {
            if (is_array($child)) {
                $text .= static::collectNodeText($child);
            }
        }

        return trim((string) preg_replace('/\s+/', ' ', $text));
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
