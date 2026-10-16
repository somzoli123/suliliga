<?php

namespace App\Filament\Support;

use App\Models\MatchGame;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

/**
 * Az élő meccs vezérlőgombjai – a Mérkőzések listában és a kezdőlapi widgetben is ezeket használjuk.
 */
class LiveMatchActions
{
    public static function start(): Action
    {
        return Action::make('startLive')
            ->label('Indítás')
            ->icon(Heroicon::OutlinedPlay)
            ->color('danger')
            ->visible(fn (MatchGame $record): bool => in_array($record->status, ['scheduled', 'postponed'], true))
            ->requiresConfirmation()
            ->modalHeading('Elindítod a mérkőzést?')
            ->modalDescription('A meccs élő lesz a nyilvános oldalon, és elindul az óra.')
            ->modalSubmitActionLabel('Indítás')
            ->action(function (MatchGame $record) {
                $record->update([
                    'status' => 'live',
                    'live_started_at' => now(),
                    'elapsed_seconds' => 0,
                    'home_score' => 0,
                    'away_score' => 0,
                ]);
                Notification::make()->title('A mérkőzés elindult')->success()->send();
            });
    }

    public static function pause(): Action
    {
        return Action::make('pauseLive')
            ->label('Szünet')
            ->icon(Heroicon::OutlinedPause)
            ->color('warning')
            ->visible(fn (MatchGame $record): bool => $record->isRunning())
            ->action(function (MatchGame $record) {
                $record->update([
                    'elapsed_seconds' => $record->elapsedSeconds(),
                    'live_started_at' => null,
                ]);
            });
    }

    public static function resume(): Action
    {
        return Action::make('resumeLive')
            ->label('Folytatás')
            ->icon(Heroicon::OutlinedPlay)
            ->color('success')
            ->visible(fn (MatchGame $record): bool => $record->isLive() && ! $record->isRunning())
            ->action(fn (MatchGame $record) => $record->update(['live_started_at' => now()]));
    }

    public static function homeGoal(): Action
    {
        return Action::make('homeGoal')
            ->label('Hazai gól +1')
            ->icon(Heroicon::OutlinedPlusCircle)
            ->color('success')
            ->visible(fn (MatchGame $record): bool => $record->isLive())
            ->action(fn (MatchGame $record) => $record->update(['home_score' => ($record->home_score ?? 0) + 1]));
    }

    public static function awayGoal(): Action
    {
        return Action::make('awayGoal')
            ->label('Vendég gól +1')
            ->icon(Heroicon::OutlinedPlusCircle)
            ->color('success')
            ->visible(fn (MatchGame $record): bool => $record->isLive())
            ->action(fn (MatchGame $record) => $record->update(['away_score' => ($record->away_score ?? 0) + 1]));
    }

    public static function finish(): Action
    {
        return Action::make('finishLive')
            ->label('Vége')
            ->icon(Heroicon::OutlinedFlag)
            ->color('gray')
            ->visible(fn (MatchGame $record): bool => $record->isLive())
            ->requiresConfirmation()
            ->modalHeading('Lefújod a mérkőzést?')
            ->modalDescription('Az eredmény véglegesedik, a meccs átkerül a lejátszottak közé.')
            ->modalSubmitActionLabel('Lefújás')
            ->action(function (MatchGame $record) {
                $record->update([
                    'status' => 'played',
                    'elapsed_seconds' => $record->elapsedSeconds(),
                    'live_started_at' => null,
                ]);
                Notification::make()->title('A mérkőzés véget ért')->success()->send();
            });
    }

    /** @return array<int, Action> */
    public static function controls(): array
    {
        return [
            self::homeGoal(),
            self::awayGoal(),
            self::pause(),
            self::resume(),
            self::finish(),
        ];
    }
}
