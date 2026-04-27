<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'KantinKu') }} - Smart Canteen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; min-height: 100vh; }
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background: #0f172a; color: white; padding: 1.5rem; z-index: 50; display: flex; flex-direction: column; transition: transform 0.3s; }
        .sidebar-brand { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 2.5rem; padding: 0.5rem; }
        .sidebar-brand-icon { width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, #6366f1, #8b5cf6); display: flex; align-items: center; justify-content: center; }
        .sidebar-brand span { font-weight: 800; font-size: 1.35rem; letter-spacing: -0.03em; }
        .sidebar-nav { display: flex; flex-direction: column; gap: 0.25rem; flex: 1; }
        .sidebar-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: 10px; color: #94a3b8; font-weight: 500; font-size: 0.9rem; text-decoration: none; transition: all 0.2s; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(99, 102, 241, 0.15); color: white; }
        .sidebar-link.active { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3); }
        .sidebar-section { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; color: #475569; margin: 1.5rem 0 0.5rem 1rem; font-weight: 700; }
        .main-content { margin-left: 260px; min-height: 100vh; }
        .topbar { background: white; border-bottom: 1px solid #e2e8f0; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 40; }
        .topbar-left { display: flex; align-items: center; gap: 1rem; }
        .topbar-left h2 { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
        .topbar-right { display: flex; align-items: center; gap: 1rem; }
        .topbar-user { display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 1rem; border-radius: 12px; background: #f8fafc; cursor: pointer; border: none; position: relative; }
        .topbar-avatar { width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; }
        .page-content { padding: 2rem; }
        .card { background: white; border-radius: 16px; padding: 1.5rem; border: 1px solid #e2e8f0; }
        .card-premium { background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .stat-card { display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: white; border-radius: 16px; border: 1px solid #e2e8f0; }
        .stat-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .stat-label { font-size: 0.8rem; color: #64748b; font-weight: 500; margin-bottom: 0.25rem; }
        .stat-value { font-size: 1.35rem; font-weight: 800; color: #1e293b; }
        .balance-card { background: linear-gradient(135deg, #6366f1, #8b5cf6, #a78bfa); border-radius: 20px; padding: 2rem; color: white; position: relative; overflow: hidden; }
        .balance-card::before { content: ''; position: absolute; top: -50%; right: -30%; width: 300px; height: 300px; border-radius: 50%; background: rgba(255,255,255,0.08); }
        .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; border-radius: 10px; font-weight: 600; font-size: 0.875rem; cursor: pointer; border: none; transition: all 0.2s; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4); }
        .btn-success { background: #10b981; color: white; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-outline { background: white; color: #1e293b; border: 1px solid #e2e8f0; }
        .badge { display: inline-flex; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-info { background: #e0e7ff; color: #4f46e5; }
        .table-container { 
            background: white; 
            border-radius: 16px; 
            border: 1px solid #e2e8f0; 
            overflow-x: auto; 
            -webkit-overflow-scrolling: touch;
        }
        .table { width: 100%; border-collapse: collapse; min-width: 600px; }
        .table th { background: #f8fafc; padding: 0.85rem 1.25rem; text-align: left; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; }
        .table td { padding: 0.85rem 1.25rem; border-top: 1px solid #f1f5f9; font-size: 0.875rem; color: #334155; }
        .table tr:hover td { background: #f8fafc; }
        .grid { display: grid; gap: 1.5rem; }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }
        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 0.35rem; }
        .form-input { width: 100%; padding: 0.65rem 1rem; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.875rem; font-family: inherit; transition: border-color 0.2s; outline: none; }
        .form-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
        .form-select { width: 100%; padding: 0.65rem 1rem; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.875rem; font-family: inherit; background: white; }
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 100; display: none; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal { background: white; border-radius: 20px; padding: 2rem; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; }
        .modal h3 { font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem; }
        .alert { padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1rem; font-size: 0.875rem; font-weight: 500; }
        .alert-success { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .mobile-toggle { display: none; background: none; border: none; cursor: pointer; color: #1e293b; }
        .dropdown { position: relative; }
        .dropdown-menu { position: absolute; right: 0; top: 100%; margin-top: 0.5rem; background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; min-width: 200px; padding: 0.5rem; display: none; z-index: 50; }
        .dropdown-menu.active { display: block; }
        .dropdown-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 1rem; border-radius: 8px; font-size: 0.875rem; color: #334155; text-decoration: none; cursor: pointer; border: none; background: none; width: 100%; font-family: inherit; }
        .dropdown-item:hover { background: #f1f5f9; }
        .empty-state { text-align: center; padding: 3rem; color: #94a3b8; }
        .empty-state i { margin-bottom: 1rem; opacity: 0.3; }
        @media (max-width: 768px) {
            .sidebar { 
                transform: translateX(-100%); 
                width: 280px;
                box-shadow: 20px 0 50px rgba(0,0,0,0.2);
            }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .topbar { padding: 1rem; }
            .mobile-toggle { display: block; }
            .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; gap: 1rem; }
            .page-content { padding: 1rem; }
            .modal { padding: 1.5rem; width: 95%; }
            .topbar-user span { display: none; }
            .topbar-left h2 { font-size: 1rem; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon"><i data-lucide="utensils" size="22" color="white"></i></div>
            <span>KantinKu</span>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" size="20"></i> Dashboard
            </a>

            @if(Auth::user()->isAdmin())
                <div class="sidebar-section">Manajemen</div>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i data-lucide="users" size="20"></i> Pengguna
                </a>
                <a href="{{ route('admin.rfid.index') }}" class="sidebar-link {{ request()->routeIs('admin.rfid.*') ? 'active' : '' }}">
                    <i data-lucide="credit-card" size="20"></i> Kartu RFID
                </a>
                <a href="{{ route('admin.topup.index') }}" class="sidebar-link {{ request()->routeIs('admin.topup.*') ? 'active' : '' }}">
                    <i data-lucide="wallet" size="20"></i> Top-Up Saldo
                </a>
                <a href="{{ route('canteen.cashier.index') }}" class="sidebar-link {{ request()->routeIs('canteen.cashier.*') ? 'active' : '' }}">
                    <i data-lucide="shopping-cart" size="20"></i> Kasir POS
                </a>
                <a href="{{ route('canteen.menu.index') }}" class="sidebar-link {{ request()->routeIs('canteen.menu.*') ? 'active' : '' }}">
                    <i data-lucide="utensils-crossed" size="20"></i> Menu Kantin
                </a>
                <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i data-lucide="settings-2" size="20"></i> Pengaturan Sistem
                </a>
            @elseif(Auth::user()->isCanteen())
                <div class="sidebar-section">Kantin</div>
                <a href="{{ route('canteen.cashier.index') }}" class="sidebar-link {{ request()->routeIs('canteen.cashier.*') ? 'active' : '' }}">
                    <i data-lucide="shopping-cart" size="20"></i> Kasir POS
                </a>
                <a href="{{ route('canteen.menu.index') }}" class="sidebar-link {{ request()->routeIs('canteen.menu.*') ? 'active' : '' }}">
                    <i data-lucide="utensils-crossed" size="20"></i> Menu Kantin
                </a>
            @else
                <div class="sidebar-section">Siswa</div>
                <a href="{{ route('student.topup.index') }}" class="sidebar-link {{ request()->routeIs('student.topup.*') ? 'active' : '' }}">
                    <i data-lucide="wallet" size="20"></i> Riwayat Top-Up
                </a>
            @endif

            <a href="{{ route('orders.index') }}" class="sidebar-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <i data-lucide="receipt" size="20"></i> Riwayat Order
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem; margin-top: 1rem;">
            <a href="{{ route('profile.edit') }}" class="sidebar-link">
                <i data-lucide="settings" size="20"></i> Pengaturan
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <button class="mobile-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
                    <i data-lucide="menu" size="24"></i>
                </button>
                <h2>@yield('page-title', 'Dashboard')</h2>
            </div>
            <div class="topbar-right">
                <div class="dropdown">
                    <button class="topbar-user" onclick="this.nextElementSibling.classList.toggle('active')">
                        <div class="topbar-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                        <div style="text-align: left;">
                            <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b;">{{ Auth::user()->name }}</div>
                            <div style="font-size: 0.7rem; color: #94a3b8; text-transform: capitalize;">{{ Auth::user()->role }}</div>
                        </div>
                        <i data-lucide="chevron-down" size="16" style="color: #94a3b8;"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="{{ route('profile.edit') }}" class="dropdown-item"><i data-lucide="user" size="16"></i> Profil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item" style="color: #ef4444;"><i data-lucide="log-out" size="16"></i> Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success">✅ {{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        <div>❌ {{ $error }}</div>
                    @endforeach
                </div>
            @endif
            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </div>

    <script>
        lucide.createIcons();
        // Close dropdown on click outside
        document.addEventListener('click', function(e) {
            document.querySelectorAll('.dropdown-menu.active').forEach(menu => {
                if (!menu.parentElement.contains(e.target)) menu.classList.remove('active');
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
