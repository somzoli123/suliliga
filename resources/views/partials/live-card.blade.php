@php
    $home = $match->homeTeam;
    $away = $match->awayTeam;
    $hue = fn ($t) => crc32((string) ($t?->name ?? '?')) % 360;
    $ini = fn ($t) => mb_strtoupper(mb_substr((string) ($t?->name ?? '?'), 0, 2));
@endphp
<article class="sl-live-card" data-live-id="{{ $match->id }}" data-elapsed="{{ $match->elapsedSeconds() }}" data-running="{{ $match->isRunning() ? 1 : 0 }}">
    <div class="sl-live-top">
        <span class="sl-live-pill"><i></i> ÉLŐ</span>
        <span class="sl-live-round">{{ $match->round }}. forduló</span>
    </div>

    <div class="sl-live-board">
        <div class="sl-live-team">
            <i class="sl-mini sl-mini--lg" style="--h: {{ $hue($home) }}">{{ $ini($home) }}</i>
            <span>{{ $home?->name ?? 'Ismeretlen csapat' }}</span>
        </div>

        <div class="sl-live-mid">
            <div class="sl-live-score"><b data-home>{{ $match->home_score ?? 0 }}</b><em>:</em><b data-away>{{ $match->away_score ?? 0 }}</b></div>
            <div class="sl-live-clock" data-clock>{{ $match->clockLabel() }}</div>
            <div class="sl-live-state" data-state>{{ $match->isRunning() ? 'Játékban' : 'Szünet' }}</div>
        </div>

        <div class="sl-live-team">
            <i class="sl-mini sl-mini--lg" style="--h: {{ $hue($away) }}">{{ $ini($away) }}</i>
            <span>{{ $away?->name ?? 'Ismeretlen csapat' }}</span>
        </div>
    </div>
</article>
