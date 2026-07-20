<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ระบบจัดการคำขอใช้บริการ')  — สำนักคอมพิวเตอร์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@500;600;700&family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f5f4e9;
            --surface: #ffffff;
            --ink: #202a1e;
            --ink-soft: #667061;
            --forest: #24422b;
            --forest-2: #33532f;
            --moss: #6e8f4e;
            --moss-light: #e7efd9;
            --amber: #e0a526;
            --amber-deep: #b9840f;
            --amber-light: #fbf0d2;
            --rust: #b1492e;
            --rust-light: #f7e2da;
            --line: #e6e2d2;
            --radius-lg: 18px;
            --radius-md: 12px;
        }

        * { box-sizing: border-box; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes glowPulse { 0%, 100% { opacity: .35; } 50% { opacity: .7; } }
        @keyframes softBob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-2px); } }
        @keyframes badgePing {
            0%   { box-shadow: 0 0 0 0 rgba(224,165,38,.55); }
            100% { box-shadow: 0 0 0 7px rgba(224,165,38,0); }
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background: var(--bg);
            color: var(--ink);
            margin: 0;
        }

        h1, h2, h3, .display-font { font-family: 'Kanit', sans-serif; }

        a { text-decoration: none; }

        /* colorful scrollbar, kept subtle */
        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cfd6c2; border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--moss); }

        :focus-visible {
            outline: 2px solid var(--amber);
            outline-offset: 2px;
            border-radius: 4px;
        }

        /* ---------- Layout shell ---------- */
        .app-shell { display: flex; min-height: 100vh; }

        .sidebar {
            width: 248px;
            flex-shrink: 0;
            background: linear-gradient(190deg, var(--forest) 0%, var(--forest-2) 65%, #3d5a34 100%);
            color: #eef2e6;
            padding: 26px 18px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .sidebar::after {
            content: '';
            position: absolute;
            width: 220px; height: 220px;
            background: radial-gradient(circle, rgba(224,165,38,.35) 0%, rgba(224,165,38,0) 70%);
            bottom: -60px; right: -70px;
            pointer-events: none;
            animation: glowPulse 5s ease-in-out infinite;
        }
        .sidebar::before {
            content: '';
            position: absolute;
            width: 140px; height: 140px;
            background: radial-gradient(circle, rgba(110,143,78,.4) 0%, rgba(110,143,78,0) 70%);
            top: -40px; left: -50px;
            pointer-events: none;
        }
        .brand {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 30px; position: relative; z-index: 1;
        }
        .brand-mark {
            width: 38px; height: 38px; border-radius: 11px;
            background: linear-gradient(135deg, var(--amber), #f0c25c);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 3px 10px rgba(224,165,38,.35);
            animation: softBob 4s ease-in-out infinite;
        }
        .brand-text { font-family: 'Kanit', sans-serif; font-size: 14.5px; font-weight: 600; line-height: 1.3; }
        .brand-text small { display: block; font-weight: 400; font-size: 11.5px; opacity: .7; }

        .nav-group { position: relative; z-index: 1; margin-top: 8px; }
        .nav-label {
            font-size: 11px; text-transform: uppercase; letter-spacing: .06em;
            opacity: .55; margin: 18px 10px 8px; display: flex; align-items: center; gap: 6px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px; margin-bottom: 4px;
            color: #dde5d5; font-size: 14px; font-weight: 500;
            transition: background .15s ease, transform .15s ease, color .15s ease;
            border-left: 3px solid transparent;
        }
        .nav-item:hover { background: rgba(255,255,255,.08); color: #fff; transform: translateX(2px); }
        .nav-item.active {
            background: rgba(224,165,38,.16);
            color: #fff;
            border-left: 3px solid var(--amber);
        }
        .nav-item svg { flex-shrink: 0; opacity: .9; transition: transform .15s ease; }
        .nav-item:hover svg { transform: scale(1.08); }
        .nav-item .nav-badge {
            margin-left: auto; font-size: 10.5px; font-weight: 700;
            background: var(--amber); color: #2c1e05; border-radius: 999px;
            padding: 1px 7px; line-height: 1.5;
        }

        .sidebar-footer {
            position: relative; z-index: 1; margin-top: auto;
            padding-top: 16px; border-top: 1px solid rgba(255,255,255,.12);
            display: flex; align-items: center; gap: 10px;
            font-size: 12px; color: rgba(238,242,230,.65);
        }
        .sidebar-footer .status-dot {
            width: 8px; height: 8px; border-radius: 50%; background: #6fd18a;
            box-shadow: 0 0 0 0 rgba(111,209,138,.5); animation: badgePing 2s ease-out infinite;
            flex-shrink: 0;
        }

        .main-col { flex: 1; min-width: 0; display: flex; flex-direction: column; }

        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--line);
            padding: 18px 32px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 1px 0 rgba(36,66,43,.02);
        }
        .topbar h1 { font-size: 20px; font-weight: 600; margin: 0; color: var(--ink); display: flex; align-items: center; gap: 8px; }
        .topbar .eyebrow {
            font-size: 12px; color: var(--moss); font-weight: 600;
            text-transform: uppercase; letter-spacing: .05em; margin-bottom: 2px;
            display: flex; align-items: center; gap: 5px;
        }
        .topbar .eyebrow svg { flex-shrink: 0; }
        .topbar-right { display: flex; align-items: center; gap: 14px; }

        .content-area { padding: 26px 32px 40px; animation: fadeUp .35s ease; }

        /* ---------- Cards & tables ---------- */
        .panel {
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 22px 26px;
            box-shadow: 0 2px 10px rgba(36,66,43,.06);
            border: 1px solid var(--line);
            transition: box-shadow .2s ease;
        }

        .stat-row { display: flex; gap: 16px; margin-bottom: 22px; flex-wrap: wrap; }
        .stat-card {
            flex: 1; min-width: 160px;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            padding: 16px 18px;
            display: flex; align-items: center; gap: 12px;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(36,66,43,.09); }
        .stat-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, var(--moss-light), var(--amber-light));
            color: var(--forest); flex-shrink: 0;
        }
        .stat-number { font-family: 'Kanit', sans-serif; font-size: 20px; font-weight: 700; line-height: 1; }
        .stat-label { font-size: 12px; color: var(--ink-soft); margin-top: 3px; }

        table.modern-table { border-collapse: separate; border-spacing: 0; width: 100%; }
        table.modern-table th {
            font-size: 12.5px; text-transform: uppercase; letter-spacing: .03em;
            color: var(--ink-soft); font-weight: 600; text-align: left;
            padding: 10px 14px; border-bottom: 2px solid var(--line);
        }
        table.modern-table td { padding: 14px; font-size: 14px; border-bottom: 1px solid var(--line); vertical-align: middle; }
        table.modern-table tbody tr { transition: background .15s ease; }
        table.modern-table tbody tr:hover { background: var(--moss-light); }
        table.modern-table tbody tr:last-child td { border-bottom: none; }

        .avatar-circle {
            width: 34px; height: 34px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-family: 'Kanit', sans-serif; font-weight: 600; font-size: 13px; color: #fff;
            margin-right: 10px; flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,.12);
        }
        .avatar-a { background: linear-gradient(135deg, var(--forest), var(--forest-2)); }
        .avatar-b { background: linear-gradient(135deg, var(--moss), #82a862); }
        .avatar-c { background: linear-gradient(135deg, var(--amber-deep), var(--amber)); }

        .pill {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 12px; font-weight: 600;
            padding: 4px 12px; border-radius: 20px; letter-spacing: .01em;
        }
        .pill svg { width: 11px; height: 11px; flex-shrink: 0; }
        .pill-submitted { background: var(--amber-light); color: #8a6408; }
        .pill-approved { background: var(--moss-light); color: var(--forest); }
        .pill-rejected { background: var(--rust-light); color: var(--rust); }
        .pill-expired  { background: #ece9dc; color: var(--ink-soft); }
        .pill-draft    { background: #ece9dc; color: var(--ink-soft); }
        .pill-active   { background: var(--moss-light); color: var(--forest); }
        .pill-disabled { background: #ece9dc; color: var(--ink-soft); }

        .btn-brand {
            background: var(--forest); border: none; color: #fff;
            font-weight: 500; border-radius: 9px; padding: 8px 16px; font-size: 14px;
            transition: background .15s ease, transform .15s ease;
        }
        .btn-brand:hover { background: #1b3220; color: #fff; transform: translateY(-1px); }

        .btn-amber {
            background: linear-gradient(135deg, var(--amber), var(--amber-deep));
            border: none; color: #2c1e05; font-weight: 600;
            border-radius: 9px; padding: 10px 22px; font-size: 14.5px;
            transition: filter .15s ease, transform .15s ease;
        }
        .btn-amber:hover { filter: brightness(1.05); color: #2c1e05; transform: translateY(-1px); }
        .btn-amber:active { transform: translateY(0); }

        .btn-outline-soft {
            border: 1px solid var(--line); background: #fff; color: var(--ink);
            border-radius: 9px; font-size: 14px;
            transition: background .15s ease, border-color .15s ease;
        }
        .btn-outline-soft:hover { background: var(--moss-light); border-color: var(--moss); }

        .form-control, .form-select {
            border: 1px solid var(--line); border-radius: 9px; font-size: 14px;
            padding: 9px 12px;
            transition: box-shadow .15s ease, border-color .15s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--moss); box-shadow: 0 0 0 3px rgba(110,143,78,.15);
        }

        .banner-success {
            background: var(--moss-light); border: 1px solid #c3d9ac; color: var(--forest);
            border-radius: var(--radius-md); padding: 14px 18px; font-size: 14px; margin-bottom: 20px;
        }
        .banner-credential {
            background: linear-gradient(120deg, var(--amber-light), #fff);
            border: 1px solid #eecf88; border-radius: var(--radius-md);
            padding: 16px 20px; font-size: 14px; margin-bottom: 20px;
            display: flex; align-items: flex-start; gap: 14px;
        }
        .banner-credential code {
            background: #fff; border: 1px solid #eecf88; padding: 2px 8px;
            border-radius: 6px; font-weight: 700; color: #6b4c05;
        }
        .banner-danger {
            background: var(--rust-light); border: 1px solid #e6b6a4; color: var(--rust);
            border-radius: var(--radius-md); padding: 14px 18px; font-size: 14px; margin-bottom: 20px;
        }

        @media (max-width: 860px) {
            .sidebar { display: none; }
            .content-area { padding: 20px 16px 32px; }
            .topbar { padding: 16px 18px; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2C7 2 3 6 3 11c0 5 4.5 9.5 9 11 4.5-1.5 9-6 9-11 0-5-4-9-9-9Z" fill="#24422b"/>
                    <path d="M12 6v10M9 9l3-3 3 3" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="brand-text">
                ระบบจัดการคำขอใช้บริการ
                <small>สำนักคอมพิวเตอร์ มรภ.นครราชสีมา</small>
            </div>
        </div>

        <div class="nav-group">
            <div class="nav-label">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                เมนูหลัก
            </div>
            <a href="{{ route('admin.requests.index') }}" class="nav-item {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 2h6l1 3h4v17H4V5h4l1-3Z"/><path d="M9 12h6M9 16h6"/></svg>
                รายการคำขอ
            </a>
            <a href="{{ route('admin.accounts.index') }}" class="nav-item {{ request()->routeIs('admin.accounts.*') ? 'active' : '' }}">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                บัญชีผู้ใช้บริการ
            </a>
        </div>

        <div class="sidebar-footer">
            <span class="status-dot"></span>
            ระบบทำงานปกติ
        </div>
    </aside>

    <div class="main-col">
        <div class="topbar">
            <div>
                <div class="eyebrow">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M13 2 3 14h7l-1 8 10-12h-7l1-8Z"/></svg>
                    @yield('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
                </div>
                <h1>@yield('page-title', 'ภาพรวม')</h1>
            </div>
            <div class="topbar-right">
                @hasSection('topbar-action')
                    <div>@yield('topbar-action')</div>
                @endif
            </div>
        </div>

        <div class="content-area">
            @yield('content')
        </div>
    </div>
</div>

</body>
</html>