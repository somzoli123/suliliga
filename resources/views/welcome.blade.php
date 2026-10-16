@extends('layouts.app')

@section('title', 'Iskolai labdarúgó-bajnokság')

@section('content')
@php
    // Védelem: ha a nézetet valaki a HomeController nélkül hívja, ne legyen 500-as hiba,
    // hanem üres állapotok jelenjenek meg.
    $tournament = $tournament ?? null;
    $stats = $stats ?? ['teams' => 0, 'played' => 0, 'goals' => 0];
    $upcoming = $upcoming ?? collect();
    $recent = $recent ?? collect();
    $live = $live ?? collect();
    $teams = $teams ?? collect();

    $isActive = $tournament?->status === 'active';
    $isUpcoming = $tournament?->status === 'upcoming';
    $avgGoals = ($stats['played'] ?? 0) > 0 ? round($stats['goals'] / $stats['played'], 1) : null;
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;800;900&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">

<style>
    .sl {
        --pitch: #0c3a2c;
        --pitch-deep: #082a20;
        --pitch-line: #7fd1a0;
        --chalk: #f2f7ef;
        --chalk-dim: #b9cfc2;
        --card-yellow: #ffd23f;
        --card-red: #ff4b3e;
        --ink: #11231b;
        --paper: #ffffff;
        --mist: #e6efe8;
        --display: 'Barlow Condensed', 'Arial Narrow', Impact, sans-serif;
        --body: 'DM Sans', system-ui, -apple-system, 'Segoe UI', sans-serif;
        font-family: var(--body);
        color: var(--ink);
    }
    .sl *, .sl *::before, .sl *::after { box-sizing: border-box; }
    .sl a:focus-visible, .sl button:focus-visible {
        outline: 3px solid var(--card-yellow);
        outline-offset: 3px;
    }

    /* ───────── HERO ───────── */
    .sl-hero {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        background:
            repeating-linear-gradient(90deg, rgba(255,255,255,.035) 0 72px, transparent 72px 144px),
            radial-gradient(1200px 500px at 85% -10%, #14634a 0%, transparent 60%),
            linear-gradient(160deg, var(--pitch) 0%, var(--pitch-deep) 100%);
        color: var(--chalk);
        padding: clamp(28px, 5vw, 64px);
        isolation: isolate;
    }
    .sl-pitch {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        pointer-events: none;
    }
    .sl-pitch * {
        fill: none;
        stroke: var(--pitch-line);
        stroke-width: 2;
        opacity: .28;
        stroke-dasharray: 1800;
        stroke-dashoffset: 1800;
        animation: sl-draw 2.4s cubic-bezier(.6,.05,.2,1) .15s forwards;
    }
    .sl-pitch .dot { fill: var(--pitch-line); stroke: none; stroke-dasharray: none; stroke-dashoffset: 0; animation: none; }
    @keyframes sl-draw { to { stroke-dashoffset: 0; } }

    .sl-status {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 16px 8px 14px;
        border: 1px solid rgba(127,209,160,.45);
        border-radius: 999px;
        font-weight: 500;
        font-size: .9rem;
        color: var(--chalk);
        background: rgba(8,42,32,.55);
        backdrop-filter: blur(6px);
    }
    .sl-pulse {
        width: 10px; height: 10px; border-radius: 50%;
        background: var(--card-yellow);
        box-shadow: 0 0 0 0 rgba(255,210,63,.7);
        animation: sl-pulse 1.8s infinite;
    }
    .sl-pulse--idle { background: var(--chalk-dim); animation: none; }
    @keyframes sl-pulse {
        70% { box-shadow: 0 0 0 12px rgba(255,210,63,0); }
        100% { box-shadow: 0 0 0 0 rgba(255,210,63,0); }
    }

    .sl-title {
        font-family: var(--display);
        font-weight: 900;
        font-size: clamp(3.6rem, 14vw, 9.5rem);
        line-height: .86;
        letter-spacing: -.01em;
        margin: 22px 0 18px;
        text-transform: uppercase;
        color: var(--chalk);
        word-break: break-word;
    }
    .sl-lead {
        max-width: 46ch;
        font-size: clamp(1rem, 1.6vw, 1.2rem);
        line-height: 1.6;
        color: var(--chalk-dim);
        margin: 0 0 30px;
    }
    .sl-cta { display: flex; flex-wrap: wrap; gap: 12px; }

    .sl-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 15px 26px;
        border-radius: 14px;
        font-family: var(--body);
        font-weight: 700;
        font-size: 1rem;
        text-decoration: none;
        border: 2px solid transparent;
        transition: transform .15s ease, background .15s ease, color .15s ease, border-color .15s ease;
        cursor: pointer;
    }
    .sl-btn:hover { transform: translateY(-2px); }
    .sl-btn--yellow { background: var(--card-yellow); color: #1a1500; }
    .sl-btn--yellow:hover { background: #ffe074; }
    .sl-btn--ghost { color: var(--chalk); border-color: rgba(242,247,239,.4); }
    .sl-btn--ghost:hover { border-color: var(--chalk); background: rgba(242,247,239,.08); }
    .sl-btn--dark { background: var(--pitch); color: var(--chalk); width: 100%; justify-content: center; }
    .sl-btn--dark:hover { background: var(--pitch-deep); }
    .sl-btn svg { width: 18px; height: 18px; }

    /* Statisztika – középvonal-elválasztókkal */
    .sl-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        margin-top: clamp(36px, 6vw, 72px);
        border-top: 1px solid rgba(127,209,160,.3);
    }
    .sl-stat { padding: 22px 18px 0 0; }
    .sl-stat + .sl-stat { padding-left: 22px; border-left: 1px solid rgba(127,209,160,.3); }
    .sl-stat strong {
        display: block;
        font-family: var(--display);
        font-weight: 800;
        font-size: clamp(2.6rem, 7vw, 5rem);
        line-height: 1;
        font-variant-numeric: tabular-nums;
    }
    .sl-stat span { display: block; margin-top: 6px; color: var(--chalk-dim); font-size: .95rem; }
    .sl-stat em { font-style: normal; color: var(--card-yellow); }

    /* ───────── SZEKCIÓK ───────── */
    .sl-section { margin-top: clamp(40px, 6vw, 72px); }
    .sl-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }
    .sl-head h2 {
        font-family: var(--display);
        font-weight: 800;
        font-size: clamp(1.9rem, 4vw, 2.8rem);
        line-height: 1;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 14px;
        color: var(--chalk);
    }
    .sl-head h2 .bar { width: 10px; height: 1.1em; border-radius: 3px; background: var(--card-yellow); }
    .sl-head h2 .bar--red { background: var(--card-red); }
    .sl-head p { margin: 0; color: var(--chalk-dim); font-size: .95rem; }

    .sl-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: clamp(24px, 4vw, 48px);
        align-items: start;
    }
    .sl-matches { display: grid; gap: 40px; }
    .sl-matches .match-list {
        background: var(--paper);
        border: 1px solid var(--mist);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 1px 0 var(--mist), 0 18px 40px -28px rgba(8,42,32,.5);
    }

    .sl-empty {
        border: 2px dashed rgba(127,209,160,.5);
        border-radius: 20px;
        padding: 30px 26px;
        background: rgba(127,209,160,.07);
        color: var(--chalk-dim);
        line-height: 1.55;
    }
    .sl-empty strong { display: block; color: var(--chalk); font-size: 1.15rem; margin-bottom: 4px; }

    /* Nevezési panel – valódi, kétlépéses folyamat */
    .sl-join {
        position: sticky;
        top: 24px;
        background: var(--chalk);
        border: 1px solid #cfe0d4;
        border-radius: 24px;
        padding: 28px;
        overflow: hidden;
    }
    .sl-join::after {
        content: '';
        position: absolute;
        right: -60px; top: -60px;
        width: 160px; height: 160px;
        border: 2px solid rgba(12,58,44,.12);
        border-radius: 50%;
    }
    .sl-join h2 {
        font-family: var(--display);
        font-weight: 800;
        font-size: 2.2rem;
        line-height: 1;
        margin: 0 0 8px;
    }
    .sl-join > p { margin: 0 0 20px; color: #4f6358; line-height: 1.55; }
    .sl-steps { list-style: none; margin: 0 0 24px; padding: 0; display: grid; gap: 14px; position: relative; }
    .sl-steps li { display: grid; grid-template-columns: 34px 1fr; gap: 14px; align-items: start; }
    .sl-steps .n {
        width: 34px; height: 34px; border-radius: 50%;
        background: var(--pitch); color: var(--chalk);
        display: grid; place-items: center;
        font-family: var(--display); font-weight: 800; font-size: 1.15rem;
    }
    .sl-steps b { display: block; font-size: 1rem; }
    .sl-steps small { color: #5b6f64; font-size: .88rem; line-height: 1.4; }
    .sl-note { margin: 14px 0 0; font-size: .85rem; color: #5b6f64; text-align: center; }

    /* ───────── MÉRKŐZÉS SOROK ───────── */
    .sl-match {
        display: grid;
        grid-template-columns: 92px minmax(0,1fr) 84px minmax(0,1fr) auto;
        gap: 14px;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid var(--mist);
    }
    .sl-match:last-child { border-bottom: 0; }
    .sl-match-when strong { display: block; font-family: var(--display); font-weight: 800; font-size: 1.5rem; line-height: 1; }
    .sl-match-when span { font-size: .8rem; color: #5b6f64; }
    .sl-match-team { display: flex; align-items: center; gap: 10px; font-weight: 700; min-width: 0; }
    .sl-match-team span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .sl-match-team--home { justify-content: flex-end; text-align: right; }
    .sl-mini {
        flex: none; width: 34px; height: 34px; border-radius: 50%;
        display: grid; place-items: center;
        font-style: normal; font-family: var(--display); font-weight: 800; font-size: .95rem; color: #fff;
        background: hsl(var(--h) 55% 32%);
    }
    .sl-match-score {
        justify-self: center; min-width: 72px; padding: 6px 12px; border-radius: 12px; text-align: center;
        background: var(--mist); color: #5b6f64;
        font-family: var(--display); font-weight: 900; font-size: 1.7rem; line-height: 1.1;
    }
    .sl-match-score small { font-family: var(--body); font-weight: 700; font-size: .9rem; }
    .sl-match-score.has { background: var(--pitch); color: var(--chalk); }
    .sl-match-score em { font-style: normal; opacity: .6; margin: 0 4px; }
    .sl-match-meta { display: flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end; }
    .sl-chip--red { background: #ffe1de; color: #a3261c; }
    @media (max-width: 720px) {
        .sl-match { grid-template-columns: 1fr auto 1fr; grid-template-areas: 'when when meta' 'home score away'; row-gap: 10px; }
        .sl-match-when { grid-area: when; display: flex; gap: 10px; align-items: baseline; }
        .sl-match-meta { grid-area: meta; }
        .sl-match-team--home { grid-area: home; }
        .sl-match-score { grid-area: score; }
        .sl-match-team--away { grid-area: away; }
    }

    /* ───────── ÉLŐ MECCSEK ───────── */
    .sl-live-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 420px), 1fr)); gap: 16px; }
    .sl-live-card {
        position: relative; overflow: hidden; isolation: isolate;
        border-radius: 24px; padding: 20px 24px 24px;
        color: var(--chalk);
        border: 1px solid rgba(255,75,62,.45);
        background:
            radial-gradient(500px 220px at 50% -30%, rgba(255,75,62,.28), transparent 70%),
            repeating-linear-gradient(90deg, rgba(255,255,255,.03) 0 60px, transparent 60px 120px),
            linear-gradient(160deg, var(--pitch), var(--pitch-deep));
        box-shadow: 0 30px 60px -36px rgba(255,75,62,.55);
    }
    .sl-live-card::before {
        content: ''; position: absolute; left: 50%; top: 0; bottom: 0; width: 1px; z-index: -1;
        background: rgba(127,209,160,.2);
    }
    .sl-live-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
    .sl-live-pill {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 4px 12px 4px 10px; border-radius: 999px;
        background: var(--card-red); color: #fff; font-weight: 800; font-size: .8rem; letter-spacing: .06em;
    }
    .sl-live-pill i { width: 8px; height: 8px; border-radius: 50%; background: #fff; animation: sl-blink 1.1s infinite; }
    @keyframes sl-blink { 50% { opacity: .2; } }
    .sl-live-round { color: var(--chalk-dim); font-size: .88rem; }
    .sl-live-board { display: grid; grid-template-columns: minmax(0,1fr) auto minmax(0,1fr); gap: 12px; align-items: center; }
    .sl-live-team { display: flex; flex-direction: column; align-items: center; gap: 10px; text-align: center; min-width: 0; }
    .sl-live-team span { font-family: var(--display); font-weight: 800; font-size: 1.5rem; line-height: 1.05; overflow-wrap: anywhere; }
    .sl-mini--lg { width: 64px; height: 64px; font-size: 1.5rem; border: 2px solid rgba(255,255,255,.25); }
    .sl-live-mid { text-align: center; }
    .sl-live-score { font-family: var(--display); font-weight: 900; font-size: clamp(3.2rem, 9vw, 5rem); line-height: .95; font-variant-numeric: tabular-nums; }
    .sl-live-score em { font-style: normal; opacity: .45; margin: 0 8px; }
    .sl-live-clock {
        display: inline-block; margin-top: 8px; padding: 4px 16px; border-radius: 12px;
        background: rgba(0,0,0,.35); border: 1px solid rgba(255,210,63,.5);
        color: var(--card-yellow); font-family: var(--display); font-weight: 800; font-size: 1.9rem; line-height: 1.2;
        font-variant-numeric: tabular-nums; letter-spacing: .04em;
    }
    .sl-live-state { margin-top: 6px; font-size: .82rem; color: var(--chalk-dim); }
    .sl-live-score b.bump { animation: sl-bump .6s ease; }
    @keyframes sl-bump { 30% { transform: scale(1.35); color: var(--card-yellow); } }
    @media (max-width: 520px) {
        .sl-live-board { grid-template-columns: 1fr 1fr; }
        .sl-live-mid { grid-column: 1 / -1; order: -1; }
        .sl-live-card::before { display: none; }
    }
    @media (prefers-reduced-motion: reduce) {
        .sl-live-pill i { animation: none; }
        .sl-live-score b.bump { animation: none; }
    }

    /* ───────── CSAPATOK ───────── */
    .sl-teams {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 16px;
    }
    .sl-team {
        position: relative;
        display: grid;
        grid-template-columns: 64px 1fr;
        gap: 16px;
        align-items: center;
        background: var(--paper);
        border: 1px solid var(--mist);
        border-radius: 20px;
        padding: 18px;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }
    button.sl-team {
        width: 100%;
        text-align: left;
        font: inherit;
        color: inherit;
        cursor: pointer;
    }
    .sl-team:hover { border-color: var(--pitch); box-shadow: 0 16px 32px -22px rgba(8,42,32,.7); transform: translateY(-2px); }
    .sl-team .hint { display: block; margin-top: 8px; font-size: .8rem; font-weight: 700; color: var(--pitch); opacity: .75; }

    /* ───────── JÁTÉKOSLISTA (modal) ───────── */
    .sl-dialog {
        width: min(480px, calc(100% - 32px));
        max-height: min(80vh, 640px);
        padding: 0;
        border: 0;
        border-radius: 26px;
        background: var(--chalk);
        color: var(--ink);
        box-shadow: 0 40px 90px -20px rgba(0,0,0,.65);
        overflow: hidden;
    }
    .sl-dialog[open] { display: flex; flex-direction: column; animation: sl-pop .22s cubic-bezier(.2,.8,.2,1); }
    .sl-dialog::backdrop { background: rgba(4,12,10,.72); backdrop-filter: blur(5px); animation: sl-fade .2s ease; }
    @keyframes sl-pop { from { opacity: 0; transform: translateY(14px) scale(.96); } }
    @keyframes sl-fade { from { opacity: 0; } }
    .sl-dialog-head {
        display: flex; align-items: center; gap: 16px;
        padding: 22px 24px;
        background: linear-gradient(160deg, var(--pitch), var(--pitch-deep));
        color: var(--chalk);
    }
    .sl-dialog-head .sl-crest { width: 52px; height: 60px; font-size: 1.4rem; flex: none; }
    .sl-dialog-head h3 { margin: 0; font-family: var(--display); font-weight: 800; font-size: 1.8rem; line-height: 1; overflow-wrap: anywhere; }
    .sl-dialog-head p { margin: 6px 0 0; color: var(--chalk-dim); font-size: .9rem; }
    .sl-dialog-head > div { flex: 1; min-width: 0; }
    .sl-close {
        flex: none;
        width: 40px; height: 40px; border-radius: 50%;
        border: 1px solid rgba(242,247,239,.35);
        background: transparent; color: var(--chalk);
        font-size: 1.4rem; line-height: 1; cursor: pointer;
    }
    .sl-close:hover { background: rgba(242,247,239,.14); }
    .sl-dialog-body { overflow-y: auto; padding: 8px 12px 16px; }
    .sl-roster { list-style: none; margin: 0; padding: 0; }
    .sl-roster li {
        display: grid; grid-template-columns: 48px 1fr; gap: 14px; align-items: center;
        padding: 11px 12px;
        border-bottom: 1px solid #d5e3d9;
    }
    .sl-roster li:last-child { border-bottom: 0; }
    .sl-roster .no {
        width: 40px; height: 40px; border-radius: 12px;
        display: grid; place-items: center;
        background: var(--pitch); color: var(--chalk);
        font-family: var(--display); font-weight: 800; font-size: 1.3rem;
    }
    .sl-roster .no--none { background: var(--mist); color: #7a8f83; }
    .sl-roster b { font-weight: 700; }
    .sl-roster small { display: block; color: #5b6f64; font-size: .85rem; }
    .sl-dialog-empty { padding: 28px 16px; text-align: center; color: #4f6358; }
    body.sl-lock { overflow: hidden; }
    .sl-crest {
        width: 64px; height: 72px;
        display: grid; place-items: center;
        color: #fff;
        font-family: var(--display);
        font-weight: 900;
        font-size: 1.7rem;
        letter-spacing: .02em;
        background: hsl(var(--h) 55% 32%);
        clip-path: polygon(0 0, 100% 0, 100% 62%, 50% 100%, 0 62%);
        box-shadow: inset 0 0 0 100px rgba(0,0,0,0);
    }
    .sl-team h3 {
        margin: 0 0 4px;
        font-family: var(--display);
        font-weight: 800;
        font-size: 1.5rem;
        line-height: 1.05;
        overflow-wrap: anywhere;
    }
    .sl-team .meta { display: flex; flex-wrap: wrap; gap: 6px 12px; font-size: .88rem; color: #5b6f64; }
    .sl-chip {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 999px;
        background: var(--mist);
        color: var(--pitch);
        font-weight: 700;
        font-size: .82rem;
    }
    .sl-team .players {
        margin-top: 10px;
        display: inline-flex; align-items: baseline; gap: 6px;
        font-size: .9rem; color: #3f554a;
    }
    .sl-team .players b { color: var(--pitch); font-family: var(--display); font-weight: 800; font-size: 1.45rem; line-height: 1; }

    /* ───────── RESZPONZÍV ───────── */
    @media (max-width: 900px) {
        .sl-grid { grid-template-columns: 1fr; }
        .sl-join { position: static; }
    }
    @media (max-width: 560px) {
        .sl-hero { border-radius: 20px; }
        .sl-stats { grid-template-columns: 1fr; border-top: 0; }
        .sl-stat, .sl-stat + .sl-stat { padding: 16px 0; border-left: 0; border-top: 1px solid rgba(127,209,160,.3); }
        .sl-btn { width: 100%; justify-content: center; }
    }
    @media (prefers-reduced-motion: reduce) {
        .sl-pitch * { animation: none; stroke-dashoffset: 0; }
        .sl-pulse { animation: none; }
        .sl-btn { transition: none; }
        .sl-btn:hover { transform: none; }
    }
</style>

<div class="sl">

    {{-- ═════════ HERO ═════════ --}}
    <header class="sl-hero">
        <svg class="sl-pitch" viewBox="0 0 1200 600" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
            <rect x="40" y="40" width="1120" height="520" rx="6"/>
            <line x1="600" y1="40" x2="600" y2="560"/>
            <circle cx="600" cy="300" r="95"/>
            <circle class="dot" cx="600" cy="300" r="5"/>
            <rect x="40" y="170" width="170" height="260"/>
            <rect x="40" y="235" width="62" height="130"/>
            <rect x="990" y="170" width="170" height="260"/>
            <rect x="1098" y="235" width="62" height="130"/>
            <path d="M210 245 A 80 80 0 0 1 210 355"/>
            <path d="M990 245 A 80 80 0 0 0 990 355"/>
        </svg>

        @if ($tournament)
            <div class="sl-status">
                <span class="sl-pulse {{ $isActive ? '' : 'sl-pulse--idle' }}"></span>
                <span>
                    {{ $tournament->season ?: 'Aktuális szezon' }}
                    @if ($isActive) – folyamatban
                    @elseif ($isUpcoming) – hamarosan indul
                    @endif
                </span>
            </div>
        @else
            <div class="sl-status"><span class="sl-pulse sl-pulse--idle"></span><span>Iskolai labdarúgó-bajnokság</span></div>
        @endif

        <h1 class="sl-title">{{ $tournament?->name ?? 'Suliliga' }}</h1>
        <p class="sl-lead">Eredmények, mérkőzésprogram és csapatok – az osztályok közötti bajnokság egy helyen.</p>

        <div class="sl-cta">
            <a href="{{ route('teams.register') }}" class="sl-btn sl-btn--yellow">
                Csapat nevezése
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
            <a href="#merkozesek" class="sl-btn sl-btn--ghost">Mérkőzések</a>
            <a href="#csapatok" class="sl-btn sl-btn--ghost">Csapatok</a>
        </div>

        <div class="sl-stats" aria-label="A bajnokság számokban">
            <div class="sl-stat">
                <strong data-count="{{ (int) $stats['teams'] }}">{{ $stats['teams'] }}</strong>
                <span>Nevezett csapat</span>
            </div>
            <div class="sl-stat">
                <strong data-count="{{ (int) $stats['played'] }}">{{ $stats['played'] }}</strong>
                <span>Lejátszott mérkőzés</span>
            </div>
            <div class="sl-stat">
                <strong data-count="{{ (int) $stats['goals'] }}">{{ $stats['goals'] }}</strong>
                <span>
                    Szerzett gól
                    @if ($avgGoals !== null) <em>· meccsenként {{ str_replace('.', ',', (string) $avgGoals) }}</em> @endif
                </span>
            </div>
        </div>
    </header>

    {{-- ═════════ ÉLŐ MÉRKŐZÉSEK ═════════ --}}
    @if ($live->isNotEmpty())
        <section class="sl-section sl-live" id="elo" aria-label="Élő mérkőzések">
            <div class="sl-head">
                <h2><span class="bar bar--red"></span>Élő mérkőzések</h2>
                <p>Az állás és az idő automatikusan frissül</p>
            </div>
            <div class="sl-live-grid">
                @foreach ($live as $match)
                    @include('partials.live-card', ['match' => $match])
                @endforeach
            </div>
        </section>
    @endif

    {{-- ═════════ MÉRKŐZÉSEK + NEVEZÉS ═════════ --}}
    <div class="sl-grid sl-section" id="merkozesek">
        <div class="sl-matches">
            <section>
                <div class="sl-head">
                    <h2><span class="bar"></span>Következő mérkőzések</h2>
                </div>
                @forelse ($upcoming as $match)
                    @if ($loop->first)<div class="match-list">@endif
                    @include('partials.match-row', ['match' => $match])
                    @if ($loop->last)</div>@endif
                @empty
                    <div class="sl-empty"><strong>Még nincs kiírt mérkőzés</strong>A mérkőzések az adminisztrátor által rögzített adatok alapján jelennek meg.</div>
                @endforelse
            </section>

            <section>
                <div class="sl-head">
                    <h2><span class="bar bar--red"></span>Legutóbbi eredmények</h2>
                </div>
                @forelse ($recent as $match)
                    @if ($loop->first)<div class="match-list">@endif
                    @include('partials.match-row', ['match' => $match])
                    @if ($loop->last)</div>@endif
                @empty
                    <div class="sl-empty"><strong>Még nincs lejátszott mérkőzés</strong>Az első eredmények rögzítése után itt jelennek meg.</div>
                @endforelse
            </section>
        </div>

        <aside class="sl-join">
            <h2>Nevezz csapatot!</h2>
            <p>Két lépésben kész, a nevezést az adminisztrátor hagyja jóvá.</p>
            <ol class="sl-steps">
                <li>
                    <span class="n">1</span>
                    <div><b>Csapat és játékosok</b><small>Add meg a csapat nevét, az osztályt és a keret tagjait.</small></div>
                </li>
                <li>
                    <span class="n">2</span>
                    <div><b>Mezszámok</b><small>Oszd ki a mezszámokat, és küldd be a nevezést.</small></div>
                </li>
            </ol>
            <a href="{{ route('teams.register') }}" class="sl-btn sl-btn--dark">Nevezés indítása</a>
        </aside>
    </div>

    {{-- ═════════ CSAPATOK ═════════ --}}
    <section class="sl-section" id="csapatok">
        <div class="sl-head">
            <h2><span class="bar"></span>Részt vevő csapatok</h2>
            @if ($teams->isNotEmpty())
                <p>{{ $teams->count() }} jóváhagyott csapat</p>
            @endif
        </div>

        @if ($teams->isEmpty())
            <div class="sl-empty"><strong>Még nincs jóváhagyott csapat</strong>A jóváhagyott nevezések itt jelennek meg.</div>
        @else
            <div class="sl-teams">
                @foreach ($teams as $team)
                    @php
                        $hue = crc32((string) $team->name) % 360;
                        $initials = mb_strtoupper(mb_substr((string) $team->name, 0, 2));
                        $roster = collect($team->players ?? [])->sortBy(fn ($p) => (int) (data_get($p, 'jersey_number') ?? data_get($p, 'number') ?? 999));
                    @endphp
                    <button type="button" class="sl-team" data-sl-open="sl-team-{{ $loop->index }}" aria-haspopup="dialog">
                        <div class="sl-crest" style="--h: {{ $hue }}" aria-hidden="true">{{ $initials }}</div>
                        <div>
                            <h3>{{ $team->name }}</h3>
                            <div class="meta">
                                <span class="sl-chip">{{ $team->class_name }}</span>
                                <span>Kapitány: {{ $team->captain_name ?? '—' }}</span>
                            </div>
                            <div class="players"><b>{{ $team->players_count }}</b> játékos</div>
                            <span class="hint">Játékosok megtekintése</span>
                        </div>
                    </button>

                    <dialog class="sl-dialog" id="sl-team-{{ $loop->index }}" aria-label="{{ $team->name }} – játékosok">
                        <div class="sl-dialog-head">
                            <div class="sl-crest" style="--h: {{ $hue }}" aria-hidden="true">{{ $initials }}</div>
                            <div>
                                <h3>{{ $team->name }}</h3>
                                <p>{{ $team->class_name }} · Kapitány: {{ $team->captain_name ?? '—' }}</p>
                            </div>
                            <button type="button" class="sl-close" data-sl-close aria-label="Bezárás">&times;</button>
                        </div>
                        <div class="sl-dialog-body">
                            @if ($roster->isEmpty())
                                <div class="sl-dialog-empty">Ehhez a csapathoz még nincs játékos rögzítve.</div>
                            @else
                                <ul class="sl-roster">
                                    @foreach ($roster as $player)
                                        @php
                                            $no = data_get($player, 'jersey_number') ?? data_get($player, 'number');
                                            $pname = data_get($player, 'name') ?? data_get($player, 'full_name') ?? '—';
                                            $isCaptain = filled($team->captain_name) && $pname === $team->captain_name;
                                        @endphp
                                        <li>
                                            <span class="no {{ filled($no) ? '' : 'no--none' }}">{{ filled($no) ? $no : '–' }}</span>
                                            <span><b>{{ $pname }}</b>@if ($isCaptain)<small>Csapatkapitány</small>@endif</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </dialog>
                @endforeach
            </div>
        @endif
    </section>
</div>

<script>
    // Élő meccsek: másodpercenként ketyegő óra + 5 mp-enként friss állás a szervertől.
    (function () {
        var pageLoaded = Date.now();
        var cards = {};
        function pad(n) { return (n < 10 ? '0' : '') + n; }
        function fmt(s) { s = Math.max(0, Math.floor(s)); return pad(Math.floor(s / 60)) + ':' + pad(s % 60); }

        document.querySelectorAll('[data-live-id]').forEach(function (el) {
            cards[el.dataset.liveId] = {
                el: el,
                base: parseInt(el.dataset.elapsed, 10) || 0,
                at: pageLoaded,
                running: el.dataset.running === '1'
            };
        });

        function render() {
            Object.keys(cards).forEach(function (id) {
                var c = cards[id];
                var secs = c.base + (c.running ? (Date.now() - c.at) / 1000 : 0);
                c.el.querySelector('[data-clock]').textContent = fmt(secs);
            });
        }
        render();
        setInterval(render, 250);

        function setScore(el, sel, value) {
            var node = el.querySelector(sel);
            if (node && node.textContent !== String(value)) {
                node.textContent = value;
                node.classList.remove('bump'); void node.offsetWidth; node.classList.add('bump');
            }
        }

        function poll() {
            if (document.hidden) return;
            fetch(@json(route('matches.live')), { headers: { 'Accept': 'application/json' }, cache: 'no-store' })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    var ids = data.matches.map(function (m) { return String(m.id); }).sort().join(',');
                    var current = Object.keys(cards).sort().join(',');
                    if (ids !== current) { window.location.reload(); return; }   // indult vagy véget ért egy meccs

                    data.matches.forEach(function (m) {
                        var c = cards[m.id];
                        c.base = m.elapsed; c.at = Date.now(); c.running = m.running;
                        setScore(c.el, '[data-home]', m.home_score);
                        setScore(c.el, '[data-away]', m.away_score);
                        c.el.querySelector('[data-state]').textContent = m.running ? 'Játékban' : 'Szünet';
                    });
                })
                .catch(function () { /* hálózati hiba: a következő körben újra próbáljuk */ });
        }
        setInterval(poll, 5000);
    })();

    // Játékoslista: kártyára kattintva középen megnyílik, alapból zárva van.
    (function () {
        var lock = function (on) { document.body.classList.toggle('sl-lock', on); };
        document.querySelectorAll('[data-sl-open]').forEach(function (btn) {
            var dlg = document.getElementById(btn.dataset.slOpen);
            if (!dlg || typeof dlg.showModal !== 'function') return;
            btn.addEventListener('click', function () { dlg.showModal(); lock(true); });
            dlg.addEventListener('click', function (e) { if (e.target === dlg) dlg.close(); });
            dlg.addEventListener('close', function () { lock(false); btn.focus(); });
            dlg.querySelector('[data-sl-close]').addEventListener('click', function () { dlg.close(); });
        });
    })();

    // Számlálók felfutása – egyetlen, betöltéskor lejátszott mozzanat.
    // JS nélkül és csökkentett mozgásnál a végső érték marad.
    (function () {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        document.querySelectorAll('.sl-stat strong[data-count]').forEach(function (el) {
            var target = parseInt(el.dataset.count, 10);
            if (!target || target < 1) return;
            var start = null, duration = 1400;
            el.textContent = '0';
            function tick(ts) {
                if (start === null) start = ts;
                var p = Math.min((ts - start) / duration, 1);
                el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
                if (p < 1) requestAnimationFrame(tick);
            }
            requestAnimationFrame(tick);
        });
    })();
</script>
@endsection
