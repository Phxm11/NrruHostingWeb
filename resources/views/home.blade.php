<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>บริการ Data Center และ Web Hosting — สำนักคอมพิวเตอร์ มหาวิทยาลัยราชภัฏนครราชสีมา</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700&family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f5f4ec;
            --surface: #ffffff;
            --ink: #15231a;
            --ink-soft: #5c6659;
            --forest: #1a3323;
            --forest-2: #244430;
            --moss: #6c9752;
            --moss-light: #e8f0dc;
            --amber: #d79a2c;
            --amber-deep: #a6740e;
            --amber-light: #faf0d3;
            --line: #e5e1d1;
            --shadow-sm: 0 1px 2px rgba(21,35,26,.05);
            --shadow-md: 0 12px 28px -14px rgba(21,35,26,.22);
            --shadow-lg: 0 28px 64px -24px rgba(21,35,26,.32);
            --radius: 24px;
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body { font-family: 'Sarabun', sans-serif; background: var(--bg); color: var(--ink); margin: 0; line-height: 1.6; -webkit-font-smoothing: antialiased; }

        h1, h2, h3, .display-font { font-family: 'Kanit', sans-serif; letter-spacing: -.01em; }

        a { text-decoration: none; }


        .container { max-width: 1180px; }
        .ui-icon { width: 20px; height: 20px; flex-shrink: 0; fill: none; stroke: currentColor; stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round; vertical-align: middle; }
        a:focus-visible, button:focus-visible { outline: 3px solid var(--amber); outline-offset: 5px; }
        .skip-link { position: fixed; top: 8px; left: 12px; z-index: 100; transform: translateY(-160%); background: white; color: var(--forest); padding: 12px 18px; border-radius: 8px; }
        .skip-link:focus { transform: none; }
        .institution-bar { background: var(--forest); color: #d8e0d0; font-size: 12px; padding: 8px 0; }
        .institution-bar .container { display: flex; justify-content: space-between; gap: 16px; }
        .navbar-custom { position: sticky; top: 0; z-index: 60; background: var(--surface); border-bottom: 1px solid var(--line); }
        .navbar-custom.scrolled { box-shadow: 0 4px 18px rgba(21,35,26,.08); }
        .navbar-inner { display: flex; align-items: center; gap: 24px; min-height: 88px; }
        .brand { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
        .brand-mark { width: 46px; height: 46px; object-fit: contain; }
        .brand-name { font-family: 'Kanit', sans-serif; font-weight: 600; font-size: 14.5px; color: var(--forest); line-height: 1.4; }
        .brand-name small { display: block; font-weight: 400; font-size: 10.5px; color: var(--ink-soft); margin-top: 3px; }
        .nav-desktop { display: flex; align-items: center; justify-content: flex-end; gap: 18px; flex: 1; }
        .nav-links-group { display: flex; gap: 2px; align-items: center; }
        .nav-link-custom { display: flex; align-items: center; gap: 7px; padding: 12px 10px; border-radius: 8px; color: var(--ink-soft); font-size: 13px; font-weight: 500; white-space: nowrap; }
        .nav-link-custom .ui-icon { width: 17px; height: 17px; }
        .nav-link-custom:hover, .nav-link-custom[aria-current] { color: var(--forest); background: var(--moss-light); }
        .nav-link-custom[aria-current] { box-shadow: inset 0 -2px var(--moss); }
        .nav-actions { display: flex; align-items: center; gap: 10px; }
        .staff-link { display: inline-flex; align-items: center; justify-content: center; gap: 7px; color: var(--forest); border: 1px solid var(--line); border-radius: 8px; padding: 10px 12px; font-size: 13px; white-space: nowrap; }
        .staff-link:hover { background: var(--bg); color: var(--forest); }
        .btn-request, .btn-amber, .btn-outline-brand { display: inline-flex; align-items: center; justify-content: center; gap: 9px; border: 1px solid transparent; border-radius: 9px; padding: 12px 18px; font-size: 14px; font-weight: 600; }
        .btn-request { background: var(--forest); color: white; white-space: nowrap; }
        .btn-request:hover { background: var(--forest-2); color: white; }
        .btn-amber { background: var(--amber); color: #2c1e05; }
        .btn-amber:hover { background: #e4ab41; color: #2c1e05; }
        .btn-outline-brand { border-color: #6c806b; color: white; }
        .btn-outline-brand:hover { background: rgba(255,255,255,.08); color: white; }
        .mobile-toggle { display: none; align-items: center; justify-content: center; gap: 8px; min-width: 44px; min-height: 44px; border: 1px solid var(--line); border-radius: 8px; background: white; color: var(--forest); font-size: 13px; padding: 8px 12px; margin-left: auto; }
        .mobile-toggle .close-icon { display: none; }
        .mobile-toggle[aria-expanded="true"] .menu-icon { display: none; }
        .mobile-toggle[aria-expanded="true"] .close-icon { display: block; }
        .mobile-menu { border-top: 1px solid var(--line); padding: 12px 0 20px; max-height: calc(100dvh - 80px); overflow-y: auto; }
        .mobile-menu[hidden] { display: none; }
        .mobile-menu .nav-link-custom { padding: 13px 14px; font-size: 14px; }
        .mobile-menu .nav-actions { border-top: 1px solid var(--line); margin-top: 10px; padding-top: 16px; }
        main:focus { outline: none; }
        section[id], header[id] { scroll-margin-top: 112px; }
        .hero { position: relative; overflow: hidden; background: var(--forest); color: white; padding: 68px 0 70px; }
        .hero::after { content: ''; position: absolute; width: 660px; height: 660px; right: -260px; top: -260px; border: 1px solid rgba(215,154,44,.22); border-radius: 50%; box-shadow: 0 0 0 65px rgba(215,154,44,.04), 0 0 0 130px rgba(215,154,44,.025); pointer-events: none; }
        .hero-layout { position: relative; z-index: 1; display: grid; grid-template-columns: 1.3fr 1fr; gap: 64px; align-items: center; }
        .hero-context { display: inline-flex; align-items: center; gap: 9px; color: #e5c783; font-size: 13px; margin-bottom: 22px; }
        .hero h1 { font-size: clamp(30px, 4vw, 46px); font-weight: 700; line-height: 1.3; margin-bottom: 22px; }
        .hero .lead { font-size: 16px; color: #d8e0d0; line-height: 1.85; max-width: 550px; margin-bottom: 28px; }
        .hero-cta { display: flex; flex-wrap: wrap; gap: 12px; }
        .hero-note { margin: 22px 0 0; display: flex; align-items: center; gap: 8px; font-size: 12px; color: #c5d0bc; }
        .service-directory { background: #244430; border: 1px solid #536749; border-radius: 18px; padding: 26px; }
        .directory-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding-bottom: 20px; border-bottom: 1px solid #536749; margin-bottom: 4px; }
        .directory-head h2 { font-size: 19px; font-weight: 600; margin: 0; }
        .directory-head > .ui-icon { color: #e5c783; }
        .service-row { display: flex; align-items: flex-start; gap: 15px; padding: 23px 0; }
        .service-row + .service-row { border-top: 1px solid #536749; }
        .service-icon { display: grid; place-items: center; width: 46px; height: 46px; background: #e8f0dc; color: var(--forest); border-radius: 12px; flex-shrink: 0; }
        .service-icon .ui-icon { width: 25px; height: 25px; }
        .service-row h3 { font-size: 19px; font-weight: 600; margin: 0 0 6px; }
        .service-row p { color: #d8e0d0; font-size: 13px; line-height: 1.8; margin: 0; }
        .directory-link { display: flex; align-items: center; justify-content: space-between; gap: 10px; border-top: 1px solid #536749; padding-top: 18px; color: #f1d493; font-size: 13px; }
        .directory-link:hover { color: white; }
        .purpose-strip { background: var(--moss-light); border-bottom: 1px solid #d8e1cb; }
        .purpose-inner { display: flex; align-items: center; gap: 24px; padding-top: 18px; padding-bottom: 18px; font-size: 13px; color: var(--forest); flex-wrap: wrap; }
        .purpose-inner > span { display: flex; align-items: center; gap: 7px; }
        .purpose-inner .ui-icon { width: 16px; height: 16px; }
        section { padding: 72px 0; }
        .section-title { font-size: clamp(24px, 3.5vw, 31px); font-weight: 700; margin-bottom: 12px; color: var(--forest); }
        .section-desc { color: var(--ink-soft); font-size: 15.5px; max-width: 620px; line-height: 1.8; }
        .section-heading { display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; margin-bottom: 32px; }
        .section-heading p { margin: 0; }
        .section-link { display: inline-flex; gap: 9px; align-items: center; color: var(--forest); font-size: 14px; font-weight: 600; flex-shrink: 0; padding-bottom: 5px; border-bottom: 1px solid var(--moss); }
        .section-link:hover { color: var(--amber-deep); }
        .steps-grid { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); background: white; border: 1px solid var(--line); border-radius: 16px; }
        .step-item { padding: 28px 24px; }
        .step-item + .step-item { border-left: 1px solid var(--line); }
        .step-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; }
        .step-top .ui-icon { color: var(--forest); width: 25px; height: 25px; }
        .step-num { font-family: 'Kanit', sans-serif; font-weight: 700; font-size: 18px; color: var(--amber-deep); background: var(--amber-light); width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; }
        .step-title { font-size: 16px; font-weight: 600; margin-bottom: 10px; }
        .step-desc { color: var(--ink-soft); font-size: 14px; line-height: 1.8; }
        .policy-section { background: white; border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); }
        .policy-layout { display: grid; grid-template-columns: .85fr 1.15fr; gap: 64px; }
        .prepare-note { margin-top: 28px; padding: 22px; background: var(--bg); border-left: 3px solid var(--amber); border-radius: 0 10px 10px 0; }
        .prepare-note strong { display: block; margin-bottom: 12px; font-size: 14px; }
        .prepare-note p { display: flex; gap: 9px; align-items: center; font-size: 13px; color: var(--ink-soft); margin: 9px 0 0; }
        .prepare-note .ui-icon { width: 17px; height: 17px; }
        .policy-list { list-style: none; margin: 0; padding: 0; }
        .policy-list li { display: flex; gap: 14px; font-size: 14.5px; line-height: 1.8; padding: 18px 0; }
        .policy-list li:first-child { padding-top: 0; }
        .policy-list li + li { border-top: 1px solid var(--line); }
        .policy-list .check { width: 27px; height: 27px; flex-shrink: 0; display: grid; place-items: center; background: var(--moss-light); color: var(--forest); border-radius: 8px; }
        .cta-section { padding: 42px 0; }
        .cta-band { display: flex; align-items: center; justify-content: space-between; gap: 30px; }
        .cta-band h2 { font-size: 24px; margin-bottom: 8px; }
        .cta-band p { margin: 0; }
        footer { background: var(--forest); color: #cfd8c6; padding: 32px 0; font-size: 13.5px; }
        footer a { color: #eef2e6; }
        footer a:hover { color: var(--amber); }
        footer .f-brand { font-family: 'Kanit', sans-serif; color: white; font-size: 15px; font-weight: 600; }
        @media (max-width: 1199px) {
            .nav-desktop { display: none; }
            .mobile-toggle { display: inline-flex; }
            .hero-layout { gap: 32px; }
        }
        @media (min-width: 1200px) { .mobile-menu { display: none; } }
        @media (max-width: 991px) {
            .hero-layout { grid-template-columns: 1fr; }
            .hero { padding: 48px 0; }
            .hero .lead { max-width: 680px; }
            .service-directory { max-width: none; }
            .steps-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
            .step-item:nth-child(3) { border-left: 0; }
            .step-item:nth-child(n+3) { border-top: 1px solid var(--line); }
            .policy-layout { grid-template-columns: 1fr; gap: 32px; }
            .purpose-inner { gap: 12px 22px; }
        }
        @media (max-width: 575px) {
            .institution-bar { font-size: 11px; }
            .institution-bar .service-label { display: none; }
            .navbar-inner { min-height: 76px; gap: 12px; }
            .brand { gap: 8px; }
            .brand-mark { width: 38px; height: 38px; }
            .brand-name { font-size: 14px; }
            .brand-name small { font-size: 10px; }
            .mobile-toggle { padding: 8px; }
            .mobile-toggle .toggle-label { display: none; }
            .mobile-menu .nav-actions { flex-direction: column-reverse; align-items: stretch; }
            .hero h1 { font-size: 30px; }
            .hero .lead { font-size: 15px; }
            .hero-cta { flex-direction: column; align-items: stretch; }
            .hero-note { align-items: flex-start; }
            .service-directory { padding: 20px; }
            .purpose-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
            .purpose-inner > strong { grid-column: 1 / -1; }
            section { padding: 48px 0; }
            .section-heading { display: block; }
            .section-link { margin-top: 18px; }
            .steps-grid { grid-template-columns: 1fr; }
            .step-item + .step-item { border-left: 0; border-top: 1px solid var(--line); }
            .step-item { padding: 22px; }
            .step-top { margin-bottom: 15px; }
            .cta-band { align-items: stretch; flex-direction: column; gap: 20px; }
            .cta-band h2 { font-size: 22px; }
            section[id], header[id] { scroll-margin-top: 94px; }
        }
        @media (prefers-reduced-motion: reduce) { html { scroll-behavior: auto; } }
    </style>
</head>
<body>
<a class="skip-link" href="#mainContent">ข้ามไปยังเนื้อหา</a>
<div class="institution-bar">
    <div class="container"><span>มหาวิทยาลัยราชภัฏนครราชสีมา</span><span class="service-label">บริการ Data Center และ Web Hosting</span></div>
</div>
<nav class="navbar-custom" id="navbar" aria-label="เมนูหลัก">
    <div class="container navbar-inner">
        <a href="{{ route('home') }}" class="brand" aria-label="สำนักคอมพิวเตอร์ หน้าหลัก">
            <img src="{{ asset('images/logo.png') }}" class="brand-mark" alt="">
            <div class="brand-name">สำนักคอมพิวเตอร์<small>มหาวิทยาลัยราชภัฏนครราชสีมา</small></div>
        </a>
        <div class="nav-desktop">
            <div class="nav-links-group">
                <a href="#top" class="nav-link-custom" aria-current="location"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m3 10 9-7 9 7M5 9v12h5v-7h4v7h5V9"/></svg><span>หน้าหลัก</span></a>
                    <a href="#services" class="nav-link-custom"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="3" width="18" height="7" rx="2"/><rect x="3" y="14" width="18" height="7" rx="2"/><path d="M7 6.5h.01M7 17.5h.01M11 6.5h6M11 17.5h6"/></svg><span>บริการของเรา</span></a>
                    <a href="#steps" class="nav-link-custom"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="4" y="4" width="16" height="17" rx="2"/><path d="M9 3h6v3H9ZM8 11h.01M12 11h5M8 16h.01M12 16h5"/></svg><span>ขั้นตอนการขอใช้</span></a>
                    <a href="#policy" class="nav-link-custom"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m12 3 8 4v5c0 5-8 9-8 9s-8-4-8-9V7Z"/><path d="m8 12 3 3 5-6"/></svg><span>ข้อกำหนด</span></a>
            </div>
            <div class="nav-actions">
                <a href="{{ route('admin.requests.index') }}" class="staff-link"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/></svg> เจ้าหน้าที่</a>
                <a href="{{ route('service-requests.create') }}" class="btn-request">ยื่นคำขอใช้บริการ <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a>
            </div>
        </div>
        <button type="button" class="mobile-toggle" id="mobileToggle" aria-label="เปิดเมนูหลัก" aria-expanded="false" aria-controls="mobileMenu">
            <svg class="ui-icon menu-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg class="ui-icon close-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M6 18 18 6"/></svg>
            <span class="toggle-label">เมนู</span>
        </button>
    </div>
    <div class="mobile-menu" id="mobileMenu" hidden>
        <div class="container">
            <a href="#top" class="nav-link-custom" aria-current="location"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m3 10 9-7 9 7M5 9v12h5v-7h4v7h5V9"/></svg><span>หน้าหลัก</span></a>
                    <a href="#services" class="nav-link-custom"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="3" width="18" height="7" rx="2"/><rect x="3" y="14" width="18" height="7" rx="2"/><path d="M7 6.5h.01M7 17.5h.01M11 6.5h6M11 17.5h6"/></svg><span>บริการของเรา</span></a>
                    <a href="#steps" class="nav-link-custom"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="4" y="4" width="16" height="17" rx="2"/><path d="M9 3h6v3H9ZM8 11h.01M12 11h5M8 16h.01M12 16h5"/></svg><span>ขั้นตอนการขอใช้</span></a>
                    <a href="#policy" class="nav-link-custom"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m12 3 8 4v5c0 5-8 9-8 9s-8-4-8-9V7Z"/><path d="m8 12 3 3 5-6"/></svg><span>ข้อกำหนด</span></a>
            <div class="nav-actions">
                <a href="{{ route('admin.requests.index') }}" class="staff-link"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/></svg> สำหรับเจ้าหน้าที่</a>
                <a href="{{ route('service-requests.create') }}" class="btn-request">ยื่นคำขอใช้บริการ <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a>
            </div>
        </div>
    </div>
</nav>
<main id="mainContent" tabindex="-1">
    <header class="hero" id="top">
        <div class="container hero-layout">
            <div>
                <div class="hero-context"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="3" width="18" height="7" rx="2"/><rect x="3" y="14" width="18" height="7" rx="2"/><path d="M7 6.5h.01M7 17.5h.01M11 6.5h6M11 17.5h6"/></svg> Data Center &amp; Web Hosting</div>
                <h2>ระบบการจัดการคำขอใช้บริการ<br>Web Hosting และ Virtual Server</h2>
                <p class="lead">ขอใช้บริการ Web Hosting และเครื่องแม่ข่ายเสมือนสำหรับบุคลากรและหน่วยงาน มหาวิทยาลัยราชภัฏนครราชสีมา ผ่านแบบฟอร์มออนไลน์ในที่เดียว</p>
                <div class="hero-cta">
                    <a href="{{ route('service-requests.create') }}" class="btn-amber"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m15 5 4 4M4 20l4-1L21 6a2.8 2.8 0 0 0-4-4L4 15ZM3 22h18"/></svg> ยื่นคำขอใช้บริการ</a>
                    <a href="#steps" class="btn-outline-brand">ดูขั้นตอนการขอใช้ <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a>
                </div>
                <p class="hero-note"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m12 3 8 4v5c0 5-8 9-8 9s-8-4-8-9V7Z"/><path d="m8 12 3 3 5-6"/></svg> พิจารณาคำขอและดูแลบริการโดยสำนักคอมพิวเตอร์</p>
            </div>
            <section class="service-directory" id="services" aria-labelledby="servicesTitle">
                <div class="directory-head"><h2 id="servicesTitle">เลือกบริการให้เหมาะกับงาน</h2><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="3" width="18" height="7" rx="2"/><rect x="3" y="14" width="18" height="7" rx="2"/><path d="M7 6.5h.01M7 17.5h.01M11 6.5h6M11 17.5h6"/></svg></div>
                <div class="service-row">
                    <span class="service-icon"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><ellipse cx="12" cy="12" rx="4" ry="9"/><path d="M3 12h18"/></svg></span>
                    <div><h3>Web Hosting</h3><p>พื้นที่สำหรับเว็บไซต์ของหน่วยงาน โครงการ และงานประชาสัมพันธ์</p></div>
                </div>
                <div class="service-row">
                    <span class="service-icon"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="3" width="18" height="7" rx="2"/><rect x="3" y="14" width="18" height="7" rx="2"/><path d="M7 6.5h.01M7 17.5h.01M11 6.5h6M11 17.5h6"/></svg></span>
                    <div><h3>Virtual Server</h3><p>เครื่องแม่ข่ายเสมือนสำหรับระบบงานที่ต้องการระบุ CPU, RAM และพื้นที่จัดเก็บ</p></div>
                </div>
                <a href="{{ route('service-requests.create') }}" class="directory-link"><span>ระบุบริการและทรัพยากรในแบบฟอร์ม</span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a>
            </section>
        </div>
    </header>
    <div class="purpose-strip">
        <div class="container purpose-inner">
            <strong>รองรับการใช้งานเพื่อ</strong>
            <span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m5 12 4 4L19 6"/></svg> การเรียนการสอน</span>
            <span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m5 12 4 4L19 6"/></svg> งานวิจัย</span>
            <span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m5 12 4 4L19 6"/></svg> บริการวิชาการ</span>
            <span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m5 12 4 4L19 6"/></svg> งานบริหารหน่วยงาน</span>
        </div>
    </div>
    <section id="steps">
        <div class="container">
            <div class="section-heading">
                <div><h2 class="section-title">ขั้นตอนการขอใช้บริการ</h2><p class="section-desc">เตรียมข้อมูลให้ครบ แล้วส่งคำขอให้เจ้าหน้าที่พิจารณา</p></div>
                <a href="{{ route('service-requests.create') }}" class="section-link">ไปที่แบบฟอร์ม <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a>
            </div>
            <div class="steps-grid">
                <div class="step-item"><div class="step-top"><span class="step-num">1</span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m15 5 4 4M4 20l4-1L21 6a2.8 2.8 0 0 0-4-4L4 15ZM3 22h18"/></svg></div><div class="step-title">กรอกแบบฟอร์ม</div><div class="step-desc">ระบุข้อมูลผู้ขอ วัตถุประสงค์ และทรัพยากรที่ต้องการใช้งาน</div></div>
                <div class="step-item"><div class="step-top"><span class="step-num">2</span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8ZM14 2v6h6M8 13h8M8 17h5"/></svg></div><div class="step-title">แนบเอกสาร</div><div class="step-desc">เตรียมรายละเอียดระบบหรือโครงสร้างระบบ พร้อมรูปลายเซ็นผู้ขอ</div></div>
                <div class="step-item"><div class="step-top"><span class="step-num">3</span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m12 3 8 4v5c0 5-8 9-8 9s-8-4-8-9V7Z"/><path d="m8 12 3 3 5-6"/></svg></div><div class="step-title">รอเจ้าหน้าที่พิจารณา</div><div class="step-desc">เจ้าหน้าที่ตรวจสอบข้อมูลและพิจารณาอนุมัติคำขอใช้บริการ</div></div>
                <div class="step-item"><div class="step-top"><span class="step-num">4</span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="8" cy="8" r="5"/><path d="m12 12 9 9m-4-4 3-3m-6 0 3-3"/></svg></div><div class="step-title">รับข้อมูลบัญชีบริการ</div><div class="step-desc">เจ้าหน้าที่จัดการบัญชีบริการหลังคำขอได้รับการอนุมัติ</div></div>
            </div>
        </div>
    </section>
    <section id="policy" class="policy-section">
        <div class="container policy-layout">
            <div>
                <h2 class="section-title">ก่อนเริ่มใช้บริการ</h2>
                <p class="section-desc">อ่านข้อกำหนดและเตรียมเอกสารประกอบคำขอ เพื่อให้เจ้าหน้าที่ตรวจสอบข้อมูลได้ครบถ้วน</p>
                <div class="prepare-note">
                    <strong>สิ่งที่ต้องเตรียม</strong>
                    <p><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/></svg> ข้อมูลผู้ขอและผู้รับผิดชอบระบบ</p>
                    <p><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8ZM14 2v6h6M8 13h8M8 17h5"/></svg> เอกสารรายละเอียดหรือโครงสร้างระบบ</p>
                    <p><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m15 5 4 4M4 20l4-1L21 6a2.8 2.8 0 0 0-4-4L4 15ZM3 22h18"/></svg> รูปลายเซ็นผู้ขอใช้บริการ</p>
                </div>
            </div>
            <ul class="policy-list">
                    <li><span class="check"><svg aria-hidden="true" focusable="false" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg></span> ปฏิบัติตาม พ.ร.บ. ว่าด้วยการกระทำความผิดเกี่ยวกับคอมพิวเตอร์ และ พ.ร.บ. การรักษาความมั่นคงปลอดภัยไซเบอร์</li>
                    <li><span class="check"><svg aria-hidden="true" focusable="false" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg></span> ปฏิบัติตามนโยบายคุ้มครองข้อมูลส่วนบุคคล (PDPA) ของมหาวิทยาลัย</li>
                    <li><span class="check"><svg aria-hidden="true" focusable="false" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg></span> ผู้ใช้บริการมีหน้าที่สำรองข้อมูล (Backup) และดูแลความปลอดภัยระบบของตนเองอย่างสม่ำเสมอ</li>
                    <li><span class="check"><svg aria-hidden="true" focusable="false" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg></span> ระยะเวลาการใช้งานไม่เกิน 1 ปีต่อครั้ง สามารถยื่นขอต่ออายุได้ก่อนวันหมดอายุ</li>
                    <li><span class="check"><svg aria-hidden="true" focusable="false" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg></span> สำนักคอมพิวเตอร์มีสิทธิ์ระงับหรือยกเลิกบริการทันทีหากพบการใช้งานที่กระทบต่อระบบส่วนรวม</li>
                </ul>
        </div>
    </section>
    <section class="cta-section">
        <div class="container cta-band">
            <div><h2 class="section-title">เริ่มต้นคำขอใช้บริการของคุณ</h2><p class="section-desc">กรอกข้อมูล เลือกบริการ และแนบเอกสารผ่านแบบฟอร์มออนไลน์</p></div>
            <a href="{{ route('service-requests.create') }}" class="btn-request">ยื่นคำขอใช้บริการ <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a>
        </div>
    </section>
</main>
<footer>
    <div class="container d-flex flex-wrap justify-content-between gap-3">
        <div>
            <div class="f-brand mb-1">สำนักคอมพิวเตอร์ มหาวิทยาลัยราชภัฏนครราชสีมา</div>
            340 ถ.สุรนารายณ์ ต.ในเมือง อ.เมือง จ.นครราชสีมา 30000
        </div>
        <div>
            <a href="{{ url('/service-requests/create') }}">ยื่นคำขอใช้บริการ</a> &nbsp;|&nbsp;
            <a href="{{ url('/admin/requests') }}">สำหรับเจ้าหน้าที่</a>
        </div>
    </div>
</footer>
<script>
    const navbar = document.getElementById('navbar');
    const toggle = document.getElementById('mobileToggle');
    const menu = document.getElementById('mobileMenu');
    const desktop = window.matchMedia('(min-width: 1200px)');

    function setMenu(open, restoreFocus = false) {
        menu.hidden = !open;
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'ปิดเมนูหลัก' : 'เปิดเมนูหลัก');
        if (restoreFocus) toggle.focus();
    }

    toggle.addEventListener('click', () => setMenu(menu.hidden));
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !menu.hidden) setMenu(false, true);
    });
    document.addEventListener('click', event => {
        if (!menu.hidden && !navbar.contains(event.target)) setMenu(false);
    });
    navbar.addEventListener('focusout', () => {
        requestAnimationFrame(() => {
            if (!menu.hidden && !navbar.contains(document.activeElement)) setMenu(false);
        });
    });
    desktop.addEventListener('change', () => {
        const focusWasInMenu = menu.contains(document.activeElement);
        setMenu(false);
        if (desktop.matches && focusWasInMenu) navbar.querySelector('.brand').focus();
    });
    menu.querySelectorAll('a').forEach(link => link.addEventListener('click', () => setMenu(false)));

    const sectionLinks = [...navbar.querySelectorAll('a[href^="#"]')];
    const sections = [...new Set(sectionLinks.map(link => document.querySelector(link.getAttribute('href'))))];
    function updateNavigation() {
        navbar.classList.toggle('scrolled', window.scrollY > 8);
        let current = 'top';
        const position = navbar.getBoundingClientRect().bottom + 32;
        sections.forEach(section => {
            if (section.getBoundingClientRect().top <= position) current = section.id;
        });
        sectionLinks.forEach(link => {
            if (link.getAttribute('href') === '#' + current) link.setAttribute('aria-current', 'location');
            else link.removeAttribute('aria-current');
        });
    }
    let scrollPending = false;
    window.addEventListener('scroll', () => {
        if (scrollPending) return;
        scrollPending = true;
        requestAnimationFrame(() => { updateNavigation(); scrollPending = false; });
    }, { passive: true });
    window.addEventListener('resize', updateNavigation);
    sectionLinks.forEach(link => link.addEventListener('click', event => {
        event.preventDefault();
        setMenu(false);
        const target = document.querySelector(link.getAttribute('href'));
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        target.setAttribute('tabindex', '-1');
        target.focus({ preventScroll: true });
        target.scrollIntoView({ behavior: reduceMotion ? 'instant' : 'smooth', block: 'start' });
        history.replaceState(null, '', link.getAttribute('href'));
        updateNavigation();
    }));
    updateNavigation();
</script>
</body>
</html>
