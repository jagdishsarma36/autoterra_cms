<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationItems(): array
    {
        return [parent::getNavigationItems()[0]->label('Users')];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Commerce';
    }

    public static function getNavigationSort(): ?int
    {
        return 20;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-users';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('company'),
                Tables\Columns\TextColumn::make('country'),
                Tables\Columns\TextColumn::make('orders_count')->counts('orders')->label('Orders'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M j, Y'),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('User Details')
                ->schema([
                    Grid::make(2)->schema([
                        \Filament\Forms\Components\TextInput::make('name')->required(),
                        \Filament\Forms\Components\TextInput::make('email')->email()->required(),
                        \Filament\Forms\Components\Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'user' => 'User',
                            ])
                            ->default('user')
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrateStateUsing(fn (string $state): string => \Illuminate\Support\Facades\Hash::make($state))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('company'),
                        \Filament\Forms\Components\TextInput::make('phone'),
                        \Filament\Forms\Components\TextInput::make('country'),
                        \Filament\Forms\Components\TextInput::make('address'),
                    ]),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\UserResource\Pages\ListUsers::route('/'),
            'create' => \App\Filament\Resources\UserResource\Pages\CreateUser::route('/create'),
            'view' => \App\Filament\Resources\UserResource\Pages\ViewUser::route('/{record}'),
            'edit' => \App\Filament\Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
