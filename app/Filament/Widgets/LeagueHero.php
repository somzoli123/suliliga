<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\MatchGames\MatchGameResource;
use App\Filament\Resources\Teams\TeamResource;
use App\Models\Team;
use App\Models\Tournament;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class LeagueHero extends Widget
{
    protected static ?int $sort = -10;

    protected int|string|array $columnSpan = 'full';

    public function render(): View
    {
        $tournament = Tournament::query()->where('status', 'active')->latest('id')->first()
            ?? Tournament::query()->latest('id')->first();

        return view('filament.widgets.hero', [
            'tournament' => $tournament,
            'userName' => auth()->user()?->name ?? 'admin',
            'pending' => Team::query()->where('status', 'pending')->count(),
            'newMatchUrl' => MatchGameResource::getUrl('create'),
            'pendingUrl' => TeamResource::getUrl('index', ['filters' => ['status' => ['value' => 'pending']]]),
        ]);
    }
}
