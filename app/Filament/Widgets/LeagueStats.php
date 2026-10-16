<?php

namespace App\Filament\Widgets;

use App\Models\MatchGame;
use App\Models\Team;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeagueStats extends StatsOverviewWidget
{
    protected static ?int $sort = -5;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $approved = Team::query()->where('status', 'approved')->count();
        $pending = Team::query()->where('status', 'pending')->count();
        $played = MatchGame::query()->where('status', 'played')->count();
        $scheduled = MatchGame::query()->where('status', 'scheduled')->count();
        $goals = (int) MatchGame::query()->where('status', 'played')->selectRaw('COALESCE(SUM(home_score + away_score), 0) as g')->value('g');

        // Gólok fordulónként – a kártya kis grafikonjához.
        $perRound = MatchGame::query()
            ->where('status', 'played')
            ->selectRaw('round, SUM(home_score + away_score) as g')
            ->groupBy('round')->orderBy('round')
            ->pluck('g')->map(fn ($v) => (int) $v)->take(-8)->values()->all();

        return [
            Stat::make('Jóváhagyott csapat', $approved)
                ->description($pending > 0 ? "{$pending} nevezés jóváhagyásra vár" : 'Nincs függő nevezés')
                ->descriptionIcon($pending > 0 ? Heroicon::OutlinedClock : Heroicon::OutlinedCheckCircle)
                ->color($pending > 0 ? 'warning' : 'success'),

            Stat::make('Lejátszott mérkőzés', $played)
                ->description("{$scheduled} még hátravan")
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color('info'),

            Stat::make('Szerzett gól', $goals)
                ->description($played > 0 ? 'Meccsenként ' . str_replace('.', ',', (string) round($goals / $played, 1)) : 'Még nincs eredmény')
                ->descriptionIcon(Heroicon::OutlinedFire)
                ->chart(count($perRound) > 1 ? $perRound : [0, 0])
                ->color('success'),
        ];
    }
}
