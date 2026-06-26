<?php

namespace App\Filament\Widgets;

use App\Models\FormSubmission;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentSubmissionsWidget extends TableWidget
{
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Recent Submissions';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(FormSubmission::with('form')->latest())
            ->paginated([5])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('form.name')
                    ->label('Form')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('data.first_name')
                    ->label('First Name')
                    ->getStateUsing(fn (FormSubmission $record): string =>
                        $record->data['first_name'] ?? $record->data['name'] ?? $record->name ?? '-'
                    ),
                Tables\Columns\TextColumn::make('data.email')
                    ->label('Email')
                    ->getStateUsing(fn (FormSubmission $record): string =>
                        $record->data['email'] ?? $record->email ?? '-'
                    ),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP'),
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Read')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ]) 
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->label('View')
                    ->url(fn ($record): string => "/admin/form-submissions/{$record->id}")
                    ->icon('heroicon-o-eye')
                    ->color('gray'),
            ])
            ->poll('60s');
    }
}
