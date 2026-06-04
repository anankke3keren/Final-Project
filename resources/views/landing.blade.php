<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NOTEPAD – Ruang Menulis Estetik & Minimalis</title>
    <meta name="description" content="Aplikasi catatan digital premium dengan editor Markdown dan antarmuka yang bersih. Tingkatkan produktivitas menulis Anda sekarang.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        /* =============================================
           RESET & TOKENS
        ============================================= */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            /* Dark, sophisticated glowing palette */
            --bg-body: #050508;
            --bg-card: rgba(15, 15, 20, 0.75);
            --bg-nav: rgba(5, 5, 8, 0.85);
            
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-light: #64748b;
            
            --border-light: rgba(255, 255, 255, 0.08);
            --border-strong: rgba(255, 255, 255, 0.15);
            
            /* Primary Accent: Sophisticated Indigo */
            --accent-primary: #818cf8;
            --accent-hover: #a78bfa;
            --accent-light: rgba(129, 140, 248, 0.15);
            
            /* Gradients */
            --gradient-accent: linear-gradient(135deg, #6366f1 0%, #a78bfa 100%);
            --gradient-mesh: radial-gradient(at 40% 20%, hsla(242,100%,70%,0.2) 0px, transparent 50%),
                             radial-gradient(at 80% 0%, hsla(279,100%,75%,0.2) 0px, transparent 50%),
                             radial-gradient(at 0% 50%, hsla(339,100%,75%,0.15) 0px, transparent 50%);
            
            /* Shadows (Glowing) */
            --shadow-sm: 0 4px 15px rgba(99, 102, 241, 0.1);
            --shadow-md: 0 10px 30px rgba(99, 102, 241, 0.2);
            --shadow-lg: 0 0 40px rgba(99, 102, 241, 0.4);
            
            /* Animations */
            --transition-smooth: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            --transition-bounce: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            background-image: var(--gradient-mesh);
            background-attachment: fixed;
            color: var(--text-main);
            overflow-x: hidden;
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        a { text-decoration: none; color: inherit; }
        button { cursor: pointer; font-family: inherit; border: none; background: none; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }

        /* =============================================
           BACKGROUND ANIMATIONS
        ============================================= */
        .ambient-blob {
            position: absolute;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.8;
            animation: floatBlob 20s ease-in-out infinite alternate;
        }
        .blob-1 { top: -10%; left: -10%; width: 50vw; height: 50vw; background: rgba(99, 102, 241, 0.15); }
        .blob-2 { bottom: -20%; right: -10%; width: 60vw; height: 60vw; background: rgba(167, 139, 250, 0.12); animation-delay: -5s; }

        @keyframes floatBlob {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(3%, 5%) scale(1.05); }
            100% { transform: translate(-2%, -3%) scale(0.95); }
        }

        /* =============================================
           NAVIGATION
        ============================================= */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            padding: 1rem 2rem;
            transition: var(--transition-smooth);
        }
        .navbar.scrolled {
            padding: 0.75rem 2rem;
            background: var(--bg-nav);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid var(--border-light);
        }
        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .nav-logo-icon {
            width: 36px; height: 36px;
            background: var(--text-main);
            color: var(--bg-body);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-family: 'Outfit'; font-size: 1.1rem;
            box-shadow: 0 0 15px rgba(255,255,255,0.15);
        }
        .nav-logo-text {
            font-family: 'Outfit';
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .nav-actions { display: flex; align-items: center; gap: 0.75rem; }
        .btn-nav-login {
            padding: 0.6rem 1.25rem;
            font-size: 0.9rem; font-weight: 500;
            color: var(--text-muted);
            transition: color 0.2s;
        }
        .btn-nav-login:hover { color: var(--text-main); }
        .btn-nav-register {
            padding: 0.6rem 1.25rem;
            font-size: 0.9rem; font-weight: 600;
            background: var(--text-main);
            color: var(--bg-body);
            border-radius: 99px;
            transition: var(--transition-bounce);
            box-shadow: 0 0 15px rgba(255,255,255,0.1);
        }
        .btn-nav-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        /* =============================================
           HERO SECTION
        ============================================= */
        .hero {
            padding: 10rem 2rem 5rem;
            min-height: 100vh;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            text-align: center;
            position: relative;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.4rem 1rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border-light);
            border-radius: 99px;
            font-size: 0.8rem; font-weight: 500; color: var(--accent-primary);
            box-shadow: 0 0 20px rgba(129, 140, 248, 0.2);
            margin-bottom: 2rem;
            opacity: 0; animation: fadeUp 0.8s forwards 0.1s;
        }
        .hero-title {
            font-size: clamp(3rem, 7vw, 5.5rem);
            font-weight: 800;
            letter-spacing: -0.04em;
            line-height: 1.05;
            margin-bottom: 1.5rem;
            max-width: 900px;
            opacity: 0; animation: fadeUp 0.8s forwards 0.2s;
        }
        .hero-title span {
            color: var(--accent-primary);
        }
        .hero-subtitle {
            font-size: 1.15rem;
            color: var(--text-muted);
            max-width: 600px;
            line-height: 1.6;
            margin-bottom: 2.5rem;
            opacity: 0; animation: fadeUp 0.8s forwards 0.3s;
        }
        .hero-cta {
            display: flex; align-items: center; gap: 1rem;
            opacity: 0; animation: fadeUp 0.8s forwards 0.4s;
        }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 1rem 2rem;
            font-size: 1.05rem; font-weight: 600;
            background: var(--accent-primary);
            color: white;
            border-radius: 99px;
            box-shadow: var(--shadow-lg);
            transition: var(--transition-bounce);
        }
        .btn-primary:hover {
            transform: translateY(-3px) scale(1.02);
            background: var(--accent-hover);
            box-shadow: 0 25px 50px -12px rgba(79, 70, 229, 0.35);
        }
        .btn-secondary {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 1rem 2rem;
            font-size: 1.05rem; font-weight: 600;
            background: rgba(255,255,255,0.05);
            color: var(--text-main);
            border: 1px solid var(--border-strong);
            border-radius: 99px;
            transition: var(--transition-bounce);
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,0.1);
            transform: translateY(-3px);
            box-shadow: 0 0 20px rgba(255,255,255,0.05);
        }

        /* Hero Image/Mockup */
        .hero-mockup-wrapper {
            margin-top: 5rem;
            width: 100%; max-width: 1000px;
            perspective: 1000px;
            opacity: 0; animation: fadeUp 1s forwards 0.6s;
        }
        .hero-mockup {
            background: #0f172a;
            border-radius: 20px;
            border: 1px solid var(--border-light);
            box-shadow: 0 30px 60px -15px rgba(0,0,0,0.5), 0 0 40px rgba(129, 140, 248, 0.15);
            overflow: hidden;
            transform: rotateX(5deg) scale(0.95);
            transition: var(--transition-smooth);
        }
        .hero-mockup-wrapper:hover .hero-mockup {
            transform: rotateX(0deg) scale(1);
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.15);
        }
        .mockup-header {
            padding: 1rem;
            border-bottom: 1px solid var(--border-light);
            display: flex; gap: 0.5rem;
            background: #1e293b;
        }
        .mockup-dot { width: 10px; height: 10px; border-radius: 50%; }
        .mockup-body {
            height: 400px;
            background: #0f172a;
            background-image: linear-gradient(rgba(255,255,255,0.03) 2px, transparent 2px);
            background-size: 100% 2.5rem;
            padding: 2.5rem 2rem;
            position: relative;
        }
        .mockup-text-line {
            height: 12px; border-radius: 4px; background: rgba(255,255,255,0.1); margin-bottom: 1.5rem;
            animation: typing 2s infinite alternate ease-in-out;
        }
        .mockup-text-line:nth-child(1) { width: 40%; background: var(--accent-light); height: 24px; margin-bottom: 2rem;}
        .mockup-text-line:nth-child(2) { width: 85%; animation-delay: 0.1s;}
        .mockup-text-line:nth-child(3) { width: 70%; animation-delay: 0.2s;}
        .mockup-text-line:nth-child(4) { width: 90%; animation-delay: 0.3s;}

        @keyframes typing { 0% { opacity: 0.5; transform: scaleX(0.95); transform-origin: left; } 100% { opacity: 1; transform: scaleX(1); transform-origin: left; } }

        /* =============================================
           MARQUEE ANIMATION
        ============================================= */
        .marquee-container {
            width: 100%;
            overflow: hidden;
            background: transparent;
            border-top: 1px solid var(--border-light);
            border-bottom: 1px solid var(--border-light);
            padding: 1.5rem 0;
            margin-bottom: 4rem;
        }
        .marquee-content {
            display: flex;
            white-space: nowrap;
            animation: scrollMarquee 30s linear infinite;
        }
        .marquee-item {
            display: inline-flex; align-items: center; gap: 0.75rem;
            font-family: 'Outfit'; font-size: 1.5rem; font-weight: 600;
            color: var(--text-light);
            padding: 0 2rem;
        }
        .marquee-item svg { color: var(--accent-primary); }
        @keyframes scrollMarquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

        /* =============================================
           BENTO GRID SECTION
        ============================================= */
        .section {
            padding: 5rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        .section-title {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
            letter-spacing: -0.03em;
            margin-bottom: 1rem;
        }
        
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-auto-rows: minmax(280px, auto);
            gap: 1.5rem;
        }
        
        .bento-card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-light);
            border-radius: 24px;
            padding: 2.5rem;
            display: flex; flex-direction: column;
            position: relative;
            overflow: hidden;
            transition: var(--transition-bounce);
            box-shadow: var(--shadow-sm);
        }
        .bento-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: var(--border-strong);
        }
        
        /* Grid Spans */
        .card-large { grid-column: span 2; grid-row: span 2; }
        .card-wide { grid-column: span 2; }
        .card-tall { grid-row: span 2; }

        .bento-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            background: var(--accent-light);
            color: var(--accent-primary);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.5rem;
        }
        .bento-title {
            font-size: 1.5rem; font-weight: 700; letter-spacing: -0.02em;
            margin-bottom: 0.75rem;
        }
        .bento-desc {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Bento Card Specific Visuals */
        .visual-markdown {
            margin-top: auto;
            background: rgba(255,255,255,0.03); border-radius: 12px; padding: 1rem;
            border: 1px solid var(--border-light);
            font-family: monospace; font-size: 0.8rem; color: var(--accent-primary);
        }
        .visual-themes {
            position: absolute; right: -20px; bottom: -20px;
            display: flex; gap: 0.5rem;
            transform: rotate(-10deg);
        }
        .theme-circle { width: 60px; height: 60px; border-radius: 50%; border: 4px solid white; box-shadow: var(--shadow-sm); }

        /* =============================================
           CTA SECTION
        ============================================= */
        .cta {
            margin: 6rem 2rem;
            padding: 5rem 2rem;
            background: var(--bg-card);
            border: 1px solid var(--border-strong);
            border-radius: 32px;
            text-align: center;
            color: var(--text-main);
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 60px rgba(99, 102, 241, 0.15);
        }
        .cta::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at 50% 0%, rgba(129, 140, 248, 0.15) 0%, transparent 70%);
        }
        .cta-content { position: relative; z-index: 1; }
        .cta-title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 700; letter-spacing: -0.03em;
            margin-bottom: 1.5rem;
        }
        .cta-btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 1.25rem 2.5rem;
            font-size: 1.1rem; font-weight: 600;
            background: var(--text-main); color: var(--bg-body);
            border-radius: 99px;
            transition: var(--transition-bounce);
            box-shadow: 0 0 20px rgba(255,255,255,0.1);
        }
        .cta-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 0 30px rgba(255,255,255,0.25);
        }

        /* =============================================
           FOOTER
        ============================================= */
        footer {
            padding: 3rem 2rem;
            text-align: center;
            border-top: 1px solid var(--border-light);
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* =============================================
           UTILITIES & ANIMATION CLASSES (JS triggered)
        ============================================= */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .bento-grid { grid-template-columns: repeat(2, 1fr); }
            .card-large { grid-column: span 2; }
            .card-wide { grid-column: span 2; }
        }
        @media (max-width: 640px) {
            .bento-grid { grid-template-columns: 1fr; }
            .card-large, .card-wide, .card-tall { grid-column: span 1; grid-row: auto; }
            .hero { padding-top: 8rem; }
            .hero-cta { flex-direction: column; width: 100%; }
            .hero-cta a { width: 100%; justify-content: center; }
            .nav-actions { display: none; } /* Simplified mobile nav */
        }
    </style>
