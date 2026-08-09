<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Kumaw X Atmosphr - Authentic Dimsum Experience')">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kumaw X Atmosphr')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/dimsum-logo-round.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* Initialize Theme Before Render to Prevent Flash */
        html[data-theme="dark"] {
            /* Dark mode overrides */
            --bg:           #3a0606;
            --bg2:          #4a0a0a;
            --surface:      rgba(255,255,255,0.05);
            --surface-hover:rgba(255,255,255,0.09);
            --gold:         #f59e0b;
            --gold-light:   #fcd34d;
            --gold-dim:     rgba(245,158,11,0.15);
            --text:         #ffe4e4;
            --muted:        #fca5a5;
            --border:       rgba(255,255,255,0.1);
            --nav-bg:       rgba(58, 6, 6, 0.85);
            --nav-border:   rgba(255,255,255,0.1);
            --shadow:       none;
        }

        :root {
            /* Default Light Mode Colors */
            --bg:           #fdf5e6;
            --bg2:          #fbf0df;
            --surface:      #ffffff;
            --surface-hover:#fdf5e6;
            --gold:         #d97706; /* slightly darker gold for better contrast on white */
            --gold-light:   #f59e0b;
            --gold-dim:     rgba(245,158,11,0.15);
            --text:         #4a0a0a;
            --muted:        #995c5c;
            --border:       #e6d5b8;
            --radius-sm:    8px;
            --radius:       12px;
            --radius-lg:    18px;
            --error:        #ef4444;
            --success:      #10b981;
            --warning:      #f59e0b;
            --info:         #3b82f6;
            --nav-bg:       rgba(253, 245, 230, 0.9);
            --nav-border:   #e6d5b8;
            --shadow:       0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }



        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            line-height: 1.6;
            background-image:
                radial-gradient(ellipse at 0% 0%, rgba(245,158,11,0.04) 0%, transparent 50%),
                radial-gradient(ellipse at 100% 100%, rgba(99,102,241,0.04) 0%, transparent 50%);
        }

        /* ---- NAVBAR ---- */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--nav-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--nav-border);
            padding: 0 1.5rem;
        }

        .navbar-inner {
            max-width: 1600px;
            margin: 0 auto;
            height: 64px;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            text-decoration: none;
            flex-shrink: 0;
        }

        .navbar-brand-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--gold), #d97706);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .navbar-brand span {
            font-weight: 700;
            font-size: 1.0625rem;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            flex: 1;
        }

        .nav-link {
            padding: 0.5rem 0.875rem;
            border-radius: var(--radius-sm);
            color: var(--muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.2s, background 0.2s;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--text);
            background: var(--surface);
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-left: auto;
        }

        .theme-toggle-btn {
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--text);
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
            font-size: 1.1rem;
        }

        .theme-toggle-btn:hover {
            background: var(--surface-hover);
        }

        .cart-btn, .order-btn {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--gold-dim);
            border: 1px solid rgba(245,158,11,0.3);
            border-radius: 10px;
            color: var(--gold);
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s;
        }

        .cart-btn:hover, .order-btn:hover { background: rgba(245,158,11,0.25); }

        .cart-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 18px;
            height: 18px;
            background: var(--gold);
            color: #1a0a00;
            border-radius: 50%;
            font-size: 0.6875rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-menu {
            position: relative;
        }

        .user-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4375rem 0.875rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }

        .user-btn:hover { background: var(--surface-hover); }

        .user-avatar {
            width: 28px; height: 28px;
            background: linear-gradient(135deg, var(--gold), #d97706);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8125rem;
            font-weight: 700;
            color: #1a0a00;
        }

        /* ---- DROPDOWN (Bootstrap JS Interop) ---- */
        .dropdown-menu-custom {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 180px;
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 0.375rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            display: none;
            z-index: 1000;
        }

        .dropdown-menu-custom.show { display: block; }

        .dropdown-menu-custom a, .dropdown-menu-custom button {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            width: 100%;
            padding: 0.625rem 0.875rem;
            border-radius: var(--radius-sm);
            color: var(--text);
            font-size: 0.875rem;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
            text-align: left;
            transition: background 0.15s;
        }

        .dropdown-menu-custom a:hover, .dropdown-menu-custom button:hover { background: var(--surface-hover); }
        .dropdown-menu-custom hr { border: none; border-top: 1px solid var(--border); margin: 0.375rem 0; }
        .dropdown-menu-custom .danger { color: var(--error); }

        /* ---- MAIN CONTENT ---- */
        .main { max-width: 1600px; margin: 0 auto; padding: 2rem 1.5rem; }

        /* ---- ALERTS ---- */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.875rem 1.125rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert-success { background: rgba(52,211,153,0.1); border: 1px solid rgba(52,211,153,0.25); color: var(--success); }
        .alert-error   { background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.25); color: var(--error); }
        .alert-info    { background: rgba(96,165,250,0.1);  border: 1px solid rgba(96,165,250,0.25);  color: var(--info); }

        /* ---- CARDS ---- */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow);
        }

        /* ---- BUTTONS ---- */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.625rem 1.25rem;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: opacity 0.2s, transform 0.15s;
            text-decoration: none;
        }

        .btn:hover { opacity: 0.88; transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }

        .btn-gold    { background: linear-gradient(135deg, var(--gold), #d97706); color: #1a0a00; }
        .btn-ghost   { background: var(--surface); border: 1px solid var(--border); color: var(--text); }
        .btn-danger  { background: rgba(248,113,113,0.15); border: 1px solid rgba(248,113,113,0.3); color: var(--error); }
        .btn-success { background: rgba(52,211,153,0.15); border: 1px solid rgba(52,211,153,0.3); color: var(--success); }
        .btn-sm { padding: 0.375rem 0.75rem; font-size: 0.8125rem; }
        .btn-lg { padding: 0.875rem 2rem; font-size: 1rem; border-radius: 12px; }
        .btn-block { width: 100%; justify-content: center; }

        /* ---- BADGE ---- */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.625rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-pending_payment { background: rgba(249,115,22,0.15);  color: #f97316;        border: 1px solid rgba(249,115,22,0.3); }
        .badge-pending   { background: rgba(251,191,36,0.15);  color: var(--warning); border: 1px solid rgba(251,191,36,0.3); }
        .badge-confirmed { background: rgba(96,165,250,0.15);  color: var(--info);    border: 1px solid rgba(96,165,250,0.3); }
        .badge-preparing { background: rgba(167,139,250,0.15); color: #a78bfa;        border: 1px solid rgba(167,139,250,0.3); }
        .badge-ready     { background: rgba(52,211,153,0.15);  color: var(--success); border: 1px solid rgba(52,211,153,0.3); }
        .badge-completed { background: rgba(148,163,184,0.15); color: var(--muted);   border: 1px solid rgba(148,163,184,0.3); }
        .badge-cancelled { background: rgba(248,113,113,0.1);  color: var(--error);   border: 1px solid rgba(248,113,113,0.25); }

        /* ---- FORMS ---- */
        .form-group { margin-bottom: 1.125rem; }
        label { display: block; font-size: 0.875rem; font-weight: 500; color: var(--muted); margin-bottom: 0.4rem; }

        input, select, textarea {
            width: 100%;
            padding: 0.6875rem 0.9375rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text);
            font-family: inherit;
            font-size: 0.9375rem;
            outline: none;
            transition: border-color 0.2s;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(245,158,11,0.1);
        }

        select option { background: var(--bg2); font-size: 0.85rem; }
        .invalid-feedback { color: var(--error); font-size: 0.8125rem; margin-top: 0.3rem; }
        .form-hint { font-size: 0.775rem; color: var(--muted); margin-top: 0.25rem; }

        /* ---- PAGE HEADER ---- */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 800;
        }

        .page-header p { color: var(--muted); font-size: 0.9375rem; margin-top: 0.25rem; }

        /* ---- SECTION TITLE ---- */
        .section-title {
            font-size: 1.0625rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* ---- EMPTY STATE ---- */
        .empty-state {
            text-align: center;
            padding: 4rem 1rem;
            color: var(--muted);
        }

        .empty-state .icon { font-size: 3.5rem; margin-bottom: 1rem; }
        .empty-state h3 { font-size: 1.125rem; color: var(--text); margin-bottom: 0.5rem; }
        .empty-state p  { font-size: 0.9rem; margin-bottom: 1.5rem; }

        @media (max-width: 640px) {
            .navbar-brand span { display: none; }
            .main { padding: 1.25rem 1rem; }
            .navbar-nav { display: none; }   /* hide text nav links on phones — brand + actions only */
            .navbar-actions { gap: 0.5rem; }
            .cart-btn, .order-btn { width: 36px; height: 36px; padding: 0; justify-content: center; font-size: 1.1rem; }
            .nav-text { display: none; }
            select {
                font-size: 0.8rem !important;
                padding: 0.5rem 0.75rem !important;
            }
        }

        /* ---- MODAL (Bootstrap JS Interop) ---- */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1050; overflow-x: hidden; overflow-y: auto; outline: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); }
        .modal.show { display: block; animation: slideUp 0.2s ease; }
        .modal-dialog { position: relative; width: auto; margin: 1.75rem auto; max-width: 450px; pointer-events: none; }
        .modal-content { position: relative; display: flex; flex-direction: column; width: 100%; pointer-events: auto; border-radius: var(--radius-lg); overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.5); }
        .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; }
        .modal-body { padding: 1.5rem; }
        .modal-footer { display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; padding: 1.25rem 1.5rem; }
        .btn-close { background: transparent; border: none; font-size: 1.25rem; color: var(--muted); cursor: pointer; padding: 0; line-height: 1; }
        .btn-close:before { content: "×"; }
        .btn-close:hover { color: var(--text); }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* ---- CHATBOT WIDGET ---- */
        .chatbot-btn { position: fixed; bottom: 1.5rem; right: 1.5rem; width: 60px; height: 60px; background: linear-gradient(135deg, var(--gold), #d97706); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #1a0a00; font-size: 1.75rem; box-shadow: 0 10px 25px rgba(217, 119, 6, 0.4); cursor: pointer; z-index: 9999; transition: transform 0.2s, box-shadow 0.2s; }
        .chatbot-btn:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(217, 119, 6, 0.5); }
        .chatbot-window { position: fixed; bottom: 5.5rem; right: 1.5rem; width: 350px; height: 500px; background: var(--bg2); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: 0 20px 40px rgba(0,0,0,0.5); display: flex; flex-direction: column; z-index: 9998; overflow: hidden; transform: translateY(20px); opacity: 0; pointer-events: none; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); }
        .chatbot-window.open { transform: translateY(0); opacity: 1; pointer-events: auto; }
        .chat-header { background: linear-gradient(135deg, var(--gold), #d97706); color: #1a0a00; padding: 1rem 1.25rem; font-weight: 700; display: flex; justify-content: space-between; align-items: center; }
        .chat-header-close { cursor: pointer; font-size: 1.2rem; }
        .chat-body { flex: 1; padding: 1rem; overflow-y: auto; display: flex; flex-direction: column; gap: 0.75rem; background: var(--bg); scroll-behavior: smooth; }
        .chat-msg { max-width: 85%; padding: 0.6rem 0.8rem; border-radius: var(--radius); font-size: 0.9rem; line-height: 1.4; word-wrap: break-word; }
        .chat-msg.bot { background: var(--bg2); border: 1px solid var(--border); align-self: flex-start; border-bottom-left-radius: 4px; }
        .chat-msg.user { background: var(--gold-dim); color: var(--text); border: 1px solid rgba(245,158,11,0.3); align-self: flex-end; border-bottom-right-radius: 4px; }
        .chat-footer { padding: 1rem; background: var(--bg2); border-top: 1px solid var(--border); display: flex; gap: 0.5rem; }
        .chat-input { flex: 1; padding: 0.5rem 0.75rem; border: 1px solid var(--border); border-radius: 20px; background: var(--bg); color: var(--text); outline: none; font-family: inherit; }
        .chat-send-btn { background: var(--gold); border: none; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #1a0a00; }
        .typing-indicator { display: flex; gap: 3px; padding: 0.5rem; align-items: center; }
        .typing-dot { width: 6px; height: 6px; background: var(--muted); border-radius: 50%; animation: typing 1.4s infinite ease-in-out both; }
        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }
        @keyframes typing { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
        @media (max-width: 480px) { .chatbot-window { width: calc(100% - 2rem); right: 1rem; bottom: 5rem; height: 60vh; } }
    </style>
    @yield('styles')
    
    <script>
        // Apply theme immediately to prevent flashing
        const savedTheme = localStorage.getItem('kumaw-theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-inner">
            @php
                $homeRoute = route('menu.index');
                if (Auth::check() && Auth::user()->isStaff()) {
                    $homeRoute = Auth::user()->hasNavMenuAccess('admin.dashboard') ? route('admin.dashboard') : route('cashier.dashboard');
                }
            @endphp
            <a href="{{ $homeRoute }}" class="navbar-brand">
                <img src="{{ asset('images/dimsum-logo.png') }}" alt="Kumaw X Atmosphr" width="36" height="36" style="border-radius: 50%; object-fit: contain;">
                <span>Kumaw X Atmosphr</span>
            </a>



            <div class="navbar-actions">
                @if(!Auth::check())
                    <a href="{{ route('orders.index') }}" class="order-btn" title="Pesanan Saya" style="background: var(--surface); color: var(--text); border-color: var(--border);">
                        🧾 <span class="nav-text">Pesanan Saya</span>
                    </a>

                    <a href="{{ route('cart.index') }}" class="cart-btn" title="Keranjang">
                        🛒 <span class="nav-text">Keranjang</span>
                        @php
                            $cartQty = collect(session('cart', []))->sum('quantity');
                        @endphp
                        <div class="cart-badge" style="display: {{ $cartQty > 0 ? 'flex' : 'none' }};">{{ $cartQty }}</div>
                    </a>
                @endif

                <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle theme" title="Ganti Tema">
                    <span id="theme-icon">☀️</span>
                </button>

                @auth
                    <div class="user-menu dropdown" style="position: relative;">
                        <button class="user-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding-right: 0.5rem;">
                            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                            <span class="nav-text">{{ Auth::user()->name }} ▾</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                            @if(Auth::user()->isStaff())
                                <a href="{{ route('admin.dashboard') }}" class="dropdown-item">📊 Dashboard</a>
                            @endif
                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changePasswordModal">🔑 Ganti Password</button>
                            <hr>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item danger">🚪 Keluar</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-gold btn-sm">Masuk</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <main class="main">
        @if(session('success'))
            <div class="alert alert-success">✅ {{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">❌ {{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <div>
                    ❌
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Chatbot Widget -->
    @if(!Auth::check())
    <div class="chatbot-btn" id="chatbot-toggle" title="Chat dengan Admin Kumaw (MiMaw)">🤖</div>
    <div class="chatbot-window" id="chatbot-window">
        <div class="chat-header">
            <div>🥟 Admin Kumaw (MiMaw)</div>
            <div class="chat-header-close" id="chatbot-close">✖</div>
        </div>
        <div class="chat-body" id="chat-body">
            <div class="chat-msg bot">Halo! Saya MiMaw 🤖. Mau pesen apa hari ini Kak?</div>
        </div>
        <div class="chat-footer">
            <input type="text" id="chat-input" class="chat-input" placeholder="Ketik pesanan di sini..." autocomplete="off">
            <button class="chat-send-btn" id="chat-send">➤</button>
        </div>
    </div>
    @endif

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="background: var(--bg2); color: var(--text); border: 1px solid var(--border);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border);">
                    <h5 class="modal-title" id="changePasswordModalLabel" style="font-weight: 700; margin: 0;">Ganti Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.password.update') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="current_password">Password Lama</label>
                            <input type="password" name="current_password" id="current_password" class="form-control" required style="background: rgba(255,255,255,0.05); border: 1px solid var(--border); color: var(--text);">
                        </div>
                        <div class="form-group mb-3">
                            <label for="password">Password Baru</label>
                            <input type="password" name="password" id="password" class="form-control" required minlength="8" style="background: rgba(255,255,255,0.05); border: 1px solid var(--border); color: var(--text);">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required minlength="8" style="background: rgba(255,255,255,0.05); border: 1px solid var(--border); color: var(--text);">
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--border);">
                        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-gold">Simpan Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @yield('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            
            // Set initial icon based on applied theme
            const currentTheme = document.documentElement.getAttribute('data-theme');
            updateThemeIcon(currentTheme);

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', () => {
                    let theme = document.documentElement.getAttribute('data-theme');
                    let newTheme = theme === 'dark' ? 'light' : 'dark';
                    
                    document.documentElement.setAttribute('data-theme', newTheme);
                    localStorage.setItem('kumaw-theme', newTheme);
                    updateThemeIcon(newTheme);
                });
            }

            function updateThemeIcon(theme) {
                if(!themeIcon) return;
                if (theme === 'dark') {
                    themeIcon.textContent = '🌙'; 
                } else {
                    themeIcon.textContent = '☀️'; 
                }
            }

            // Chatbot Logic
            const chatToggle = document.getElementById('chatbot-toggle');
            const chatWindow = document.getElementById('chatbot-window');
            const chatClose = document.getElementById('chatbot-close');
            const chatInput = document.getElementById('chat-input');
            const chatSend = document.getElementById('chat-send');
            const chatBody = document.getElementById('chat-body');

            if(chatToggle) {
                chatToggle.addEventListener('click', () => {
                    if (chatWindow.classList.contains('open')) {
                        chatWindow.classList.remove('open');
                    } else {
                        chatWindow.classList.add('open');
                    }
                });
                chatClose.addEventListener('click', () => {
                    chatWindow.classList.remove('open');
                });

                chatInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') sendChatMessage();
                });
                chatSend.addEventListener('click', sendChatMessage);
            }

            function appendMessage(sender, text) {
                const msg = document.createElement('div');
                msg.className = 'chat-msg ' + sender;
                msg.innerHTML = text.replace(/\n/g, '<br>');
                chatBody.appendChild(msg);
                chatBody.scrollTop = chatBody.scrollHeight;
            }

            function showTyping() {
                const typing = document.createElement('div');
                typing.className = 'chat-msg bot';
                typing.innerHTML = `
                    <div style="font-size: 0.8rem; color: var(--gold); margin-bottom: 0.4rem; font-style: italic;">
                        MiMaw sedang berpikir...
                    </div>
                    <div class="typing-indicator" style="padding: 0;">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                `;
                typing.id = 'typing-indicator';
                chatBody.appendChild(typing);
                chatBody.scrollTop = chatBody.scrollHeight;
            }

            function removeTyping() {
                const el = document.getElementById('typing-indicator');
                if (el) el.remove();
            }

            function updateCartBadge() {
                fetch('{{ route('cart.index') }}', { headers: { 'Accept': 'text/html' } })
                    .then(res => res.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newBadge = doc.querySelector('.cart-badge');
                        const oldBadge = document.querySelector('.cart-badge');
                        
                        if (newBadge) {
                            if (oldBadge) {
                                oldBadge.textContent = newBadge.textContent;
                                oldBadge.style.display = newBadge.style.display;
                            } else {
                                const cartBtn = document.querySelector('.cart-btn');
                                if(cartBtn) cartBtn.insertAdjacentHTML('beforeend', `<div class="cart-badge" style="display: flex;">${newBadge.textContent}</div>`);
                            }
                        }
                        
                        // If we are on the cart page, reload it to show new items
                        if (window.location.pathname.includes('/cart')) {
                            window.location.reload();
                        }
                    });
            }

            async function sendChatMessage() {
                const text = chatInput.value.trim();
                if (!text) return;

                appendMessage('user', text);
                chatInput.value = '';
                showTyping();

                try {
                    const res = await fetch('{{ route('chat.send') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ message: text })
                    });
                    
                    const data = await res.json();
                    removeTyping();
                    
                    if (res.ok && data.reply) {
                        appendMessage('bot', data.reply);
                        if (data.cart_updated) {
                            updateCartBadge();
                        }
                    } else {
                        appendMessage('bot', data.reply || 'Maaf, terjadi kesalahan server.');
                    }
                } catch (e) {
                    removeTyping();
                    appendMessage('bot', 'Gagal terhubung ke server.');
                }
            }
        });
    </script>
</body>
</html>
