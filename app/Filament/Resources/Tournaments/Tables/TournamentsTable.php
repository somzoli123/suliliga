<?php

namespace App\Filament\Resources\Tournaments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TournamentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')->label('Bajnokság')->weight('bold')->searchable()->sortable()
                    ->description(fn ($record): ?string => $record->season),
                TextColumn::make('start_date')->label('Kezdés')->date('Y. m. d.')->sortable(),
                TextColumn::make('end_date')->label('Zárás')->date('Y. m. d.')->sortable(),
                TextColumn::make('matches_count')->label('Mérkőzések')->counts('matches')->badge()->color('gray'),
                TextColumn::make('status')
                    ->label('Állapot')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'active' => 'Folyamatban',
                        'upcoming' => 'Hamarosan',
                        'finished' => 'Lezárult',
                        default => (string) $state,
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'active' => 'success',
                        'finished' => 'gray',
                        default => 'warning',
                    }),
            ])
            ->recordActions([
                EditAction::make()->label('Szerkesztés'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Kijelöltek törlése'),
                ]),
            ])
            ->emptyStateHeading('Még nincs bajnokság')
            ->emptyStateDescription('Hozd létre az első bajnokságot, hogy a mérkőzések hozzá tartozzanak.')
            ->emptyStateIcon(Heroicon::OutlinedTrophy);
    }
}
