<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kumaw Dimsum – Panel Admin">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') – Kumaw Dimsum</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Bootstrap Icons added via CDN because Vite is not loaded --}}
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* Initialize Theme Before Render to Prevent Flash */
        html[data-theme="dark"] {
            --bg:           #3a0606;
            --bg2:          #4a0a0a;
            --sidebar-w:    260px;
            --topbar-h:     60px;
            --gold:         #f59e0b;
            --gold-light:   #fcd34d;
            --gold-dim:     rgba(245,158,11,0.12);
            --gold-border:  rgba(245,158,11,0.25);
            --text:         #ffe4e4;
            --muted:        #fca5a5;
            --border:       rgba(255,255,255,0.1);
            --surface:      rgba(255,255,255,0.05);
            --surface-hover:rgba(255,255,255,0.09);
            --radius-sm:    8px;
            --radius:       12px;
            --radius-lg:    16px;
            --error:        #f87171;
            --success:      #34d399;
            --warning:      #fbbf24;
            --info:         #60a5fa;
            --nav-bg:       rgba(58, 6, 6, 0.85);
        }

        :root {
            --bg:           #fdf5e6;
            --bg2:          #fbf0df;
            --sidebar-w:    260px;
            --topbar-h:     60px;
            --gold:         #d97706;
            --gold-light:   #f59e0b;
            --gold-dim:     rgba(245,158,11,0.15);
            --gold-border:  #e6d5b8;
            --text:         #4a0a0a;
            --muted:        #995c5c;
            --border:       #e6d5b8;
            --surface:      #ffffff;
            --surface-hover:#fdf5e6;
            --radius-sm:    8px;
            --radius:       12px;
            --radius-lg:    16px;
            --error:        #ef4444;
            --success:      #10b981;
            --warning:      #f59e0b;
            --info:         #3b82f6;
            --nav-bg:       rgba(253, 245, 230, 0.9);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            line-height: 1.6;
        }

        /* ================================================================
           SIDEBAR
        ================================================================ */
        .sidebar {
            width: var(--sidebar-w);
            height: 100%; /* Fallback */
            height: 100dvh; /* Modern mobile fix */
            background: var(--bg2);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 200;
            transition: transform 0.3s ease;
        }

        /* Brand */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid var(--border);
            text-decoration: none;
            flex-shrink: 0;
        }

        .sidebar-brand-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--gold), #d97706);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            box-shadow: 0 0 20px rgba(245,158,11,0.3);
            flex-shrink: 0;
        }

        .sidebar-brand-text h2 {
            font-size: 0.9375rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .sidebar-brand-text span {
            font-size: 0.6875rem;
            color: var(--muted);
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Navigation */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0.75rem;
        }

        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

        .nav-section-label {
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            padding: 0 0.625rem;
            margin: 1.25rem 0 0.5rem;
        }

        .nav-section-label:first-child { margin-top: 0; }

        .nav-item {
            margin-bottom: 2px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.5625rem 0.75rem;
            border-radius: var(--radius-sm);
            color: var(--muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.15s, background 0.15s;
            cursor: pointer;
        }

        .nav-link i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-link:hover {
            color: var(--text);
            background: var(--surface-hover);
        }

        .nav-link.active {
            color: var(--gold);
            background: var(--gold-dim);
            font-weight: 600;
        }

        .nav-link.active i { color: var(--gold); }

        /* Collapsible group */
        .nav-group-toggle {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.5625rem 0.75rem;
            border-radius: var(--radius-sm);
            color: var(--muted);
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.15s, background 0.15s;
            cursor: pointer;
            width: 100%;
            background: none;
            border: none;
            font-family: inherit;
            text-align: left;
        }

        .nav-group-toggle:hover { color: var(--text); background: var(--surface); }

        .nav-group-toggle i { font-size: 1rem; width: 20px; text-align: center; flex-shrink: 0; }

        .nav-group-toggle .chevron {
            margin-left: auto;
            font-size: 0.75rem;
            transition: transform 0.2s;
            flex-shrink: 0;
        }

        .nav-group-toggle.open .chevron { transform: rotate(90deg); }
        .nav-group-toggle.open { color: var(--text); }

        .nav-sub {
            padding-left: 1.5rem;
            margin-top: 2px;
            display: none;
        }

        .nav-sub.open { display: block; }

        .nav-sub .nav-link {
            font-size: 0.8125rem;
            padding: 0.4375rem 0.625rem;
        }

        /* Sidebar footer / user info */
        .sidebar-footer {
            padding: 0.875rem 1.25rem;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .sidebar-user-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--gold), #d97706);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 700;
            color: #1a0a00;
            flex-shrink: 0;
        }

        .sidebar-user-info { min-width: 0; }

        .sidebar-user-info strong {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-info span {
            font-size: 0.75rem;
            color: var(--gold);
            font-weight: 500;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.5rem;
            border-radius: var(--radius-sm);
            background: rgba(248,113,113,0.08);
            border: 1px solid rgba(248,113,113,0.2);
            color: var(--error);
            font-size: 0.8125rem;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.15s;
            text-decoration: none;
        }

        .btn-logout:hover { background: rgba(248,113,113,0.15); }

        /* ================================================================
           TOPBAR
        ================================================================ */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: var(--nav-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            gap: 1rem;
            z-index: 100;
        }

        .topbar-hamburger {
            display: none;
            align-items: center;
            justify-content: center;
            width: 36px; height: 36px;
            background: none;
            border: none;
            color: var(--muted);
            font-size: 1.25rem;
            cursor: pointer;
            border-radius: var(--radius-sm);
        }

        .topbar-hamburger:hover { background: var(--surface); color: var(--text); }

        .topbar-title {
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--text);
        }

        .topbar-spacer { flex: 1; }

        .topbar-time {
            font-size: 0.8125rem;
            color: var(--muted);
        }

        /* ================================================================
           MAIN CONTENT
        ================================================================ */
        .admin-body {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            flex: 1;
            min-height: calc(100vh - var(--topbar-h));
            padding: 1.75rem 2rem;
            max-width: 100%;
        }

        /* ================================================================
           ALERTS
        ================================================================ */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.875rem 1.125rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert-success { background: rgba(52,211,153,0.1);  border: 1px solid rgba(52,211,153,0.25); color: var(--success); }
        .alert-error   { background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.25); color: var(--error); }
        .alert-warning { background: rgba(251,191,36,0.1);  border: 1px solid rgba(251,191,36,0.25);  color: var(--warning); }
        .alert-info    { background: rgba(96,165,250,0.1);  border: 1px solid rgba(96,165,250,0.25);  color: var(--info); }

        /* ================================================================
           PAGE HEADER
        ================================================================ */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .page-header h1 { font-size: 1.5rem; font-weight: 700; }
        .page-header p  { color: var(--muted); font-size: 0.9rem; margin-top: 0.25rem; }

        /* ================================================================
           CARDS
        ================================================================ */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        .card-body { padding: 1.5rem; }

        /* ================================================================
           TABLE
        ================================================================ */
        .table-wrapper { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }

        thead { background: rgba(255,255,255,0.03); }

        th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            vertical-align: middle;
        }

        tbody tr:hover { background: var(--surface); }
        tbody tr:last-child td { border-bottom: none; }

        /* ================================================================
           BUTTONS
        ================================================================ */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5625rem 1.125rem;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: opacity 0.2s, transform 0.15s, background 0.15s;
            white-space: nowrap;
        }

        .btn:hover  { opacity: 0.88; transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }

        .btn-gold    { background: linear-gradient(135deg, var(--gold), #d97706); color: #1a0a00; }
        .btn-ghost   { background: var(--surface); border: 1px solid var(--border); color: var(--text); }
        .btn-danger  { background: rgba(248,113,113,0.12); border: 1px solid rgba(248,113,113,0.3); color: var(--error); }
        .btn-success { background: rgba(52,211,153,0.12);  border: 1px solid rgba(52,211,153,0.3);  color: var(--success); }
        .btn-indigo  { background: rgba(99,102,241,0.12);  border: 1px solid rgba(99,102,241,0.3);  color: #818cf8; }

        .btn-sm { padding: 0.375rem 0.75rem; font-size: 0.8125rem; }
        .btn-lg { padding: 0.75rem 1.75rem; font-size: 1rem; border-radius: var(--radius); }

        /* ================================================================
           BADGE
        ================================================================ */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.625rem;
            border-radius: 999px;
            font-size: 0.6875rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-gold    { background: var(--gold-dim); border: 1px solid var(--gold-border); color: var(--gold); }
        .badge-muted   { background: rgba(148,163,184,0.12); border: 1px solid rgba(148,163,184,0.25); color: var(--muted); }
        .badge-success { background: rgba(52,211,153,0.12); border: 1px solid rgba(52,211,153,0.3); color: var(--success); }
        .badge-error   { background: rgba(248,113,113,0.12); border: 1px solid rgba(248,113,113,0.3); color: var(--error); }

        /* ================================================================
           FORMS
        ================================================================ */
        .form-group { margin-bottom: 1.25rem; }

        label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: 0.4rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        input[type="tel"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 0.6875rem 0.9375rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text);
            font-family: inherit;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            appearance: auto;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(245,158,11,0.1);
        }

        select option { background: var(--bg2); }

        input.is-invalid, select.is-invalid { border-color: var(--error); }

        .invalid-feedback { color: var(--error); font-size: 0.8125rem; margin-top: 0.3rem; }

        .form-hint { color: var(--muted); font-size: 0.8125rem; margin-top: 0.3rem; }

        input[type="checkbox"] { width: auto; accent-color: var(--gold); }

        /* ================================================================
           PAGINATION
        ================================================================ */
        nav[role="navigation"] { display: flex; align-items: center; gap: 0.25rem; flex-wrap: wrap; margin-top: 1rem; font-size: 0.875rem; }
        nav[role="navigation"] svg { width: 1.25rem; height: 1.25rem; }
        nav[role="navigation"] p { color: var(--muted); margin: 0; display: inline-block; margin-right: 1rem; }
        nav[role="navigation"] a, nav[role="navigation"] span[aria-disabled] { 
            display: inline-flex; align-items: center; justify-content: center;
            padding: 0.5rem 0.75rem; border: 1px solid var(--border);
            border-radius: var(--radius-sm); color: var(--text); background: var(--surface);
            text-decoration: none; min-width: 32px;
        }
        nav[role="navigation"] a:hover { background: var(--surface-hover); }
        nav[role="navigation"] span[aria-current="page"] {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 0.5rem 0.75rem; border: 1px solid var(--gold);
            border-radius: var(--radius-sm); background: var(--gold-dim); color: var(--gold);
            font-weight: 600; min-width: 32px;
        }
        .hidden { display: none !important; }

        /* ================================================================
           OVERLAY (mobile sidebar)
        ================================================================ */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 199;
        }

        /* ================================================================
           RESPONSIVE
        ================================================================ */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
                box-shadow: 4px 0 40px rgba(0,0,0,0.6);
            }

            .sidebar-overlay.open { display: block; }

            .topbar { left: 0; }
            .topbar-hamburger { display: flex; }

            .admin-body { margin-left: 0; }
        }

        @media (max-width: 640px) {
            .admin-body { padding: 1.25rem 1rem; }
            .topbar { padding: 0 1rem; }
            .topbar-title { font-size: 0.8125rem; }
            .page-header h1 { font-size: 1.25rem; }
            .page-header p  { font-size: 0.8125rem; }
        }

    /* ---- DROPDOWN (Bootstrap JS Interop) ---- */
    .dropdown-menu-custom {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
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
        display: flex; align-items: center; gap: 0.625rem; width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-sm); color: var(--text); font-size: 0.875rem; text-decoration: none; background: none; border: none; cursor: pointer; font-family: inherit; text-align: left; transition: background 0.15s;
    }
    .dropdown-menu-custom a:hover, .dropdown-menu-custom button:hover { background: var(--surface-hover); }
    .dropdown-menu-custom hr { border: none; border-top: 1px solid var(--border); margin: 0.375rem 0; }
    .dropdown-menu-custom .danger { color: var(--error); }

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
    @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(52,211,153,0.7); } 70% { box-shadow: 0 0 0 6px rgba(52,211,153,0); } 100% { box-shadow: 0 0 0 0 rgba(52,211,153,0); } }
    </style>
    @yield('styles')
    <script>
        const savedTheme = localStorage.getItem('kumaw-theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</head>
<body>

{{-- ── Sidebar Overlay (mobile) ── --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ================================================================
     SIDEBAR
================================================================ --}}
<aside class="sidebar" id="sidebar" aria-label="Navigasi Admin">

    {{-- Brand --}}
    @php
        $dashRoute = Auth::user()->hasNavMenuAccess('admin.dashboard') ? route('admin.dashboard') : route('cashier.dashboard');
    @endphp
    <a href="{{ $dashRoute }}" class="sidebar-brand">
        <div class="sidebar-brand-icon" style="background: transparent; box-shadow: none;">
            <img src="{{ asset('images/dimsum-logo.png') }}" alt="Logo" style="height: 36px; width: 36px; border-radius: 50%; object-fit: cover;">
        </div>
        <div class="sidebar-brand-text">
            <h2>Kumaw Dimsum</h2>
            <span>Panel Admin</span>
        </div>
    </a>

    {{-- Navigation — dynamically rendered from role_menu --}}
    <nav class="sidebar-nav">

        @php
            /*
             * Load the logged-in user's accessible nav menus from the role_menu pivot.
             * Top-level menus (parent_id = null) are grouped as sections;
             * children are rendered as sub-items within a collapsible group.
             *
             * This query is cached for the request lifecycle via loadMissing().
             */
            $user = auth()->user();
            $user?->loadMissing('role.navMenus');
            $assignedMenus = $user?->role?->navMenus ?? collect();

            // Separate top-level and group-parent menus
            $topLevel  = $assignedMenus->whereNull('parent_id')
                                        ->sortBy('sort_order');

            $childIds  = $assignedMenus->whereNotNull('parent_id')->pluck('parent_id')->unique();
        @endphp

        @foreach($topLevel as $menu)
            @php
                $children = $assignedMenus->where('parent_id', $menu->id)->sortBy('sort_order');
                $isGroup  = $children->isNotEmpty();

                // A group is "open" if any child route is active
                $groupActive = $isGroup && $children->contains(fn($c) => request()->routeIs(rtrim($c->route_name, '.index') . '*'));
            @endphp

            @if($isGroup)
                {{-- Collapsible section (e.g. Pengaturan) --}}
                <div class="nav-item">
                    <button type="button"
                            id="nav-group-{{ $menu->id }}"
                            class="nav-group-toggle {{ $groupActive ? 'open' : '' }}"
                            onclick="toggleNavGroup(this)">
                        @if($menu->icon)
                            <i class="{{ $menu->icon }}"></i>
                        @endif
                        {{ $menu->name }}
                        <i class="bi bi-chevron-right chevron"></i>
                    </button>
                    <div class="nav-sub {{ $groupActive ? 'open' : '' }}" id="sub-{{ $menu->id }}">
                        @foreach($children as $child)
                            <div class="nav-item">
                                <a href="{{ route($child->route_name) }}"
                                   id="nav-{{ Str::slug($child->name) }}"
                                   class="nav-link {{ request()->routeIs(rtrim($child->route_name, '.index') . '*') ? 'active' : '' }}">
                                    @if($child->icon)
                                        <i class="{{ $child->icon }}"></i>
                                    @endif
                                    {{ $child->name }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Regular top-level item --}}
                <div class="nav-item">
                    <a href="{{ route($menu->route_name) }}"
                       id="nav-{{ Str::slug($menu->name) }}"
                       class="nav-link {{ request()->routeIs(rtrim($menu->route_name, '.index') . '*') ? 'active' : '' }}">
                        @if($menu->icon)
                            <i class="{{ $menu->icon }}"></i>
                        @endif
                        {{ $menu->name }}
                    </a>
                </div>
            @endif
        @endforeach

    </nav>

    {{-- Sidebar Footer: user info + logout --}}
    <div class="sidebar-footer">
        <div style="width: 100%; position: relative;">
            <button
                type="button"
                id="sidebarUserBtn"
                onclick="toggleUserDropdown()"
                style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 0.5rem; border: none; background: transparent; color: inherit; text-align: left; margin: 0; cursor: pointer; border-radius: var(--radius-sm); transition: background 0.15s;"
                onmouseenter="this.style.background='var(--surface-hover)'"
                onmouseleave="this.style.background='transparent'"
            >
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div class="sidebar-user-avatar" style="margin: 0;">
                        {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="sidebar-user-info" style="text-align: left; margin: 0; padding: 0; line-height: 1.2;">
                        <strong style="display: block; margin-bottom: 0.25rem;">{{ auth()->user()?->name ?? '' }}</strong>
                        <span style="display: block;">{{ auth()->user()?->role?->name ?? '—' }}</span>
                    </div>
                </div>
                <i class="bi bi-chevron-up" id="sidebarUserChevron" style="font-size: 0.75rem; color: var(--muted); transition: transform 0.2s;"></i>
            </button>

            {{-- Popup menu — opens ABOVE the button --}}
            <div
                id="sidebarUserDropdown"
                style="
                    display: none;
                    position: absolute;
                    bottom: calc(100% + 6px);
                    left: 0;
                    right: 0;
                    background: var(--bg2);
                    border: 1px solid var(--border);
                    border-radius: var(--radius);
                    padding: 0.375rem;
                    box-shadow: 0 -8px 30px rgba(0,0,0,0.45);
                    z-index: 9999;
                "
            >
                <button
                    type="button"
                    onclick="openPasswordModal()"
                    style="display: flex; align-items: center; gap: 0.625rem; width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-sm); color: var(--text); font-size: 0.875rem; background: none; border: none; cursor: pointer; font-family: inherit; text-align: left; transition: background 0.15s;"
                    onmouseenter="this.style.background='var(--surface-hover)'"
                    onmouseleave="this.style.background='none'"
                >
                    <i class="bi bi-key"></i> Ganti Password
                </button>
                @php
                    $activeShift = \App\Models\Shift::where('user_id', auth()->id())->where('status', 'open')->first();
                @endphp
                @if($activeShift)
                <a href="{{ route('cashier.shifts.summary') }}"
                   style="display: flex; align-items: center; gap: 0.625rem; width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-sm); color: var(--gold); font-size: 0.875rem; background: none; border: none; cursor: pointer; font-family: inherit; text-align: left; transition: background 0.15s; text-decoration: none;"
                   onmouseenter="this.style.background='var(--surface-hover)'"
                   onmouseleave="this.style.background='none'"
                >
                    <i class="bi bi-stop-circle"></i> Tutup Shift
                </a>
                @endif
                <hr style="border: none; border-top: 1px solid var(--border); margin: 0.375rem 0;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        style="display: flex; align-items: center; gap: 0.625rem; width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-sm); color: var(--error); font-size: 0.875rem; background: none; border: none; cursor: pointer; font-family: inherit; text-align: left; transition: background 0.15s;"
                        onmouseenter="this.style.background='rgba(248,113,113,0.08)'"
                        onmouseleave="this.style.background='none'"
                    >
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

