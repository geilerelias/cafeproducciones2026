<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CAFE Producciones · @yield('title', 'Error')</title>
    <meta name="description" content="CAFE Producciones - Producción y logística para eventos empresariales y sociales.">
    <style>
        :root {
            color-scheme: dark;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #f5ede4;
            background-color: #120f0d;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(199,143,79,.24), transparent 24%),
                radial-gradient(circle at bottom right, rgba(245,220,170,.16), transparent 20%),
                url('{{ asset('build/assets/eventooim-BsqSqFrX.jpg') }}') center/cover no-repeat;
            background-attachment: fixed;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(180deg, rgba(18,14,12,.92), rgba(13,10,9,.98));
            pointer-events: none;
        }
        .page {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.25rem;
            text-align: center;
        }
            :root {
                color-scheme: dark;
                font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                color: #f7f1ea;
                background-color: #0f1620;
            }
            * { box-sizing: border-box; }
            body {
                margin: 0;
                min-height: 100vh;
                background:
                    radial-gradient(circle at top left, rgba(252, 197, 106, 0.18), transparent 22%),
                    radial-gradient(circle at bottom right, rgba(65, 188, 255, 0.18), transparent 18%),
                    linear-gradient(180deg, rgba(10, 26, 50, 0.95), rgba(20, 36, 68, 0.9)),
                    url('{{ asset('build/assets/eventooim-BsqSqFrX.jpg') }}') center/cover no-repeat;
                background-attachment: fixed;
                overflow-x: hidden;
            }
            body::before {
                content: '';
                position: fixed;
                inset: 0;
                background: radial-gradient(circle at center, rgba(255, 255, 255, 0.08), transparent 32%),
                    linear-gradient(180deg, rgba(7, 14, 29, 0.76), rgba(5, 11, 21, 0.88));
                pointer-events: none;
            }
        .brand-badge::before {
            content: '';
            width: 0.7rem;
            height: 0.7rem;
            border-radius: 50%;
            background: linear-gradient(135deg, #d6b48b, #c78f4f);
            box-shadow: 0 0 16px rgba(199,143,79,.55);
        }
        .brand-logo {
            width: 120px;
            margin: 0 auto 1.8rem;
            display: block;
            filter: drop-shadow(0 25px 45px rgba(0,0,0,.28));
            animation: pulse 6s ease-in-out infinite;
        }
                background: rgba(12, 23, 44, 0.94);
                box-shadow: 0 40px 100px rgba(0, 0, 0, 0.2);
            font-weight: 800;
            letter-spacing: -.05em;
            margin: 0 0 1rem;
            color: #e8cf99;
        }
        .title {
            margin: 0;
            font-size: clamp(2.25rem, 4vw, 3.75rem);
            line-height: 1.02;
            font-weight: 800;
                background: rgba(255,255,255,.08);
                color: #f7f2e7;
        .subtitle, .message, .meta {
            margin: 1.25rem auto 0;
            max-width: 720px;
            color: rgba(245, 237, 229, 0.88);
        }
        .subtitle {
            font-size: 1.05rem;
            line-height: 1.8;
        }
        .message {
            font-size: 1rem;
            line-height: 1.8;
        }
        .actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
            .code {
                font-size: clamp(4rem, 6vw, 6.5rem);
                font-weight: 900;
                letter-spacing: -.08em;
                margin: 0 0 1rem;
                color: #ffe18d;
                text-shadow: 0 0 24px rgba(255, 225, 141, 0.35), 0 0 60px rgba(255, 178, 54, 0.2);
                animation: glowPulse 1.6s ease-in-out infinite alternate;
            }
            border-radius: 999px;
            border: none;
            background: linear-gradient(135deg, #c2925a, #a66f38);
            color: #15100d;
            font-weight: 700;
            text-decoration: none;
                background: linear-gradient(120deg, #f6e0c1 0%, #ffe9b1 28%, #e0c88f 48%, #f2ddae 100%);
                background-clip: text;
                -webkit-background-clip: text;
                color: transparent;
                position: relative;
                animation: shimmer 4s linear infinite;
                text-shadow: 0 0 24px rgba(255, 233, 167, 0.2);
            transition: transform .25s ease, box-shadow .25s ease, filter .25s ease;
        }
        .button:hover {
            transform: translateY(-3px);
            box-shadow: 0 24px 58px rgba(79, 44, 19, 0.3);
        }
        .button.secondary {
            background: transparent;
            border: 1px solid rgba(255,255,255,.12);
            color: #f5ede4;
        }
        .meta {
            font-size: .95rem;
            color: rgba(245, 237, 229, 0.72);
        }
        @keyframes pulse {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        @keyframes glowPulse {
            from { text-shadow: 0 0 24px rgba(255, 225, 141, 0.35), 0 0 60px rgba(255, 178, 54, 0.2); }
            to { text-shadow: 0 0 42px rgba(255, 241, 168, 0.65), 0 0 90px rgba(255, 193, 81, 0.35); }
        }
        @keyframes shimmer {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }
        @media (max-width: 720px) {
            .page { padding: 1.8rem .9rem; }
            .card { padding: 2rem; }
            .title { font-size: 2.9rem; }
            .code { font-size: 4.5rem; }
        }
    </style>
</head>
<body>
    <main class="page">
        <div class="card">
            <div class="brand-badge">CAFE Producciones</div>
            <img class="brand-logo" src="{{ asset('images/logo-cafe.png') }}" alt="Logo CAFE Producciones" loading="lazy">
            <div class="code">@yield('code', 'Error')</div>
            <h1 class="title">@yield('title', 'Ups...')</h1>
            <p class="subtitle">@yield('headline', 'Algo no salió como esperábamos.')</p>
            <p class="message">@yield('message', 'Nuestro equipo ya está trabajando para resolverlo.')</p>
            <div class="actions">
                <a href="@yield('actionUrl', url('/'))" class="button">@yield('action', 'Volver al inicio')</a>
                <a href="/" class="button secondary">Explorar servicios</a>
            </div>
            <p class="meta">@yield('meta', 'Eventos corporativos, sociales e institucionales con alto impacto.')</p>
        </div>
    </main>
</body>
</html>
