<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Hospital Queue Management System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:400,500,600,700&family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <style>
    :root {
        /* Color Palette - Sage Green & Black */
        --sage-50: #f6f7f4;
        --sage-100: #e8ebe3;
        --sage-200: #d4d8c9;
        --sage-300: #b5c0a0;
        --sage-400: #94a57e;
        --sage-500: #7a8b66;      /* Primary Sage */
        --sage-600: #637150;
        --sage-700: #4f5c42;
        --sage-800: #424b39;
        --sage-900: #353e2e;

        --black: #111111;
        --gray: #808080;
        --black-soft: #1a1a1a;
        --black-softest: #282828;
        --white: #ffffff;

        /* Semantic colors */
        --success: #166534;
        --success-soft: rgba(22, 101, 52, 0.12);
        --warning: #b7791f;
        --warning-soft: rgba(183, 121, 31, 0.15);
        --danger: #991b1b;
        --danger-soft: rgba(153, 27, 27, 0.14);

        /* Palette mapping */
        --bg: var(--sage-50);
        --bg-soft: var(--sage-100);
        --surface: rgba(255, 255, 255, 0.92);
        --surface-strong: var(--white);
        --surface-dark: var(--black);
        --surface-dark-soft: var(--black-soft);
        --border: rgba(17, 17, 17, 0.1);
        --border-strong: rgba(17, 17, 17, 0.16);
        --text: var(--black);
        --text-form: var(--gray);
        --text-soft: var(--black-soft);
        --accent: var(--sage-500);
        --accent-soft: rgba(122, 139, 102, 0.15);
        --accent-ink: var(--white);
        --shadow: 0 24px 70px rgba(17, 17, 17, 0.08);
        --radius-xl: 28px;
        --radius-lg: 22px;
        --radius-md: 16px;
        --radius-sm: 12px;
    }

    body {
        min-height: 100vh;
        background:
            radial-gradient(
                circle at top left,
                rgba(122, 139, 102, 0.12),
                transparent 28rem
            ),
            radial-gradient(
                circle at right center,
                rgba(122, 139, 102, 0.08),
                transparent 24rem
            ),
            linear-gradient(180deg, #fafbf8 0%, var(--bg) 52%, #e8ebe3 100%);
        color: var(--text);
        font-family: "Manrope", system-ui, sans-serif;
        overflow-x: hidden;
        overflow-y: auto;
        scroll-behavior: smooth;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    .display-font {
        font-family: "Cormorant Garamond", Georgia, serif;
        letter-spacing: -0.03em;
    }

    a {
        color: inherit;
    }

    main {
        min-height: calc(100vh - 120px);
        width: 100%;
    }

    .display-board {
        width: 100vw;
        min-height: 100vh;
        margin-left: calc(50% - 50vw);
        margin-right: calc(50% - 50vw);
        background:
            radial-gradient(circle at top right, rgba(255, 255, 255, 0.05), transparent 20rem),
            linear-gradient(180deg, #080808 0%, #171717 100%);
    }

    .app-shell {
        position: relative;
        overflow-x: hidden;
        overflow-y: auto;
    }

    .app-shell::before,
    .app-shell::after {
        content: "";
        position: fixed;
        z-index: -1;
        border-radius: 999px;
        filter: blur(10px);
    }

    .app-shell::before {
        top: -10rem;
        right: -6rem;
        width: 22rem;
        height: 22rem;
        background: rgba(122, 139, 102, 0.15);
    }

    .app-shell::after {
        left: -7rem;
        bottom: -10rem;
        width: 18rem;
        height: 18rem;
        background: rgba(255, 255, 255, 0.38);
    }

    .site-nav {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(18px);
        border-bottom: 1px solid rgba(122, 139, 102, 0.12);
        z-index: 1030;
    }

    .brand-mark {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 0.9rem;
        background: linear-gradient(135deg, var(--accent) 0%, var(--sage-600) 100%);
        color: var(--accent-ink);
        font-weight: 800;
        letter-spacing: 0.08em;
        box-shadow: 0 14px 30px rgba(122, 139, 102, 0.2);
    }

    .app-card,
    .glass-panel {
        border: 1px solid rgba(122, 139, 102, 0.15);
        border-radius: var(--radius-xl);
        background: linear-gradient(
            180deg,
            rgba(255, 255, 255, 0.95),
            rgba(255, 255, 255, 0.75)
        );
        box-shadow: var(--shadow);
    }

    .dark-panel {
        border: 1px solid rgba(122, 139, 102, 0.2);
        border-radius: var(--radius-xl);
        background: linear-gradient(
            180deg,
            var(--black),
            var(--black-soft)
        );
        box-shadow: 0 28px 80px rgba(0, 0, 0, 0.28);
        color: #f8f4eb;
    }

    .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.22em;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--text-soft);
    }

    .eyebrow::before {
        content: "";
        width: 2.5rem;
        height: 1px;
        background: var(--accent-soft);
    }

    .hero-title {
        font-size: clamp(3rem, 7vw, 5.4rem);
        line-height: 0.95;
    }

    .section-title {
        font-size: clamp(2rem, 4vw, 3rem);
        line-height: 1;
    }

    .lede {
        color: var(--text-soft);
        font-size: 1.02rem;
        line-height: 1.8;
    }

    .btn {
        border-radius: 999px;
        padding: 0.85rem 1.4rem;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--accent) 0%, var(--sage-600) 100%);
        border-color: transparent;
        color: var(--accent-ink);
        box-shadow: 0 16px 34px rgba(122, 139, 102, 0.3);
    }

    .btn-primary:hover,
    .btn-primary:focus {
        background: linear-gradient(135deg, var(--sage-600) 0%, var(--sage-700) 100%);
        color: var(--accent-ink);
    }

    .btn-outline-primary {
        color: var(--accent);
        border-color: var(--accent);
    }

    .btn-outline-primary:hover,
    .btn-outline-primary:focus {
        background: var(--accent);
        color: var(--accent-ink);
    }

    .btn-outline-dark,
    .btn-outline-secondary,
    .btn-outline-danger {
        border-width: 1px;
    }

    .form-control,
    .form-select,
    .input-group-text,
    textarea.form-control {
        border-radius: var(--radius-sm);
        border: 1px solid rgba(122, 139, 102, 0.2);
        background: rgba(255, 255, 255, 0.9);
        color: var(--text);
        padding: 0.9rem 1rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 0.2rem rgba(122, 139, 102, 0.15);
    }

    .form-control::placeholder {
        color: var(--text-form);
    }

    .table {
        --bs-table-bg: transparent;
        --bs-table-hover-bg: rgba(122, 139, 102, 0.05);
    }

    .table > :not(caption) > * > * {
        border-bottom-color: rgba(122, 139, 102, 0.1);
    }

    .metric-card {
        padding: 1.6rem;
        border-radius: var(--radius-lg);
        border: 1px solid rgba(122, 139, 102, 0.12);
        background: rgba(255, 255, 255, 0.75);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 32px 80px rgba(122, 139, 102, 0.15);
    }

    .metric-value {
        font-size: clamp(2rem, 4vw, 2.8rem);
        font-weight: 800;
        line-height: 1;
    }

    .subtle-chip {
        background: var(--accent-soft);
        color: var(--text);
        border: 1px solid rgba(122, 139, 102, 0.2);
    }

    .status-chip.waiting {
        background: var(--bg-soft);
        color: var(--text);
        border: 1px solid var(--sage-200);
    }

    .status-chip.ready {
        background: var(--accent-soft);
        color: var(--accent);
        border: 1px solid var(--accent);
    }

    .status-chip.busy {
        background: var(--warning-soft);
        color: var(--warning);
        border: 1px solid rgba(183, 121, 31, 0.3);
    }

    .status-chip.unavailable {
        background: var(--danger-soft);
        color: var(--danger);
        border: 1px solid rgba(153, 27, 27, 0.3);
    }

    .status-chip.called {
        background: linear-gradient(135deg, var(--sage-100) 0%, var(--sage-200) 100%);
        color: var(--accent);
        border: 1px solid var(--sage-300);
    }

    .status-chip.completed {
        background: var(--success-soft);
        color: var(--success);
        border: 1px solid rgba(22, 101, 52, 0.3);
    }

    .status-chip.cancelled {
        background: var(--danger-soft);
        color: var(--danger);
        border: 1px solid rgba(153, 27, 27, 0.3);
    }

    .ticket-no {
        font-size: clamp(4rem, 12vw, 7rem);
        line-height: 0.9;
        font-weight: 800;
        color: var(--accent);
    }

    .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.22em;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--accent);
    }

    .eyebrow::before {
        content: "";
        width: 2.5rem;
        height: 1px;
        background: var(--accent-soft);
    }

    .info-list {
        display: grid;
        gap: 1rem;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: 0.9rem;
        border-bottom: 1px solid rgba(122, 139, 102, 0.1);
    }

    .info-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .info-label {
        color: var(--text-soft);
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        font-weight: 700;
    }

    .info-value {
        font-weight: 700;
        text-align: right;
    }

    .feature-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .feature-tile {
        padding: 1.2rem;
        border-radius: var(--radius-md);
        background: rgba(255, 255, 255, 0.64);
        border: 1px solid var(--accent-soft);
    }

    .queue-board-grid {
        display: grid;
        gap: 1.25rem;
    }

    .counter-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.5rem;
        border-radius: var(--radius-lg);
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .counter-ticket {
        min-width: 7rem;
        padding: 1.2rem;
        border-radius: 1.4rem;
        background: linear-gradient(180deg, var(--sage-50), var(--sage-100));
        color: var(--accent);
        text-align: center;
    }

    .counter-ticket strong {
        font-size: 2.6rem;
        line-height: 1;
        font-weight: 800;
        color: var(--accent);
    }

    .timeline-note {
        padding: 1rem 0 1rem 1.3rem;
        border-left: 2px solid var(--accent-soft);
    }

    .timeline-note + .timeline-note {
        margin-top: 0.6rem;
    }

    .modal-content {
        border-radius: var(--radius-xl);
        border: 1px solid var(--accent-soft);
        box-shadow: var(--shadow);
    }

    .auth-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }

   .auth-panel {
        width: min(100%, 1100px);
        display: grid;
        grid-template-columns: 1.05fr 0.95fr;
        overflow: hidden;
        
    }

    .auth-side {
        padding: 3rem;
        background: linear-gradient(160deg, var(--sage-700) 0%, var(--sage-800) 55%, var(--sage-900) 100%);
        color: #f8f4eb;
    }

    .auth-form {
        padding: 3rem;
    }

    .footer-note {
        color: var(--accent);
        opacity: 0.7;
        font-size: 0.9rem;
    }

    .empty-state {
        padding: 3rem 1.5rem;
        text-align: center;
        color: var(--text-soft);
    }

    .floating-note {
        border-radius: var(--radius-md);
        background: var(--accent-soft);
        padding: 1rem 1.1rem;
    }

    @media (max-width: 991.98px) {
        .auth-panel {
            grid-template-columns: 1fr;
        }

        .auth-side,
        .auth-form {
            padding: 2rem;
        }

        .counter-card {
            flex-direction: column;
            align-items: flex-start;
        }
    }
    </style>
