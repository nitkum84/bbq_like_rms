<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Login - {{ \App\Models\WebsiteSetting::get('restaurant_name', config('app.name')) }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('admin/css/admin.css') }}" rel="stylesheet">

    <style>
        :root {
            --auth-ink: #1f252b;
            --auth-muted: #63707c;
            --auth-cream: #f6efe8;
            --auth-panel: rgba(255, 255, 255, 0.92);
            --auth-line: rgba(44, 62, 80, 0.10);
            --auth-shadow: 0 24px 80px rgba(36, 28, 21, 0.18);
        }

        * { box-sizing: border-box; }

        body.auth-login-page {
            margin: 0;
            min-height: 100vh;
            font-family: 'Manrope', sans-serif;
            color: var(--auth-ink);
            background:
                radial-gradient(circle at top left, rgba(230, 126, 34, 0.22), transparent 28%),
                radial-gradient(circle at bottom left, rgba(192, 57, 43, 0.18), transparent 24%),
                linear-gradient(135deg, #fcf8f3 0%, #f6efe8 44%, #fffdf9 100%);
        }

        .login-stage {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(320px, 1.1fr) minmax(380px, 0.9fr);
        }

        .login-showcase {
            position: relative;
            overflow: hidden;
            padding: 56px clamp(28px, 4vw, 72px);
            background:
                linear-gradient(180deg, rgba(44, 62, 80, 0.96), rgba(29, 41, 53, 0.96)),
                linear-gradient(135deg, rgba(230, 126, 34, 0.18), transparent 60%);
            color: #fff7f1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-chip {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(10px);
        }

        .brand-chip img,
        .brand-badge {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .brand-badge {
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            font-size: 1.4rem;
            box-shadow: 0 14px 34px rgba(192, 57, 43, 0.28);
        }

        .showcase-copy h1 {
            margin: 0 0 18px;
            font-family: 'Fraunces', serif;
            font-size: clamp(2.8rem, 5vw, 4.7rem);
            line-height: 0.96;
            letter-spacing: -0.04em;
        }

        .showcase-copy p {
            margin: 0;
            max-width: 470px;
            font-size: 1.05rem;
            line-height: 1.8;
            color: rgba(255, 247, 241, 0.76);
        }

        .login-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
        }

        .login-card {
            width: min(100%, 480px);
            background: var(--auth-panel);
            border: 1px solid rgba(255, 255, 255, 0.65);
            box-shadow: var(--auth-shadow);
            border-radius: 30px;
            padding: 34px;
            backdrop-filter: blur(14px);
        }

        .login-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(192, 57, 43, 0.08);
            color: var(--primary);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .login-card h2 {
            margin: 0 0 10px;
            font-family: 'Fraunces', serif;
            font-size: 2.25rem;
            letter-spacing: -0.03em;
        }

        .login-card > p {
            margin: 0 0 26px;
            color: var(--auth-muted);
            line-height: 1.7;
        }

        .login-form {
            display: grid;
            gap: 18px;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--auth-ink);
        }

        .field input {
            width: 100%;
            border: 1px solid var(--auth-line);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.92);
            padding: 15px 16px;
            font: inherit;
            color: var(--auth-ink);
        }

        .login-submit {
            width: 100%;
            border: none;
            border-radius: 18px;
            padding: 15px 18px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            font-size: 0.96rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            cursor: pointer;
        }

        .login-alert,
        .login-errors {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 16px;
            font-size: 0.9rem;
        }

        .login-alert {
            background: rgba(39, 174, 96, 0.10);
            color: #1f7a46;
        }

        .login-errors {
            background: rgba(192, 57, 43, 0.08);
            color: #a12c21;
        }

        .login-errors ul {
            margin: 0;
            padding-left: 18px;
        }

        @media (max-width: 1080px) {
            .login-stage {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="auth-login-page">
    <div class="login-stage">
        <section class="login-showcase">
            <div class="brand-chip">
                @if(\App\Models\WebsiteSetting::get('logo'))
                    <img src="{{ asset('storage/'.\App\Models\WebsiteSetting::get('logo')) }}" alt="Logo">
                @else
                    <div class="brand-badge"><i class="bi bi-shop"></i></div>
                @endif
                <div>
                    <small>Restaurant Booking System</small>
                    <strong>{{ \App\Models\WebsiteSetting::get('restaurant_name', config('app.name')) }}</strong>
                </div>
            </div>

            <div class="showcase-copy">
                <h1>Admin control for restaurant operations.</h1>
                <p>Use this page only for restaurant staff and super-admin users. Front customer login is separate.</p>
            </div>
        </section>

        <section class="login-panel">
            <div class="login-card">
                <div class="login-kicker">Admin Access</div>
                <h2>Sign in</h2>
                <p>Continue to the restaurant management dashboard.</p>

                @if (session('status'))
                    <div class="login-alert">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="login-errors">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.store') }}" class="login-form">
                    @csrf

                    <div class="field">
                        <label for="email">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="admin@restaurant.com">
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                    </div>

                    <button type="submit" class="login-submit">Admin Login</button>
                </form>
            </div>
        </section>
    </div>
</body>
</html>
