<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>แบบฟอร์มขอใช้บริการระบบ Data Center และ Web Hosting</title>
    @include('partials.site-icons')
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
            --rust: #ae4830;
            --rust-light: #f6e1d8;
            --radius: 20px;
            --shadow-sm: 0 1px 2px rgba(21,35,26,.06);
            --shadow-md: 0 14px 36px -16px rgba(21,35,26,.28);
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Sarabun', sans-serif; background: var(--bg); color: var(--ink); margin: 0; line-height: 1.6; -webkit-font-smoothing: antialiased; }
        h1, h2, h3, .display-font { font-family: 'Kanit', sans-serif; letter-spacing: -.01em; }

        /* ---------- Header ---------- */
        .page-header {
            position: relative; overflow: hidden;
            background: radial-gradient(120% 130% at 85% -20%, var(--forest-2) 0%, var(--forest) 60%, #142a1a 100%);
            color: #fff; padding: 40px 0;
        }
        .page-header::before {
            content: ''; position: absolute; width: 360px; height: 360px;
            background: radial-gradient(circle, rgba(224,165,38,.28), transparent 70%); top: -120px; right: -80px; pointer-events: none;
        }
        .page-header::after {
            content: ''; position: absolute; inset: 0; pointer-events: none; opacity: .5;
            background-image: repeating-radial-gradient(circle at 88% 10%, rgba(255,255,255,.09) 0px, rgba(255,255,255,.09) 1px, transparent 1px, transparent 18px);
            mask-image: radial-gradient(85% 100% at 80% 0%, #000, transparent);
        }
        .page-header h1 { font-size: clamp(20px, 3vw, 26px); font-weight: 700; margin: 0; }
        .page-header p { font-size: 14px; margin: 6px 0 0; color: #cbd6c1; }
        .page-header .brand-row { display: flex; align-items: center; gap: 12px; position: relative; z-index: 1; }


        .form-icon { width: 20px; height: 20px; flex-shrink: 0; display: inline-block; vertical-align: middle; fill: none; stroke: currentColor; stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round; }
        a:focus-visible, button:focus-visible { outline: 3px solid var(--amber-deep); outline-offset: 4px; }
        .request-container { max-width: 1180px; padding-top: 26px; padding-bottom: 40px; }
        .form-breadcrumb { display: flex; align-items: center; gap: 10px; margin-bottom: 24px; color: var(--ink-soft); font-size: 13px; }
        .form-breadcrumb a { display: inline-flex; align-items: center; gap: 7px; color: var(--forest); text-decoration: none; }
        .form-breadcrumb a:hover { text-decoration: underline; }
        .form-intro { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 28px; }
        .form-intro h2 { font-size: 26px; margin: 0 0 6px; color: var(--forest); }
        .form-intro p { color: var(--ink-soft); margin: 0; font-size: 14px; }
        .required-note { flex-shrink: 0; font-size: 13px; padding: 8px 12px; border: 1px solid var(--line); border-radius: 8px; background: var(--surface); }
        .required-note span { color: var(--rust); font-weight: 700; }
        .form-layout { display: grid; grid-template-columns: 240px minmax(0, 1fr); gap: 28px; align-items: start; }
        .side-nav { position: sticky; top: 24px; }
        .nav-card { padding: 20px 0; }
        .nav-title { font-family: 'Sarabun', sans-serif; font-weight: 600; color: var(--forest); font-size: 16px; margin: 0 0 16px 12px; }
        .step-link { display: flex; align-items: center; gap: 12px; padding: 12px; margin-bottom: 6px; border: 1px solid transparent; border-radius: 12px; color: var(--ink-soft); text-decoration: none; font-size: 14px; font-weight: 500; transition: background .15s, border-color .15s; }
        .step-link small { display: block; font-size: 11px; margin-bottom: 1px; color: var(--ink-soft); }
        .step-link .num { width: 38px; height: 38px; display: grid; place-items: center; flex-shrink: 0; border: 1px solid var(--line); background: var(--surface); border-radius: 11px; }
        .step-link:hover { background: #eeeee3; color: var(--forest); }
        .step-link.active { background: var(--surface); color: var(--forest); border-color: var(--line); box-shadow: 0 3px 10px rgba(21,35,26,.04); }
        .step-link.active .num { color: white; background: var(--forest); border-color: var(--forest); }
        .side-hint { margin: 22px 12px 0; border-top: 1px solid var(--line); padding-top: 22px; color: var(--ink-soft); font-size: 13px; }
        .side-hint h3 { font-family: 'Sarabun', sans-serif; font-size: 14px; color: var(--forest); display: flex; gap: 8px; align-items: center; margin: 0 0 12px; }
        .side-hint p { margin: 0 0 8px; display: flex; gap: 8px; align-items: flex-start; }
        .side-hint p .form-icon { width: 16px; height: 20px; color: var(--amber-deep); }
        .side-hint small { display: block; margin-top: 14px; line-height: 1.7; }
        .mobile-stepper { display: none; position: sticky; top: 0; z-index: 40; background: var(--bg); border-bottom: 1px solid var(--line); padding: 10px 0; }
        .ms-track { display: flex; gap: 4px; }
        .ms-dot { flex: 1; min-width: 0; text-align: center; text-decoration: none; font-size: 11px; color: var(--ink-soft); border-radius: 8px; padding: 5px 0; }
        .ms-dot .ring { width: 30px; height: 30px; margin: 0 auto 3px; display: grid; place-items: center; border-radius: 8px; }
        .ms-dot.active { background: var(--moss-light); color: var(--forest); font-weight: 600; }
        .ms-dot.active .ring { background: var(--forest); color: white; }
        .form-card { background: var(--surface); border: 1px solid var(--line); border-radius: 18px; padding: 28px 30px 30px; margin-bottom: 24px; scroll-margin-top: 24px; box-shadow: 0 3px 12px rgba(21,35,26,.025); }
        .section-head { display: flex; align-items: center; gap: 14px; margin: -28px -30px 26px; padding: 23px 30px; border-bottom: 1px solid var(--line); }
        .sec-num { width: 46px; height: 46px; border-radius: 13px; display: grid; place-items: center; background: var(--moss-light); color: var(--forest); flex-shrink: 0; }
        .sec-num .form-icon { width: 24px; height: 24px; }
        .section-head h2 { font-size: 17px; font-weight: 600; color: var(--forest); margin: 0; line-height: 1.5; }
        .section-head p { margin: 3px 0 0; font-size: 13px; color: var(--ink-soft); }
        .section-index { margin-left: auto; align-self: flex-start; font-size: 12px; color: var(--ink-soft); white-space: nowrap; padding-top: 4px; }
        .sub-title { font-family: 'Sarabun', sans-serif; font-size: 14.5px; font-weight: 600; color: var(--forest); margin: 28px 0 14px; padding-top: 23px; border-top: 1px solid var(--line); display: flex; gap: 9px; align-items: center; }
        .section-head + .sub-title { margin-top: 0; padding-top: 0; border-top: 0; }
        .sub-title .form-icon { color: var(--amber-deep); }
        label.form-label { font-weight: 500; font-size: 14px; margin-bottom: 8px; }
        .form-label .form-icon { width: 16px; height: 16px; margin-right: 5px; color: var(--ink-soft); }
        .required::after { content: " *"; color: var(--rust); }
        .helper-text { font-size: 12.5px; color: var(--ink-soft); }
        .form-control, .form-select { min-height: 46px; border: 1px solid #d9ddcf; border-radius: 9px; font-size: 14px; padding: 10px 12px; color: var(--ink); background-color: #fff; transition: box-shadow .15s, border-color .15s; }
        .form-control::placeholder { color: #737b6d; font-size: 13px; }
        .form-control:hover, .form-select:hover { border-color: #9ba88e; }
        .form-control:focus, .form-select:focus { border-color: var(--moss); box-shadow: 0 0 0 3px rgba(108,151,82,.18); }
        .form-check { font-size: 14px; }
        .form-check-input { border-color: #8b977e; }
        .form-check-input:checked { background-color: var(--forest); border-color: var(--forest); }
        .form-check-input:focus { border-color: var(--moss); box-shadow: 0 0 0 3px rgba(108,151,82,.22); }
        .service-type-card { height: 100%; border: 1px solid var(--line); border-radius: 11px; padding: 16px; cursor: pointer; display: flex; align-items: center; gap: 12px; transition: border-color .15s, background .15s; }
        .service-type-card > input { order: 3; margin-left: auto; flex-shrink: 0; }
        .service-type-card:hover { border-color: var(--moss); }
        .service-type-card:has(input:checked) { border-color: var(--forest); background: #f2f6ed; }
        .service-type-card:focus-within { outline: 3px solid rgba(108,151,82,.25); outline-offset: 2px; }
        .st-icon { width: 38px; height: 38px; display: grid; place-items: center; background: var(--amber-light); color: var(--amber-deep); border-radius: 10px; flex-shrink: 0; }
        .st-text { min-width: 0; }
        .st-text b { display: block; font-size: 14px; color: var(--forest); }
        .st-text span { display: block; font-size: 12px; color: var(--ink-soft); margin-top: 3px; }
        .plan-choice { position: relative; height: 100%; cursor: pointer; }
        .plan-radio { position: absolute; top: 19px; right: 16px; z-index: 1; }
        .plan-option { border: 1px solid var(--line); border-radius: 11px; padding: 16px; height: 100%; transition: background .15s, border-color .15s; }
        .plan-option:hover { border-color: var(--moss); }
        .plan-option.selected, .plan-choice:has(input:checked) .plan-option { border-color: var(--forest); background: #f2f6ed; }
        .plan-choice:focus-within .plan-option { outline: 3px solid rgba(108,151,82,.25); outline-offset: 2px; }
        .po-title { display: flex; align-items: center; gap: 8px; padding-right: 24px; font-family: 'Sarabun', sans-serif; font-weight: 600; color: var(--forest); font-size: 15px; }
        .po-meta { font-size: 13px; color: var(--ink-soft); margin-top: 10px; }
        .plan-price { font-family: 'Sarabun', sans-serif; font-size: 12.5px; color: var(--forest); margin-top: 14px; }
        .plan-price span { font-family: 'Sarabun', sans-serif; font-size: 12px; color: var(--ink-soft); }
        .custom-card { height: 100%; border: 1px dashed #aeb99e; border-radius: 11px; padding: 16px; background: #fafbf8; }
        .custom-card .fw-semibold { color: var(--forest); font-size: 14px; }
        .dev-row { border: 1px solid var(--line); border-radius: 11px; padding: 16px; margin-bottom: 12px; background: #fafbf8; }
        .dev-row .form-label { font-size: 13px; }
        .btn-outline-secondary { color: var(--forest); border-color: #bec8b1; border-radius: 8px; padding: 9px 14px; font-size: 13px; }
        .btn-outline-secondary:hover { color: var(--forest); background: var(--moss-light); border-color: var(--moss); }
        .remove-dev { min-width: 40px; min-height: 42px; display: grid; place-items: center; border-color: #dec8bf; border-radius: 8px; color: var(--rust); }
        .doc-drop { display: flex; align-items: center; gap: 14px; border: 1px dashed #b8c2a9; border-radius: 11px; padding: 18px; background: #fafbf8; }
        .doc-drop > .form-icon { width: 28px; height: 28px; color: var(--moss); }
        .doc-drop .form-control { min-width: 0; background: var(--surface); font-size: 13px; }
        .doc-drop .form-control::file-selector-button { color: var(--forest); background: var(--moss-light); margin-right: 12px; }
        .doc-drop:focus-within { border-color: var(--moss); }
        .accept-box { background: var(--amber-light); border-left: 3px solid var(--amber); border-radius: 0 10px 10px 0; padding: 18px 20px; font-size: 14px; color: var(--ink); line-height: 1.9; }
        .accept-check { border: 1px solid var(--line); border-radius: 10px; padding: 16px 16px 16px 42px; background: #fafbf8; }
        .btn-brand { display: inline-flex; align-items: center; justify-content: center; gap: 9px; background: var(--forest); border: 1px solid var(--forest); color: white; font-weight: 600; border-radius: 10px; padding: 13px 24px; font-size: 15px; white-space: nowrap; }
        .btn-brand:hover, .btn-brand:focus-visible { color: white; background: var(--forest-2); border-color: var(--forest-2); }
        .submit-bar { position: sticky; bottom: 12px; z-index: 30; background: var(--surface); border: 1px solid var(--line); border-radius: 14px; padding: 18px 22px; box-shadow: 0 6px 20px rgba(21,35,26,.07); }
        .submit-bar .inner { display: flex; align-items: center; justify-content: space-between; gap: 20px; }
        .submit-bar .hint { display: flex; gap: 10px; align-items: center; font-size: 12px; color: var(--ink-soft); max-width: 340px; }
        .submit-bar .hint b { display: block; font-size: 14px; color: var(--forest); margin-bottom: 3px; }
        .submit-bar .hint > .form-icon { width: 24px; height: 24px; color: var(--moss); }
        @media (max-width: 1199px) and (min-width: 992px) {
            .form-layout { grid-template-columns: 210px minmax(0, 1fr); gap: 20px; }
        }
        @media (max-width: 991px) {
            .form-layout { grid-template-columns: minmax(0, 1fr); }
            .side-nav { display: none; }
            .mobile-stepper { display: block; }
            .form-card { scroll-margin-top: 100px; }
            .form-intro h2 { font-size: 23px; }
        }
        @media (max-width: 575px) {
            .request-container { padding-top: 20px; }
            .form-breadcrumb { margin-bottom: 18px; }
            .form-intro { display: block; margin-bottom: 22px; }
            .form-intro h2 { font-size: 22px; }
            .required-note { display: inline-block; margin-top: 14px; }
            .form-card { padding: 20px 18px; border-radius: 13px; margin-bottom: 18px; }
            .section-head { margin: -20px -18px 22px; padding: 20px 18px; gap: 11px; }
            .section-head h2 { font-size: 17px; }
            .section-head p { font-size: 12px; }
            .sec-num { width: 39px; height: 39px; border-radius: 10px; }
            .section-index { display: none; }
            .form-control, .form-select { font-size: 16px; }
            .doc-drop { padding: 12px; gap: 9px; }
            .doc-drop > .form-icon { width: 22px; height: 22px; }
            .submit-bar { position: static; padding: 18px; }
            .submit-bar .inner { flex-direction: column; align-items: stretch; gap: 16px; }
            .submit-bar .hint { max-width: none; }
        }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { transition: none !important; }
        }
    </style>
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" width="0" height="0" aria-hidden="true" focusable="false" style="position:absolute;overflow:hidden">
    <symbol id="form-icon-user" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/></symbol>
    <symbol id="form-icon-server" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="7" rx="2"/><rect x="3" y="14" width="18" height="7" rx="2"/><path d="M7 6.5h.01M7 17.5h.01M11 6.5h6M11 17.5h6"/></symbol>
    <symbol id="form-icon-globe" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><ellipse cx="12" cy="12" rx="4" ry="9"/><path d="M3 12h18"/></symbol>
    <symbol id="form-icon-file" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8ZM14 2v6h6M8 13h8M8 17h5"/></symbol>
    <symbol id="form-icon-shield" viewBox="0 0 24 24"><path d="m12 3 8 4v5c0 5-8 9-8 9s-8-4-8-9V7Z"/><path d="m8 12 3 3 5-6"/></symbol>
    <symbol id="form-icon-home" viewBox="0 0 24 24"><path d="m3 10 9-7 9 7M5 9v12h5v-7h4v7h5V9"/></symbol>
    <symbol id="form-icon-info" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7h.01"/></symbol>
    <symbol id="form-icon-calendar" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 11h18M7 15h3M14 15h3"/></symbol>
    <symbol id="form-icon-users" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3 21v-3a6 6 0 0 1 12 0v3M16 5a3 3 0 0 1 0 6M18 15a5 5 0 0 1 3 4v2"/></symbol>
    <symbol id="form-icon-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
    <symbol id="form-icon-trash" viewBox="0 0 24 24"><path d="M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7M14 10v7"/></symbol>
    <symbol id="form-icon-send" viewBox="0 0 24 24"><path d="m22 2-7 20-4-9-9-4ZM22 2 11 13"/></symbol>
    <symbol id="form-icon-upload" viewBox="0 0 24 24"><path d="M12 16V3m-5 5 5-5 5 5M4 15v5a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-5"/></symbol>
    <symbol id="form-icon-pen" viewBox="0 0 24 24"><path d="m15 5 4 4M4 20l4-1L21 6a2.8 2.8 0 0 0-4-4L4 15ZM3 22h18"/></symbol>
    <symbol id="form-icon-building" viewBox="0 0 24 24"><rect x="5" y="3" width="14" height="18" rx="1"/><path d="M9 21v-4h6v4M9 7h1M14 7h1M9 11h1M14 11h1"/></symbol>
    <symbol id="form-icon-mail" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 6 9 7 9-7"/></symbol>
    <symbol id="form-icon-phone" viewBox="0 0 24 24"><path d="m8 3 2 5-3 2a14 14 0 0 0 7 7l2-3 5 2v4a2 2 0 0 1-2 2A19 19 0 0 1 2 5a2 2 0 0 1 2-2Z"/></symbol>
    <symbol id="form-icon-code" viewBox="0 0 24 24"><path d="m8 6-6 6 6 6m8-12 6 6-6 6M14 3l-4 18"/></symbol>
    <symbol id="form-icon-database" viewBox="0 0 24 24"><ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 4 16 4 16 0V5M4 12c0 4 16 4 16 0"/></symbol>
    <symbol id="form-icon-settings" viewBox="0 0 24 24"><path d="M4 7h2m6 0h8M4 17h8m6 0h2"/><circle cx="9" cy="7" r="3"/><circle cx="15" cy="17" r="3"/></symbol>
    <symbol id="form-icon-wallet" viewBox="0 0 24 24"><path d="M20 8V5a2 2 0 0 0-2-2H5a3 3 0 0 0 0 6h15v12H5a3 3 0 0 1-3-3V6M20 13h-5v4h5"/></symbol>
    <symbol id="form-icon-check" viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></symbol>
    <symbol id="form-icon-book" viewBox="0 0 24 24"><path d="M12 5v16M12 5C8 2 4 3 2 4v15c3-1 7-1 10 2 3-3 7-3 10-2V4c-2-1-6-2-10 1Z"/></symbol>
</svg>

<div class="page-header">
    <div class="container">
        <div class="brand-row">
            <div style="width:40px;height:40px;border-radius:11px;background:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,.16);padding:5px;">
                <img src="{{ asset('images/logo.png') }}?v={{ filemtime(public_path('images/logo.png')) }}" alt="โลโก้ระบบ" style="width:100%;height:100%;object-fit:contain;display:block;">
            </div>
            <div>
                <h1>แบบฟอร์มขอใช้บริการระบบ Data Center และ Web Hosting</h1>
                <p>สำนักคอมพิวเตอร์ มหาวิทยาลัยราชภัฏนครราชสีมา</p>
            </div>
        </div>
    </div>
</div>

<nav class="mobile-stepper" aria-label="ส่วนต่าง ๆ ของแบบฟอร์ม">
    <div class="container">
        <div class="ms-track" id="msTrack">
            <a class="ms-dot active" href="#sec1" data-target="sec1" aria-current="location"><span class="ring"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-user"></use></svg></span>ผู้ขอ</a>
            <a class="ms-dot" href="#sec2" data-target="sec2"><span class="ring"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-server"></use></svg></span>ความต้องการ</a>
            <a class="ms-dot" href="#sec3" data-target="sec3"><span class="ring"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-globe"></use></svg></span>โดเมน</a>
            <a class="ms-dot" href="#sec4" data-target="sec4"><span class="ring"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-file"></use></svg></span>เอกสาร</a>
            <a class="ms-dot" href="#sec5" data-target="sec5"><span class="ring"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-shield"></use></svg></span>ยืนยัน</a>
        </div>
    </div>
</nav>

<main class="container request-container">
    <nav class="form-breadcrumb" aria-label="เส้นทางนำทาง">
        <a href="{{ route('home') }}"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-home"></use></svg> หน้าหลัก</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">แบบฟอร์มขอใช้บริการ</span>
    </nav>
    <div class="form-intro">
        <div>
            <h2>ลงทะเบียนขอใช้บริการ</h2>
            <p>กรอกข้อมูลและแนบเอกสาร เพื่อให้เจ้าหน้าที่พิจารณาคำขอของคุณ</p>
        </div>
        <div class="required-note"><span>*</span> ข้อมูลที่จำเป็นต้องกรอก</div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>กรุณาตรวจสอบข้อมูล</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="ปิด"></button>
        </div>
    @endif

    <form action="{{ url('/service-requests') }}" method="POST" enctype="multipart/form-data" id="serviceRequestForm">
        @csrf

        <div class="form-layout">
            {{-- Sticky progress nav (desktop) --}}
            <aside class="side-nav d-none d-lg-block">
                <nav class="nav-card" aria-label="ส่วนต่าง ๆ ของแบบฟอร์ม">
                    <h2 class="nav-title">ข้อมูลในแบบฟอร์ม</h2>
                    <a class="step-link active" href="#sec1" data-target="sec1" aria-current="location"><span class="num"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-user"></use></svg></span><span><small>ส่วนที่ 1</small>ข้อมูลผู้ขอ</span></a>
                    <a class="step-link" href="#sec2" data-target="sec2"><span class="num"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-server"></use></svg></span><span><small>ส่วนที่ 2</small>ความต้องการพื้นฐาน</span></a>
                    <a class="step-link" href="#sec3" data-target="sec3"><span class="num"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-globe"></use></svg></span><span><small>ส่วนที่ 3</small>ชื่อโดเมน</span></a>
                    <a class="step-link" href="#sec4" data-target="sec4"><span class="num"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-file"></use></svg></span><span><small>ส่วนที่ 4</small>เอกสารแนบ</span></a>
                    <a class="step-link" href="#sec5" data-target="sec5"><span class="num"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-shield"></use></svg></span><span><small>ส่วนที่ 5</small>ยอมรับข้อกำหนด</span></a>
                </nav>
                <div class="side-hint">
                    <h3><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-info"></use></svg> เตรียมข้อมูลก่อนเริ่ม</h3>
                    <p><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-check"></use></svg> ข้อมูลผู้ขอและผู้ดูแลระบบ</p>
                    <p><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-check"></use></svg> เอกสารรายละเอียดระบบ</p>
                    <p><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-check"></use></svg> รูปลายเซ็นผู้ขอใช้บริการ</p>
                    <small>กรอกครบทั้ง 5 ส่วน แล้วกดส่งแบบฟอร์มเพื่อให้เจ้าหน้าที่พิจารณา</small>
                </div>
            </aside>

            <div>
                {{-- ส่วนที่ 1: ข้อมูลผู้ขอรับบริการ --}}
                <div class="form-card" id="sec1">
                    <div class="section-head">
                        <div class="sec-num"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-user"></use></svg></div>
                        <div>
                            <h2>ข้อมูลผู้ขอรับบริการและหน่วยงาน</h2>
                            <p>ข้อมูลผู้ติดต่อ วัตถุประสงค์ และผู้ดูแลระบบ</p>
                        </div>
                        <span class="section-index">1 / 5</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="full_name"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-user"></use></svg>ชื่อ-สกุลผู้ขอใช้บริการ</label>
                            <input type="text" name="full_name" id="full_name" class="form-control" value="{{ old('full_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="customer_name"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-user"></use></svg>Customer Name</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ old('customer_name') }}" placeholder="ชื่อบัญชีที่ใช้ในระบบ Plesk (ถ้ามี)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="staff_or_student_id"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-user"></use></svg>รหัสบุคลากร</label>
                            <input type="text" name="staff_or_student_id" id="staff_or_student_id" class="form-control" value="{{ old('staff_or_student_id') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="unit_name"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-building"></use></svg>หน่วยงาน</label>
                            <input type="text" name="unit_name" id="unit_name" class="form-control" value="{{ old('unit_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="affiliation"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-building"></use></svg>สังกัด</label>
                            <input type="text" name="affiliation" id="affiliation" class="form-control" value="{{ old('affiliation') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="position_title"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-user"></use></svg>ตำแหน่ง</label>
                            <input type="text" name="position_title" id="position_title" class="form-control" value="{{ old('position_title') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="phone"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-phone"></use></svg>โทรศัพท์</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="email"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-mail"></use></svg>อีเมล</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="sub-title"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-book"></use></svg>หลักการและวัตถุประสงค์</div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="service-type-card">
                                <input class="form-check-input" type="radio" name="purpose_type" value="1.1_teaching" {{ old('purpose_type') == '1.1_teaching' ? 'checked' : '' }} required>
                                <div class="st-icon"><svg aria-hidden="true" focusable="false" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-5 9 5M5 10v9h14v-9"/></svg></div>
                                <div class="st-text"><b>การเรียนการสอน</b><span>สนับสนุนการจัดการเรียนการสอน</span></div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="service-type-card">
                                <input class="form-check-input" type="radio" name="purpose_type" value="1.2_academic_research_community" {{ old('purpose_type') == '1.2_academic_research_community' ? 'checked' : '' }}>
                                <div class="st-icon"><svg aria-hidden="true" focusable="false" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V5h6l2 3h8v11Z"/></svg></div>
                                <div class="st-text"><b>บริการวิชาการ/วิจัย</b><span>สนับสนุนการวิจัยและท้องถิ่น</span></div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="service-type-card">
                                <input class="form-check-input" type="radio" name="purpose_type" value="1.3_internal_admin" {{ old('purpose_type') == '1.3_internal_admin' ? 'checked' : '' }}>
                                <div class="st-icon"><svg aria-hidden="true" focusable="false" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/></svg></div>
                                <div class="st-text"><b>บริหารจัดการองค์กร</b><span>สนับสนุนการบริหารภายใน</span></div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="service-type-card">
                                <input class="form-check-input" type="radio" name="purpose_type" value="1.4_other" {{ old('purpose_type') == '1.4_other' ? 'checked' : '' }}>
                                <div class="st-icon"><svg aria-hidden="true" focusable="false" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/></svg></div>
                                <div class="st-text"><b>อื่น ๆ</b><span>ต้องได้รับการพิจารณา</span></div>
                            </label>
                        </div>
                    </div>
                    <input type="text" name="purpose_other_detail" aria-label="รายละเอียดวัตถุประสงค์อื่น ๆ" class="form-control mt-2" placeholder="ระบุกรณีอื่น ๆ" value="{{ old('purpose_other_detail') }}">

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label required" for="project_start_date"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-calendar"></use></svg>ระยะเวลาโครงการ/การขอใช้งาน ตั้งแต่วันที่</label>
                            <input type="date" name="project_start_date" id="project_start_date" class="form-control" value="{{ old('project_start_date') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="project_end_date"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-calendar"></use></svg>ถึงวันที่ (ไม่เกิน 1 ปี)</label>
                            <input type="date" name="project_end_date" id="project_end_date" class="form-control" value="{{ old('project_end_date') }}" required>
                        </div>
                    </div>

                    <div class="sub-title"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-users"></use></svg>รายชื่อและช่องทางติดต่อผู้รับผิดชอบในการพัฒนาระบบ</div>
                    <div id="developersWrapper">
                        <div class="dev-row">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label required" for="developer-0-full_name">ชื่อ-นามสกุล</label>
                                    <input id="developer-0-full_name" type="text" name="developers[0][full_name]" class="form-control" placeholder="ชื่อ-นามสกุล *" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="developer-0-role_desc">บทบาท/หน้าที่</label>
                                    <input id="developer-0-role_desc" type="text" name="developers[0][role_desc]" class="form-control" placeholder="บทบาท/หน้าที่">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="developer-0-phone">เบอร์โทร</label>
                                    <input id="developer-0-phone" type="text" name="developers[0][phone]" class="form-control" placeholder="เบอร์โทร">
                                </div>
                                <div class="col-10 col-md-5">
                                    <label class="form-label" for="developer-0-email">อีเมล</label>
                                    <input id="developer-0-email" type="email" name="developers[0][email]" class="form-control" placeholder="อีเมล">
                                </div>
                                <div class="col-2 col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-dev" title="ลบผู้รับผิดชอบ" aria-label="ลบผู้รับผิดชอบ"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-trash"></use></svg></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="addDeveloper"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-plus"></use></svg> เพิ่มผู้รับผิดชอบ</button>
                </div>

                {{-- ส่วนที่ 2: ความต้องการพื้นฐาน --}}
                <div class="form-card" id="sec2">
                    <div class="section-head">
                        <div class="sec-num"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-server"></use></svg></div>
                        <div>
                            <h2>บริการและทรัพยากรที่ต้องการ</h2>
                            <p>เลือกประเภทบริการ ขนาดทรัพยากร และรายละเอียดระบบ</p>
                        </div>
                        <span class="section-index">2 / 5</span>
                    </div>

                    <div class="sub-title"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-server"></use></svg>ประเภทของบริการ</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="service-type-card">
                                <input class="form-check-input service-type-radio" type="radio" name="service_type" id="stVirtual" value="virtual_server" {{ old('service_type') == 'virtual_server' ? 'checked' : '' }} required>
                                <div class="st-icon"><svg aria-hidden="true" focusable="false" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="4" width="16" height="6" rx="1.5"/><rect x="4" y="14" width="16" height="6" rx="1.5"/><path d="M7 7h.01M7 17h.01"/></svg></div>
                                <div class="st-text"><b>เครื่องแม่ข่ายเสมือน</b><span>Virtual Server</span></div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="service-type-card">
                                <input class="form-check-input service-type-radio" type="radio" name="service_type" id="stHosting" value="web_hosting" {{ old('service_type') == 'web_hosting' ? 'checked' : '' }}>
                                <div class="st-icon"><svg aria-hidden="true" focusable="false" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg></div>
                                <div class="st-text"><b>บริการ Web Hosting</b><span>โฮสต์เว็บไซต์</span></div>
                            </label>
                        </div>
                    </div>

                    <div class="sub-title"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-settings"></use></svg>ขนาดทรัพยากรที่ต้องการ</div>
                    <div class="row g-3" id="planOptions">
                        @foreach($resourcePlans as $plan)
                            <div class="col-md-6 plan-group" data-service-type="{{ $plan->service_type }}">
                                <label class="w-100 plan-choice">
                                    <input type="radio" name="plan_id" value="{{ $plan->plan_id }}" class="form-check-input plan-radio" {{ old('plan_id') == $plan->plan_id ? 'checked' : '' }}>
                                    <div class="plan-option">
                                        <div class="po-title"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-server"></use></svg>{{ $plan->size_label }}</div>
                                        <div class="po-meta">
                                            @if($plan->cpu_vcpu) {{ $plan->cpu_vcpu }} vCPU / {{ $plan->ram_gb }} GB RAM /@endif
                                            {{ $plan->storage_gb }} GB Storage
                                        </div>
                                        <div class="plan-price">{{ number_format($plan->fee_per_year, 0) }} <span>บาท / ปี</span></div>
                                        <div class="po-meta">{{ $plan->suitable_for }}</div>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                        <div class="col-12">
                            <div class="custom-card">
                                <div class="fw-semibold mb-2"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-settings"></use></svg> กำหนดทรัพยากรเอง</div>
                                <div class="row g-2">
                                    <div class="col-6 col-lg-3">
                                        <label class="form-label" for="custom_cpu_vcpu">vCPU</label>
                                        <input id="custom_cpu_vcpu" type="number" name="custom_cpu_vcpu" aria-label="จำนวน vCPU ที่ต้องการ" class="form-control form-control-sm" placeholder="vCPU">
                                    </div>
                                    <div class="col-6 col-lg-3">
                                        <label class="form-label" for="custom_ram_gb">RAM (GB)</label>
                                        <input id="custom_ram_gb" type="number" name="custom_ram_gb" aria-label="RAM ที่ต้องการ (GB)" class="form-control form-control-sm" placeholder="RAM (GB)">
                                    </div>
                                    <div class="col-6 col-lg-3">
                                        <label class="form-label" for="custom_storage_gb">Storage (GB)</label>
                                        <input id="custom_storage_gb" type="number" name="custom_storage_gb" aria-label="พื้นที่จัดเก็บที่ต้องการ (GB)" class="form-control form-control-sm" placeholder="Storage (GB)">
                                    </div>
                                    <div class="col-6 col-lg-3">
                                        <label class="form-label" for="custom_fee">ค่าบริการ (บาท/ปี)</label>
                                        <input id="custom_fee" type="number" step="0.01" name="custom_fee" aria-label="ค่าบริการ (บาทต่อปี)" class="form-control form-control-sm" placeholder="ค่าบริการ (บาท/ปี)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sub-title"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-code"></use></svg>บริการที่ต้องการเปิดใช้งาน</div>
                    @php $oldServices = old('enabled_services', []); @endphp
                    <div class="row g-2">
                        <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="enabled_services[]" value="ssh" id="svcSsh" {{ in_array('ssh', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcSsh">SSH — ภายในเครือข่ายหรือผ่าน VPN</label></div></div>
                        <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="enabled_services[]" value="http_https" id="svcHttp" {{ in_array('http_https', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcHttp">HTTP / HTTPS สำหรับ Web Service</label></div></div>
                        <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="enabled_services[]" value="database_access" id="svcDb" {{ in_array('database_access', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcDb">Database Access ภายในระบบที่อนุญาต</label></div></div>
                        <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="enabled_services[]" value="other" id="svcOther" {{ in_array('other', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcOther">อื่น ๆ</label></div></div>
                    </div>
                    <input type="text" name="enabled_services_other_detail" aria-label="รายละเอียดบริการอื่น ๆ" class="form-control mt-2" placeholder="ระบุบริการอื่น ๆ" value="{{ old('enabled_services_other_detail') }}">

                    <div class="row g-3 mt-1">
                        <div class="col-md-4">
                            <label class="form-label" for="language_framework"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-code"></use></svg>ภาษา/Framework ที่ใช้</label>
                            <input type="text" name="language_framework" id="language_framework" class="form-control" placeholder="เช่น PHP 8.3" value="{{ old('language_framework') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="database_used"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-database"></use></svg>ฐานข้อมูลที่ใช้</label>
                            <input type="text" name="database_used" id="database_used" class="form-control" placeholder="เช่น MySQL" value="{{ old('database_used') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="port_service_needed"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-settings"></use></svg>พอร์ตหรือบริการที่ต้องเปิดใช้งาน</label>
                            <input type="text" name="port_service_needed" id="port_service_needed" class="form-control" value="{{ old('port_service_needed') }}">
                        </div>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="needs_external_connection" value="1" id="extConn" {{ old('needs_external_connection') ? 'checked' : '' }}>
                        <label class="form-check-label" for="extConn">ต้องการเชื่อมต่อกับระบบภายนอก</label>
                    </div>
                </div>

                {{-- ส่วนที่ 3: โดเมน --}}
                <div class="form-card" id="sec3">
                    <div class="section-head">
                        <div class="sec-num"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-globe"></use></svg></div>
                        <div>
                            <h2>ชื่อโดเมนและหน่วยงาน</h2>
                            <p>ระบุชื่อเว็บไซต์และสังกัดที่ต้องการใช้งาน</p>
                        </div>
                        <span class="section-index">3 / 5</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="domain_name"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-globe"></use></svg>ชื่อโดเมนที่ต้องการ</label>
                            <input type="text" name="domain_name" id="domain_name" class="form-control" placeholder="เช่น wellbeing-nurse.nrru.ac.th" value="{{ old('domain_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="domain_format"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-globe"></use></svg>รูปแบบโดเมน</label>
                            <input type="text" name="domain_format" id="domain_format" class="form-control" placeholder="เช่น test -> test-edu.nrru.ac.th" value="{{ old('domain_format') }}">
                        </div>
                    </div>

                    <div class="sub-title"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-building"></use></svg>รหัสสังกัดคณะ/หน่วยงานย่อย</div>
                    <div class="row g-1">
                        @foreach($departmentCodes as $dept)
                            <div class="col-md-6">
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="radio" name="department_code" value="{{ $dept->code }}" id="dept{{ $loop->index }}" {{ old('department_code') == $dept->code ? 'checked' : '' }}>
                                    <label class="form-check-label" for="dept{{ $loop->index }}">{{ $dept->code }} {{ $dept->department_name }}</label>
                                </div>
                            </div>
                        @endforeach
                        <div class="col-md-6">
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="radio" name="department_code" value="" id="deptOther" {{ old('department_code') ? '' : 'checked' }}>
                                <label class="form-check-label" for="deptOther">อื่น ๆ ระบุ</label>
                            </div>
                            <input type="text" name="department_other" aria-label="สังกัดคณะหรือหน่วยงานอื่น ๆ" class="form-control form-control-sm" value="{{ old('department_other') }}">
                        </div>
                    </div>
                </div>

                {{-- ส่วนที่ 4: เอกสารแนบและการรับรอง --}}
                <div class="form-card" id="sec4">
                    <div class="section-head">
                        <div class="sec-num"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-file"></use></svg></div>
                        <div>
                            <h2>เอกสารแนบและการรับรอง</h2>
                            <p>แนบรายละเอียดระบบและระบุการรับรองค่าใช้จ่าย</p>
                        </div>
                        <span class="section-index">4 / 5</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required" for="system_detail_doc"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-file"></use></svg>เอกสารรายละเอียดระบบ / โครงสร้างระบบ (บังคับแนบไฟล์)</label>
                        <div class="doc-drop"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-upload"></use></svg><input type="file" name="system_detail_doc" id="system_detail_doc" class="form-control" accept=".pdf,.doc,.docx,.zip" required></div>
                        <div class="helper-text mt-1">รองรับไฟล์ PDF, Word หรือ ZIP ขนาดไม่เกิน 10 MB</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="screenshot_evidence"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-shield"></use></svg>หลักฐานการสแกนโค้ด (ถ้ามี)</label>
                        <div class="doc-drop"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-upload"></use></svg><input type="file" name="screenshot_evidence" id="screenshot_evidence" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
                        <div class="helper-text mt-2">รองรับ PDF, JPG หรือ PNG ไม่เกิน 10 MB หากยังไม่แนบ ต้องส่งภายหลังก่อนขึ้นโฮสต์</div>
                    </div>

                    <div class="sub-title"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-wallet"></use></svg>การรับรองค่าใช้จ่าย</div>
                    <div class="form-check mb-1"><input class="form-check-input" type="checkbox" name="agree_to_pay" value="1" id="agreePay" {{ old('agree_to_pay') ? 'checked' : '' }}><label class="form-check-label" for="agreePay">ยินยอมชำระค่าบริการตามอัตราที่มหาวิทยาลัยกำหนด</label></div>
                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="request_fee_waiver" value="1" id="requestWaiver" {{ old('request_fee_waiver') ? 'checked' : '' }}><label class="form-check-label" for="requestWaiver">ขอรับการยกเว้นค่าธรรมเนียม</label></div>
                    <label class="form-label" for="waiver_reason"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-wallet"></use></svg>เหตุผลประกอบการขอยกเว้นค่าธรรมเนียม</label>
                    <textarea name="waiver_reason" id="waiver_reason" class="form-control" rows="2">{{ old('waiver_reason') }}</textarea>
                </div>

                {{-- ส่วนที่ 5: ยอมรับข้อกำหนด --}}
                <div class="form-card" id="sec5">
                    <div class="section-head">
                        <div class="sec-num"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-shield"></use></svg></div>
                        <div>
                            <h2>ข้อกำหนดและการยืนยัน</h2>
                            <p>อ่านข้อกำหนด แนบลายเซ็น และยืนยันคำขอ</p>
                        </div>
                        <span class="section-index">5 / 5</span>
                    </div>
                    <div class="accept-box mb-3">
                        ผู้ขอใช้บริการต้องปฏิบัติตาม พ.ร.บ. ว่าด้วยการกระทำความผิดเกี่ยวกับคอมพิวเตอร์ พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล (PDPA)
                        และระเบียบด้านเทคโนโลยีสารสนเทศของมหาวิทยาลัยอย่างเคร่งครัด รวมถึงมีหน้าที่สำรองข้อมูลและดูแลความปลอดภัยของระบบด้วยตนเอง
                    </div>

                    <div class="mb-3">
                        <label class="form-label required" for="signature_image"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-pen"></use></svg>แนบรูปลายเซ็นผู้ขอใช้บริการ</label>
                        <div class="doc-drop"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-pen"></use></svg><input type="file" name="signature_image" id="signature_image" class="form-control" accept="image/*" required></div>
                        <div class="helper-text mt-2">แนบไฟล์รูปลายเซ็น ขนาดไม่เกิน 2 MB</div>
                    </div>

                    <div class="form-check accept-check">
                        <input class="form-check-input" type="checkbox" name="accepted" value="1" id="accepted" {{ old('accepted') ? 'checked' : '' }} required>
                        <label class="form-check-label" for="accepted">
                            ข้าพเจ้าได้รับทราบและยินยอมปฏิบัติตามข้อกำหนดและแนวปฏิบัติในการใช้บริการของสำนักคอมพิวเตอร์
                        </label>
                    </div>
                </div>

                <div class="submit-bar">
                    <div class="container px-0">
                        <div class="inner">
                            <div class="hint"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-shield"></use></svg><div><b>ตรวจสอบข้อมูลก่อนส่งคำขอ</b>ระบบจะบันทึกคำขอและรอเจ้าหน้าที่พิจารณา</div></div>
                            <button type="submit" class="btn btn-brand px-4"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-send"></use></svg> ส่งแบบฟอร์ม</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

@include('partials.alert-popup')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // เพิ่ม/ลบแถวผู้รับผิดชอบพัฒนาระบบ
    let devIndex = 1;
    document.getElementById('addDeveloper').addEventListener('click', function () {
        const wrapper = document.getElementById('developersWrapper');
        const row = document.createElement('div');
        row.className = 'dev-row';
        row.innerHTML = `
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label required" for="developer-${devIndex}-full_name">ชื่อ-นามสกุล</label>
                    <input id="developer-${devIndex}-full_name" type="text" name="developers[${devIndex}][full_name]" class="form-control" placeholder="ชื่อ-นามสกุล *" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="developer-${devIndex}-role_desc">บทบาท/หน้าที่</label>
                    <input id="developer-${devIndex}-role_desc" type="text" name="developers[${devIndex}][role_desc]" class="form-control" placeholder="บทบาท/หน้าที่">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="developer-${devIndex}-phone">เบอร์โทร</label>
                    <input id="developer-${devIndex}-phone" type="text" name="developers[${devIndex}][phone]" class="form-control" placeholder="เบอร์โทร">
                </div>
                <div class="col-10 col-md-5">
                    <label class="form-label" for="developer-${devIndex}-email">อีเมล</label>
                    <input id="developer-${devIndex}-email" type="email" name="developers[${devIndex}][email]" class="form-control" placeholder="อีเมล">
                </div>
                <div class="col-2 col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-dev" title="ลบผู้รับผิดชอบ" aria-label="ลบผู้รับผิดชอบ"><svg class="form-icon" aria-hidden="true" focusable="false"><use href="#form-icon-trash"></use></svg></button>
                </div>
            </div>`;
        wrapper.appendChild(row);
        devIndex++;
    });

    document.getElementById('developersWrapper').addEventListener('click', function (e) {
        const removeButton = e.target.closest('.remove-dev');
        if (removeButton) {
            if (document.querySelectorAll('.dev-row').length > 1) {
                removeButton.closest('.dev-row').remove();
            }
        }
    });

    // แสดงเฉพาะแพ็กเกจทรัพยากรที่ตรงกับประเภทบริการที่เลือก
    function filterPlans() {
        const selected = document.querySelector('.service-type-radio:checked');
        const type = selected ? selected.value : null;
        document.querySelectorAll('.plan-group').forEach(function (group) {
            group.style.display = (!type || group.dataset.serviceType === type) ? '' : 'none';
        });
    }
    document.querySelectorAll('.service-type-radio').forEach(function (radio) {
        radio.addEventListener('change', filterPlans);
    });
    filterPlans();

    // ไฮไลต์การ์ดแพ็กเกจที่เลือก
    function highlightPlan(radio) {
        document.querySelectorAll('.plan-option').forEach(el => el.classList.remove('selected'));
        if (radio && radio.closest('label')) {
            radio.closest('label').querySelector('.plan-option').classList.add('selected');
        }
    }
    document.querySelectorAll('.plan-radio').forEach(function (radio) {
        radio.addEventListener('change', function () { highlightPlan(this); });
        if (radio.checked) highlightPlan(radio);
    });

    // Scroll-spy: ไฮไลต์ส่วนที่กำลังดูใน nav และ mobile stepper
    const sections = ['sec1','sec2','sec3','sec4','sec5'].map(id => document.getElementById(id));
    const links = document.querySelectorAll('.step-link');
    const dots = document.querySelectorAll('.ms-dot');
    const spy = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.id;
                document.querySelectorAll('.step-link, .ms-dot').forEach(link => {
                    if (link.dataset.target === id) {
                        link.setAttribute('aria-current', 'location');
                    } else {
                        link.removeAttribute('aria-current');
                    }
                });
                links.forEach(l => l.classList.toggle('active', l.dataset.target === id));

                dots.forEach(d => {
                    d.classList.toggle('active', d.dataset.target === id);
                });
            }
        });
    }, { rootMargin: '-45% 0px -45% 0px', threshold: 0 });
    sections.forEach(s => s && spy.observe(s));

    // คลิกเพื่อเลื่อนไปส่วนนั้น
    document.querySelectorAll('.step-link, .ms-dot').forEach(el => {
        el.addEventListener('click', (event) => {
            event.preventDefault();
            const target = document.getElementById(el.dataset.target);
            if (target) {
                target.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth', block: 'start' });
                target.setAttribute('tabindex', '-1');
                target.focus({ preventScroll: true });
            }
        });
    });
</script>
</body>
</html>