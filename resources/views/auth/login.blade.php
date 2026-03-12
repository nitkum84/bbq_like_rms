<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - {{ \App\Models\WebsiteSetting::get('restaurant_name', config('app.name')) }}</title>

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

        .login-showcase::before,
        .login-showcase::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            filter: blur(8px);
            opacity: 0.28;
        }

        .login-showcase::before {
            width: 260px;
            height: 260px;
            right: -60px;
            top: -40px;
            background: linear-gradient(135deg, var(--accent), transparent);
        }

        .login-showcase::after {
            width: 340px;
            height: 340px;
            left: -80px;
            bottom: -100px;
            background: linear-gradient(135deg, rgba(192, 57, 43, 0.85), transparent);
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

        .brand-chip small {
            display: block;
            font-size: 0.74rem;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: rgba(255, 247, 241, 0.66);
        }

        .brand-chip strong {
            display: block;
            font-size: 1rem;
            font-weight: 800;
            color: #fff7f1;
        }

        .showcase-copy {
            position: relative;
            z-index: 1;
            max-width: 560px;
            margin: 52px 0;
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

        .showcase-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            max-width: 620px;
        }

        .showcase-card {
            padding: 18px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
        }

        .showcase-card span {
            display: block;
            font-size: 0.78rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(255, 247, 241, 0.62);
            margin-bottom: 8px;
        }

        .showcase-card strong {
            display: block;
            font-size: 1.55rem;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .showcase-card p {
            margin: 0;
            font-size: 0.9rem;
            color: rgba(255, 247, 241, 0.74);
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
            transition: 0.2s ease;
        }

        .field input:focus {
            outline: none;
            border-color: rgba(192, 57, 43, 0.38);
            box-shadow: 0 0 0 4px rgba(192, 57, 43, 0.10);
        }

        .login-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--auth-muted);
            font-size: 0.9rem;
        }

        .remember input {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }

        .login-link {
            color: var(--primary);
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: none;
        }

        .login-link:hover {
            color: var(--primary-dark);
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
            box-shadow: 0 18px 36px rgba(192, 57, 43, 0.22);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .login-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 22px 42px rgba(192, 57, 43, 0.26);
        }

        .login-note {
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid var(--auth-line);
            font-size: 0.86rem;
            color: var(--auth-muted);
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
            border: 1px solid rgba(39, 174, 96, 0.14);
        }

        .login-errors {
            background: rgba(192, 57, 43, 0.08);
            color: #a12c21;
            border: 1px solid rgba(192, 57, 43, 0.12);
        }

        .login-errors ul {
            margin: 0;
            padding-left: 18px;
        }

        @media (max-width: 1080px) {
            .login-stage {
                grid-template-columns: 1fr;
            }

            .login-showcase {
                min-height: auto;
                padding-bottom: 34px;
            }

            .login-panel {
                padding-top: 0;
            }
        }

        @media (max-width: 640px) {
            .login-showcase {
                padding: 30px 20px 26px;
            }

            .login-panel {
                padding: 18px;
            }

            .login-card {
                padding: 24px 20px;
                border-radius: 24px;
            }

            .showcase-grid {
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
                <h1>Table operations, guest flow, and promotions in one place.</h1>
                <p>Use the control center built for your restaurant team to manage bookings, menu offers, customer communication, and day-to-day service decisions with less friction.</p>
            </div>

            <div class="showcase-grid">
                <div class="showcase-card">
                    <span>Bookings</span>
                    <strong>Daily</strong>
                    <p>Track reservations, guest mix, slot usage, and table readiness.</p>
                </div>
                <div class="showcase-card">
                    <span>Engagement</span>
                    <strong>Direct</strong>
                    <p>Handle enquiries, vouchers, offers, and campaign messages from one backend.</p>
                </div>
                <div class="showcase-card">
                    <span>Control</span>
                    <strong>Admin</strong>
                    <p>Access settings, activity logs, system logs, and business content securely.</p>
                </div>
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

                <form method="POST" action="{{ route('login') }}" class="login-form">
                    @csrf

                    <div class="field">
                        <label for="email">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="admin@restaurant.com">
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                    </div>

                    <div class="login-row">
                        <label for="remember_me" class="remember">
                            <input id="remember_me" type="checkbox" name="remember">
                            <span>Keep me signed in</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="login-link" href="{{ route('password.request') }}">Forgot password?</a>
                        @endif
                    </div>

                    <button type="submit" class="login-submit">Log In</button>
                </form>

                <div class="login-note">
                    Authorized restaurant staff only. Your activity can be monitored through admin audit and system logs.
                </div>
            </div>
        </section>
    </div>
</body>
</html>
