<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=barlow-condensed:600,800,900&display=swap" rel="stylesheet">

<style>
    /* ───────── Színek: a nyilvános oldal navy–zöld világa ───────── */
    :root {
        --gray-50:  #f4f7fb;
        --gray-100: #e8eef6;
        --gray-200: #d3deec;
        --gray-300: #aebdd2;
        --gray-400: #8497b3;
        --gray-500: #62779a;
        --gray-600: #475b7d;
        --gray-700: #2b3b55;
        --gray-800: #1a2840;
        --gray-900: #111c2e;
        --gray-950: #0b1220;

        --lg-lime: #a3e635;
        --lg-yellow: #ffd23f;
        --lg-red: #ff4b3e;
        --lg-pitch: #0c3a2c;
        --lg-pitch-deep: #082a20;
        --lg-line: #7fd1a0;
        --lg-chalk: #f2f7ef;
        --lg-display: 'Barlow Condensed', 'Arial Narrow', Impact, sans-serif;
    }

    /* ───────── Betűk ───────── */
    .fi-header-heading,
    .fi-section-header-heading,
    .fi-wi-stats-overview-stat-value,
    .fi-simple-header-heading,
    .fi-ta-header-heading {
        font-family: var(--lg-display);
        font-weight: 800;
        letter-spacing: .005em;
    }
    .fi-header-heading { font-size: 2.4rem; line-height: 1; }
    .fi-wi-stats-overview-stat-value { font-size: 2.8rem; line-height: 1; font-weight: 900; }

    /* ───────── Logó ───────── */
    .lg-brand { display: flex; align-items: center; gap: 10px; color: var(--lg-lime); }
    .lg-brand span {
        font-family: var(--lg-display);
        font-weight: 900;
        font-size: 1.7rem;
        line-height: 1;
        color: #fff;
        letter-spacing: .01em;
    }
    .lg-brand b { color: var(--lg-lime); font-weight: 900; }

    /* ───────── Oldalsáv ───────── */
    .fi-sidebar {
        background: #0d1728;
        border-right: 1px solid var(--gray-700);
    }
    .fi-sidebar-item-btn { border-radius: 12px; transition: background .15s ease; }
    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn,
    .fi-sidebar-item.fi-active .fi-sidebar-item-btn {
        background: linear-gradient(90deg, rgba(163,230,53,.16), rgba(163,230,53,.03));
        box-shadow: inset 3px 0 0 var(--lg-lime);
    }
    .fi-sidebar-item.fi-active .fi-sidebar-item-icon { color: var(--lg-lime); }
    .fi-sidebar-group-label { font-family: var(--lg-display); font-weight: 800; font-size: .95rem; letter-spacing: .04em; }
    /* FIGYELEM: a topbaron nincs backdrop-filter – az eltolná a lenyíló menüket (profil menü). */
    .fi-topbar > nav, .fi-topbar nav {
        background: #0d1728;
        border-bottom: 1px solid var(--gray-700);
    }

    /* ───────── Kártyák, szekciók, táblák ───────── */
    .fi-section, .fi-ta-ctn, .fi-wi-stats-overview-stat {
        border-radius: 20px;
        border: 1px solid var(--gray-700);
        background: var(--gray-900);
        box-shadow: 0 24px 50px -34px rgba(0,0,0,.9);
    }
    .fi-wi-stats-overview-stat { position: relative; overflow: hidden; }
    .fi-wi-stats-overview-stat::before {
        content: '';
        position: absolute; inset: 0 0 auto 0; height: 3px;
        background: linear-gradient(90deg, var(--lg-lime), var(--lg-line), transparent);
    }
    .fi-ta-header-cell { background: rgba(255,255,255,.025); }
    .fi-ta-row { transition: background .12s ease; }
    .fi-ta-row:hover { background: rgba(163,230,53,.045); }
    .fi-badge { border-radius: 999px; font-weight: 700; }
    .fi-btn, .fi-input-wrp, .fi-modal-window { border-radius: 12px; }
    .fi-modal-window { border: 1px solid var(--gray-700); }

    /* ───────── Bejelentkezés: pálya a háttérben ───────── */
    .fi-simple-layout {
        background:
            url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 600' preserveAspectRatio='xMidYMid slice'><g fill='none' stroke='%237fd1a0' stroke-width='2' opacity='.22'><rect x='40' y='40' width='1120' height='520' rx='6'/><line x1='600' y1='40' x2='600' y2='560'/><circle cx='600' cy='300' r='95'/><rect x='40' y='170' width='170' height='260'/><rect x='990' y='170' width='170' height='260'/></g></svg>") center / cover no-repeat,
            radial-gradient(900px 500px at 80% -10%, #14634a 0%, transparent 60%),
            linear-gradient(160deg, var(--lg-pitch), var(--lg-pitch-deep) 60%, #060f0c);
    }
    .fi-simple-main {
        border-radius: 26px;
        border: 1px solid rgba(127,209,160,.3);
        background: rgba(11,18,32,.82);
        backdrop-filter: blur(14px);
        box-shadow: 0 50px 90px -30px rgba(0,0,0,.8);
    }
    .fi-simple-header-heading { font-size: 2.2rem; }

    /* ───────── Kezdőlap: hero ───────── */
    .lg-hero {
        position: relative; isolation: isolate; overflow: hidden;
        border-radius: 26px;
        padding: clamp(24px, 4vw, 44px);
        color: var(--lg-chalk);
        border: 1px solid rgba(127,209,160,.25);
        background:
            repeating-linear-gradient(90deg, rgba(255,255,255,.035) 0 72px, transparent 72px 144px),
            radial-gradient(900px 400px at 90% -20%, #14634a 0%, transparent 60%),
            linear-gradient(160deg, var(--lg-pitch), var(--lg-pitch-deep));
    }
    .lg-hero svg.pitch { position: absolute; inset: 0; width: 100%; height: 100%; z-index: -1; }
    .lg-hero svg.pitch * {
        fill: none; stroke: var(--lg-line); stroke-width: 2; opacity: .26;
        stroke-dasharray: 1800; stroke-dashoffset: 1800;
        animation: lg-draw 2.2s cubic-bezier(.6,.05,.2,1) .1s forwards;
    }
    @keyframes lg-draw { to { stroke-dashoffset: 0; } }
    .lg-status {
        display: inline-flex; align-items: center; gap: 10px;
        padding: 6px 14px; border-radius: 999px;
        border: 1px solid rgba(127,209,160,.45);
        background: rgba(8,42,32,.6);
        font-size: .88rem; font-weight: 500;
    }
    .lg-dot { width: 9px; height: 9px; border-radius: 50%; background: var(--lg-yellow); box-shadow: 0 0 0 0 rgba(255,210,63,.7); animation: lg-pulse 1.8s infinite; }
    .lg-dot--idle { background: #b9cfc2; animation: none; }
    @keyframes lg-pulse { 70% { box-shadow: 0 0 0 11px rgba(255,210,63,0); } 100% { box-shadow: 0 0 0 0 rgba(255,210,63,0); } }
    .lg-hero h2 {
        font-family: var(--lg-display); font-weight: 900; text-transform: uppercase;
        font-size: clamp(2.8rem, 8vw, 5.6rem); line-height: .88; margin: 16px 0 8px; color: var(--lg-chalk);
    }
    .lg-hero p { margin: 0 0 22px; color: #b9cfc2; max-width: 52ch; }
    .lg-actions { display: flex; flex-wrap: wrap; gap: 10px; }
    .lg-act {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 11px 20px; border-radius: 12px; font-weight: 700; font-size: .95rem;
        text-decoration: none; border: 2px solid rgba(242,247,239,.35); color: var(--lg-chalk);
        transition: transform .15s ease, background .15s ease;
    }
    .lg-act:hover { transform: translateY(-2px); background: rgba(242,247,239,.08); }
    .lg-act--primary { background: var(--lg-yellow); border-color: var(--lg-yellow); color: #1a1500; }
    .lg-act--primary:hover { background: #ffe074; }
    .lg-act .lg-count {
        min-width: 24px; padding: 0 7px; border-radius: 999px;
        background: var(--lg-red); color: #fff; font-size: .8rem; line-height: 22px; text-align: center;
    }

    @media (prefers-reduced-motion: reduce) {
        .lg-hero svg.pitch * { animation: none; stroke-dashoffset: 0; }
        .lg-dot { animation: none; }
        .lg-act, .fi-ta-row { transition: none; }
    }
</style>
