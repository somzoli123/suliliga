<?php

namespace App\Filament\Resources\Games\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GamesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Játék neve')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('short_name')
                    ->label('Rövidítés')
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label('Aktív')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Létrehozva')
                    ->dateTime('Y. m. d.')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Szerkesztés'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Kijelöltek törlése'),
                ]),
            ]);
    }
}
