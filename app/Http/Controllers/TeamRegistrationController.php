<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeamRegistrationController extends Controller
{
    public function step2(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'class_name' => ['required', 'string', 'max:50'],
            'captain_name' => ['required', 'string', 'max:100'],
            'players' => ['required', 'string', 'max:2000'],
        ]);

        $playerNames = collect(explode(',', $validated['players']))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique(fn ($name) => mb_strtolower($name))
            ->values();

        if ($playerNames->isEmpty() || $playerNames->count() > 30) {
            return redirect()
                ->route('teams.register')
                ->withErrors([
                    'players' => 'Adj meg legalább 1, legfeljebb 30 játékost!',
                ])
                ->withInput();
        }

        foreach ($playerNames as $playerName) {
            if (mb_strlen($playerName) > 100) {
                return redirect()
                    ->route('teams.register')
                    ->withErrors([
                        'players' => 'Egy játékos neve legfeljebb 100 karakter lehet.',
                    ])
                    ->withInput();
            }
        }

        $teamData = [
            'name' => $validated['name'],
            'class_name' => $validated['class_name'],
            'captain_name' => $validated['captain_name'],
        ];

        session([
            'team_registration' => [
                'team' => $teamData,
                'players' => $playerNames->all(),
            ],
        ]);

        return view('teams.jersey-numbers', [
            'teamData' => $teamData,
            'playerNames' => $playerNames,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $registration = session('team_registration');

        if (
            ! is_array($registration) ||
            ! isset($registration['team'], $registration['players']) ||
            ! is_array($registration['players'])
        ) {
            return redirect()
                ->route('teams.register')
                ->withErrors([
                    'players' => 'A nevezési folyamat lejárt. Kérlek, kezdd újra!',
                ]);
        }

        $playerCount = count($registration['players']);

        $validated = $request->validate([
            'jersey_numbers' => ['required', 'array', 'size:' . $playerCount],
            'jersey_numbers.*' => [
                'required',
                'integer',
                'min:1',
                'max:99',
                'distinct',
            ],
        ]);

        DB::transaction(function () use ($registration, $validated) {
            $team = Team::create([
                'name' => $registration['team']['name'],
                'class_name' => $registration['team']['class_name'],
                'captain_name' => $registration['team']['captain_name'],
                'captain_id' => null,
                'status' => 'pending',
            ]);

            foreach ($registration['players'] as $index => $playerName) {
                $team->players()->create([
                    'name' => $playerName,
                    'jersey_number' => $validated['jersey_numbers'][$index],
                ]);
            }
        });

        session()->forget('team_registration');

        return redirect()
            ->route('teams.register')
            ->with('success', 'A nevezés beérkezett! Az admin jóváhagyására vár.');
    }
}
