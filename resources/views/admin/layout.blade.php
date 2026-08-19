<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ระบบจัดการคำขอใช้บริการ')  — สำนักคอมพิวเตอร์</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700&family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f4f3ea;
            --surface: #ffffff;
            --surface-2: #fbfaf3;
            --ink: #15231a;
            --ink-soft: #5c6659;
            --forest: #1a3323;
            --forest-2: #244430;
            --moss: #6c9752;
            --moss-light: #e8f0dc;
            --amber: #d79a2c;
            --amber-deep: #a6740e;
            --amber-light: #faf0d3;
            --rust: #ae4830;
            --rust-light: #f6e1d8;
            --line: #e8e4d5;
            --radius-xs: 10px;
            --radius-sm: 14px;
            --radius-lg: 22px;
            --radius-md: 16px;
            --shadow-sm: 0 1px 2px rgba(21,35,26,.05), 0 1px 1px rgba(21,35,26,.03);
            --shadow-md: 0 16px 32px -18px rgba(21,35,26,.28), 0 4px 10px -6px rgba(21,35,26,.1);
            --shadow-lg: 0 32px 70px -28px rgba(21,35,26,.36);
        }

        * { box-sizing: border-box; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes glowPulse { 0%, 100% { opacity: .3; } 50% { opacity: .65; } }
        @keyframes softBob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-2px); } }
        @keyframes badgePing {
            0%   { box-shadow: 0 0 0 0 rgba(111,209,138,.5); }
            100% { box-shadow: 0 0 0 7px rgba(111,209,138,0); }
        }

        /* Signature texture — topographic contour rings, echoing the
           reserve-map motif used across the whole site (sidebar, heroes). */
        .contour-field {
            background-image:
                repeating-radial-gradient(circle at 82% 8%, rgba(255,255,255,.09) 0px, rgba(255,255,255,.09) 1px, transparent 1px, transparent 16px),
                repeating-radial-gradient(circle at 6% 96%, rgba(224,165,38,.14) 0px, rgba(224,165,38,.14) 1px, transparent 1px, transparent 13px);
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background: var(--bg);
            color: var(--ink);
            margin: 0;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, .display-font { font-family: 'Kanit', sans-serif; letter-spacing: -.01em; }

        a { text-decoration: none; }

        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d3d9c8; border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--moss); }

        :focus-visible {
            outline: 2px solid var(--amber);
            outline-offset: 2px;
            border-radius: 4px;
        }

        /* ---------- Layout shell ---------- */
        .app-shell { display: flex; min-height: 100vh; }

        .sidebar {
            width: 264px;
            flex-shrink: 0;
            background:
                repeating-radial-gradient(circle at 78% 6%, rgba(255,255,255,.07) 0px, rgba(255,255,255,.07) 1px, transparent 1px, transparent 17px),
                radial-gradient(140% 120% at 20% 0%, var(--forest-2) 0%, var(--forest) 58%, #142a1a 100%);
            color: #eef2e6;
            padding: 26px 17px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .sidebar::after {
            content: '';
            position: absolute;
            width: 240px; height: 240px;
            background: radial-gradient(circle, rgba(224,165,38,.28) 0%, rgba(224,165,38,0) 70%);
            bottom: -70px; right: -80px;
            pointer-events: none;
            animation: glowPulse 6s ease-in-out infinite;
        }
        .sidebar::before {
            content: '';
            position: absolute;
            width: 160px; height: 160px;
            background: radial-gradient(circle, rgba(108,151,82,.4) 0%, rgba(108,151,82,0) 70%);
            top: -50px; left: -60px;
            pointer-events: none;
        }
        .brand {
            display: flex; align-items: center; gap: 11px;
            margin-bottom: 26px; position: relative; z-index: 1;
        }
        .brand-mark {
            width: 40px; height: 40px; border-radius: var(--radius-xs);
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,.18);
            animation: softBob 4s ease-in-out infinite;
            padding: 5px;
        }
        .brand-mark img { width: 100%; height: 100%; object-fit: contain; display: block; }
        .brand-text { font-family: 'Kanit', sans-serif; font-size: 14.5px; font-weight: 600; line-height: 1.3; }
        .brand-text small { display: block; font-weight: 400; font-size: 11.5px; opacity: .7; }

        .nav-group {
            position: relative; z-index: 1; margin-top: 6px;
            background: rgba(255,255,255,.045);
            border: 1px solid rgba(255,255,255,.07);
            border-radius: var(--radius-md);
            padding: 6px;
        }
        .nav-group + .nav-group { margin-top: 18px; }
        .nav-label {
            font-size: 10.5px; text-transform: uppercase; letter-spacing: .09em;
            opacity: .48; margin: 8px 10px 8px; display: flex; align-items: center; gap: 6px;
            font-weight: 600;
        }
        .nav-item {
            display: flex; align-items: center; gap: 11px;
            padding: 10px 12px; border-radius: var(--radius-xs); margin-bottom: 2px;
            color: #dde5d5; font-size: 14px; font-weight: 500;
            transition: background .15s ease, transform .15s ease, color .15s ease;
            position: relative;
        }
        .nav-item:hover { background: rgba(255,255,255,.08); color: #fff; transform: translateX(2px); }
        .nav-item.active {
            background: linear-gradient(90deg, rgba(224,165,38,.24), rgba(224,165,38,.05));
            color: #fff;
            box-shadow: inset 0 0 0 1px rgba(224,165,38,.18);
        }
        .nav-item.active::before {
            content: ''; position: absolute; left: -13px; top: 50%; transform: translateY(-50%);
            width: 3.5px; height: 20px; border-radius: 0 4px 4px 0; background: var(--amber);
            box-shadow: 0 0 10px rgba(224,165,38,.6);
        }
        .nav-item .nav-icon {
            width: 30px; height: 30px; border-radius: 9px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,.06); transition: background .15s ease, transform .15s ease;
        }
        .nav-item.active .nav-icon { background: rgba(224,165,38,.22); }
        .nav-item:hover .nav-icon { transform: scale(1.06); }
        .nav-item svg { flex-shrink: 0; opacity: .92; }
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
        .footer-user { display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0; }
        .footer-user__avatar {
            width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Kanit', sans-serif; font-weight: 600; font-size: 13px; color: #2c1e05;
            background: linear-gradient(135deg, var(--amber), #f0c25c);
        }
        .footer-user__name { font-size: 13px; color: #eef2e6; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .footer-user__role { font-size: 11px; opacity: .6; }
        .logout-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.14);
            color: #eef2e6; border-radius: var(--radius-xs); padding: 8px 11px; font-size: 12.5px; font-weight: 500;
            cursor: pointer; transition: background .15s ease, transform .15s ease; flex-shrink: 0;
        }
        .logout-btn:hover { background: rgba(255,255,255,.16); color: #fff; }

        .main-col { flex: 1; min-width: 0; display: flex; flex-direction: column; }

        .topbar {
            background: rgba(255,255,255,.86);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--line);
            padding: 20px 36px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 20;
        }
        .topbar::after {
            content: ''; position: absolute; left: 0; right: 0; bottom: -1px; height: 1px;
            background: linear-gradient(90deg, var(--moss) 0%, var(--amber) 50%, transparent 100%);
            opacity: .35;
        }
        .topbar h1 { font-size: 22px; font-weight: 600; margin: 0; color: var(--ink); display: flex; align-items: center; gap: 8px; letter-spacing: -.015em; }
        .topbar .eyebrow {
            font-size: 11.5px; color: var(--moss); font-weight: 700;
            text-transform: uppercase; letter-spacing: .08em; margin-bottom: 4px;
            display: flex; align-items: center; gap: 5px;
        }
        .topbar .eyebrow svg { flex-shrink: 0; }
        .topbar-right { display: flex; align-items: center; gap: 14px; }

        .content-area { padding: 30px 36px 46px; animation: fadeUp .4s ease; }

        /* ---------- Cards & tables ---------- */
        .panel {
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 26px 30px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--line);
            transition: box-shadow .2s ease;
            position: relative;
        }

        .stat-row { display: flex; gap: 16px; margin-bottom: 26px; flex-wrap: wrap; }
        .stat-card {
            flex: 1; min-width: 170px;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            padding: 19px 21px;
            display: flex; align-items: center; gap: 14px;
            box-shadow: var(--shadow-sm);
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); border-color: transparent; }
        .stat-icon {
            width: 46px; height: 46px; border-radius: 13px;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, var(--moss-light), var(--amber-light));
            color: var(--forest); flex-shrink: 0;
            box-shadow: inset 0 0 0 1px rgba(21,35,26,.04);
        }
        .stat-number { font-family: 'Kanit', sans-serif; font-size: 23px; font-weight: 700; line-height: 1; }
        .stat-label { font-size: 12px; color: var(--ink-soft); margin-top: 4px; }

        table.modern-table { border-collapse: separate; border-spacing: 0; width: 100%; }
        table.modern-table th {
            font-size: 11.5px; text-transform: uppercase; letter-spacing: .05em;
            color: var(--ink-soft); font-weight: 700; text-align: left;
            padding: 13px 14px; border-bottom: 2px solid var(--line);
        }
        table.modern-table td { padding: 16px 14px; font-size: 14px; border-bottom: 1px solid var(--line); vertical-align: middle; }
        table.modern-table tbody tr { transition: background .15s ease; }
        table.modern-table tbody tr:hover { background: var(--moss-light); }
        table.modern-table tbody tr:last-child td { border-bottom: none; }

        .avatar-circle {
            width: 36px; height: 36px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-family: 'Kanit', sans-serif; font-weight: 600; font-size: 14px; color: #fff;
            margin-right: 10px; flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0,0,0,.12);
        }
        .avatar-a { background: linear-gradient(135deg, var(--forest), var(--forest-2)); }
        .avatar-b { background: linear-gradient(135deg, var(--moss), #82a862); }
        .avatar-c { background: linear-gradient(135deg, var(--amber-deep), var(--amber)); }

        .pill {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 12px; font-weight: 700;
            padding: 5px 13px; border-radius: 999px; letter-spacing: .01em;
            box-shadow: inset 0 0 0 1px rgba(0,0,0,.03);
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
            background: linear-gradient(135deg, var(--forest), var(--forest-2)); border: none; color: #fff;
            font-weight: 500; border-radius: var(--radius-sm); padding: 9px 17px; font-size: 14px;
            box-shadow: 0 6px 16px -8px rgba(26,51,35,.55);
            transition: filter .15s ease, transform .15s ease, box-shadow .15s ease;
        }
        .btn-brand:hover { filter: brightness(1.12); color: #fff; transform: translateY(-1px); box-shadow: var(--shadow-md); }

        .btn-amber {
            background: linear-gradient(135deg, var(--amber), var(--amber-deep));
            border: none; color: #2c1e05; font-weight: 600;
            border-radius: var(--radius-sm); padding: 10px 22px; font-size: 14.5px;
            box-shadow: 0 8px 20px -8px rgba(185,132,15,.65);
            transition: filter .15s ease, transform .15s ease, box-shadow .15s ease;
        }
        .btn-amber:hover { filter: brightness(1.05); color: #2c1e05; transform: translateY(-1px); box-shadow: 0 10px 24px -8px rgba(185,132,15,.7); }
        .btn-amber:active { transform: translateY(0); }

        .btn-outline-soft {
            border: 1px solid var(--line); background: #fff; color: var(--ink);
            border-radius: var(--radius-sm); font-size: 14px; padding: 8px 15px;
            transition: background .15s ease, border-color .15s ease, transform .15s ease;
        }
        .btn-outline-soft:hover { background: var(--moss-light); border-color: var(--moss); transform: translateY(-1px); }

        .form-control, .form-select {
            border: 1px solid var(--line); border-radius: var(--radius-sm); font-size: 14px;
            padding: 9px 13px;
            transition: box-shadow .15s ease, border-color .15s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--moss); box-shadow: 0 0 0 3px rgba(95,139,70,.16);
        }

        .banner-success {
            background: var(--moss-light); border: 1px solid #c3d9ac; color: var(--forest);
            border-radius: var(--radius-md); padding: 14px 18px; font-size: 14px; margin-bottom: 20px;
        }

        /* ---------- Mobile ---------- */
        .mobile-topbar { display: none; }
        .sidebar-backdrop { display: none; }

        @media (max-width: 991px) {
            .sidebar {
                position: fixed; top: 0; left: 0; z-index: 60; height: 100vh;
                transform: translateX(-100%); transition: transform .25s ease;
                box-shadow: var(--shadow-lg);
            }
            .sidebar.open { transform: translateX(0); }
            .sidebar-backdrop.show { display: block; position: fixed; inset: 0; background: rgba(20,30,18,.4); z-index: 55; }
            .mobile-topbar {
                display: flex; align-items: center; gap: 12px;
                padding: 12px 16px; background: var(--surface); border-bottom: 1px solid var(--line);
                position: sticky; top: 0; z-index: 40;
            }
            .mobile-topbar .menu-btn {
                background: none; border: 1px solid var(--line); border-radius: var(--radius-xs); padding: 7px 9px; cursor: pointer;
            }
            .mobile-topbar .m-brand { font-family: 'Kanit', sans-serif; font-weight: 600; font-size: 14px; color: var(--forest); }
            .topbar { padding: 16px 18px; }
            .content-area { padding: 20px 16px 36px; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="mobile-topbar">
    <button class="menu-btn" id="menuBtn" aria-label="เปิดเมนู">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#21402a" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <span class="m-brand" style="display:inline-flex;align-items:center;gap:8px;">
        <img src="{{ asset('images/logo.png') }}" alt="โลโก้ระบบ" style="width:22px;height:22px;object-fit:contain;">
        ระบบจัดการคำขอใช้บริการ
    </span>
</div>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-mark">
                <img src="{{ asset('images/logo.png') }}" alt="โลโก้ระบบ">
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
                <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 2h6l1 3h4v17H4V5h4l1-3Z"/><path d="M9 12h6M9 16h6"/></svg></span>
                รายการคำขอ
            </a>
            <a href="{{ route('admin.accounts.index') }}" class="nav-item {{ request()->routeIs('admin.accounts.*') ? 'active' : '' }}">
                <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></span>
                บัญชีผู้ใช้บริการ
            </a>
            <a href="{{ route('admin.domains.index') }}" class="nav-item {{ request()->routeIs('admin.domains.*') ? 'active' : '' }}">
                <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg></span>
                โดเมนผู้ใช้บริการ
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                จัดการผู้ใช้
            </a>
        </div>

        <div class="nav-group">
            <div class="nav-label">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/></svg>
                ลิงก์ด่วน
            </div>
            <a href="{{ url('/') }}" class="nav-item" target="_blank">
                <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2C7 2 3 6 3 11c0 5 4.5 9.5 9 11 4.5-1.5 9-6 9-11 0-5-4-9-9-9Z"/><path d="M12 6v10M9 9l3-3 3 3"/></svg></span>
                หน้าเว็บบริการ
            </a>
            <a href="{{ route('service-requests.create') }}" class="nav-item" target="_blank">
                <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 2h6l1 3h4v17H4V5h4l1-3Z"/><path d="M12 11v6M9 14h6"/></svg></span>
                แบบฟอร์มขอใช้บริการ
            </a>
        </div>

        <div class="sidebar-footer">
            <div class="footer-user">
                <span class="footer-user__avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                <div style="min-width:0;">
                    <div class="footer-user__name">{{ auth()->user()->name }}</div>
                    <div class="footer-user__role">เจ้าหน้าที่สำนักคอมพิวเตอร์</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn" title="ออกจากระบบ">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/></svg>
                </button>
            </form>
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

@include('partials.alert-popup')
@include('partials.confirm-modal')

<script>
    (function () {
        var sidebar = document.getElementById('sidebar');
        var backdrop = document.getElementById('sidebarBackdrop');
        var menuBtn = document.getElementById('menuBtn');
        function close() { sidebar.classList.remove('open'); backdrop.classList.remove('show'); }
        if (menuBtn) menuBtn.addEventListener('click', function () {
            sidebar.classList.toggle('open'); backdrop.classList.toggle('show');
        });
        if (backdrop) backdrop.addEventListener('click', close);
        sidebar.querySelectorAll('.nav-item').forEach(function (a) { a.addEventListener('click', close); });
    })();
</script>

</body>
</html>
