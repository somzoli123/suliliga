<?php

namespace App\Filament\Resources\Teams\Tables;

use App\Models\Team;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class TeamsTable
{
    public const STATUSES = [
        'pending' => 'Jóváhagyásra vár',
        'approved' => 'Jóváhagyva',
        'rejected' => 'Elutasítva',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('players'))
            ->defaultSort('id', 'desc')
            ->striped()
            ->columns([
                TextColumn::make('name')
                    ->label('Csapat')
                    ->weight('bold')
                    ->description(fn (Team $record): string => 'Kapitány: ' . ($record->captain_name ?: '—'))
                    ->searchable(['name', 'captain_name'])
                    ->sortable(),

                TextColumn::make('class_name')
                    ->label('Osztály')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('players_count')
                    ->label('Játékosok')
                    ->icon(Heroicon::OutlinedUsers)
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Állapot')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    })
                    ->icon(fn (string $state) => match ($state) {
                        'approved' => Heroicon::OutlinedCheckCircle,
                        'rejected' => Heroicon::OutlinedXCircle,
                        default => Heroicon::OutlinedClock,
                    })
                    ->sortable(),

                // Az eredeti listák megmaradtak, de alapból rejtve vannak.
                TextColumn::make('players.name')
                    ->label('Játékos nevek')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('players.jersey_number')
                    ->label('Mezszámok')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->label('Állapot')->options(self::STATUSES),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Jóváhagyás')
                    ->icon(Heroicon::OutlinedCheck)
                    ->color('success')
                    ->visible(fn (Team $record): bool => $record->status !== 'approved')
                    ->action(function (Team $record) {
                        $record->update(['status' => 'approved']);
                        Notification::make()->title("{$record->name} jóváhagyva")->success()->send();
                    }),
                Action::make('reject')
                    ->label('Elutasítás')
                    ->icon(Heroicon::OutlinedXMark)
                    ->color('danger')
                    ->visible(fn (Team $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Team $record) {
                        $record->update(['status' => 'rejected']);
                        Notification::make()->title("{$record->name} elutasítva")->danger()->send();
                    }),
                EditAction::make()->label('Szerkesztés'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approveSelected')
                        ->label('Kijelöltek jóváhagyása')
                        ->icon(Heroicon::OutlinedCheck)
                        ->color('success')
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'approved'])),
                    DeleteBulkAction::make()->label('Kijelöltek törlése'),
                ]),
            ])
            ->emptyStateHeading('Még nincs nevezett csapat')
            ->emptyStateDescription('A beérkező nevezések itt jelennek meg jóváhagyásra.')
            ->emptyStateIcon(Heroicon::OutlinedUserGroup);
    }
}
