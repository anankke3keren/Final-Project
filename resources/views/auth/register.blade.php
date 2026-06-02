<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun – AuraPad</title>
    <meta name="description" content="Buat akun AuraPad Anda dan mulai mencatat dengan elegan.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <style>
        /* =============================================
           AUTH PAGE – AURAPAD REGISTER
        ============================================= */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --auth-bg: #0f0f14;
            --auth-card: #16161e;
            --auth-card-border: rgba(255,255,255,0.07);
            --auth-input-bg: rgba(255,255,255,0.04);
            --auth-input-border: rgba(255,255,255,0.10);
            --auth-input-focus: rgba(129,140,248,0.5);
            --auth-text: #e2e8f0;
            --auth-muted: #64748b;
            --auth-accent: #818cf8;
            --auth-accent-2: #a78bfa;
            --auth-danger: #f87171;
            --auth-success: #34d399;
        }

        html, body {
            height: 100%;
            font-family: 'Instrument Sans', system-ui, sans-serif;
            background-color: var(--auth-bg);
            color: var(--auth-text);
            overflow-x: hidden;
        }

        /* Background canvas with ambient glow */
        .auth-scene {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            overflow: hidden;
        }

        /* Ambient gradient blobs */
        .auth-scene::before {
            content: '';
            position: absolute;
            top: -20%;
            left: -10%;
            width: 60%;
            height: 70%;
            background: radial-gradient(ellipse, rgba(99,102,241,0.12) 0%, transparent 70%);
            pointer-events: none;
            animation: floatBlob1 10s ease-in-out infinite alternate;
        }
        .auth-scene::after {
            content: '';
            position: absolute;
            bottom: -20%;
            right: -10%;
            width: 55%;
            height: 65%;
            background: radial-gradient(ellipse, rgba(167,139,250,0.10) 0%, transparent 70%);
            pointer-events: none;
            animation: floatBlob2 12s ease-in-out infinite alternate;
        }

        @keyframes floatBlob1 {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(5%, 5%) scale(1.05); }
        }
        @keyframes floatBlob2 {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(-5%, -5%) scale(1.08); }
        }

        /* Floating particles */
        .particles {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(129, 140, 248, 0.15);
            animation: floatParticle linear infinite;
        }

        @keyframes floatParticle {
            0%   { transform: translateY(110vh) scale(0.5); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 0.6; }
            100% { transform: translateY(-10vh) scale(1); opacity: 0; }
        }

        /* Grid overlay */
        .grid-overlay {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        /* ---- CARD ---- */
        .auth-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 460px;
            background: var(--auth-card);
            border: 1px solid var(--auth-card-border);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.03),
                0 20px 60px rgba(0,0,0,0.5),
                0 0 80px rgba(99,102,241,0.06);
            animation: cardEntrance 0.6s cubic-bezier(.16,1,.3,1) both;
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(24px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Card top glow edge */
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 20%;
            right: 20%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(129,140,248,0.4), transparent);
            border-radius: 100%;
        }

        /* ---- LOGO ---- */
        .auth-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.75rem;
        }
        .auth-logo-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, #6366f1 0%, #a78bfa 50%, #ec4899 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
            box-shadow: 0 4px 20px rgba(99,102,241,0.35);
        }
        .auth-logo-text h1 {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            background: linear-gradient(90deg, #818cf8, #a78bfa, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .auth-logo-text p {
            font-size: 0.72rem;
            color: var(--auth-muted);
            margin-top: 1px;
        }

        /* ---- HEADER ---- */
        .auth-header { margin-bottom: 1.75rem; }
        .auth-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--auth-text);
            line-height: 1.2;
        }
        .auth-header p {
            font-size: 0.85rem;
            color: var(--auth-muted);
            margin-top: 0.4rem;
        }

        /* ---- FORM ELEMENTS ---- */
        .form-group {
            margin-bottom: 1.1rem;
        }
        .form-group label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--auth-text);
            margin-bottom: 0.4rem;
            letter-spacing: 0.01em;
        }

        .input-wrapper {
            position: relative;
        }
        .input-icon {
            position: absolute;
            inset-y: 0;
            left: 0;
            width: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--auth-muted);
            pointer-events: none;
        }
        .input-icon svg { width: 16px; height: 16px; }

        .auth-input {
            width: 100%;
            padding: 0.7rem 0.9rem 0.7rem 2.6rem;
            background: var(--auth-input-bg);
            border: 1px solid var(--auth-input-border);
            border-radius: 12px;
            color: var(--auth-text);
            font-size: 0.875rem;
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
        }
        .auth-input::placeholder { color: var(--auth-muted); }
        .auth-input:focus {
            border-color: var(--auth-input-focus);
            background: rgba(129,140,248,0.04);
            box-shadow: 0 0 0 3px rgba(129,140,248,0.10);
        }
        .auth-input.is-invalid {
            border-color: rgba(248,113,113,0.5);
            box-shadow: 0 0 0 3px rgba(248,113,113,0.08);
        }

        /* Password toggle button */
        .btn-password-toggle {
            position: absolute;
            inset-y: 0;
            right: 0;
            width: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--auth-muted);
            background: none;
            border: none;
            border-radius: 0 12px 12px 0;
            transition: color 0.2s;
        }
        .btn-password-toggle:hover { color: var(--auth-accent); }
        .btn-password-toggle svg { width: 15px; height: 15px; }

        /* Error text */
        .field-error {
            font-size: 0.73rem;
            color: var(--auth-danger);
            margin-top: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        .field-error svg { width: 12px; height: 12px; flex-shrink: 0; }

        /* Password strength bar */
        .password-strength-bar {
            margin-top: 0.5rem;
            height: 3px;
            border-radius: 99px;
            background: rgba(255,255,255,0.08);
            overflow: hidden;
        }
        .password-strength-fill {
            height: 100%;
            border-radius: 99px;
            transition: width 0.4s ease, background-color 0.4s ease;
            width: 0%;
        }
        .strength-label {
            font-size: 0.7rem;
            color: var(--auth-muted);
            margin-top: 0.25rem;
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 0.8rem 1.25rem;
            margin-top: 0.5rem;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #6366f1 0%, #a78bfa 100%);
            color: white;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.25s ease;
            box-shadow: 0 4px 20px rgba(99,102,241,0.30);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 28px rgba(99,102,241,0.45);
        }
        .btn-submit:active {
            transform: translateY(0);
        }
        .btn-submit::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, transparent 60%);
        }
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Divider */
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.5rem 0;
            color: var(--auth-muted);
            font-size: 0.72rem;
        }
        .auth-divider::before, .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--auth-input-border);
        }

        /* Footer link */
        .auth-footer-link {
            text-align: center;
            font-size: 0.82rem;
            color: var(--auth-muted);
        }
        .auth-footer-link a {
            color: var(--auth-accent);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }
        .auth-footer-link a:hover { color: var(--auth-accent-2); text-decoration: underline; }

        /* Terms note */
        .auth-terms {
            font-size: 0.7rem;
            color: var(--auth-muted);
            text-align: center;
            margin-top: 1.25rem;
            line-height: 1.6;
        }
        .auth-terms a {
            color: var(--auth-accent);
            text-decoration: none;
        }
        .auth-terms a:hover { text-decoration: underline; }

        /* Alert errors */
        .alert-error {
            background: rgba(248,113,113,0.08);
            border: 1px solid rgba(248,113,113,0.2);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 0.8rem;
            color: var(--auth-danger);
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }
        .alert-error svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px; }

        /* Floating label animation */
        .input-wrapper input:focus ~ .input-icon,
        .input-wrapper input:not(:placeholder-shown) ~ .input-icon {
            color: var(--auth-accent);
        }
    </style>
