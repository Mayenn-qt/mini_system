<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Ohaiyo Japan Surplus</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #C8102E;
            --bg-color: #FFFFFF;
            --sec-bg-color: #F5F5F5;
            --border-color: #D9D9D9;
            --text-color: #4A4A4A;
            --accent-color: #1E1E1E;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--sec-bg-color);
            color: var(--text-color);
            margin: 0;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--bg-color);
            border-right: 1px solid var(--border-color);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding-bottom: 20px;
        }

        .sidebar-brand {
            padding: 24px;
            border-bottom: 1px solid var(--sec-bg-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-dot {
            width: 24px;
            height: 24px;
            background-color: var(--primary-color);
            border-radius: 50%;
            display: inline-block;
        }

        .brand-title {
            font-weight: 700;
            color: var(--accent-color);
            font-size: 16px;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 12px;
            margin: 0;
            flex-grow: 1;
            overflow-y: auto;
        }

        .menu-item {
            margin-bottom: 6px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--text-color);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .menu-link:hover {
            background-color: var(--sec-bg-color);
            color: var(--accent-color);
        }

        .menu-link.active {
            background-color: rgba(200, 16, 46, 0.08);
            color: var(--primary-color);
            font-weight: 600;
        }

        .menu-link i {
            font-size: 18px;
        }

        /* Topbar Styling */
        .topbar {
            height: 70px;
            background-color: var(--bg-color);
            border-bottom: 1px solid var(--border-color);
            margin-left: var(--sidebar-width);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        /* Content Styling */
        .content-area {
            margin-left: var(--sidebar-width);
            padding: 30px;
            min-height: calc(100vh - 70px);
        }

        /* Premium Cards */
        .card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background-color: var(--bg-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--sec-bg-color);
            padding: 20px;
            font-weight: 600;
            color: var(--accent-color);
        }

        .card-body {
            padding: 20px;
        }

        /* Premium Table */
        .table-responsive {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: var(--bg-color);
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: var(--sec-bg-color);
            color: var(--accent-color);
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .table td {
            padding: 14px 20px;
            font-size: 14px;
            vertical-align: middle;
            border-bottom: 1px solid var(--sec-bg-color);
        }

        /* Badges & Modals */
        .badge {
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 6px;
        }

        .badge-low-stock {
            background-color: rgba(200, 16, 46, 0.1);
            color: var(--primary-color);
        }

        .modal-content {
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .modal-header {
            border-bottom: 1px solid var(--sec-bg-color);
        }

        .modal-footer {
            border-top: 1px solid var(--sec-bg-color);
        }

        /* Form elements */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 10px 14px;
            font-size: 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.1);
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 20px;
            font-size: 14px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #a00d24;
            border-color: #a00d24;
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }

        /* Utilities */
        .text-accent {
            color: var(--accent-color);
        }

        .text-japanese-red {
            color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .topbar, .content-area {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div>
        <div class="sidebar-brand">
            <span class="brand-dot"></span>
            <span class="brand-title">OHAIYO JAPAN</span>
        </div>
        
        <ul class="sidebar-menu">
            @if(auth()->user()->isOwner())
                <!-- Owner / Admin Menu -->
                <li class="menu-item">
                    <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('products.index') }}" class="menu-link {{ request()->routeIs('products.index') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i> Products
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('categories.index') }}" class="menu-link {{ request()->routeIs('categories.index') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i> Categories
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('inventory.index') }}" class="menu-link {{ request()->routeIs('inventory.index') ? 'active' : '' }}">
                        <i class="bi bi-journal-bookmark"></i> Inventory
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('sales.index') }}" class="menu-link {{ request()->routeIs('sales.index') ? 'active' : '' }}">
                        <i class="bi bi-cart-check"></i> Sales
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('branches.index') }}" class="menu-link {{ request()->routeIs('branches.index') || request()->routeIs('branches.show') ? 'active' : '' }}">
                        <i class="bi bi-shop"></i> Branches
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('users.index') }}" class="menu-link {{ request()->routeIs('users.index') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Users
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('reports.index') }}" class="menu-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart"></i> Reports
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('sms.index') }}" class="menu-link {{ request()->routeIs('sms.index') ? 'active' : '' }}">
                        <i class="bi bi-chat-left-text"></i> SMS Notification
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('settings.index') }}" class="menu-link {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </li>
            @else
                <!-- Staff Menu -->
                <li class="menu-item">
                    <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('staff.products') }}" class="menu-link {{ request()->routeIs('staff.products') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i> Products
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('staff.inventory') }}" class="menu-link {{ request()->routeIs('staff.inventory') ? 'active' : '' }}">
                        <i class="bi bi-journal-bookmark"></i> Inventory
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('pos') }}" class="menu-link {{ request()->routeIs('pos') ? 'active' : '' }}">
                        <i class="bi bi-calculator"></i> POS Recording
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('staff.sales.history') }}" class="menu-link {{ request()->routeIs('staff.sales.history') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i> Sales History
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('reports.index') }}" class="menu-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> Reports
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('profile') }}" class="menu-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                        <i class="bi bi-person-circle"></i> Profile
                    </a>
                </li>
            @endif
        </ul>
    </div>

    <!-- Bottom Sidebar (Logout) -->
    <div class="px-3">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="menu-link border-0 bg-transparent w-100 text-start">
                <i class="bi bi-box-arrow-right text-danger"></i> <span class="text-danger">Logout</span>
            </button>
        </form>
    </div>
</div>

<!-- Top Navigation Bar -->
<div class="topbar">
    <div class="d-flex align-items-center">
        <button class="btn d-lg-none p-0 me-3" id="toggle-sidebar">
            <i class="bi bi-list fs-3"></i>
        </button>
        <span class="fw-semibold text-accent">@yield('header_title')</span>
    </div>
    
    <div class="d-flex align-items-center gap-3">
        @if(auth()->user()->isStaff())
            <span class="badge bg-secondary">{{ auth()->user()->branch->name ?? 'Staff' }}</span>
        @endif
        <div class="text-end">
            <div class="fw-semibold text-accent" style="font-size:14px;">{{ auth()->user()->name }}</div>
            <div class="text-muted" style="font-size:12px; text-transform: capitalize;">{{ auth()->user()->role }}</div>
        </div>
    </div>
</div>

<!-- Scrollable Content Area -->
<div class="content-area">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @yield('content')
</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Responsive Sidebar Toggle
    const toggleBtn = document.getElementById('toggle-sidebar');
    const sidebar = document.getElementById('sidebar');

    if(toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
    }
</script>
@yield('scripts')
</body>
</html>