{{-- ================================================================
     TOPBAR
================================================================ --}}
<header class="topbar">
    <button class="topbar-hamburger" id="hamburgerBtn"
            aria-label="Toggle sidebar" aria-expanded="false"
            onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>

    <span class="topbar-title">@yield('title', 'Dashboard')</span>

    <div class="topbar-spacer"></div>

    @if(isset($activeShift) && $activeShift)
        <div style="background: var(--gold-dim); border: 1px solid var(--gold-border); padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); display: flex; align-items: center; gap: 0.5rem; margin-right: 1rem;">
            <div style="width: 8px; height: 8px; background: var(--success); border-radius: 50%; box-shadow: 0 0 8px var(--success); animation: pulse 2s infinite;"></div>
            <span style="font-size: 0.75rem; font-weight: 600; color: var(--gold); text-transform: uppercase; letter-spacing: 0.05em;">{{ auth()->user()->name }}</span>
        </div>
    @endif

    <button id="theme-toggle" style="background: transparent; border: none; color: var(--text); font-size: 1.25rem; margin-right: 1rem; cursor: pointer;" aria-label="Toggle theme">
        <span id="theme-icon">☀️</span>
    </button>

    {{-- Expiry warning --}}
    @if(auth()->user()?->expired_at && auth()->user()->expired_at->diffInDays(now()) <= 7 && !auth()->user()->isExpired())
        <span style="font-size:0.75rem; color:var(--warning);">
            <i class="bi bi-exclamation-triangle-fill"></i>
            Akun berakhir {{ auth()->user()->expired_at->diffForHumans() }}
        </span>
    @endif

    <span class="topbar-time" id="topbarClock"></span>