</head>
<body>
<div class="auth-scene">

    <!-- Grid overlay -->
    <div class="grid-overlay"></div>

    <!-- Floating particles -->
    <div class="particles" id="particles-container"></div>

    <!-- Auth Card -->
    <div class="auth-card">

        <!-- Logo -->
        <div class="auth-logo">
            <div class="auth-logo-icon">N</div>
            <div class="auth-logo-text">
                <h1>NOTEPAD</h1>
                <p>Catatan Digital Estetik</p>
            </div>
        </div>

        <!-- Header -->
        <div class="auth-header">
            <h2>Buat Akun Baru ✨</h2>
            <p>Bergabunglah dan mulai perjalanan mencatat Anda hari ini.</p>
        </div>

        <!-- General Errors -->
        @if ($errors->any() && !$errors->has('name') && !$errors->has('email') && !$errors->has('password') && !$errors->has('password_confirmation'))
        <div class="alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <!-- Register Form -->
        <form method="POST" action="{{ route('register') }}" id="register-form" novalidate>
            @csrf

            <!-- Name -->
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/></svg>
                    </span>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="auth-input @error('name') is-invalid @enderror"
                        placeholder="Contoh: Budi Santoso"
                        value="{{ old('name') }}"
                        autocomplete="name"
                        autofocus
                    >
                </div>
                @error('name')
                    <div class="field-error">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    </span>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="auth-input @error('email') is-invalid @enderror"
                        placeholder="kamu@email.com"
                        value="{{ old('email') }}"
                        autocomplete="email"
                    >
                </div>
                @error('email')
                    <div class="field-error">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="auth-input @error('password') is-invalid @enderror"
                        placeholder="Minimal 8 karakter"
                        autocomplete="new-password"
                        id="password-field"
                    >
                    <button type="button" class="btn-password-toggle" id="toggle-password" title="Tampilkan/Sembunyikan">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <!-- Password strength meter -->
                <div class="password-strength-bar">
                    <div class="password-strength-fill" id="strength-fill"></div>
                </div>
                <div class="strength-label" id="strength-label">Masukkan kata sandi</div>
                @error('password')
                    <div class="field-error">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/></svg>
                    </span>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="auth-input @error('password_confirmation') is-invalid @enderror"
                        placeholder="Ulangi kata sandi"
                        autocomplete="new-password"
                    >
                    <button type="button" class="btn-password-toggle" id="toggle-confirm" title="Tampilkan/Sembunyikan">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('password_confirmation')
                    <div class="field-error">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-submit" id="btn-register">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/></svg>
                Buat Akun Sekarang
            </button>
        </form>

        <!-- Divider -->
        <div class="auth-divider">Sudah punya akun?</div>

        <!-- Footer link -->
        <div class="auth-footer-link">
            <a href="{{ route('login') }}">Masuk ke AuraPad →</a>
        </div>

        <!-- Terms -->
        <p class="auth-terms">
            Dengan mendaftar, Anda menyetujui <a href="#">Syarat Layanan</a> dan <a href="#">Kebijakan Privasi</a> kami.
        </p>
    </div>
