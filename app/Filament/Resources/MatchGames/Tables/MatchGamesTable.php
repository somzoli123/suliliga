<?php

namespace App\Filament\Resources\MatchGames\Tables;

use App\Models\MatchGame;
use App\Filament\Support\LiveMatchActions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MatchGamesTable
{
    public const STATUSES = [
        'scheduled' => 'Kiírva',
        'live' => 'Élőben',
        'played' => 'Lejátszva',
        'postponed' => 'Elhalasztva',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['homeTeam', 'awayTeam', 'tournament']))
            ->defaultSort('scheduled_at', 'desc')
            ->columns([
                TextColumn::make('scheduled_at')
                    ->label('Időpont')
                    ->dateTime('Y. m. d. H:i')
                    ->placeholder('Nincs időpont')
                    ->description(fn (MatchGame $r): ?string => $r->tournament?->name)
                    ->sortable(),

                TextColumn::make('homeTeam.name')
                    ->label('Hazai')
                    ->weight('bold')
                    ->alignEnd()
                    ->searchable(),

                TextColumn::make('score')
                    ->label('Eredmény')
                    ->alignCenter()
                    ->badge()
                    ->color(fn (MatchGame $r): string => match ($r->status) { 'played' => 'success', 'live' => 'danger', default => 'gray' })
                    ->state(fn (MatchGame $r): string => $r->home_score !== null && $r->away_score !== null
                        ? "{$r->home_score} : {$r->away_score}"
                        : '– : –'),

                TextColumn::make('awayTeam.name')
                    ->label('Vendég')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('round')
                    ->label('Forduló')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Állapot')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'live' => 'danger',
                        'played' => 'success',
                        'postponed' => 'gray',
                        default => 'info',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')->label('Állapot')->options(self::STATUSES),
                SelectFilter::make('round')
                    ->label('Forduló')
                    ->options(fn () => MatchGame::query()->distinct()->orderBy('round')->pluck('round', 'round')->all()),
                SelectFilter::make('tournament_id')->label('Bajnokság')->relationship('tournament', 'name'),
            ])
            ->recordActions([
                LiveMatchActions::start(),
                ActionGroup::make(LiveMatchActions::controls())->label('Élő vezérlés')->icon(Heroicon::OutlinedSignal)->color('danger')->button(),
                Action::make('result')
                    ->label('Eredmény')
                    ->icon(Heroicon::OutlinedFlag)
                    ->color('success')
                    ->modalHeading('Eredmény rögzítése')
                    ->modalSubmitActionLabel('Mentés és lejátszva')
                    ->fillForm(fn (MatchGame $r): array => ['home_score' => $r->home_score ?? 0, 'away_score' => $r->away_score ?? 0])
                    ->schema([
                        TextInput::make('home_score')->label('Hazai gólok')->numeric()->minValue(0)->required(),
                        TextInput::make('away_score')->label('Vendéggólok')->numeric()->minValue(0)->required(),
                    ])
                    ->action(function (MatchGame $record, array $data) {
                        $record->update([...$data, 'status' => 'played']);
                        Notification::make()->title('Eredmény rögzítve')->success()->send();
                    }),
                EditAction::make()->label('Szerkesztés'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Kijelöltek törlése'),
                ]),
            ])
            ->emptyStateHeading('Még nincs mérkőzés')
            ->emptyStateDescription('Írj ki egy mérkőzést a jobb felső gombbal.')
            ->emptyStateIcon(Heroicon::OutlinedCalendarDays);
    }
}
