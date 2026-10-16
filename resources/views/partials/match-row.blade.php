@php
    $home = $match->homeTeam;
    $away = $match->awayTeam;
    $played = $match->status === 'played';
    $hue = fn ($t) => crc32((string) ($t?->name ?? '?')) % 360;
    $ini = fn ($t) => mb_strtoupper(mb_substr((string) ($t?->name ?? '?'), 0, 2));
    $hasScore = $match->home_score !== null && $match->away_score !== null;
@endphp
<div class="sl-match {{ $played ? 'is-played' : '' }}">
    <div class="sl-match-when">
        <strong>{{ $match->scheduled_at ? $match->scheduled_at->format('H:i') : '–' }}</strong>
        <span>{{ $match->scheduled_at ? $match->scheduled_at->format('Y. m. d.') : 'Időpont később' }}</span>
    </div>

    <div class="sl-match-team sl-match-team--home">
        <span>{{ $home?->name ?? 'Ismeretlen csapat' }}</span>
        <i class="sl-mini" style="--h: {{ $hue($home) }}">{{ $ini($home) }}</i>
    </div>

    <div class="sl-match-score {{ $hasScore ? 'has' : '' }}">
        @if ($hasScore)
            {{ $match->home_score }}<em>:</em>{{ $match->away_score }}
        @else
            <small>vs</small>
        @endif
    </div>

    <div class="sl-match-team sl-match-team--away">
        <i class="sl-mini" style="--h: {{ $hue($away) }}">{{ $ini($away) }}</i>
        <span>{{ $away?->name ?? 'Ismeretlen csapat' }}</span>
    </div>

    <div class="sl-match-meta">
        <span class="sl-chip">{{ $match->round }}. forduló</span>
        @if ($match->status === 'postponed')<span class="sl-chip sl-chip--red">Elhalasztva</span>@endif
    </div>
</div>
