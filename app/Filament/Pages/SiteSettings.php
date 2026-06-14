<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Site Settings';
    protected static string | \UnitEnum | null $navigationGroup = 'CMS';
    protected static ?int $navigationSort = 2;
    protected static ?string $title = 'Site Settings';
    protected string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'site_name' => Setting::get('site_name', 'AutoTerra'),
            'site_email' => Setting::get('site_email', 'support@autoterra.net'),
            'company_address' => Setting::get('company_address', ''),
            'user_can_print' => Setting::get('user_can_print', true),
            'license_key_mode' => Setting::get('license_key_mode', 'auto'),
            'header_logo_a' => Setting::get('header_logo_a', 'AUTO'),
            'header_logo_t' => Setting::get('header_logo_t', 'TERRA'),
            'header_logo_image' => Setting::get('header_logo_image', ''),
            'header_nav_links' => json_decode(Setting::get('header_nav_links', '[{"label":"Products","url":"/products"},{"label":"Solutions","url":"/solutions"},{"label":"Resources","url":"/resources"},{"label":"Pricing","url":"/pricing"},{"label":"Blog","url":"/blog"}]'), true),
            'header_login_text' => Setting::get('header_login_text', 'Login'),
            'header_cta_text' => Setting::get('header_cta_text', 'Buy Now'),
            'header_cta_url' => Setting::get('header_cta_url', '/buy'),
            'footer_logo_a' => Setting::get('footer_logo_a', 'AUTO'),
            'footer_logo_t' => Setting::get('footer_logo_t', 'TERRA'),
            'footer_logo_image' => Setting::get('footer_logo_image', ''),
            'footer_description' => Setting::get('footer_description', 'Full lifecycle geospatial software for survey, design, construction, maintenance and monitoring of infrastructure.'),
            'footer_links' => json_decode(Setting::get('footer_links', '[{"label":"Products","url":"/products"},{"label":"Pricing","url":"/pricing"},{"label":"Blog","url":"/blog"},{"label":"Contact","url":"/contact"},{"label":"Privacy Policy","url":"/privacy-policy"},{"label":"Terms of Service","url":"/terms-of-service"}]'), true),
            'footer_copyright' => Setting::get('footer_copyright', '© ' . date('Y') . ' AutoTerra. All rights reserved.'),
            'custom_header_css' => Setting::get('custom_header_css', ''),
            'custom_header_js' => Setting::get('custom_header_js', ''),
            'custom_footer_js' => Setting::get('custom_footer_js', ''),
            'media_allowed_types' => Setting::get('media_allowed_types', 'image/jpeg,image/png,image/gif,image/webp,image/svg+xml,image/avif,video/mp4,video/webm,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv,application/zip,application/x-zip-compressed'),
            'media_max_size' => Setting::get('media_max_size', '50'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('General')
                    ->schema([
                        Forms\Components\TextInput::make('site_name')->required(),
                        Forms\Components\TextInput::make('site_email')->email()->required(),
                        Forms\Components\Textarea::make('company_address')->rows(3),
                    ])->columns(2),

                Section::make('User Permissions')
                    ->schema([
                        Forms\Components\Toggle::make('user_can_print')
                            ->label('Allow users to print invoices')
                            ->helperText('When enabled, users can print order and subscription invoices from their dashboard.')
                            ->default(true),
                        Forms\Components\Select::make('license_key_mode')
                            ->label('License Key Generation')
                            ->options([
                                'auto' => 'Auto — generate keys automatically on payment',
                                'manual' => 'Manual — enter keys manually after payment',
                            ])
                            ->default('auto')
                            ->helperText('In manual mode, license keys must be entered manually on the order/subscription detail page.'),
                    ]),

                Section::make('Header')
                    ->description('Controls the site-wide navigation bar.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('header_logo_a')
                                ->label('Logo Text — Part 1')
                                ->default('AUTO')
                                ->helperText('First half of text logo'),
                            Forms\Components\TextInput::make('header_logo_t')
                                ->label('Logo Text — Part 2')
                                ->default('TERRA')
                                ->helperText('Second half of text logo'),
                            Forms\Components\TextInput::make('header_logo_image')
                                ->label('Logo Image URL')
                                ->placeholder('/storage/media/logo.png')
                                ->helperText('Optional. Overrides text logo if set.'),
                        ]),
                        Forms\Components\Repeater::make('header_nav_links')
                            ->label('Navigation Links')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Label')
                                    ->required()
                                    ->maxLength(100)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('url')
                                    ->label('URL')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('/products')
                                    ->columnSpan(1),
                            ])
                            ->columns(2)
                            ->defaultItems(5)
                            ->addActionLabel('Add link')
                            ->reorderable()
                            ->columnSpanFull()
                            ->helperText('Dashboard link is added automatically when the user is logged in.'),
                        \Filament\Schemas\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('header_login_text')
                                ->label('Login Button Text')
                                ->default('Login'),
                            Forms\Components\TextInput::make('header_cta_text')
                                ->label('CTA Button Text')
                                ->default('Buy Now'),
                            Forms\Components\TextInput::make('header_cta_url')
                                ->label('CTA Button URL')
                                ->default('/buy'),
                        ]),
                    ]),

                Section::make('Footer')
                    ->description('Controls the site-wide footer.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('footer_logo_a')
                                ->label('Logo Text — Part 1')
                                ->default('AUTO')
                                ->helperText('First half of text logo'),
                            Forms\Components\TextInput::make('footer_logo_t')
                                ->label('Logo Text — Part 2')
                                ->default('TERRA')
                                ->helperText('Second half of text logo'),
                            Forms\Components\TextInput::make('footer_logo_image')
                                ->label('Logo Image URL')
                                ->placeholder('/storage/media/logo.png')
                                ->helperText('Optional. Overrides text logo if set.'),
                        ]),
                        Forms\Components\Textarea::make('footer_description')
                            ->label('Footer Description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Repeater::make('footer_links')
                            ->label('Footer Links')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Label')
                                    ->required()
                                    ->maxLength(100)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('url')
                                    ->label('URL')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('/products')
                                    ->columnSpan(1),
                            ])
                            ->columns(2)
                            ->defaultItems(6)
                            ->addActionLabel('Add link')
                            ->reorderable()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('footer_copyright')
                            ->label('Copyright Text')
                            ->default('© ' . date('Y') . ' AutoTerra. All rights reserved.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Custom CSS')
                    ->description('Add custom CSS that loads in the <head> of every page.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Textarea::make('custom_header_css')
                            ->label('Header CSS')
                            ->rows(8)
                            ->columnSpanFull()
                            ->helperText('Paste CSS inside <style> tags or raw CSS rules.'),
                    ]),

                Section::make('Custom Scripts')
                    ->description('Add custom JavaScript that loads on every page.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Textarea::make('custom_header_js')
                            ->label('Header Scripts')
                            ->rows(6)
                            ->columnSpanFull()
                            ->helperText('Scripts that load in <head> (e.g. Google Tag Manager, consent banners).'),
                        Forms\Components\Textarea::make('custom_footer_js')
                            ->label('Footer Scripts')
                            ->rows(6)
                            ->columnSpanFull()
                            ->helperText('Scripts that load before </body> (e.g. Google Analytics, chat widgets).'),
                    ]),

                Section::make('Media Upload')
                    ->description('Configure which file types can be uploaded and the maximum file size.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Textarea::make('media_allowed_types')
                            ->label('Allowed MIME Types')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Comma-separated MIME types. Leave blank to allow all types.'),
                        Forms\Components\TextInput::make('media_max_size')
                            ->label('Max Upload Size (MB)')
                            ->numeric()
                            ->default(50)
                            ->columnSpan(1)
                            ->helperText('Maximum file size in megabytes.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->data;

        Setting::set('site_name', $data['site_name'], 'text');
        Setting::set('site_email', $data['site_email'], 'text');
        Setting::set('company_address', $data['company_address'], 'text');
        Setting::set('user_can_print', $data['user_can_print'] ? '1' : '0', 'boolean');
        Setting::set('license_key_mode', $data['license_key_mode'] ?? 'auto', 'text');

        // Header
        Setting::set('header_logo_a', $data['header_logo_a'] ?? 'AUTO', 'text');
        Setting::set('header_logo_t', $data['header_logo_t'] ?? 'TERRA', 'text');
        Setting::set('header_logo_image', $data['header_logo_image'] ?? '', 'text');
        Setting::set('header_nav_links', json_encode($data['header_nav_links'] ?? []), 'json');
        Setting::set('header_login_text', $data['header_login_text'] ?? 'Login', 'text');
        Setting::set('header_cta_text', $data['header_cta_text'] ?? 'Buy Now', 'text');
        Setting::set('header_cta_url', $data['header_cta_url'] ?? '/buy', 'text');

        // Footer
        Setting::set('footer_logo_a', $data['footer_logo_a'] ?? 'AUTO', 'text');
        Setting::set('footer_logo_t', $data['footer_logo_t'] ?? 'TERRA', 'text');
        Setting::set('footer_logo_image', $data['footer_logo_image'] ?? '', 'text');
        Setting::set('footer_description', $data['footer_description'] ?? '', 'text');
        Setting::set('footer_links', json_encode($data['footer_links'] ?? []), 'json');
        Setting::set('footer_copyright', $data['footer_copyright'] ?? '', 'text');

        // Custom CSS/JS
        Setting::set('custom_header_css', $data['custom_header_css'] ?? '', 'text');
        Setting::set('custom_header_js', $data['custom_header_js'] ?? '', 'text');
        Setting::set('custom_footer_js', $data['custom_footer_js'] ?? '', 'text');

        // Media
        Setting::set('media_allowed_types', $data['media_allowed_types'] ?? '', 'text');
        Setting::set('media_max_size', $data['media_max_size'] ?? '50', 'text');

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
