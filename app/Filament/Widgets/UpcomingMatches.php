<?php

namespace App\Filament\Widgets;

use App\Models\MatchGame;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class UpcomingMatches extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Következő mérkőzések')
            ->query(fn () => MatchGame::query()
                ->with(['homeTeam', 'awayTeam'])
                ->where('status', 'scheduled')
                ->orderByRaw('scheduled_at IS NULL')
                ->orderBy('scheduled_at')
                ->limit(6))
            ->paginated(false)
            ->emptyStateHeading('Nincs kiírt mérkőzés')
            ->emptyStateDescription('Írj ki egy új mérkőzést a Mérkőzések menüben.')
            ->columns([
                TextColumn::make('scheduled_at')->label('Időpont')->dateTime('Y. m. d. H:i')->placeholder('Még nincs időpont'),
                TextColumn::make('homeTeam.name')->label('Hazai')->weight('bold'),
                TextColumn::make('awayTeam.name')->label('Vendég')->weight('bold'),
                TextColumn::make('round')->label('Forduló')->badge()->color('gray'),
            ]);
    }
}
