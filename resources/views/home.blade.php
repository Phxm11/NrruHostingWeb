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

        /* Scroll progress bar */
        .scroll-progress {
            position: fixed; top: 0; left: 0; height: 3px; width: 0%;
            background: linear-gradient(90deg, var(--moss), var(--amber));
            z-index: 100; transition: width .1s ease-out;
        }

        /* Reveal on scroll */
        .reveal { opacity: 0; transform: translateY(20px); transition: opacity .7s cubic-bezier(.2,.7,.2,1), transform .7s cubic-bezier(.2,.7,.2,1); }
        .reveal.in { opacity: 1; transform: none; }

        /* ---------- Navbar ---------- */
        .navbar-custom {
            position: sticky; top: 0; z-index: 60;
            padding: 16px 20px 0; background: transparent;
            transition: padding .25s ease;
        }
        .navbar-custom.scrolled { padding-top: 9px; }
        .navbar-inner {
            max-width: 1180px; margin: 0 auto;
            background: rgba(255,255,255,.88); backdrop-filter: blur(14px);
            border: 1px solid var(--line); border-radius: 999px;
            padding: 8px 10px 8px 18px;
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            box-shadow: var(--shadow-sm);
            transition: box-shadow .25s ease;
        }
        .navbar-custom.scrolled .navbar-inner { box-shadow: var(--shadow-md); }
        .brand-badge { position: relative; width: 38px; height: 38px; flex-shrink: 0; }
        .brand-badge::before {
            content: ''; position: absolute; inset: -5px;
            border: 1.5px dashed rgba(108,151,82,.5); border-radius: 14px; pointer-events: none;
        }
        .brand-mark {
            width: 38px; height: 38px; border-radius: 11px;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,.14);
            padding: 4px;
        }
        .brand-mark img { width: 100%; height: 100%; object-fit: contain; display: block; }
        .brand-name { font-family: 'Kanit', sans-serif; font-weight: 600; font-size: 14.5px; color: var(--forest); line-height: 1.2; }
        .brand-name small { display: block; font-weight: 400; font-size: 10.5px; color: var(--ink-soft); }

        .nav-links-group {
            display: flex; align-items: center; gap: 2px;
            background: var(--moss-light); border-radius: 999px; padding: 4px;
        }
        .nav-link-custom {
            color: var(--ink); font-size: 14px; font-weight: 500;
            padding: 8px 15px; border-radius: 999px;
            transition: background .15s ease, color .15s ease, box-shadow .15s ease;
        }
        .nav-link-custom:hover { background: #fff; color: var(--forest); box-shadow: var(--shadow-sm); }
        .nav-link-staff {
            display: inline-flex; align-items: center; gap: 6px;
            color: var(--ink-soft); font-size: 13.5px; font-weight: 500;
            padding: 8px 6px; border-left: 1px solid var(--line); padding-left: 16px;
            transition: color .15s ease;
        }
        .nav-link-staff:hover { color: var(--forest); }
        .nav-link-staff svg { flex-shrink: 0; opacity: .8; }

        .btn-amber { background: linear-gradient(135deg, var(--amber), var(--amber-deep)); border: none; color: #2c1e05; font-weight: 600; border-radius: 999px; padding: 10px 22px; font-size: 14px; box-shadow: 0 6px 16px -6px rgba(185,132,15,.6); transition: transform .15s ease, filter .15s ease; }
        .btn-amber:hover { filter: brightness(1.04); color: #2c1e05; transform: translateY(-1px); }
        .btn-outline-brand { border: 1.5px solid rgba(255,255,255,.5); color: #fff; border-radius: 12px; padding: 9px 20px; font-size: 14px; font-weight: 500; transition: background .15s ease; }
        .btn-outline-brand:hover { background: rgba(255,255,255,.14); color: #fff; }

        .mobile-toggle { display: none; background: none; border: 1px solid var(--line); border-radius: 999px; padding: 8px 11px; cursor: pointer; }
        .mobile-menu {
            display: none; flex-direction: column; gap: 3px;
            max-width: 1180px; margin: 8px auto 0;
            background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius);
            padding: 12px; box-shadow: var(--shadow-sm);
        }
        .mobile-menu.open { display: flex; }
        .mobile-menu .nav-link-custom { padding: 10px 14px; }
        .mobile-menu .nav-link-staff { border-left: none; padding-left: 14px; border-top: 1px solid var(--line); padding-top: 12px; margin-top: 4px; }

        /* ---------- Hero ---------- */
        .hero {
            position: relative; overflow: hidden;
            background: radial-gradient(120% 120% at 80% -10%, var(--forest-2) 0%, var(--forest) 55%, #142a1a 100%);
            color: #eef2e6; padding: 92px 0 120px;
        }
        .hero::before {
            content: ''; position: absolute; width: 540px; height: 540px;
            background: radial-gradient(circle, rgba(224,165,38,.28) 0%, rgba(224,165,38,0) 68%);
            top: -180px; right: -120px; pointer-events: none;
        }
        .hero::after {
            content: ''; position: absolute; width: 420px; height: 420px;
            background: radial-gradient(circle, rgba(108,151,82,.30) 0%, rgba(108,151,82,0) 70%);
            bottom: -200px; left: -120px; pointer-events: none;
        }
        /* Signature: topographic contour rings — a reserve-map motif that
           carries through every screen (hero, sidebar, forms) instead of a
           generic dot grid. */
        .hero-grid {
            position: absolute; inset: 0; opacity: .5; pointer-events: none;
            background-image:
                repeating-radial-gradient(circle at 82% 6%, rgba(255,255,255,.10) 0px, rgba(255,255,255,.10) 1px, transparent 1px, transparent 20px),
                repeating-radial-gradient(circle at 8% 92%, rgba(224,165,38,.16) 0px, rgba(224,165,38,.16) 1px, transparent 1px, transparent 16px);
            mask-image: radial-gradient(90% 70% at 70% 25%, #000, transparent);
        }
        .hero-eyebrow {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 12.5px; letter-spacing: .08em; text-transform: uppercase;
            color: #f0c25c; font-weight: 600; background: rgba(240,194,92,.12);
            padding: 6px 14px; border-radius: 999px; border: 1px solid rgba(240,194,92,.25); margin-bottom: 22px;
        }
        .hero h1 { font-size: clamp(30px, 5vw, 46px); font-weight: 700; line-height: 1.22; margin-bottom: 20px; }
        .hero h1 .accent { color: var(--amber); }
        .hero p.lead { font-size: 17px; color: #d8e0d0; max-width: 560px; margin-bottom: 32px; }
        .hero-cta { display: flex; gap: 14px; flex-wrap: wrap; }

        .hero-stats { display: flex; gap: 34px; margin-top: 46px; flex-wrap: wrap; }
        .hero-stats .hs { position: relative; z-index: 1; }
        .hero-stats .hs b { font-family: 'Kanit', sans-serif; font-size: 26px; font-weight: 700; display: block; }
        .hero-stats .hs span { font-size: 13px; color: #aebda0; }

        /* ---------- Sections ---------- */
        section { padding: 84px 0; }
        .section-eyebrow { font-size: 13px; color: var(--moss); font-weight: 600; text-transform: uppercase; letter-spacing: .07em; margin-bottom: 10px; }
        .section-title { font-size: clamp(24px, 3.5vw, 31px); font-weight: 700; margin-bottom: 14px; }
        .section-desc { color: var(--ink-soft); font-size: 15.5px; max-width: 620px; }

        .plan-card {
            position: relative; background: var(--surface); border: 1px solid var(--line);
            border-radius: var(--radius); padding: 28px 26px; height: 100%;
            box-shadow: var(--shadow-sm); transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }
        .plan-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); border-color: #d9d3bf; }
        .plan-card.featured { border-color: var(--amber); box-shadow: var(--shadow-md); }
        .plan-card.featured::before {
            content: 'แนะนำ'; position: absolute; top: -11px; left: 26px;
            background: linear-gradient(135deg, var(--amber), var(--amber-deep)); color: #2c1e05;
            font-size: 11.5px; font-weight: 700; padding: 4px 12px; border-radius: 999px;
        }
        .plan-badge {
            display: inline-block; font-size: 11.5px; font-weight: 600; padding: 5px 12px;
            border-radius: 999px; background: var(--moss-light); color: var(--forest); margin-bottom: 14px;
        }
        .plan-price { font-family: 'Kanit', sans-serif; font-size: 28px; font-weight: 700; color: var(--forest); line-height: 1; }
        .plan-price span { font-size: 13px; font-weight: 400; color: var(--ink-soft); }
        .plan-divider { height: 1px; background: var(--line); margin: 18px 0; }
        .plan-card ul { list-style: none; padding: 0; margin: 0; font-size: 14px; }
        .plan-card ul li { padding: 6px 0; display: flex; align-items: center; gap: 9px; color: var(--ink); }
        .plan-card ul li svg { flex-shrink: 0; color: var(--moss); }
        .plan-suitable { margin-top: 16px; font-size: 13px; color: var(--ink-soft); background: var(--bg); border-radius: 12px; padding: 10px 14px; }

        .group-label { display: flex; align-items: center; gap: 10px; margin: 4px 0 22px; }
        .group-label .dot { width: 9px; height: 9px; border-radius: 50%; background: var(--moss); }
        .group-label h3 { font-size: 18px; font-weight: 600; margin: 0; }

        /* Steps timeline */
        .steps-wrap { display: grid; gap: 0; }
        .step-item { display: flex; gap: 20px; padding-bottom: 28px; position: relative; }
        .step-item:last-child { padding-bottom: 0; }
        .step-item::before {
            content: ''; position: absolute; left: 23px; top: 46px; bottom: 0; width: 2px;
            background: linear-gradient(var(--line), transparent);
        }
        .step-item:last-child::before { display: none; }
        .step-num {
            width: 46px; height: 46px; border-radius: 14px; flex-shrink: 0; position: relative; z-index: 1;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Kanit', sans-serif; font-weight: 700; font-size: 18px; color: #fff;
            background: linear-gradient(135deg, var(--forest), var(--forest-2)); box-shadow: var(--shadow-sm);
        }
        .step-item:nth-child(even) .step-num { background: linear-gradient(135deg, var(--amber), var(--amber-deep)); color: #2c1e05; }
        .step-body { padding-top: 4px; }
        .step-title { font-weight: 600; font-size: 16px; margin-bottom: 4px; }
        .step-desc { color: var(--ink-soft); font-size: 14.5px; }

        .panel-soft { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius); padding: 30px 32px; box-shadow: var(--shadow-sm); }

        .policy-list { list-style: none; padding: 0; margin: 0; display: grid; gap: 14px; }
        .policy-list li {
            display: flex; gap: 14px; font-size: 14.5px; color: var(--ink);
            background: var(--surface); border: 1px solid var(--line); border-radius: 14px; padding: 16px 18px;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .policy-list li:hover { transform: translateX(3px); box-shadow: var(--shadow-sm); }
        .policy-list .check {
            flex-shrink: 0; width: 26px; height: 26px; border-radius: 8px; display: flex; align-items: center; justify-content: center;
            background: var(--moss-light); color: var(--forest); margin-top: 1px;
        }

        .cta-band {
            position: relative; overflow: hidden;
            background: linear-gradient(120deg, var(--moss-light), var(--amber-light));
            border: 1px solid #ecdfb8; border-radius: 26px; padding: 52px 44px; text-align: center;
        }
        .cta-band::after {
            content: ''; position: absolute; width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(224,165,38,.35), transparent 70%); bottom: -120px; right: -60px; pointer-events: none;
        }

        footer { background: var(--forest); color: #cfd8c6; padding: 44px 0 30px; font-size: 13.5px; }
        footer a { color: #eef2e6; }
        footer a:hover { color: var(--amber); }
        footer .f-brand { font-family: 'Kanit', sans-serif; color: #fff; font-size: 15px; font-weight: 600; }

        @media (max-width: 767px) {
            .nav-desktop { display: none; }
            .mobile-toggle { display: inline-flex; }
            .hero { padding: 64px 0 84px; }
            section { padding: 60px 0; }
        }
    </style>
</head>
<body>

<div class="scroll-progress" id="scrollProgress"></div>

<nav class="navbar-custom" id="navbar">
    <div class="navbar-inner">
        <a href="{{ url('/') }}" class="d-flex align-items-center gap-2">
            <div class="brand-badge">
                <div class="brand-mark">
                    <img src="{{ asset('images/logo.png') }}" alt="โลโก้ระบบ">
                </div>
            </div>
            <div class="brand-name">
                สำนักคอมพิวเตอร์
                <small>มหาวิทยาลัยราชภัฏนครราชสีมา</small>
            </div>
        </a>
        <div class="nav-desktop d-none d-md-flex align-items-center gap-2">
            <div class="nav-links-group">
                <a href="#services" class="nav-link-custom">บริการ</a>
                <a href="#steps" class="nav-link-custom">ขั้นตอนการขอใช้</a>
                <a href="#policy" class="nav-link-custom">ข้อกำหนด</a>
            </div>
            <a href="{{ route('admin.requests.index') }}" class="nav-link-staff">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                สำหรับเจ้าหน้าที่
            </a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('service-requests.create') }}" class="btn-amber d-none d-sm-inline-block">ยื่นคำขอใช้บริการ</a>
            <button class="mobile-toggle d-md-none" id="mobileToggle" aria-label="เมนู">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1a3323" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
    <div class="mobile-menu" id="mobileMenu">
        <a href="#services" class="nav-link-custom">บริการ</a>
        <a href="#steps" class="nav-link-custom">ขั้นตอนการขอใช้</a>
        <a href="#policy" class="nav-link-custom">ข้อกำหนด</a>
        <a href="{{ route('service-requests.create') }}" class="btn-amber mt-1 text-center">ยื่นคำขอใช้บริการ</a>
        <a href="{{ route('admin.requests.index') }}" class="nav-link-custom nav-link-staff">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
            สำหรับเจ้าหน้าที่
        </a>
    </div>
</nav>

<header class="hero">
    <div class="hero-grid"></div>
    <div class="container">
        <div class="hero-eyebrow">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            Data Center &amp; Web Hosting Service
        </div>
        <h1>ระบบจัดการคำขอใช้บริการ<br>และ <span class="accent">Web Hosting</span> เพื่อบุคลากร มรภ.นครราชสีมา</h1>
        <p class="lead">ยื่นคำขอใช้บริการออนไลน์ได้ทันที ระบบตรวจสอบและออกบัญชีผู้ใช้ให้อัตโนมัติ รองรับทั้งงานเรียนการสอน งานวิจัย บริการวิชาการ และการบริหารจัดการภายในหน่วยงาน</p>
        <div class="hero-cta">
            <a href="{{ route('service-requests.create') }}" class="btn-amber">เริ่มยื่นคำขอใช้บริการ</a>
            <a href="#services" class="btn-outline-brand">ดูแพ็กเกจบริการ</a>
        </div>
        <div class="hero-stats">
            <div class="hs"><b>24/7</b><span>ดูแลโดยสำนักคอมพิวเตอร์</span></div>
            <div class="hs"><b>&lt; 10 นาที</b><span>เวลากรอกแบบฟอร์ม</span></div>
            <div class="hs"><b>อัตโนมัติ</b><span>ออกบัญชีผู้ใช้</span></div>
        </div>
    </div>
</header>

<section id="services">
    <div class="container">
        <div class="reveal">
            <div class="section-eyebrow">แพ็กเกจบริการ</div>
            <h2 class="section-title">เลือกบริการที่เหมาะกับระบบของคุณ</h2>
            <p class="section-desc mb-5">ราคาต่อปี ครอบคลุมพื้นที่จัดเก็บข้อมูลและทรัพยากรพื้นฐาน สามารถระบุความต้องการเพิ่มเติมในแบบฟอร์มได้</p>
        </div>

        <div class="group-label reveal"><span class="dot"></span><h3>เครื่องแม่ข่ายเสมือน (Virtual Server)</h3></div>
        <div class="row g-4 mb-5">
            @foreach ($virtualServerPlans as $plan)
                <div class="col-md-4 reveal">
                    <div class="plan-card {{ $loop->iteration === 2 ? 'featured' : '' }}">
                        <span class="plan-badge">{{ $plan->size_label }}</span>
                        <div class="plan-price">{{ number_format($plan->fee_per_year, 0) }} <span>บาท/ปี</span></div>
                        <div class="plan-divider"></div>
                        <ul>
                            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg> {{ $plan->cpu_vcpu }} vCPU</li>
                            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg> RAM {{ $plan->ram_gb }} GB</li>
                            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg> Storage {{ $plan->storage_gb }} GB</li>
                        </ul>
                        <div class="plan-suitable">เหมาะสำหรับ {{ $plan->suitable_for }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="group-label reveal"><span class="dot"></span><h3>Web Hosting</h3></div>
        <div class="row g-4">
            @foreach ($webHostingPlans as $plan)
                <div class="col-md-4 reveal">
                    <div class="plan-card {{ $loop->iteration === 2 ? 'featured' : '' }}">
                        <span class="plan-badge">{{ $plan->size_label }}</span>
                        <div class="plan-price">{{ number_format($plan->fee_per_year, 0) }} <span>บาท/ปี</span></div>
                        <div class="plan-divider"></div>
                        <ul>
                            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg> Storage {{ $plan->storage_gb }} GB</li>
                        </ul>
                        <div class="plan-suitable">เหมาะสำหรับ {{ $plan->suitable_for }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="steps" style="background:var(--surface);border-top:1px solid var(--line);border-bottom:1px solid var(--line);">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5 reveal">
                <div class="section-eyebrow">ขั้นตอนการขอใช้บริการ</div>
                <h2 class="section-title">ใช้เวลาไม่นาน เริ่มได้ทันที</h2>
                <p class="section-desc">กรอกแบบฟอร์มออนไลน์ครั้งเดียว แนบเอกสารที่เกี่ยวข้อง แล้วรอการพิจารณาจากสำนักคอมพิวเตอร์</p>
            </div>
            <div class="col-lg-7 reveal">
                <div class="steps-wrap">
                    <div class="step-item">
                        <div class="step-num">1</div>
                        <div class="step-body">
                            <div class="step-title">กรอกแบบฟอร์มขอใช้บริการ</div>
                            <div class="step-desc">ระบุข้อมูลผู้ขอใช้บริการ วัตถุประสงค์ และเลือกแพ็กเกจทรัพยากรที่ต้องการ</div>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">2</div>
                        <div class="step-body">
                            <div class="step-title">แนบเอกสารประกอบ</div>
                            <div class="step-desc">แนบรายละเอียดระบบ/โครงสร้างระบบ และลายเซ็นยอมรับข้อกำหนด</div>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">3</div>
                        <div class="step-body">
                            <div class="step-title">รอการพิจารณาอนุมัติ</div>
                            <div class="step-desc">เจ้าหน้าที่สำนักคอมพิวเตอร์ตรวจสอบข้อมูลและอนุมัติคำขอ</div>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">4</div>
                        <div class="step-body">
                            <div class="step-title">รับ Username / Password</div>
                            <div class="step-desc">ระบบออกบัญชีผู้ใช้บริการให้ พร้อมใช้งานตามระยะเวลาโครงการที่ระบุ</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="policy">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5 reveal">
                <div class="section-eyebrow">ข้อกำหนดและนโยบาย</div>
                <h2 class="section-title">สิ่งที่ผู้ขอใช้บริการต้องทราบ</h2>
                <p class="section-desc">ผู้ขอใช้บริการต้องปฏิบัติตามกฎหมายและระเบียบของมหาวิทยาลัยอย่างเคร่งครัด</p>
            </div>
            <div class="col-lg-7 reveal">
                <ul class="policy-list">
                    <li><span class="check"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg></span> ปฏิบัติตาม พ.ร.บ. ว่าด้วยการกระทำความผิดเกี่ยวกับคอมพิวเตอร์ และ พ.ร.บ. การรักษาความมั่นคงปลอดภัยไซเบอร์</li>
                    <li><span class="check"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg></span> ปฏิบัติตามนโยบายคุ้มครองข้อมูลส่วนบุคคล (PDPA) ของมหาวิทยาลัย</li>
                    <li><span class="check"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg></span> ผู้ใช้บริการมีหน้าที่สำรองข้อมูล (Backup) และดูแลความปลอดภัยระบบของตนเองอย่างสม่ำเสมอ</li>
                    <li><span class="check"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg></span> ระยะเวลาการใช้งานไม่เกิน 1 ปีต่อครั้ง สามารถยื่นขอต่ออายุได้ก่อนวันหมดอายุ</li>
                    <li><span class="check"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg></span> สำนักคอมพิวเตอร์มีสิทธิ์ระงับหรือยกเลิกบริการทันทีหากพบการใช้งานที่กระทบต่อระบบส่วนรวม</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section style="padding-top:0;">
    <div class="container">
        <div class="cta-band reveal">
            <h2 class="section-title mb-2">พร้อมเริ่มใช้งานแล้วใช่ไหม?</h2>
            <p class="section-desc mx-auto mb-4">ยื่นคำขอใช้บริการออนไลน์วันนี้ ใช้เวลากรอกฟอร์มไม่ถึง 10 นาที</p>
            <a href="{{ route('service-requests.create') }}" class="btn-amber">ยื่นคำขอใช้บริการตอนนี้</a>
        </div>
    </div>
</section>

<footer>
    <div class="container d-flex flex-wrap justify-content-between gap-3">
        <div>
            <div class="f-brand mb-1">สำนักคอมพิวเตอร์ มหาวิทยาลัยราชภัฏนครราชสีมา</div>
            340 ถ.สุรนารายณ์ ต.ในเมือง อ.เมือง จ.นครราชสีมา 30000
        </div>
        <div>
            <a href="{{ route('service-requests.create') }}">ยื่นคำขอใช้บริการ</a> &nbsp;|&nbsp;
            <a href="{{ route('admin.requests.index') }}">สำหรับเจ้าหน้าที่</a>
        </div>
    </div>
</footer>

<script>
    // Scroll progress bar
    const progress = document.getElementById('scrollProgress');
    const navbar = document.getElementById('navbar');
    function onScroll() {
        const h = document.documentElement;
        const scrolled = h.scrollTop / (h.scrollHeight - h.clientHeight) * 100;
        progress.style.width = scrolled + '%';
        navbar.classList.toggle('scrolled', h.scrollTop > 8);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // Mobile menu toggle
    const toggle = document.getElementById('mobileToggle');
    const menu = document.getElementById('mobileMenu');
    toggle.addEventListener('click', () => menu.classList.toggle('open'));
    menu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => menu.classList.remove('open')));

    // Reveal on scroll
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach((el, i) => {
        el.style.transitionDelay = (Math.min(i, 6) * 40) + 'ms';
        io.observe(el);
    });
</script>

</body>
</html>
