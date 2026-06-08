<?php

namespace App\Filament\Resources;

use App\Models\PageContent;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageContentResource extends Resource
{
    protected static ?string $model = PageContent::class;

    public static function getNavigationItems(): array
    {
        return [parent::getNavigationItems()[0]->label('Page Content (CMS)')];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'CMS';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('page')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('key')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge(fn (string $state): string => match ($state) {
                        'text' => 'gray',
                        'richtext' => 'warning',
                        'json' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('value')
                    ->limit(80)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime('M j, Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('page')
                    ->options(PageContent::distinct()->pluck('page', 'page')->toArray()),
                Tables\Filters\SelectFilter::make('type')
                    ->options(['text' => 'Text', 'richtext' => 'Rich Text', 'json' => 'JSON']),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Content Block')
                    ->schema([
                        Forms\Components\Select::make('page')
                            ->options([
                                'global' => 'Global',
                                'home' => 'Home',
                                'about' => 'About',
                                'products' => 'Products',
                                'pricing' => 'Pricing',
                                'buy' => 'Buy',
                                'contact' => 'Contact',
                                'quote' => 'Quote',
                                'pro' => 'AutoTerra Pro',
                                'pro_spatial' => 'Pro Spatial',
                                'solutions' => 'Solutions',
                                'resources' => 'Resources',
                                'blog' => 'Blog',
                                'login' => 'Login',
                                'signup' => 'Signup',
                                'eula' => 'EULA',
                                'products' => 'Products',
                                'cookies' => 'Cookies',
                            ])
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('key')
                            ->required()
                            ->maxLength(255)
                            ->helperText('e.g. hero.heading, cta.description, faq.items'),
                        Forms\Components\Select::make('type')
                            ->options([
                                'text' => 'Plain Text',
                                'richtext' => 'Rich Text (HTML)',
                                'json' => 'JSON (structured data)',
                            ])
                            ->required()
                            ->default('text'),
                    ]),
                Section::make('Content')
                    ->schema(fn (Get $get) => match ($get('type')) {
                        'richtext' => [
                            Forms\Components\RichEditor::make('value')
                                ->required()
                                ->columnSpanFull(),
                        ],
                        'json' => [
                            Forms\Components\Textarea::make('value')
                                ->required()
                                ->rows(20)
                                ->helperText('Valid JSON — arrays, objects, key-value pairs')
                                ->columnSpanFull(),
                        ],
                        default => [
                            Forms\Components\Textarea::make('value')
                                ->required()
                                ->rows(4)
                                ->columnSpanFull(),
                        ],
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PageContentResource\Pages\ListPageContents::route('/'),
            'edit' => \App\Filament\Resources\PageContentResource\Pages\EditPageContent::route('/{record}/edit'),
        ];
    }

    protected static function mutateFormDataAfterSave(array $data): array
    {
        if (isset($data['page'])) {
            PageContent::clearCache($data['page']);
        }
        return $data;
    }
}
