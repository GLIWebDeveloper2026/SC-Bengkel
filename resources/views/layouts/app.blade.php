<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bengkel Jaya Motor') — System Operasional</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
            --bg-card-hover: #334155;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent-primary: #3b82f6;
            --accent-success: #10b981;
            --accent-warning: #f59e0b;
            --accent-danger: #ef4444;
            --accent-purple: #8b5cf6;
            --border-color: #334155;
            --glass-bg: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 260px;
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 24px 16px;
            position: fixed;
            height: 100vh;
            z-index: 100;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 24px;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .brand-icon {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .brand-text h2 {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, #60a5fa, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-text p {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .nav-label {
            font-size: 11px;
            text-transform: uppercase;
            color: var(--text-muted);
            font-weight: 600;
            letter-spacing: 1px;
            margin: 16px 12px 8px;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-item a:hover, .nav-item.active a {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border-left: 3px solid #3b82f6;
        }

        .nav-item a i {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        /* Main Content Styles */
        .main-wrapper {
            margin-left: 260px;
            flex: 1;
            padding: 24px 32px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-color);
        }

        .header-title h1 {
            font-size: 24px;
            font-weight: 700;
        }

        .header-title p {
            font-size: 13px;
            color: var(--text-muted);
        }

        .user-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--bg-card);
            padding: 8px 16px;
            border-radius: 30px;
            border: 1px solid var(--border-color);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #fff;
            font-size: 14px;
        }

        /* UI Cards & Glass Grid */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .alert-success { background: rgba(16, 185, 129, 0.15); border: 1px solid var(--accent-success); color: #34d399; }
        .alert-warning { background: rgba(245, 158, 11, 0.15); border: 1px solid var(--accent-warning); color: #fbbf24; }
        .alert-danger  { background: rgba(239, 68, 68, 0.15); border: 1px solid var(--accent-danger); color: #f87171; }
        .alert-info    { background: rgba(59, 130, 246, 0.15); border: 1px solid var(--accent-primary); color: #60a5fa; }

        /* Tables & Forms */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th, td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }

        tbody tr:hover {
            background-color: var(--bg-card-hover);
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-queue { background: rgba(148, 163, 184, 0.2); color: #cbd5e1; }
        .badge-diagnosing { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
        .badge-waiting_approval { background: rgba(239, 68, 68, 0.2); color: #f87171; }
        .badge-working { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
        .badge-completed { background: rgba(16, 185, 129, 0.2); color: #34d399; }
        .badge-paid { background: rgba(16, 185, 129, 0.25); color: #10b981; border: 1px solid #10b981; }
        .badge-unpaid { background: rgba(239, 68, 68, 0.25); color: #ef4444; border: 1px solid #ef4444; }
        .badge-partially_paid { background: rgba(245, 158, 11, 0.25); color: #f59e0b; border: 1px solid #f59e0b; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-primary { background: #3b82f6; color: #fff; }
        .btn-primary:hover { background: #2563eb; transform: translateY(-1px); }
        .btn-success { background: #10b981; color: #fff; }
        .btn-success:hover { background: #059669; }
        .btn-warning { background: #f59e0b; color: #1e293b; }
        .btn-warning:hover { background: #d97706; }
        .btn-danger  { background: #ef4444; color: #fff; }
        .btn-danger:hover  { background: #dc2626; }
        .btn-secondary { background: #475569; color: #fff; }
        .btn-secondary:hover { background: #334155; }

        .form-group {
            margin-bottom: 16px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .form-control, select, textarea {
            width: 100%;
            padding: 10px 14px;
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-main);
            font-size: 14px;
            outline: none;
        }

        .form-control:focus, select:focus, textarea:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }

        @media (max-width: 1024px) {
            .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
            .sidebar { width: 80px; padding: 16px 8px; }
            .brand-text, .nav-label, .nav-item span { display: none; }
            .main-wrapper { margin-left: 80px; padding: 16px; }
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon"><i class="fa-solid fa-wrench"></i></div>
            <div class="brand-text">
                <h2>JAYA MOTOR</h2>
                <p>Servis & Sparepart</p>
            </div>
        </div>

        <span class="nav-label">Operasional</span>
        <ul class="nav-menu">
            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}"><i class="fa-solid fa-chart-line"></i> <span>Dashboard</span></a>
            </li>
            <li class="nav-item {{ request()->routeIs('work-orders.*') ? 'active' : '' }}">
                <a href="{{ route('work-orders.index') }}"><i class="fa-solid fa-clipboard-list"></i> <span>Work Orders</span></a>
            </li>
            @if(in_array(auth()->user()->role ?? '', ['owner', 'cashier']))
                <li class="nav-item {{ request()->routeIs('payments.bulk') ? 'active' : '' }}">
                    <a href="{{ route('payments.bulk') }}"><i class="fa-solid fa-money-check-dollar"></i> <span>Bulk Payment (Rental)</span></a>
                </li>
                <li class="nav-item {{ request()->routeIs('warranty.index') ? 'active' : '' }}">
                    <a href="{{ route('warranty.index') }}"><i class="fa-solid fa-shield-halved"></i> <span>Garansi 14 Hari</span></a>
                </li>
            @endif
        </ul>

        @if((auth()->user()->role ?? '') === 'owner')
            <span class="nav-label">Laporan Owner</span>
            <ul class="nav-menu">
                <li class="nav-item {{ request()->routeIs('reports.commissions') ? 'active' : '' }}">
                    <a href="{{ route('reports.commissions') }}"><i class="fa-solid fa-users-gear"></i> <span>Komisi Mekanik</span></a>
                </li>
                <li class="nav-item {{ request()->routeIs('reports.profit-loss') ? 'active' : '' }}">
                    <a href="{{ route('reports.profit-loss') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span>Laba & Rugi</span></a>
                </li>
                <li class="nav-item {{ request()->routeIs('reports.scrap') ? 'active' : '' }}">
                    <a href="{{ route('reports.scrap') }}"><i class="fa-solid fa-recycle"></i> <span>Aki Bekas (Scrap)</span></a>
                </li>
            </ul>
        @endif
    </aside>

    <!-- Main Content Wrapper -->
    <main class="main-wrapper">
        <header class="header">
            <div class="header-title">
                <h1>@yield('title', 'Dashboard')</h1>
                <p>@yield('subtitle', 'Sistem Manajemen Bengkel & Kasir Terintegrasi')</p>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <div class="user-badge">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size: 13px; font-weight: 600;">{{ auth()->user()->name ?? 'User' }}</div>
                        <div style="font-size: 11px;">
                            @if((auth()->user()->role ?? '') === 'owner')
                                <span class="badge" style="background: rgba(139, 92, 246, 0.2); color: #c084fc; border: 1px solid #8b5cf6;">Owner</span>
                            @elseif((auth()->user()->role ?? '') === 'cashier')
                                <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid #10b981;">Kasir</span>
                            @elseif((auth()->user()->role ?? '') === 'mechanic')
                                <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid #f59e0b;">Mekanik</span>
                            @endif
                        </div>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="padding: 8px 14px; font-size: 13px;" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </header>

        @if(session('success'))
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('warning') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i> {{ session('info') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

</body>
</html>