</div>

<script>
    /* ============================================
       PARTICLES
    ============================================ */
    (function () {
        const container = document.getElementById('particles-container');
        const count = 15;
        for (let i = 0; i < count; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            const size = Math.random() * 4 + 2;
            p.style.cssText = `
                width: ${size}px; height: ${size}px;
                left: ${Math.random() * 100}%;
                animation-duration: ${Math.random() * 15 + 10}s;
                animation-delay: ${Math.random() * -20}s;
                opacity: ${Math.random() * 0.6 + 0.1};
            `;
            container.appendChild(p);
        }
    })();

    /* ============================================
       PASSWORD VISIBILITY TOGGLE
    ============================================ */
    function makeToggle(btnId, inputId) {
        const btn = document.getElementById(btnId);
        const input = document.getElementById(inputId);
        if (!btn || !input) return;
        const eyeOpen = `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0z"/><circle cx="12" cy="12" r="3"/></svg>`;
        const eyeOff  = `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/><path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/><path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/><path d="m2 2 20 20"/></svg>`;
        btn.addEventListener('click', () => {
            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            btn.innerHTML = showing ? eyeOpen : eyeOff;
        });
    }
    makeToggle('toggle-password', 'password');
    makeToggle('toggle-confirm', 'password_confirmation');

    /* ============================================
       PASSWORD STRENGTH METER
    ============================================ */
    const passwordInput = document.getElementById('password');
    const strengthFill  = document.getElementById('strength-fill');
    const strengthLabel = document.getElementById('strength-label');

    passwordInput && passwordInput.addEventListener('input', function () {
        const val = this.value;
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [
            { pct: '0%',   color: '',              label: 'Masukkan kata sandi' },
            { pct: '25%',  color: '#f87171',       label: '⚠️ Sangat lemah' },
            { pct: '50%',  color: '#fb923c',       label: '🔐 Lemah' },
            { pct: '75%',  color: '#facc15',       label: '🔒 Cukup kuat' },
            { pct: '100%', color: '#34d399',       label: '✅ Kuat' },
        ];

        const lvl = val.length === 0 ? levels[0] : levels[score] || levels[1];
        strengthFill.style.width = lvl.pct;
        strengthFill.style.backgroundColor = lvl.color;
        strengthLabel.textContent = lvl.label;
        strengthLabel.style.color = lvl.color || '#64748b';
    });

    /* ============================================
       FORM SUBMIT LOADING STATE
    ============================================ */
    document.getElementById('register-form') && document.getElementById('register-form').addEventListener('submit', function () {
        const btn = document.getElementById('btn-register');
        btn.disabled = true;
        btn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 1s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
            Memproses...
        `;
    });

    /* spin keyframe */
    const style = document.createElement('style');
    style.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
    document.head.appendChild(style);
</script>
</body>
</html>
