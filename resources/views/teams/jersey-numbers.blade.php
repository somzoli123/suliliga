@extends('layouts.app')

@section('title', 'Mezszámok')

@section('content')
    <div class="panel panel--pad panel--narrow">
        <ol class="steps" aria-label="Lépések">
            <li>1. Csapat és játékosok</li>
            <li class="is-active">2. Mezszámok</li>
        </ol>

        <h1>Mezszámok megadása</h1>
        <p class="muted" style="margin:8px 0 4px">Csapat: <strong style="color:var(--text)">{{ $teamData['name'] }}</strong></p>
        <p class="muted" style="margin-bottom:18px">Adj minden játékosnak 1 és 99 közötti, a csapaton belül egyedi mezszámot.</p>

        @if ($errors->any())
            <div class="flash flash--error" role="alert" style="margin:0 0 16px">
                Kérlek, ellenőrizd a mezszámokat. Minden számnak egyedinek kell lennie.
            </div>
        @endif

        <form method="POST" action="{{ route('teams.register.store') }}">
            @csrf

            @foreach ($playerNames as $index => $playerName)
                <div class="player-row">
                    <div class="name">{{ $playerName }}</div>
                    <input type="number" inputmode="numeric" name="jersey_numbers[{{ $index }}]" min="1" max="99" required
                           value="{{ old('jersey_numbers.'.$index) }}"
                           aria-label="{{ $playerName }} mezszáma" placeholder="Szám">
                </div>
                @error('jersey_numbers.'.$index)<div class="field-error" style="margin:-4px 0 8px">{{ $message }}</div>@enderror
            @endforeach

            <div class="form-actions">
                <a href="{{ route('teams.register') }}" class="btn btn--ghost">← Vissza</a>
                <button type="submit" class="btn btn--primary">Nevezés véglegesítése ✓</button>
            </div>
        </form>
    </div>
@endsection
