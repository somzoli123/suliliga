<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function index(): View
    {
        $tournament = Tournament::query()->where('status', 'active')->latest('id')->first()
            ?? Tournament::query()->latest('id')->first();

        $matches = fn () => MatchGame::query()->with(['homeTeam', 'awayTeam']);

        // Élő meccsek (az admin felületen indíthatók).
        $live = $matches()->where('status', 'live')->orderBy('id')->get();

        $upcoming = $matches()
            ->where('status', 'scheduled')
            ->orderByRaw('scheduled_at IS NULL')
            ->orderBy('scheduled_at')
            ->limit(6)
            ->get();

        $recent = $matches()
            ->where('status', 'played')
            ->orderByDesc('scheduled_at')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $played = MatchGame::query()->where('status', 'played');

        $stats = [
            'teams' => Team::query()->where('status', 'approved')->count(),
            'played' => (clone $played)->count(),
            'goals' => (int) (clone $played)->selectRaw('COALESCE(SUM(home_score + away_score), 0) as g')->value('g'),
        ];

        $teams = Team::query()
            ->where('status', 'approved')
            ->with('players')
            ->withCount('players')
            ->orderBy('name')
            ->get();

        return view('welcome', compact('tournament', 'stats', 'live', 'upcoming', 'recent', 'teams'));
    }

    /** Az élő meccsek állapota – a főoldal ezt kérdezi le néhány másodpercenként. */
    public function live(): JsonResponse
    {
        $matches = MatchGame::query()->where('status', 'live')->orderBy('id')->get();

        return response()->json([
            'matches' => $matches->map(fn (MatchGame $m) => [
                'id' => $m->id,
                'home_score' => (int) ($m->home_score ?? 0),
                'away_score' => (int) ($m->away_score ?? 0),
                'elapsed' => $m->elapsedSeconds(),
                'running' => $m->isRunning(),
            ])->values(),
        ])->header('Cache-Control', 'no-store');
    }
}
