<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Login - {{ \App\Models\WebsiteSetting::get('restaurant_name', config('app.name')) }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/front.css'])
    <style>
        .front-login-page {
            min-height: 100vh;
            margin: 0;
            font-family: "Manrope", sans-serif;
            color: var(--front-ink);
            background:
                radial-gradient(circle at top left, rgba(244, 199, 102, 0.22), transparent 24%),
                radial-gradient(circle at bottom right, rgba(217, 74, 39, 0.14), transparent 26%),
                linear-gradient(180deg, #fffdf8 0%, #f7f3ec 100%);
        }

        .front-login-shell {
            width: min(calc(100% - 2rem), 1120px);
            margin: 0 auto;
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(340px, 430px);
            gap: 1.5rem;
            align-items: center;
            padding: 2rem 0;
        }

        .front-login-showcase,
        .front-login-card {
            padding: 2rem;
            border: 1px solid rgba(17, 17, 17, 0.08);
            border-radius: 32px;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 24px 70px rgba(17, 17, 17, 0.08);
        }

        .front-login-showcase {
            background: linear-gradient(145deg, #fff7ea, #ffffff 55%, #fff0e4);
        }

        .front-login-showcase h1,
        .front-login-card h2 {
            margin: 0;
            font-family: "Bricolage Grotesque", sans-serif;
            line-height: 0.98;
        }

        .front-login-showcase h1 {
            font-size: clamp(2.8rem, 5vw, 4.6rem);
            max-width: 10ch;
        }

        .front-login-showcase p,
        .front-login-card p,
        .front-login-note {
            color: var(--front-muted);
            line-height: 1.75;
        }

        .front-login-points {
            display: grid;
            gap: 0.9rem;
            margin-top: 1.6rem;
        }

        .front-login-points article {
            padding: 1rem 1.1rem;
            border-radius: 20px;
            background: #fff;
            border: 1px solid rgba(17, 17, 17, 0.06);
        }

        .front-login-points strong {
            display: block;
            margin-bottom: 0.3rem;
            font-family: "Bricolage Grotesque", sans-serif;
        }

        .front-login-form {
            display: grid;
            gap: 1rem;
        }

        .front-login-field {
            display: grid;
            gap: 0.4rem;
        }

        .front-login-field span {
            font-size: 0.82rem;
            font-weight: 800;
        }

        .front-login-field input {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 1px solid rgba(17, 17, 17, 0.12);
            border-radius: 16px;
        }

        .front-login-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .front-login-remember {
            display: inline-flex;
            gap: 0.55rem;
            align-items: center;
            color: var(--front-muted);
        }

        .front-login-link {
            color: var(--front-accent-dark);
            font-weight: 800;
        }

        .front-login-alert,
        .front-login-errors {
            margin-bottom: 1rem;
            padding: 0.9rem 1rem;
            border-radius: 16px;
        }

        .front-login-alert {
            background: rgba(33, 83, 67, 0.12);
            color: var(--front-forest);
        }

        .front-login-errors {
            background: rgba(217, 74, 39, 0.10);
            color: #a03018;
        }

        .front-login-errors ul {
            margin: 0;
            padding-left: 1rem;
        }

        .front-login-actions {
            display: grid;
            gap: 0.8rem;
            margin-top: 0.3rem;
        }

        .front-login-note {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(17, 17, 17, 0.08);
        }

        @media (max-width: 900px) {
            .front-login-shell {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="front-login-page">
    <div class="front-login-shell">
        <section class="front-login-showcase">
            <p class="section-kicker">Front User Access</p>
            <h1>Login to your dining dashboard.</h1>
            <p>Use your customer account to review bookings, confirm OTP, and manage your front-user experience. Admin access is separate.</p>
            <div class="front-login-points">
                <article>
                    <strong>User dashboard only</strong>
                    <p>This login is for guests and registered front users.</p>
                </article>
                <article>
                    <strong>OTP on login</strong>
                    <p>Every fresh user login asks for OTP before dashboard access is unlocked.</p>
                </article>
                <article>
                    <strong>Need admin access?</strong>
                    <p><a class="front-login-link" href="{{ route('admin.login') }}">Go to admin login</a></p>
                </article>
            </div>
        </section>

        <section class="front-login-card">
            <p class="section-kicker">User Login</p>
            <h2>Sign in</h2>
            <p>Continue to your front user dashboard.</p>

            @if (session('status'))
                <div class="front-login-alert">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="front-login-errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="front-login-form">
                @csrf

                <label class="front-login-field">
                    <span>Email Address</span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com">
                </label>

                <label class="front-login-field">
                    <span>Password</span>
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
                </label>

                <div class="front-login-row">
                    <label class="front-login-remember" for="remember_me">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>Keep me signed in</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="front-login-link" href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                </div>

                <div class="front-login-actions">
                    <button type="submit" class="button button--solid">Login to Dashboard</button>
                    <a class="button button--ghost-dark" href="{{ route('register') }}">Create Account</a>
                </div>
            </form>

            <div class="front-login-note">
                Admin users should use <a class="front-login-link" href="{{ route('admin.login') }}">/admin-login</a>.
            </div>
        </section>
    </div>
</body>
</html>
