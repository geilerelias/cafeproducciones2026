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
        .card {
            width: min(100%, 900px);
            padding: 2.5rem;
            border-radius: 32px;
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(18, 14, 12, 0.92);
            box-shadow: 0 38px 90px rgba(0, 0, 0, 0.32);
            backdrop-filter: blur(20px);
        }
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1.5rem;
            padding: .85rem 1.25rem;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.05);
            color: #f1eadf;
            letter-spacing: .14em;
            text-transform: uppercase;
            font-size: .85rem;
            box-shadow: 0 18px 40px rgba(0,0,0,.16);
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
        .code {
            font-size: clamp(3.5rem, 6vw, 6rem);
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
            letter-spacing: -.05em;
        }
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
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 190px;
            padding: 1rem 1.75rem;
            border-radius: 999px;
            border: none;
            background: linear-gradient(135deg, #c2925a, #a66f38);
            color: #15100d;
            font-weight: 700;
            text-decoration: none;
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