</head>
<body class="app-shell">
    @php
        $hideChrome = request()->routeIs('display');
    @endphp

    @unless($hideChrome)
        <nav class="navbar navbar-expand-md site-nav sticky-top">
            <div class="container py-1">  
                <a class="navbar-brand d-flex align-items-center gap-3 fw-semibold" href="{{ auth()->check() ? route('dashboard') : route('kiosk.index') }}">
                    <span class="brand-mark">PH</span>
                    <span>
                        <span class="d-block lh-1">Palang Hospital</span>
                    </span>
                </a>
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="main-nav">
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2 px-1">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link m-2 fw-semibold" href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            
                             @if(in_array(auth()->user()->role?->name, ['Administrator', 'Admin'], true))
                                 <li class="nav-item">
                                     <a class="nav-link m-2" href="{{ route('admin.departments') }}">Departments</a>
                                 </li>
                                 <li class="nav-item">
                                     <a class="nav-link m-2" href="{{ route('admin.counters') }}">Counters</a>
                                 </li>
                                 <li class="nav-item">
                                     <a class="nav-link m-2" href="{{ route('admin.services') }}">Services</a>
                                 </li>
                                 <li class="nav-item">
                                     <a class="nav-link m-2" href="{{ route('admin.users') }}">Users</a>
                                 </li>
                                 
                                 <li class="nav-item">
                                     <a class="nav-link m-2" href="{{ route('admin.patients') }}">Patients</a>
                                 </li>
                                 
                              @endif
                            <li class="nav-item">
                                <span class="subtle-chip m-2">{{ auth()->user()->role?->name ?? 'User' }} · {{ auth()->user()->name }}  </span>
                            </li>
                            <li class="nav-item">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-dark btn-sm m-2">Log Out</button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link m-2 {{ request()->routeIs('kiosk.*') ? 'fw-semibold' : '' }}" href="{{ route('kiosk.index') }}">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link m-2 {{ request()->routeIs('about') ? 'fw-semibold' : '' }}" href="{{ route('about') }}">About</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link m-2 {{ request()->routeIs('display') ? 'fw-semibold' : '' }}" href="{{ route('display') }}">Queue Display</a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="btn btn-primary btn-sm px-4" href="{{ route('login') }}">Staff Login</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
    @endunless

    <main class="pb-5">
        @if(session('success') || session('error'))
            <div class="container pt-4">
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-4">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger border-0 shadow-sm rounded-4">{{ session('error') }}</div>
                @endif
            </div>
        @endif

        @yield('content')
    </main>

    @unless($hideChrome)
        <footer class="container pb-4">
            <div class="footer-note text-center">Designed for fast patient registration, orderly queue handling, and calm modern hospital service.</div>
        </footer>
    @endunless

    <!-- Axios for AJAX requests -->
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.7/dist/axios.min.js" integrity="sha384-wH9 community" crossorigin="anonymous"></script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Custom JavaScript setup -->
    <script>
        // Set up Axios defaults (equivalent to bootstrap.js)
        window.axios = axios;
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
</body>
</html>
