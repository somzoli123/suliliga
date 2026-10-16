<x-filament-widgets::widget>
    <div class="lg-hero">
        <svg class="pitch" viewBox="0 0 1200 600" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
            <rect x="40" y="40" width="1120" height="520" rx="6"/>
            <line x1="600" y1="40" x2="600" y2="560"/>
            <circle cx="600" cy="300" r="95"/>
            <rect x="40" y="170" width="170" height="260"/>
            <rect x="990" y="170" width="170" height="260"/>
        </svg>

        <span class="lg-status">
            <span class="lg-dot {{ $tournament?->status === 'active' ? '' : 'lg-dot--idle' }}"></span>
            @if ($tournament)
                {{ $tournament->season ?: 'Aktuális szezon' }}
                @if ($tournament->status === 'active') – folyamatban
                @elseif ($tournament->status === 'upcoming') – hamarosan indul
                @elseif ($tournament->status === 'finished') – lezárult
                @endif
            @else
                Még nincs bajnokság létrehozva
            @endif
        </span>

        <h2>{{ $tournament?->name ?? 'Suliliga' }}</h2>
        <p>Szia, {{ $userName }}! Itt kezelheted a nevezéseket, a mérkőzéseket és az eredményeket.</p>

        <div class="lg-actions">
            <a class="lg-act lg-act--primary" href="{{ $newMatchUrl }}">Új mérkőzés kiírása</a>
            <a class="lg-act" href="{{ $pendingUrl }}">
                Függő nevezések
                @if ($pending > 0)<span class="lg-count">{{ $pending }}</span>@endif
            </a>
            <a class="lg-act" href="{{ url('/') }}" target="_blank" rel="noopener">Nyilvános oldal</a>
        </div>
    </div>
</x-filament-widgets::widget>
