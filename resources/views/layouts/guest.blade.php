<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'KantinKu') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            position: relative;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 2rem 0;
        }

        /* ===== Animated Background ===== */
        body::before {
            content: '';
            position: fixed;
            top: -40%;
            left: -20%;
            width: 700px;
            height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.25), transparent 70%);
            filter: blur(80px);
            animation: orb1 12s ease-in-out infinite;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -30%;
            right: -15%;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.2), transparent 70%);
            filter: blur(80px);
            animation: orb2 10s ease-in-out infinite;
        }
        @keyframes orb1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(60px, 40px) scale(1.1); }
        }
        @keyframes orb2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-50px, -30px) scale(1.05); }
        }

        /* Extra glow orb */
        .orb-extra {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.1), transparent 70%);
            filter: blur(100px);
            pointer-events: none;
        }

        /* ===== Grid Pattern Overlay ===== */
        .grid-pattern {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
        }

        /* ===== Auth Card ===== */
        .auth-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 1.5rem;
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-brand-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-bottom: 1rem;
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.4);
        }
        .auth-brand h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            letter-spacing: -0.03em;
        }
        .auth-brand h1 span {
            background: linear-gradient(135deg, #a5b4fc, #c4b5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            box-shadow:
                0 25px 60px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .auth-card-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-card-header h2 {
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.4rem;
        }
        .auth-card-header p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.875rem;
        }

        /* ===== Form ===== */
        .auth-form .field {
            margin-bottom: 1.15rem;
        }
        .auth-form label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0.4rem;
            letter-spacing: 0.03em;
        }
        .auth-form label .req { color: #f87171; }

        .auth-form .input-wrap {
            position: relative !important;
            display: block !important;
        }
        .auth-form .input-wrap svg,
        .auth-form .input-wrap i {
            position: absolute !important;
            left: 0.9rem !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            color: rgba(255, 255, 255, 0.3) !important;
            pointer-events: none !important;
            transition: color 0.2s !important;
            z-index: 2 !important;
            width: 17px !important;
            height: 17px !important;
        }
        .auth-form input[type="text"],
        .auth-form input[type="email"],
        .auth-form input[type="password"] {
            width: 100% !important;
            padding: 0.75rem 1rem 0.75rem 2.75rem !important;
            border: 1.5px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 12px !important;
            font-size: 0.9rem !important;
            font-family: inherit !important;
            color: white !important;
            outline: none !important;
            transition: all 0.25s !important;
            background: rgba(255, 255, 255, 0.06) !important;
            position: relative !important;
            z-index: 1 !important;
        }
        .auth-form input:focus {
            border-color: #6366f1 !important;
            background: rgba(99, 102, 241, 0.08) !important;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15) !important;
        }
        .auth-form .input-wrap:focus-within i {
            color: #a5b4fc;
        }
        .auth-form input::placeholder {
            color: rgba(255, 255, 255, 0.25);
        }

        .auth-form .field-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .auth-form .remember {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        .auth-form .remember input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #6366f1;
            cursor: pointer;
        }
        .auth-form .remember span {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.5);
            font-weight: 500;
        }
        .auth-form .forgot {
            font-size: 0.82rem;
            color: #a5b4fc;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        .auth-form .forgot:hover { color: #c4b5fd; }

        .btn-auth {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
        }
        .btn-auth::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: left 0.5s;
        }
        .btn-auth:hover::before { left: 100%; }
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.5);
        }
        .btn-auth:active { transform: translateY(0); }

        .auth-divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }
        .auth-divider span {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.35);
            font-weight: 500;
        }

        .auth-footer {
            text-align: center;
        }
        .auth-footer p {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.45);
        }
        .auth-footer a {
            color: #a5b4fc;
            font-weight: 700;
            text-decoration: none;
            transition: color 0.2s;
        }
        .auth-footer a:hover { color: #c4b5fd; }

        .input-error {
            font-size: 0.75rem;
            color: #f87171;
            margin-top: 0.3rem;
            font-weight: 500;
        }
        .session-status {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 1.25rem;
            border: 1px solid rgba(16, 185, 129, 0.2);
            text-align: center;
        }

        .auth-back {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 600;
            transition: color 0.2s;
            margin-bottom: 0.5rem;
        }
        .auth-back:hover { color: #a5b4fc; }

        @media (max-width: 500px) {
            .auth-card { padding: 2rem 1.5rem; border-radius: 20px; }
            .auth-container { padding: 1rem; }
        }
    </style>
</head>
<body>
    <div class="grid-pattern"></div>
    <div class="orb-extra"></div>

    <div class="auth-container">
        <!-- Brand -->
        <div class="auth-brand">
            <a href="/" style="text-decoration: none;">
                <div class="auth-brand-icon"><i data-lucide="utensils" size="28"></i></div>
                <h1>Kantin<span>Ku</span></h1>
            </a>
        </div>

        <!-- Card -->
        <div class="auth-card">
            {{ $slot }}
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
