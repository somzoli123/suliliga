 
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0b1220">
    <title>@yield('title', 'Suliliga')</title>
    <style>

        
        :root {
            color-scheme: dark;
            --bg: #0b1220;
            --panel: #111c2e;
            --border: #26354a;
            --text: #f1f5f9;
            --muted: #9aaac0;
            --green: #a3e635;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            line-height: 1.6;
        }

        a { color: inherit; text-decoration: none; }

        .site-header {
            border-bottom: 1px solid var(--border);
            background: #0d1728;
        }

        .nav {
            max-width: 1180px;
            margin: auto;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .brand {
            font-size: 1.4rem;
            font-weight: 900;
            letter-spacing: -0.7px;
        }

        .brand span { color: var(--green); }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 22px;
            color: var(--muted);
            font-size: .95rem;
        }

        .nav-links a:hover { color: var(--green); }

        main {
            max-width: 1180px;
            margin: auto;
            padding: 38px 24px 64px;
            min-height: 75vh;
        }

        h1, h2, p { margin-top: 0; }
        h1 { font-size: clamp(2rem, 5vw, 3.2rem); line-height: 1.15; letter-spacing: -1.3px; }
        h2 { font-size: 1.2rem; }
        p { color: var(--muted); }

        .page-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            margin-bottom: 30px;
        }

        .eyebrow {
            color: var(--green);
            font-size: .8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            margin-bottom: 10px;
        }

        .btn {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            padding: 11px 18px;
            border-radius: 9px;
            font-weight: 800;
            cursor: pointer;
            border: 1px solid var(--border);
        }

        .btn--primary {
            color: #142006;
            background: var(--green);
            border-color: var(--green);
        }

        .btn--primary:hover { background: #bef264; }
        .btn--block { display: flex; width: 100%; }

        .stat-strip {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 36px;
        }

        .stat-strip > div, .panel, .empty, .match-list, .table-wrap {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 12px;
        }

        .stat-strip > div { padding: 20px; }
        .stat-strip strong { display: block; font-size: 1.8rem; color: var(--green); }
        .stat-strip small { color: var(--muted); }

        .layout-2 {
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) minmax(250px, 1fr);
            gap: 24px;
        }

        .section { margin-bottom: 36px; }
        .section-head { margin-bottom: 14px; }
        .section-head h2 { margin-bottom: 0; }

        .panel { padding: 24px; }
        .panel--pad { padding: 24px; }
        .muted { color: var(--muted); }

        .empty { padding: 24px; color: var(--muted); }
        .empty strong { display: block; color: var(--text); margin-bottom: 5px; }

        .match-list { overflow: hidden; }
        .match-list > * { padding: 15px 18px; border-bottom: 1px solid var(--border); }
        .match-list > *:last-child { border-bottom: 0; }

        .table-wrap { overflow-x: auto; }
        table.data { width: 100%; border-collapse: collapse; }
        .data th, .data td { padding: 14px 16px; border-bottom: 1px solid var(--border); text-align: center; }
        .data th { color: var(--muted); font-size: .85rem; }
        .data .l { text-align: left; }
        .data tr:last-child td { border-bottom: 0; }

        input, select, textarea {
            width: 100%;
            padding: 12px 14px;
            background: #0b1220;
            color: var(--text);
            border: 1px solid var(--border);
            border-radius: 8px;
            font: inherit;
        }

        label { display: block; margin-bottom: 6px; font-weight: 650; }
        form button { font: inherit; }

        .site-footer {
            border-top: 1px solid var(--border);
            padding: 24px;
            text-align: center;
            color: var(--muted);
            font-size: .9rem;
        }

        @media (max-width: 720px) {
            .nav { padding: 15px 18px; }
            .nav-links { gap: 12px; font-size: .85rem; }
            main { padding: 28px 16px 48px; }
            .page-head { align-items: flex-start; flex-direction: column; }
            .layout-2 { grid-template-columns: 1fr; }
            .stat-strip { gap: 8px; }
            .stat-strip > div { padding: 12px; }
            .stat-strip strong { font-size: 1.4rem; }
        }

        @media (max-width: 420px) {
            .brand { font-size: 1.2rem; }
            .nav-links { gap: 9px; }
            .stat-strip small { font-size: .72rem; }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <nav class="nav">
            <a href="{{ url('/') }}" class="brand">SULI<span>LIGA</span></a>
            <div class="nav-links">
                <a href="{{ url('/#merkozesek') }}">Mérkőzések</a>
                <a href="{{ url('/#csapatok') }}">Csapatok</a>
                <a href="{{ route('teams.register') }}">Nevezés</a>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="site-footer">
        Suliliga · Iskolai labdarúgó-bajnokság
    </footer>
</body>
</html>
