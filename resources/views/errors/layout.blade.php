<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CAFE Producciones · @yield('title', 'Error')</title>
    <meta name="description" content="CAFE Producciones - Producción y logística para eventos empresariales y sociales.">
    <style>
        :root{
            --bg-dark-1: #071228;
            --bg-dark-2: #11263f;
            --accent-1: #ffd98a;
            --accent-2: #ffb347;
            --muted: rgba(255,255,255,0.88);
            --card-bg: rgba(10,18,34,0.88);
        }
        html,body{height:100%;}
        *{box-sizing:border-box}
        body{
            margin:0;
            font-family:Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color:var(--muted);
            background:
                radial-gradient(circle at top left, rgba(252,197,106,0.12), transparent 22%),
                radial-gradient(circle at bottom right, rgba(65,188,255,0.08), transparent 18%),
                linear-gradient(180deg,var(--bg-dark-1),var(--bg-dark-2));
            overflow-x:hidden;
            position:relative;
        }
        /* background figure (very subtle cafe image) */
        .bg-figure{
            position:fixed;inset:0;z-index:0;pointer-events:none;
            background-image:url('{{ asset('build/assets/eventooim-BsqSqFrX.jpg') }}');
            background-size:cover;background-position:center;
            opacity:0.06;filter:grayscale(.25) blur(2px);
            transform:scale(1.02);
        }
        /* Page/card layout */
        .page{position:relative;z-index:1;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2.5rem 1.25rem;text-align:center}
        .card{width:min(100%,980px);padding:2.25rem;border-radius:20px;background:var(--card-bg);box-shadow:0 40px 90px rgba(2,6,23,0.45);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.04);overflow:hidden}
        .brand-badge{display:inline-flex;align-items:center;gap:.6rem;margin-bottom:1rem;padding:.6rem 1rem;border-radius:999px;background:rgba(255,255,255,0.04);color:var(--muted);letter-spacing:.12em;text-transform:uppercase;font-size:.85rem}
        .brand-badge::before{content:'';width:.6rem;height:.6rem;border-radius:50%;background:linear-gradient(135deg,var(--accent-1),var(--accent-2));box-shadow:0 0 14px rgba(255,176,69,.35)}
        .brand-logo{width:110px;margin:0 auto 1.15rem;display:block;filter:drop-shadow(0 16px 28px rgba(0,0,0,.35))}

        /* Error code with glow */
        .code{font-size:clamp(3.4rem,8vw,6rem);font-weight:900;letter-spacing:-0.04em;margin:0 0 .6rem;color:var(--accent-1);text-shadow:0 6px 30px rgba(255,200,120,.08);display:block}
        .code.glow{animation:glowPulse 1.6s ease-in-out infinite alternate}

        /* Title reveal: letter by letter */
        .title{font-size:clamp(1.7rem,4vw,3.2rem);margin:0;font-weight:800;line-height:1.02}
        .title-letters{display:inline-block;white-space:pre-wrap}
        .title-letters .letter{display:inline-block;opacity:0;transform:translateY(8px);animation:letterIn .5s forwards;}
        .title-letters .letter:nth-child(odd){animation-duration:.48s}

        .subtitle,.message,.meta{margin:1rem auto 0;max-width:720px;color:rgba(255,255,255,0.9)}

        /* Actions/buttons */
        .actions{display:flex;flex-wrap:wrap;gap:.8rem;justify-content:center;margin-top:1.6rem}
        .button{display:inline-flex;align-items:center;justify-content:center;min-width:160px;padding:.9rem 1.2rem;border-radius:999px;border:0;background:linear-gradient(135deg,var(--accent-1),var(--accent-2));color:#0b0b0b;font-weight:700;text-decoration:none;box-shadow:0 18px 44px rgba(73,35,11,.18);transition:transform .22s ease,box-shadow .22s ease}
        .button:hover{transform:translateY(-4px);box-shadow:0 30px 60px rgba(73,35,11,.28)}
        .button.secondary{background:transparent;border:1px solid rgba(255,255,255,.08);color:var(--muted)}

        /* Entrance */
        .card.enter{animation:cardIn .7s cubic-bezier(.2,.9,.2,1) both}

        /* Keyframes */
        @keyframes letterIn{0%{opacity:0;transform:translateY(10px) scale(.98)}50%{opacity:1;transform:translateY(-4px) scale(1.02)}100%{opacity:1;transform:translateY(0) scale(1)}}
        @keyframes glowPulse{from{text-shadow:0 8px 30px rgba(255,214,140,.25),0 0 60px rgba(255,180,70,.12)}to{text-shadow:0 20px 60px rgba(255,240,180,.45),0 0 120px rgba(255,170,50,.22)}}
        @keyframes cardIn{0%{opacity:0;transform:translateY(14px) scale(.996)}100%{opacity:1;transform:translateY(0) scale(1)}}

        /* Theme: light override */
        body[data-theme="light"]{color:#111;--muted:rgba(17,24,39,0.9);--card-bg:rgba(255,255,255,0.92);--bg-dark-1:#f6f8fb;--bg-dark-2:#eef4fb}
        body[data-theme="light"] .bg-figure{opacity:0.08;filter:grayscale(0) blur(1px)}
        body[data-theme="light"] .button{color:#111}

        @media(max-width:720px){.card{padding:1.6rem;border-radius:16px}.brand-logo{width:92px}.code{font-size:3.6rem}.title{font-size:1.9rem}}
    </style>
</head>
<body>
    <main class="page" id="errorPage">
        <div class="card">
            <div class="brand-badge">CAFE Producciones</div>
            <img class="brand-logo" src="{{ asset('images/logo-cafe.png') }}" alt="Logo CAFE Producciones" loading="lazy">
            <div class="code" aria-hidden="true">@yield('code', 'Error')</div>
            <h1 class="title" aria-live="polite">@yield('title', 'Ups...')</h1>
            <p class="subtitle">@yield('headline', 'Algo no salió como esperábamos.')</p>
            <p class="message">@yield('message', 'Nuestro equipo ya está trabajando para resolverlo.')</p>
            <div class="actions">
                <a href="@yield('actionUrl', url('/'))" class="button" id="primaryAction">@yield('action', 'Volver al inicio')</a>
                <a href="/" class="button secondary">Explorar servicios</a>
                <a href="mailto:contacto@cafeproducciones.com" class="button secondary">Contactar</a>
                <a href="/report" class="button secondary">Reportar problema</a>
            </div>
            <p class="meta">@yield('meta', 'Eventos corporativos, sociales e institucionales con alto impacto.')</p>
        </div>
        <div class="bg-figure" aria-hidden="true"></div>
    </main>

    <script>
        (function(){
            // Detect prefered color scheme and set data-theme on body
            function applyTheme() {
                var prefersLight = window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches;
                document.body.dataset.theme = prefersLight ? 'light' : 'dark';
            }
            applyTheme();
            window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', applyTheme);

            // Animate error code with flicker and glow
            var codeEl = document.querySelector('.code');
            if (codeEl) {
                // small flicker effect
                codeEl.classList.add('glow');
                // add accessible title text for screen readers
                codeEl.setAttribute('role','img');
                codeEl.setAttribute('aria-label', codeEl.textContent.trim());
            }

            // Letter-by-letter reveal for title
            var titleEl = document.querySelector('.title');
            if (titleEl) {
                var text = titleEl.textContent.trim();
                titleEl.textContent = '';
                var container = document.createElement('span');
                container.className = 'title-letters';
                titleEl.appendChild(container);
                for (var i=0;i<text.length;i++){
                    var ch = document.createElement('span');
                    ch.className = 'letter';
                    ch.textContent = text[i];
                    ch.style.animationDelay = (i*30)+'ms';
                    container.appendChild(ch);
                }
            }

            // small entrance for card
            document.querySelector('.card').classList.add('enter');
        })();
    </script>
</body>
</html>
