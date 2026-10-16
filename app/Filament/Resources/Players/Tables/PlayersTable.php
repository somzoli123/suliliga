<?php

namespace App\Filament\Resources\Players\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlayersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Játékos neve')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('team.name')
                    ->label('Csapat')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nincs csapathoz rendelve'),

                TextColumn::make('jersey_number')
                    ->label('Mezszám')
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                //
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