</header>

{{-- ================================================================
     MAIN CONTENT
================================================================ --}}
<main class="admin-body">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" role="alert">
            <i class="bi bi-x-circle-fill"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>{{ session('warning') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error" role="alert">
            <i class="bi bi-x-circle-fill"></i>
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    @yield('content')
</main>

<script>
    // ── Sidebar toggle (mobile) ─────────────────────────────────────────────
    function toggleSidebar() {
        const sidebar  = document.getElementById('sidebar');
        const overlay  = document.getElementById('sidebarOverlay');
        const btn      = document.getElementById('hamburgerBtn');
        const isOpen   = sidebar.classList.contains('open');

        sidebar.classList.toggle('open', !isOpen);
        overlay.classList.toggle('open', !isOpen);
        btn.setAttribute('aria-expanded', String(!isOpen));
    }

    document.getElementById('sidebarOverlay').addEventListener('click', function () {
        document.getElementById('sidebar').classList.remove('open');
        this.classList.remove('open');
        document.getElementById('hamburgerBtn').setAttribute('aria-expanded', 'false');
    });

    // ── Theme Toggle Logic ─────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        
        const currentTheme = document.documentElement.getAttribute('data-theme');
        updateThemeIcon(currentTheme);

        themeToggleBtn.addEventListener('click', () => {
            let theme = document.documentElement.getAttribute('data-theme');
            let newTheme = theme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('kumaw-theme', newTheme);
            updateThemeIcon(newTheme);
        });

        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.textContent = '🌙'; 
            } else {
                themeIcon.textContent = '☀️'; 
            }
        }
    });

    // ── Collapsible nav groups ──────────────────────────────────────────────
    function toggleNavGroup(btn) {
        const subId = btn.id.replace('nav-group-', 'sub-');
        const sub   = document.getElementById(subId);
        if (!sub) return;

        const isOpen = sub.classList.contains('open');
        sub.classList.toggle('open', !isOpen);
        btn.classList.toggle('open', !isOpen);
    }

    // ── Live clock in topbar ────────────────────────────────────────────────
    function updateClock() {
        const el = document.getElementById('topbarClock');
        if (!el) return;
        el.textContent = new Date().toLocaleTimeString('id-ID', {
            hour: '2-digit', minute: '2-digit', second: '2-digit',
            timeZone: 'Asia/Jakarta'
        });
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── Sidebar user popup (opens ABOVE the button) ─────────────────────────
    function toggleUserDropdown() {
        const menu    = document.getElementById('sidebarUserDropdown');
        const chevron = document.getElementById('sidebarUserChevron');
        const isOpen  = menu.style.display === 'block';
        menu.style.display = isOpen ? 'none' : 'block';
        chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
    }

    function openPasswordModal() {
        // Close the user dropdown first
        document.getElementById('sidebarUserDropdown').style.display = 'none';
        document.getElementById('sidebarUserChevron').style.transform = 'rotate(0deg)';
        // Trigger Bootstrap modal
        const modal = document.getElementById('changePasswordModal');
        if (modal) {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
    }

    // Close user dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const btn  = document.getElementById('sidebarUserBtn');
        const menu = document.getElementById('sidebarUserDropdown');
        if (menu && btn && !btn.contains(e.target) && !menu.contains(e.target)) {
            menu.style.display = 'none';
            const chevron = document.getElementById('sidebarUserChevron');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }
    });
</script>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@yield('scripts')
    {{-- Auto-redirect to login before session expires to prevent 419 errors --}}
    <script>
        // ponytail: simple JS timer over complex ping/heartbeat systems
        setTimeout(function() {
            window.location.href = "{{ route('login') }}";
        }, {{ (config('session.lifetime') - 1) * 60 * 1000 }});
    </script>
</body>
</html>
