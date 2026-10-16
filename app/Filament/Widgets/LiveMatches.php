<?php

namespace App\Filament\Widgets;

use App\Filament\Support\LiveMatchActions;
use App\Models\MatchGame;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LiveMatches extends TableWidget
{
    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Élő mérkőzések')
            ->description('Itt vezérelheted a futó meccseket: gól, szünet, lefújás.')
            ->query(fn () => MatchGame::query()->with(['homeTeam', 'awayTeam'])->where('status', 'live')->orderBy('id'))
            ->poll('5s')
            ->paginated(false)
            ->emptyStateHeading('Nincs élő mérkőzés')
            ->emptyStateDescription('Indíts el egy kiírt meccset a Mérkőzések listában az „Indítás" gombbal.')
            ->emptyStateIcon(Heroicon::OutlinedSignal)
            ->columns([
                TextColumn::make('clock')
                    ->label('Idő')
                    ->badge()
                    ->color(fn (MatchGame $r): string => $r->isRunning() ? 'danger' : 'warning')
                    ->icon(fn (MatchGame $r) => $r->isRunning() ? Heroicon::OutlinedSignal : Heroicon::OutlinedPause)
                    ->state(fn (MatchGame $r): string => $r->clockLabel() . ($r->isRunning() ? '' : ' (szünet)')),
                TextColumn::make('homeTeam.name')->label('Hazai')->weight('bold')->alignEnd(),
                TextColumn::make('score')
                    ->label('Állás')
                    ->alignCenter()
                    ->badge()
                    ->color('success')
                    ->state(fn (MatchGame $r): string => ($r->home_score ?? 0) . ' : ' . ($r->away_score ?? 0)),
                TextColumn::make('awayTeam.name')->label('Vendég')->weight('bold'),
            ])
            ->recordActions(LiveMatchActions::controls());
    }
}
