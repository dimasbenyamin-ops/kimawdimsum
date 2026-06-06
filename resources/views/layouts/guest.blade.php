<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kumaw Dimsum - Masuk ke akun Anda">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Kumaw Dimsum')</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🥟</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0a0a1a;
            --bg2:       #12122a;
            --gold:      #f59e0b;
            --gold-light:#fcd34d;
            --text:      #e2e8f0;
            --muted:     #94a3b8;
            --border:    rgba(255,255,255,0.08);
            --glass:     rgba(255,255,255,0.05);
            --radius:    14px;
            --error:     #f87171;
            --success:   #34d399;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(245,158,11,0.06) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(99,102,241,0.06) 0%, transparent 60%);
        }

        .auth-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, var(--gold), #d97706);
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 0 40px rgba(245,158,11,0.3);
        }

        .brand h1 {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand p { color: var(--muted); font-size: 0.875rem; margin-top: 0.25rem; }

        .card {
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 2rem;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .form-group { margin-bottom: 1.25rem; }

        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: 0.5rem;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"],
        input[type="tel"] {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-family: inherit;
            font-size: 0.9375rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(245,158,11,0.12);
        }

        input.is-invalid { border-color: var(--error); }

        .invalid-feedback {
            color: var(--error);
            font-size: 0.8125rem;
            margin-top: 0.375rem;
        }

        .alert-success {
            background: rgba(52,211,153,0.1);
            border: 1px solid rgba(52,211,153,0.3);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: var(--success);
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
        }

        .alert-error {
            background: rgba(248,113,113,0.1);
            border: 1px solid rgba(248,113,113,0.3);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: var(--error);
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .checkbox-row input[type="checkbox"] { accent-color: var(--gold); width: 16px; height: 16px; }
        .checkbox-row label { margin: 0; color: var(--muted); cursor: pointer; }

        .btn-primary {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, var(--gold), #d97706);
            border: none;
            border-radius: 10px;
            color: #1a0a00;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
        }

        .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-primary:active { transform: translateY(0); }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            color: var(--muted);
            font-size: 0.875rem;
            position: relative;
        }

        .divider::before, .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: var(--border);
        }

        .divider::before { left: 0; }
        .divider::after  { right: 0; }

        .link-row {
            text-align: center;
            font-size: 0.875rem;
            color: var(--muted);
        }

        .link-row a {
            color: var(--gold);
            text-decoration: none;
            font-weight: 500;
        }

        .link-row a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="brand">
            <img src="{{ asset('images/dimsum-logo.png') }}" alt="Kumaw Dimsum Logo" width="72" height="72" class="mb-4" style="border-radius: 18px; box-shadow: 0 0 40px rgba(245,158,11,0.3); object-fit: cover;">
            <h1>Kumaw Dimsum</h1>
            <p>Authentic Dimsum Experience</p>
        </div>

        <div class="card">
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->has('username'))
                <div class="alert-error">{{ $errors->first('username') }}</div>
            @endif

            @if(session('error'))
                <div class="alert-error">{{ session('error') }}</div>
            @endif

            @yield('content')
        </div>
    </div>
</body>
</html>
