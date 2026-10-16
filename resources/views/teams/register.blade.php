@extends('layouts.app')

@section('title', 'Csapatnevezés')

@section('content')
    <div class="panel panel--pad panel--narrow">
        @if (session('success'))
            <div class="eyebrow">Sikeres nevezés</div>
            <h1>Köszönjük a nevezést!</h1>
            <p class="muted" style="margin-top:10px">A csapatod adatai megérkeztek. Amint az adminisztrátor jóváhagyja, a csapat megjelenik a bajnokságban.</p>
            <a href="{{ url('/') }}" class="btn btn--primary">Vissza a főoldalra</a>
        @else
            <ol class="steps" aria-label="Lépések">
                <li class="is-active">1. Csapat és játékosok</li>
                <li>2. Mezszámok</li>
            </ol>

            <h1>Nevezd be a csapatodat</h1>
            <p class="muted" style="margin:8px 0 20px">Add meg a csapat adatait és a játékosok neveit. A következő lépésben minden játékosnak mezszámot adhatsz.</p>

            @if ($errors->any())
                <div class="flash flash--error" role="alert" style="margin:0 0 16px">Kérlek, ellenőrizd a megadott adatokat.</div>
            @endif

            <form method="POST" action="{{ route('teams.register.step2') }}">
                @csrf

                <div class="field">
                    <label for="name">Csapat neve</label>
                    <input id="name" name="name" value="{{ old('name') }}" maxlength="100" required>
                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="field">
                    <label for="class_name">Osztály</label>
                    <p class="hint">Például 11.A</p>
                    <input id="class_name" name="class_name" value="{{ old('class_name') }}" maxlength="50" required>
                    @error('class_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="field">
                    <label for="captain_name">Csapatkapitány neve</label>
                    <input id="captain_name" name="captain_name" value="{{ old('captain_name') }}" maxlength="100" required>
                    @error('captain_name')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <div class="field">
                    <label for="players">Játékosok nevei</label>
                    <p class="hint">Vesszővel elválasztva, legfeljebb 30 játékos.</p>
                    <textarea id="players" name="players" maxlength="2000" required placeholder="Kiss Péter, Nagy Ádám, Tóth Bence">{{ old('players') }}</textarea>
                    @error('players')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn--primary btn--block">Tovább a mezszámokhoz →</button>
            </form>
        @endif
    </div>
@endsection
