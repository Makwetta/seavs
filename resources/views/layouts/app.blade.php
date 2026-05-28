<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>IHET – @yield('title', 'Smart Attendance System')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:       #1a3a5c;
            --primary-light: #2563a8;
            --accent:        #e8a020;
            --accent-light:  #fbbf24;
            --success:       #16a34a;
            --danger:        #dc2626;
            --sidebar-w:     260px;
            --topbar-h:      64px;
            --bg:            #f0f4f8;
            --card:          #ffffff;
            --text:          #1e293b;
            --muted:         #64748b;
            --border:        #e2e8f0;
        }
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
        }

        /* ── SIDEBAR ── */
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--primary);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: transform .25s ease;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .sidebar-brand .brand-logo {
            width: 44px; height: 44px;
            background: var(--accent);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: #fff; font-weight: 700;
            margin-bottom: 10px;
        }
        .sidebar-brand h5 {
            color: #fff; font-family: 'Space Grotesk', sans-serif;
            font-size: .85rem; font-weight: 600; margin: 0;
            line-height: 1.3;
        }
        .sidebar-brand small { color: rgba(255,255,255,.5); font-size: .7rem; }

        .sidebar-section {
            padding: 12px 16px 4px;
            font-size: .65rem; font-weight: 700;
            letter-spacing: .08em; text-transform: uppercase;
            color: rgba(255,255,255,.35);
        }
        .sidebar-nav { list-style: none; padding: 0 10px; margin: 0; }
        .sidebar-nav li a {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px;
            color: rgba(255,255,255,.75);
            text-decoration: none; font-size: .87rem; font-weight: 500;
            transition: all .18s;
        }
        .sidebar-nav li a:hover,
        .sidebar-nav li a.active {
            background: rgba(255,255,255,.12);
            color: #fff;
        }
        .sidebar-nav li a.active { background: var(--accent); color: #fff; }
        .sidebar-nav li a i { font-size: 1.05rem; width: 20px; text-align: center; }
        .sidebar-nav .badge-count {
            margin-left: auto;
            background: var(--accent);
            color: #fff; font-size: .65rem;
            padding: 2px 7px; border-radius: 20px;
        }

        .sidebar-user {
            margin-top: auto;
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,.1);
        }
        .sidebar-user .user-info {
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-user .avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: .85rem;
        }
        .sidebar-user .name { color: #fff; font-size: .82rem; font-weight: 600; }
        .sidebar-user .role { color: rgba(255,255,255,.5); font-size: .72rem; }
        .sidebar-user a {
            color: rgba(255,255,255,.5); font-size: .75rem;
            text-decoration: none; margin-top: 8px; display: block;
        }
        .sidebar-user a:hover { color: #fff; }

        /* ── TOPBAR ── */
        #topbar {
            position: fixed;
            top: 0; left: var(--sidebar-w);
            right: 0; height: var(--topbar-h);
            background: var(--card);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 28px; gap: 16px;
            z-index: 1030;
        }
        .topbar-toggle {
            display: none;
            background: none; border: none;
            font-size: 1.4rem; color: var(--text); cursor: pointer;
        }
        .topbar-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.05rem; font-weight: 600; color: var(--text);
        }
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 12px; }
        .topbar-icon-btn {
            width: 36px; height: 36px; border-radius: 8px;
            border: 1px solid var(--border);
            background: none; display: flex; align-items: center;
            justify-content: center; font-size: 1rem;
            color: var(--muted); cursor: pointer;
            position: relative; transition: all .18s;
        }
        .topbar-icon-btn:hover { background: var(--bg); color: var(--text); }
        .notif-dot {
            position: absolute; top: 6px; right: 6px;
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--accent); border: 2px solid white;
        }

        /* ── MAIN CONTENT ── */
        #main-content {
            margin-left: var(--sidebar-w);
            padding-top: var(--topbar-h);
            min-height: 100vh;
        }
        .page-body { padding: 28px; }

        /* ── CARDS ── */
        .card {
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border);
            padding: 16px 20px;
            font-weight: 600; font-size: .95rem;
        }
        .stat-card {
            border-radius: 12px; padding: 20px;
            border: none;
        }
        .stat-card .stat-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; margin-bottom: 14px;
        }
        .stat-card .stat-value {
            font-size: 2rem; font-weight: 700;
            font-family: 'Space Grotesk', sans-serif;
            line-height: 1;
        }
        .stat-card .stat-label {
            font-size: .82rem; color: var(--muted); margin-top: 4px;
        }
        .stat-card .stat-change {
            font-size: .78rem; margin-top: 10px;
        }

        /* ── TABLES ── */
        .table { font-size: .88rem; }
        .table thead th {
            background: var(--bg); color: var(--muted);
            font-weight: 600; font-size: .78rem;
            text-transform: uppercase; letter-spacing: .05em;
            border-bottom: 2px solid var(--border);
            padding: 10px 14px;
        }
        .table tbody td { padding: 12px 14px; vertical-align: middle; }
        .table tbody tr:hover { background: #f8fafc; }

        /* ── BADGES ── */
        .badge-verified   { background: #dcfce7; color: #166534; }
        .badge-rejected   { background: #fee2e2; color: #991b1b; }
        .badge-pending    { background: #fef9c3; color: #854d0e; }
        .badge-admin      { background: #dbeafe; color: #1d4ed8; }
        .badge-supervisor { background: #f3e8ff; color: #6b21a8; }

        /* ── FORMS ── */
        .form-control, .form-select {
            border: 1px solid var(--border); border-radius: 8px;
            font-size: .88rem; padding: 8px 12px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(37,99,168,.15);
        }
        .form-label { font-size: .83rem; font-weight: 600; color: var(--text); margin-bottom: 5px; }

        /* ── BUTTONS ── */
        .btn-primary-ihet {
            background: var(--primary); color: #fff; border: none;
            border-radius: 8px; padding: 9px 20px;
            font-size: .87rem; font-weight: 600;
            transition: all .18s;
        }
        .btn-primary-ihet:hover { background: var(--primary-light); color: #fff; }
        .btn-accent {
            background: var(--accent); color: #fff; border: none;
            border-radius: 8px; padding: 9px 20px;
            font-size: .87rem; font-weight: 600;
        }
        .btn-accent:hover { background: var(--accent-light); color: var(--text); }

        /* ── FINGERPRINT WIDGET ── */
        .fingerprint-zone {
            border: 2px dashed var(--border);
            border-radius: 16px; padding: 40px 20px;
            text-align: center; background: #f8fafc;
            transition: all .25s;
            cursor: pointer;
        }
        .fingerprint-zone:hover,
        .fingerprint-zone.scanning {
            border-color: var(--primary-light);
            background: #eff6ff;
        }
        .fingerprint-zone .fp-icon {
            font-size: 3.5rem; color: var(--primary);
            margin-bottom: 12px; display: block;
        }
        .fingerprint-zone.success { border-color: var(--success); background: #f0fdf4; }
        .fingerprint-zone.success .fp-icon { color: var(--success); }
        .fingerprint-zone.error   { border-color: var(--danger);  background: #fff1f2; }
        .fingerprint-zone.error .fp-icon   { color: var(--danger);  }

        /* ── ALERTS ── */
        .alert { border-radius: 10px; border: none; font-size: .87rem; }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-danger  { background: #fee2e2; color: #991b1b; }
        .alert-warning { background: #fef9c3; color: #854d0e; }
        .alert-info    { background: #dbeafe; color: #1d4ed8; }

        /* ── RESPONSIVE ── */
        @media (max-width: 991px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #topbar { left: 0; }
            #main-content { margin-left: 0; }
            .topbar-toggle { display: flex; }
        }
        @media (max-width: 576px) {
            .page-body { padding: 16px; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<nav id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo">I</div>
        <h5>IHET Smart Attendance</h5>
        <small>Dar es Salaam, Tanzania</small>
    </div>

    <div class="mt-2">
        <div class="sidebar-section">Main</div>
        <ul class="sidebar-nav">
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
            </li>
        </ul>

        @if(auth()->user()->role === 'admin')
        <div class="sidebar-section">Management</div>
        <ul class="sidebar-nav">
            <li>
                <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Students
                </a>
            </li>
            <li>
                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i> System Users
                </a>
            </li>
            <li>
                <a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard"></i> Courses
                </a>
            </li>
            <li>
                <a href="{{ route('subjects.index') }}" class="{{ request()->routeIs('subjects.*') ? 'active' : '' }}">
                    <i class="bi bi-book"></i> Subjects
                </a>
            </li>
        </ul>
        @endif

        <div class="sidebar-section">Examinations</div>
        <ul class="sidebar-nav">
            <li>
                <a href="{{ route('exams.index') }}" class="{{ request()->routeIs('exams.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i> Exam Schedule
                </a>
            </li>
            <li>
                <a href="{{ route('attendance.index') }}" class="{{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                    <i class="bi bi-fingerprint"></i> Attendance
                </a>
            </li>
            <li>
                <a href="{{ route('attendance.verify') }}" class="{{ request()->routeIs('attendance.verify') ? 'active' : '' }}">
                    <i class="bi bi-shield-check"></i> Live Verification
                </a>
            </li>
        </ul>

        <div class="sidebar-section">Reports</div>
        <ul class="sidebar-nav">
            <li>
                <a href="{{ route('reports.attendance') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart"></i> Attendance Reports
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-user">
        <div class="user-info">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <div>
                <div class="name">{{ auth()->user()->name }}</div>
                <div class="role">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background:none;border:none;padding:0;margin-top:10px;">
                <a href="#" onclick="event.preventDefault(); this.closest('form').submit();"
                   style="color:rgba(255,255,255,.5);font-size:.75rem;text-decoration:none;">
                    <i class="bi bi-box-arrow-right"></i> Sign Out
                </a>
            </button>
        </form>
    </div>
</nav>

{{-- TOPBAR --}}
<header id="topbar">
    <button class="topbar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
        <i class="bi bi-list"></i>
    </button>
    <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
    <div class="topbar-right">
        <button class="topbar-icon-btn">
            <i class="bi bi-bell"></i>
            <span class="notif-dot"></span>
        </button>
        <button class="topbar-icon-btn">
            <i class="bi bi-question-circle"></i>
        </button>
    </div>
</header>

{{-- MAIN --}}
<main id="main-content">
    <div class="page-body">
        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>