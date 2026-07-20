<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>บริการ Data Center และ Web Hosting — สำนักคอมพิวเตอร์ มหาวิทยาลัยราชภัฏนครราชสีมา</title>
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
            --line: #e6e2d2;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Sarabun', sans-serif; background: var(--bg); color: var(--ink); margin: 0; }
        h1, h2, h3, .display-font { font-family: 'Kanit', sans-serif; }
        a { text-decoration: none; }

        /* Navbar */
        .navbar-custom {
            background: rgba(245,244,233,.92); backdrop-filter: blur(6px);
            border-bottom: 1px solid var(--line);
            position: sticky; top: 0; z-index: 50;
            padding: 14px 0;
        }
        .brand-mark {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, var(--amber), #f0c25c);
            display: flex; align-items: center; justify-content: center;
        }
        .brand-name { font-family: 'Kanit', sans-serif; font-weight: 600; font-size: 15px; color: var(--forest); }
        .brand-name small { display: block; font-weight: 400; font-size: 11px; color: var(--ink-soft); }
        .nav-link-custom { color: var(--ink); font-size: 14.5px; font-weight: 500; margin: 0 14px; }
        .nav-link-custom:hover { color: var(--forest); }
        .btn-amber { background: linear-gradient(135deg, var(--amber), var(--amber-deep)); border: none; color: #2c1e05; font-weight: 600; border-radius: 9px; padding: 9px 20px; font-size: 14.5px; }
        .btn-amber:hover { filter: brightness(1.05); color: #2c1e05; }
        .btn-outline-brand { border: 1.5px solid var(--forest); color: var(--forest); border-radius: 9px; padding: 8px 18px; font-size: 14px; font-weight: 500; }
        .btn-outline-brand:hover { background: var(--forest); color: #fff; }

        /* Hero */ 
        .hero {
            background: linear-gradient(190deg, var(--forest) 0%, var(--forest-2) 65%, #3d5a34 100%);
            color: #eef2e6; padding: 78px 0 96px; position: relative; overflow: hidden;
        }
        .hero::after {
            content: ''; position: absolute; width: 420px; height: 420px;
            background: radial-gradient(circle, rgba(224,165,38,.28) 0%, rgba(224,165,38,0) 70%);
            top: -120px; right: -100px; pointer-events: none;
        }
        .hero-eyebrow { font-size: 13px; letter-spacing: .08em; text-transform: uppercase; color: #f0c25c; font-weight: 600; margin-bottom: 14px; }
        .hero h1 { font-size: 40px; font-weight: 700; line-height: 1.25; margin-bottom: 18px; }
        .hero p.lead { font-size: 16.5px; color: #d8e0d0; max-width: 560px; margin-bottom: 30px; }

        /* Sections */
        section { padding: 72px 0; }
        .section-eyebrow { font-size: 13px; color: var(--moss); font-weight: 600; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 8px; }
        .section-title { font-size: 27px; font-weight: 700; margin-bottom: 14px; }
        .section-desc { color: var(--ink-soft); font-size: 15px; max-width: 620px; }

        .plan-card {
            background: var(--surface); border: 1px solid var(--line); border-radius: 16px;
            padding: 26px 24px; height: 100%; transition: transform .15s ease, box-shadow .15s ease;
        }
        .plan-card:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(36,66,43,.1); }
        .plan-card .plan-badge {
            display: inline-block; font-size: 11.5px; font-weight: 600; padding: 4px 10px;
            border-radius: 20px; background: var(--moss-light); color: var(--forest); margin-bottom: 12px;
        }
        .plan-card .plan-price { font-family: 'Kanit', sans-serif; font-size: 26px; font-weight: 700; color: var(--forest); }
        .plan-card .plan-price span { font-size: 13px; font-weight: 400; color: var(--ink-soft); }
        .plan-card ul { list-style: none; padding: 0; margin: 16px 0 0; font-size: 14px; }
        .plan-card ul li { padding: 5px 0; display: flex; align-items: center; gap: 8px; color: var(--ink); }
        .plan-card ul li svg { flex-shrink: 0; color: var(--moss); }

        .step-item { display: flex; gap: 16px; margin-bottom: 26px; }
        .step-num {
            width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Kanit', sans-serif; font-weight: 700; font-size: 16px; color: #fff;
        }
        .step-item:nth-child(odd) .step-num { background: var(--forest); }
        .step-item:nth-child(even) .step-num { background: var(--amber-deep); }
        .step-title { font-weight: 600; margin-bottom: 3px; }
        .step-desc { color: var(--ink-soft); font-size: 14px; }

        .policy-list { list-style: none; padding: 0; margin: 0; }
        .policy-list li { display: flex; gap: 10px; padding: 9px 0; font-size: 14.5px; border-bottom: 1px dashed var(--line); }
        .policy-list li:last-child { border-bottom: none; }
        .policy-list svg { flex-shrink: 0; color: var(--moss); margin-top: 2px; }

        .cta-band {
            background: linear-gradient(120deg, var(--moss-light), var(--amber-light));
            border-radius: 20px; padding: 44px 40px; text-align: center;
        }

        footer { background: var(--forest); color: #cfd8c6; padding: 36px 0; font-size: 13.5px; }
        footer a { color: #eef2e6; }
        footer a:hover { color: var(--amber); }
    </style>
</head>
<body>

<nav class="navbar-custom">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="{{ url('/') }}" class="d-flex align-items-center gap-2">
            <div class="brand-mark">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2C7 2 3 6 3 11c0 5 4.5 9.5 9 11 4.5-1.5 9-6 9-11 0-5-4-9-9-9Z" fill="#24422b"/>
                    <path d="M12 6v10M9 9l3-3 3 3" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="brand-name">
                สำนักคอมพิวเตอร์
                <small>มหาวิทยาลัยราชภัฏนครราชสีมา</small>
            </div>
        </a>
        <div class="d-none d-md-flex align-items-center">
            <a href="#services" class="nav-link-custom">บริการ</a>
            <a href="#steps" class="nav-link-custom">ขั้นตอนการขอใช้</a>
            <a href="#policy" class="nav-link-custom">ข้อกำหนด</a>
            <a href="{{ route('admin.requests.index') }}" class="nav-link-custom">สำหรับเจ้าหน้าที่</a>
        </div>
        <a href="{{ route('service-requests.create') }}" class="btn-amber">ยื่นคำขอใช้บริการ</a>
    </div>
</nav>

<header class="hero">
    <div class="container">
        <div class="hero-eyebrow">Data Center &amp; Web Hosting Service</div>
        <h1>บริการเครื่องแม่ข่ายเสมือน<br>และ Web Hosting เพื่อบุคลากร มรภ.นครราชสีมา</h1>
        <p class="lead">ยื่นคำขอใช้บริการออนไลน์ได้ทันที ระบบตรวจสอบและออกบัญชีผู้ใช้ให้อัตโนมัติ รองรับทั้งงานเรียนการสอน งานวิจัย บริการวิชาการ และการบริหารจัดการภายในหน่วยงาน</p>
        <div class="d-flex gap-3 flex-wrap">
            <a href="{{ route('service-requests.create') }}" class="btn-amber">เริ่มยื่นคำขอใช้บริการ</a>
            <a href="#services" class="btn-outline-brand" style="border-color:#cfd8c6;color:#eef2e6;">ดูแพ็กเกจบริการ</a>
        </div>
    </div>
</header>

<section id="services">
    <div class="container">
        <div class="section-eyebrow">แพ็กเกจบริการ</div>
        <h2 class="section-title">เลือกบริการที่เหมาะกับระบบของคุณ</h2>
        <p class="section-desc mb-5">ราคาต่อปี ครอบคลุมพื้นที่จัดเก็บข้อมูลและทรัพยากรพื้นฐาน สามารถระบุความต้องการเพิ่มเติมในแบบฟอร์มได้</p>

        <h3 class="h5 mb-3" style="font-family:'Kanit',sans-serif;">เครื่องแม่ข่ายเสมือน (Virtual Server)</h3>
        <div class="row g-4 mb-5">
            @foreach ($virtualServerPlans as $plan)
                <div class="col-md-4">
                    <div class="plan-card">
                        <span class="plan-badge">{{ $plan->size_label }}</span>
                        <div class="plan-price">{{ number_format($plan->fee_per_year, 0) }} <span>บาท/ปี</span></div>
                        <ul>
                            <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg> {{ $plan->cpu_vcpu }} vCPU</li>
                            <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg> RAM {{ $plan->ram_gb }} GB</li>
                            <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg> Storage {{ $plan->storage_gb }} GB</li>
                        </ul>
                        <div class="mt-3" style="font-size:13px;color:var(--ink-soft);">เหมาะสำหรับ {{ $plan->suitable_for }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <h3 class="h5 mb-3" style="font-family:'Kanit',sans-serif;">Web Hosting</h3>
        <div class="row g-4">
            @foreach ($webHostingPlans as $plan)
                <div class="col-md-4">
                    <div class="plan-card">
                        <span class="plan-badge">{{ $plan->size_label }}</span>
                        <div class="plan-price">{{ number_format($plan->fee_per_year, 0) }} <span>บาท/ปี</span></div>
                        <ul>
                            <li><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg> Storage {{ $plan->storage_gb }} GB</li>
                        </ul>
                        <div class="mt-3" style="font-size:13px;color:var(--ink-soft);">เหมาะสำหรับ {{ $plan->suitable_for }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="steps" style="background:var(--surface);border-top:1px solid var(--line);border-bottom:1px solid var(--line);">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div class="section-eyebrow">ขั้นตอนการขอใช้บริการ</div>
                <h2 class="section-title">ใช้เวลาไม่นาน เริ่มได้ทันที</h2>
                <p class="section-desc">กรอกแบบฟอร์มออนไลน์ครั้งเดียว แนบเอกสารที่เกี่ยวข้อง แล้วรอการพิจารณาจากสำนักคอมพิวเตอร์</p>
            </div>
            <div class="col-lg-7">
                <div class="step-item">
                    <div class="step-num">1</div>
                    <div>
                        <div class="step-title">กรอกแบบฟอร์มขอใช้บริการ</div>
                        <div class="step-desc">ระบุข้อมูลผู้ขอใช้บริการ วัตถุประสงค์ และเลือกแพ็กเกจทรัพยากรที่ต้องการ</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">2</div>
                    <div>
                        <div class="step-title">แนบเอกสารประกอบ</div>
                        <div class="step-desc">แนบรายละเอียดระบบ/โครงสร้างระบบ และลายเซ็นยอมรับข้อกำหนด</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">3</div>
                    <div>
                        <div class="step-title">รอการพิจารณาอนุมัติ</div>
                        <div class="step-desc">เจ้าหน้าที่สำนักคอมพิวเตอร์ตรวจสอบข้อมูลและอนุมัติคำขอ</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">4</div>
                    <div>
                        <div class="step-title">รับ Username / Password</div>
                        <div class="step-desc">ระบบออกบัญชีผู้ใช้บริการให้ พร้อมใช้งานตามระยะเวลาโครงการที่ระบุ</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="policy">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5">
                <div class="section-eyebrow">ข้อกำหนดและนโยบาย</div>
                <h2 class="section-title">สิ่งที่ผู้ขอใช้บริการต้องทราบ</h2>
                <p class="section-desc">ผู้ขอใช้บริการต้องปฏิบัติตามกฎหมายและระเบียบของมหาวิทยาลัยอย่างเคร่งครัด</p>
            </div>
            <div class="col-lg-7">
                <ul class="policy-list">
                    <li><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> ปฏิบัติตาม พ.ร.บ. ว่าด้วยการกระทำความผิดเกี่ยวกับคอมพิวเตอร์ และ พ.ร.บ. การรักษาความมั่นคงปลอดภัยไซเบอร์</li>
                    <li><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> ปฏิบัติตามนโยบายคุ้มครองข้อมูลส่วนบุคคล (PDPA) ของมหาวิทยาลัย</li>
                    <li><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> ผู้ใช้บริการมีหน้าที่สำรองข้อมูล (Backup) และดูแลความปลอดภัยระบบของตนเองอย่างสม่ำเสมอ</li>
                    <li><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> ระยะเวลาการใช้งานไม่เกิน 1 ปีต่อครั้ง สามารถยื่นขอต่ออายุได้ก่อนวันหมดอายุ</li>
                    <li><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> สำนักคอมพิวเตอร์มีสิทธิ์ระงับหรือยกเลิกบริการทันทีหากพบการใช้งานที่กระทบต่อระบบส่วนรวม</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="cta-band">
            <h2 class="section-title mb-2">พร้อมเริ่มใช้งานแล้วใช่ไหม?</h2>
            <p class="section-desc mx-auto mb-4">ยื่นคำขอใช้บริการออนไลน์วันนี้ ใช้เวลากรอกฟอร์มไม่ถึง 10 นาที</p>
            <a href="{{ route('service-requests.create') }}" class="btn-amber">ยื่นคำขอใช้บริการตอนนี้</a>
        </div>
    </div>
</section>

<footer>
    <div class="container d-flex flex-wrap justify-content-between gap-3">
        <div>
            <strong style="color:#fff;">สำนักคอมพิวเตอร์ มหาวิทยาลัยราชภัฏนครราชสีมา</strong><br>
            340 ถ.สุรนารายณ์ ต.ในเมือง อ.เมือง จ.นครราชสีมา 30000
        </div>
        <div>
            <a href="{{ route('service-requests.create') }}">ยื่นคำขอใช้บริการ</a> &nbsp;|&nbsp;
            <a href="{{ route('admin.requests.index') }}">สำหรับเจ้าหน้าที่</a>
        </div>
    </div>
</footer>

</body>
</html>