</head>
<body>

    <!-- Ambient Background Blobs -->
    <div class="ambient-blob blob-1"></div>
    <div class="ambient-blob blob-2"></div>

    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="nav-inner">
            <a href="#" class="nav-logo">
                <div class="nav-logo-icon">N</div>
                <span class="nav-logo-text">NOTEPAD</span>
            </a>
            <div class="nav-actions">
                <a href="{{ route('login') }}" class="btn-nav-login">Masuk</a>
                <a href="{{ route('register') }}" class="btn-nav-register">Mulai Gratis</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-badge">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m17 5-5-3-5 3"/><path d="m17 19-5 3-5-3"/></svg>
            Ruang Fokus Baru Anda
        </div>
        
        <h1 class="hero-title">
            Tulis dengan bebas,<br><span>tanpa distraksi.</span>
        </h1>
        
        <p class="hero-subtitle">
            Notepad dirancang khusus untuk membebaskan ide Anda. Antarmuka bersih, dukungan Markdown penuh, dan performa secepat kilat.
        </p>
        
        <div class="hero-cta">
            <a href="{{ route('register') }}" class="btn-primary">
                Coba Sekarang
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
            <a href="#features" class="btn-secondary">Lihat Fitur</a>
        </div>

        <div class="hero-mockup-wrapper">
            <div class="hero-mockup">
                <div class="mockup-header">
                    <div class="mockup-dot" style="background:#ff5f56;"></div>
                    <div class="mockup-dot" style="background:#ffbd2e;"></div>
                    <div class="mockup-dot" style="background:#27c93f;"></div>
                </div>
                <div class="mockup-body">
                    <div class="mockup-text-line"></div>
                    <div class="mockup-text-line"></div>
                    <div class="mockup-text-line"></div>
                    <div class="mockup-text-line"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Marquee Animation -->
    <div class="marquee-container">
        <div class="marquee-content" id="marquee">
            <!-- Items injected by JS for infinite loop -->
        </div>
    </div>

    <!-- Bento Grid Features -->
    <section class="section" id="features">
        <div class="section-header reveal">
            <h2 class="section-title">Semua yang Anda butuhkan.</h2>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Disusun dalam kotak-kotak rapi untuk produktivitas maksimal.</p>
        </div>

        <div class="bento-grid">
            <!-- Card 1: Large -->
            <div class="bento-card card-large reveal">
                <div class="bento-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="4 7 4 4 20 4 20 7"/><line x1="9" x2="15" y1="20" y2="20"/><line x1="12" x2="12" y1="4" y2="20"/></svg>
                </div>
                <h3 class="bento-title">Fokus Penuh pada Teks</h3>
                <p class="bento-desc">Antarmuka pengguna kami memudar saat Anda mulai mengetik. Tidak ada tombol yang mengganggu, hanya Anda dan kata-kata Anda.</p>
                <div class="visual-markdown">
                    # Header Besar<br>
                    **Teks tebal** untuk penekanan.<br>
                    > Kutipan yang menginspirasi.<br><br>
                    * Fokus mengalir seketika...
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bento-card reveal">
                <div class="bento-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                </div>
                <h3 class="bento-title">Real-time Markdown</h3>
                <p class="bento-desc">Tulis menggunakan sintaks markdown dan lihat perubahannya secara langsung tanpa jeda.</p>
            </div>

            <!-- Card 3 -->
            <div class="bento-card reveal">
                <div class="bento-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                </div>
                <h3 class="bento-title">Ekspor Mudah</h3>
                <p class="bento-desc">Satu klik untuk mengunduh catatan Anda sebagai .txt atau .md. Data Anda selalu milik Anda.</p>
            </div>

            <!-- Card 4: Wide -->
            <div class="bento-card card-wide reveal">
                <div class="bento-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2v20"/><path d="M2 12h20"/></svg>
                </div>
                <h3 class="bento-title">Tema yang Menyesuaikan Suasana</h3>
                <p class="bento-desc">Pilih dari Light, Dark, Sepia, atau Cyberpunk. Kami menyediakan palet warna yang sempurna untuk setiap waktu dan *mood* menulis Anda.</p>
                <div class="visual-themes">
                    <div class="theme-circle" style="background: #ffffff;"></div>
                    <div class="theme-circle" style="background: #111827;"></div>
                    <div class="theme-circle" style="background: #fdf6e3;"></div>
                </div>
            </div>

            <!-- Card 5 -->
            <div class="bento-card reveal">
                <div class="bento-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <h3 class="bento-title">Kategori Warna</h3>
                <p class="bento-desc">Organisir ide-ide liar Anda ke dalam folder dan kategori berwarna yang indah.</p>
            </div>

            <!-- Card 6 -->
            <div class="bento-card reveal">
                <div class="bento-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="17" y2="22"/><path d="M5 17h14v-1.76a2 2 0 0 0-.44-1.24l-2.78-3.5A2 2 0 0 1 15 9.26V3.5a1.5 1.5 0 0 0-3 0v5.76a2 2 0 0 1-.78 1.24l-2.78 3.5a2 2 0 0 0-.44 1.24H5Z"/></svg>
                </div>
                <h3 class="bento-title">Sematkan & Simpan</h3>
                <p class="bento-desc">Pin catatan penting ke atas, arsipkan yang sudah selesai. Ruang kerja selalu bersih.</p>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta reveal">
        <div class="cta-content">
            <h2 class="cta-title">Mulai Menulis Hari Ini.</h2>
            <a href="{{ route('register') }}" class="cta-btn">
                Buat Akun Gratis
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; {{ date('Y') }} NOTEPAD. Dibangun untuk pikiran yang tenang.</p>
    </footer>

    <!-- Scripts -->
    <script>
        // 1. Navbar Glass Effect on Scroll
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // 2. Marquee Generation
        const marqueeWords = [
            "FOKUS", "ESTETIK", "MARKDOWN", "CEPAT", "MINIMALIS", "PRODUKTIF", "AMAN", "GRATIS"
        ];
        const marqueeContent = document.getElementById('marquee');
        
        // Create repeating items for infinite effect
        let marqueeHTML = '';
        const starSVG = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5l-10 14"/><path d="M5 5l14 14"/></svg>`;
        
        for(let i=0; i<4; i++) { // Repeat 4 times to ensure it fills screen
            marqueeWords.forEach(word => {
                marqueeHTML += `<div class="marquee-item">${starSVG} ${word}</div>`;
            });
        }
        marqueeContent.innerHTML = marqueeHTML;

        // 3. Scroll Reveal Animation using Intersection Observer
        const revealElements = document.querySelectorAll('.reveal');
        
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    observer.unobserve(entry.target); // Only animate once
                }
            });
        }, {
            root: null,
            threshold: 0.1, // Trigger when 10% visible
            rootMargin: "0px 0px -50px 0px" 
        });

        revealElements.forEach(el => revealObserver.observe(el));
    </script>
</body>
</html>
