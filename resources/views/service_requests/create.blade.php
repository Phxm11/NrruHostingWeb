<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>แบบฟอร์มขอใช้บริการระบบ Data Center และ Web Hosting</title>
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

        /* ---------- Progress nav (desktop) ---------- */
        .form-layout { display: grid; grid-template-columns: 270px 1fr; gap: 32px; align-items: start; }
        .side-nav { position: sticky; top: 24px; }
        .side-nav .nav-card {
            background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius);
            padding: 18px; box-shadow: var(--shadow-sm);
        }
        .side-nav .nav-title { font-size: 12px; text-transform: uppercase; letter-spacing: .06em; color: var(--moss); font-weight: 600; margin-bottom: 14px; }
        .step-link {
            display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 12px;
            color: var(--ink-soft); font-size: 14px; font-weight: 500; cursor: pointer; transition: background .15s ease, color .15s ease; margin-bottom: 4px;
            border: 1px solid transparent;
        }
        .step-link:hover { background: var(--moss-light); color: var(--forest); }
        .step-link .num {
            width: 28px; height: 28px; border-radius: 9px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
            font-family: 'Kanit', sans-serif; font-weight: 700; font-size: 13px; background: var(--bg); color: var(--ink-soft);
            border: 1px solid var(--line); transition: all .15s ease;
        }
        .step-link.active { background: var(--moss-light); color: var(--forest); border-color: #d6e3c6; }
        .step-link.active .num { background: linear-gradient(135deg, var(--forest), var(--forest-2)); color: #fff; border-color: transparent; }
        .step-link.done .num { background: linear-gradient(135deg, var(--moss), #74a357); color: #fff; border-color: transparent; }
        .side-hint { margin-top: 14px; font-size: 12.5px; color: var(--ink-soft); background: var(--amber-light); border: 1px solid #ecdfb8; border-radius: 12px; padding: 10px 12px; }

        /* ---------- Mobile stepper ---------- */
        .mobile-stepper { display: none; position: sticky; top: 0; z-index: 40; background: rgba(250,249,244,.92); backdrop-filter: blur(10px); border-bottom: 1px solid var(--line); padding: 12px 0; }
        .mobile-stepper .ms-track { display: flex; gap: 6px; overflow-x: auto; }
        .ms-dot { flex: 1; min-width: 54px; text-align: center; font-size: 11px; color: var(--ink-soft); padding-top: 6px; position: relative; }
        .ms-dot .ring { width: 26px; height: 26px; border-radius: 50%; margin: 0 auto 4px; display: flex; align-items: center; justify-content: center; font-family: 'Kanit', sans-serif; font-weight: 700; font-size: 12px; background: var(--surface); border: 1px solid var(--line); color: var(--ink-soft); }
        .ms-dot.active .ring { background: linear-gradient(135deg, var(--forest), var(--forest-2)); color: #fff; border-color: transparent; }
        .ms-dot.done .ring { background: var(--moss); color: #fff; border-color: transparent; }

        /* ---------- Form cards ---------- */
        .form-card {
            background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius);
            padding: 26px 28px; margin-bottom: 22px; box-shadow: var(--shadow-sm); scroll-margin-top: 90px;
        }
        .section-head { display: flex; align-items: center; gap: 14px; margin-bottom: 22px; }
        .section-head .sec-num {
            width: 38px; height: 38px; border-radius: 12px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
            font-family: 'Kanit', sans-serif; font-weight: 700; font-size: 16px; color: #fff;
            background: linear-gradient(135deg, var(--amber), var(--amber-deep)); box-shadow: 0 6px 14px -6px rgba(185,132,15,.6);
        }
        .section-head h2 { font-size: 17px; font-weight: 600; color: var(--forest); margin: 0; }
        .sub-title { font-size: 14.5px; font-weight: 600; color: var(--ink); margin: 22px 0 10px; display: flex; align-items: center; gap: 8px; }
        .sub-title::before { content: ''; width: 4px; height: 16px; border-radius: 4px; background: var(--moss); display: inline-block; }

        label.form-label { font-weight: 500; font-size: 14px; }
        .required::after { content: " *"; color: var(--rust); }
        .helper-text { font-size: 12.5px; color: var(--ink-soft); }

        .form-control, .form-select { border: 1px solid var(--line); border-radius: 11px; font-size: 14px; padding: 9px 13px; transition: box-shadow .15s ease, border-color .15s ease; }
        .form-control:focus, .form-select:focus { border-color: var(--moss); box-shadow: 0 0 0 3px rgba(95,139,70,.16); }

        .form-check-input:checked { background-color: var(--forest); border-color: var(--forest); }
        .form-switch .form-check-input:checked { background-color: var(--moss); border-color: var(--moss); }

        /* plan options */
        .plan-option {
            border: 1.5px solid var(--line); border-radius: 14px; padding: 14px 16px; cursor: pointer; height: 100%;
            transition: border-color .15s ease, background .15s ease, box-shadow .15s ease; background: var(--surface);
        }
        .plan-option:hover { border-color: var(--moss); }
        .plan-option .po-title { font-weight: 600; font-size: 15px; color: var(--forest); }
        .plan-option .po-meta { font-size: 12.5px; color: var(--ink-soft); margin-top: 4px; }
        .plan-option.selected { border-color: var(--forest); background: var(--moss-light); box-shadow: 0 0 0 3px rgba(95,139,70,.12); }
        .plan-option.selected .po-title::after { content: ' ✓'; color: var(--forest); }

        .dev-row { border: 1px dashed var(--line); border-radius: 12px; padding: 14px; margin-bottom: 12px; background: var(--bg); }

        .service-type-card { border: 1.5px solid var(--line); border-radius: 14px; padding: 14px 18px; cursor: pointer; display: flex; align-items: center; gap: 12px; transition: border-color .15s ease, background .15s ease; }
        .service-type-card:hover { border-color: var(--moss); }
        .service-type-card:has(input:checked) { border-color: var(--forest); background: var(--moss-light); }
        .service-type-card .st-icon { width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: var(--amber-light); color: var(--amber-deep); flex-shrink: 0; }
        .service-type-card .st-text b { display: block; font-size: 14.5px; }
        .service-type-card .st-text span { font-size: 12.5px; color: var(--ink-soft); }

        .custom-card { border: 1.5px dashed var(--line); border-radius: 14px; padding: 14px 16px; background: var(--surface); }
        .custom-card .fw-semibold { color: var(--forest); }

        .doc-drop {
            border: 1.5px dashed var(--line); border-radius: 12px; padding: 10px; background: var(--surface);
        }
        .doc-drop:focus-within { border-color: var(--moss); box-shadow: 0 0 0 3px rgba(95,139,70,.12); }

        .accept-box { background: var(--bg); border: 1px solid var(--line); border-radius: 14px; padding: 16px 18px; font-size: 14px; color: var(--ink-soft); }

        .btn-brand { background: linear-gradient(135deg, var(--forest), var(--forest-2)); border: none; color: #fff; font-weight: 600; border-radius: 12px; padding: 12px 30px; font-size: 15.5px; transition: transform .15s ease, filter .15s ease; }
        .btn-brand:hover { filter: brightness(1.08); color: #fff; transform: translateY(-1px); }

        .submit-bar { position: sticky; bottom: 0; z-index: 30; background: rgba(250,249,244,.92); backdrop-filter: blur(10px); border-top: 1px solid var(--line); padding: 16px 0; margin-top: 8px; }
        .submit-bar .inner { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .submit-bar .hint { font-size: 13px; color: var(--ink-soft); }
        .submit-bar .hint b { color: var(--forest); }

        @media (max-width: 991px) {
            .form-layout { grid-template-columns: 1fr; }
            .side-nav { display: none; }
            .mobile-stepper { display: block; }
        }
        @media (max-width: 575px) {
            .form-card { padding: 20px 18px; }
        }
    </style>
</head>
<body>

<div class="page-header">
    <div class="container">
        <div class="brand-row">
            <div style="width:40px;height:40px;border-radius:11px;background:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,.16);padding:5px;">
                <img src="{{ asset('images/logo.png') }}" alt="โลโก้ระบบ" style="width:100%;height:100%;object-fit:contain;display:block;">
            </div>
            <div>
                <h1>แบบฟอร์มขอใช้บริการระบบ Data Center และ Web Hosting</h1>
                <p>สำนักคอมพิวเตอร์ มหาวิทยาลัยราชภัฏนครราชสีมา</p>
            </div>
        </div>
    </div>
</div>

<div class="mobile-stepper">
    <div class="container">
        <div class="ms-track" id="msTrack">
            <div class="ms-dot" data-target="sec1"><div class="ring">1</div>ผู้ขอ</div>
            <div class="ms-dot" data-target="sec2"><div class="ring">2</div>ความต้องการ</div>
            <div class="ms-dot" data-target="sec3"><div class="ring">3</div>โดเมน</div>
            <div class="ms-dot" data-target="sec4"><div class="ring">4</div>เอกสาร</div>
            <div class="ms-dot" data-target="sec5"><div class="ring">5</div>ยืนยัน</div>
        </div>
    </div>
</div>

<div class="container py-4" style="max-width: 1040px;">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>กรุณาตรวจสอบข้อมูล:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('service-requests.store') }}" method="POST" enctype="multipart/form-data" id="serviceRequestForm">
        @csrf

        <div class="form-layout">
            {{-- Sticky progress nav (desktop) --}}
            <aside class="side-nav d-none d-lg-block">
                <div class="nav-card">
                    <div class="nav-title">ขั้นตอนในแบบฟอร์ม</div>
                    <a class="step-link" data-target="sec1"><span class="num">1</span> ข้อมูลผู้ขอ</a>
                    <a class="step-link" data-target="sec2"><span class="num">2</span> ความต้องการพื้นฐาน</a>
                    <a class="step-link" data-target="sec3"><span class="num">3</span> ชื่อโดเมน</a>
                    <a class="step-link" data-target="sec4"><span class="num">4</span> เอกสารแนบ</a>
                    <a class="step-link" data-target="sec5"><span class="num">5</span> ยอมรับข้อกำหนด</a>
                    <div class="side-hint">กรอกข้อมูลให้ครบถ้วนทุกส่วนแล้วกดส่งแบบฟอร์มด้านล่าง</div>
                </div>
            </aside>

            <div>
                {{-- ส่วนที่ 1: ข้อมูลผู้ขอรับบริการ --}}
                <div class="form-card" id="sec1">
                    <div class="section-head">
                        <div class="sec-num">1</div>
                        <h2>ข้อมูลผู้ขอรับบริการและหน่วยงาน</h2>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">ชื่อ-สกุลผู้ขอใช้บริการ</label>
                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">รหัสบุคลากร/รหัสนักศึกษา</label>
                            <input type="text" name="staff_or_student_id" class="form-control" value="{{ old('staff_or_student_id') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">หน่วยงาน</label>
                            <input type="text" name="unit_name" class="form-control" value="{{ old('unit_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">สังกัด</label>
                            <input type="text" name="affiliation" class="form-control" value="{{ old('affiliation') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ตำแหน่ง</label>
                            <input type="text" name="position_title" class="form-control" value="{{ old('position_title') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">โทรศัพท์</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">อีเมล</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="sub-title">หลักการและวัตถุประสงค์</div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="service-type-card">
                                <input class="form-check-input" type="radio" name="purpose_type" value="1.1_teaching" {{ old('purpose_type') == '1.1_teaching' ? 'checked' : '' }} required>
                                <div class="st-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-5 9 5M5 10v9h14v-9"/></svg></div>
                                <div class="st-text"><b>การเรียนการสอน</b><span>สนับสนุนการจัดการเรียนการสอน</span></div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="service-type-card">
                                <input class="form-check-input" type="radio" name="purpose_type" value="1.2_academic_research_community" {{ old('purpose_type') == '1.2_academic_research_community' ? 'checked' : '' }}>
                                <div class="st-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V5h6l2 3h8v11Z"/></svg></div>
                                <div class="st-text"><b>บริการวิชาการ/วิจัย</b><span>สนับสนุนการวิจัยและท้องถิ่น</span></div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="service-type-card">
                                <input class="form-check-input" type="radio" name="purpose_type" value="1.3_internal_admin" {{ old('purpose_type') == '1.3_internal_admin' ? 'checked' : '' }}>
                                <div class="st-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/></svg></div>
                                <div class="st-text"><b>บริหารจัดการองค์กร</b><span>สนับสนุนการบริหารภายใน</span></div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="service-type-card">
                                <input class="form-check-input" type="radio" name="purpose_type" value="1.4_other" {{ old('purpose_type') == '1.4_other' ? 'checked' : '' }}>
                                <div class="st-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/></svg></div>
                                <div class="st-text"><b>อื่น ๆ</b><span>ต้องได้รับการพิจารณา</span></div>
                            </label>
                        </div>
                    </div>
                    <input type="text" name="purpose_other_detail" class="form-control mt-2" placeholder="ระบุกรณีอื่น ๆ" value="{{ old('purpose_other_detail') }}">

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label required">ระยะเวลาโครงการ/การขอใช้งาน ตั้งแต่วันที่</label>
                            <input type="date" name="project_start_date" class="form-control" value="{{ old('project_start_date') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">ถึงวันที่ (ไม่เกิน 1 ปี)</label>
                            <input type="date" name="project_end_date" class="form-control" value="{{ old('project_end_date') }}" required>
                        </div>
                    </div>

                    <div class="sub-title">รายชื่อและช่องทางติดต่อผู้รับผิดชอบในการพัฒนาระบบ</div>
                    <div id="developersWrapper">
                        <div class="dev-row">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" name="developers[0][full_name]" class="form-control" placeholder="ชื่อ-นามสกุล *" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="developers[0][role_desc]" class="form-control" placeholder="บทบาท/หน้าที่">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="developers[0][phone]" class="form-control" placeholder="เบอร์โทร">
                                </div>
                                <div class="col-md-2">
                                    <input type="email" name="developers[0][email]" class="form-control" placeholder="อีเมล">
                                </div>
                                <div class="col-md-1 d-flex align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-dev" title="ลบแถวนี้">×</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="addDeveloper">+ เพิ่มผู้รับผิดชอบ</button>
                </div>

                {{-- ส่วนที่ 2: ความต้องการพื้นฐาน --}}
                <div class="form-card" id="sec2">
                    <div class="section-head">
                        <div class="sec-num">2</div>
                        <h2>ข้อมูลความต้องการพื้นฐานสำหรับการพิจารณาการให้บริการ</h2>
                    </div>

                    <div class="sub-title">2.1 ประเภทของบริการ</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="service-type-card">
                                <input class="form-check-input service-type-radio" type="radio" name="service_type" id="stVirtual" value="virtual_server" {{ old('service_type') == 'virtual_server' ? 'checked' : '' }} required>
                                <div class="st-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="4" width="16" height="6" rx="1.5"/><rect x="4" y="14" width="16" height="6" rx="1.5"/><path d="M7 7h.01M7 17h.01"/></svg></div>
                                <div class="st-text"><b>เครื่องแม่ข่ายเสมือน</b><span>Virtual Server</span></div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="service-type-card">
                                <input class="form-check-input service-type-radio" type="radio" name="service_type" id="stHosting" value="web_hosting" {{ old('service_type') == 'web_hosting' ? 'checked' : '' }}>
                                <div class="st-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg></div>
                                <div class="st-text"><b>บริการ Web Hosting</b><span>โฮสต์เว็บไซต์</span></div>
                            </label>
                        </div>
                    </div>

                    <div class="sub-title">2.2 ขนาดทรัพยากรที่ต้องการ</div>
                    <div class="row g-3" id="planOptions">
                        @foreach ($resourcePlans as $plan)
                            <div class="col-md-4 plan-group" data-service-type="{{ $plan->service_type }}">
                                <label class="w-100">
                                    <input type="radio" name="plan_id" value="{{ $plan->plan_id }}" class="d-none plan-radio" {{ old('plan_id') == $plan->plan_id ? 'checked' : '' }}>
                                    <div class="plan-option">
                                        <div class="po-title">{{ $plan->size_label }}</div>
                                        <div class="po-meta">
                                            @if($plan->cpu_vcpu) {{ $plan->cpu_vcpu }} vCPU / {{ $plan->ram_gb }} GB RAM /@endif
                                            {{ $plan->storage_gb }} GB Storage
                                        </div>
                                        <div class="po-meta">{{ number_format($plan->fee_per_year, 0) }} บาท/ปี — {{ $plan->suitable_for }}</div>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                        <div class="col-md-4">
                            <div class="custom-card">
                                <div class="fw-semibold mb-2">อื่น ๆ (ระบุเอง)</div>
                                <input type="number" name="custom_cpu_vcpu" class="form-control form-control-sm mb-2" placeholder="vCPU">
                                <input type="number" name="custom_ram_gb" class="form-control form-control-sm mb-2" placeholder="RAM (GB)">
                                <input type="number" name="custom_storage_gb" class="form-control form-control-sm mb-2" placeholder="Storage (GB)">
                                <input type="number" step="0.01" name="custom_fee" class="form-control form-control-sm" placeholder="ค่าบริการ (บาท/ปี)">
                            </div>
                        </div>
                    </div>

                    <div class="sub-title">บริการที่ต้องการเปิดใช้งาน</div>
                    @php $oldServices = old('enabled_services', []); @endphp
                    <div class="row g-2">
                        <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="enabled_services[]" value="ssh" id="svcSsh" {{ in_array('ssh', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcSsh">SSH — ภายในเครือข่ายหรือผ่าน VPN</label></div></div>
                        <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="enabled_services[]" value="http_https" id="svcHttp" {{ in_array('http_https', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcHttp">HTTP / HTTPS สำหรับ Web Service</label></div></div>
                        <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="enabled_services[]" value="database_access" id="svcDb" {{ in_array('database_access', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcDb">Database Access ภายในระบบที่อนุญาต</label></div></div>
                        <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="enabled_services[]" value="other" id="svcOther" {{ in_array('other', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcOther">อื่น ๆ</label></div></div>
                    </div>
                    <input type="text" name="enabled_services_other_detail" class="form-control mt-2" placeholder="ระบุบริการอื่น ๆ" value="{{ old('enabled_services_other_detail') }}">

                    <div class="row g-3 mt-1">
                        <div class="col-md-4">
                            <label class="form-label">ภาษา/Framework ที่ใช้</label>
                            <input type="text" name="language_framework" class="form-control" placeholder="เช่น PHP 8.3" value="{{ old('language_framework') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ฐานข้อมูลที่ใช้</label>
                            <input type="text" name="database_used" class="form-control" placeholder="เช่น MySQL" value="{{ old('database_used') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">พอร์ตหรือบริการที่ต้องเปิดใช้งาน</label>
                            <input type="text" name="port_service_needed" class="form-control" value="{{ old('port_service_needed') }}">
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
                        <div class="sec-num">3</div>
                        <h2>ชื่อโดเมนและบริการที่ต้องการเปิดใช้งาน</h2>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">ชื่อโดเมนที่ต้องการ</label>
                            <input type="text" name="domain_name" class="form-control" placeholder="เช่น wellbeing-nurse.nrru.ac.th" value="{{ old('domain_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">รูปแบบโดเมน</label>
                            <input type="text" name="domain_format" class="form-control" placeholder="เช่น test -> test-edu.nrru.ac.th" value="{{ old('domain_format') }}">
                        </div>
                    </div>

                    <div class="sub-title">รหัสสังกัดคณะ/หน่วยงานย่อย</div>
                    <div class="row g-1">
                        @foreach ($departmentCodes as $dept)
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
                            <input type="text" name="department_other" class="form-control form-control-sm" value="{{ old('department_other') }}">
                        </div>
                    </div>
                </div>

                {{-- ส่วนที่ 4: เอกสารแนบและการรับรอง --}}
                <div class="form-card" id="sec4">
                    <div class="section-head">
                        <div class="sec-num">4</div>
                        <h2>เอกสารแนบและการรับรอง</h2>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">เอกสารรายละเอียดระบบ / โครงสร้างระบบ (บังคับแนบไฟล์)</label>
                        <div class="doc-drop"><input type="file" name="system_detail_doc" class="form-control" accept=".pdf,.doc,.docx,.zip" required></div>
                        <div class="helper-text mt-1">รองรับไฟล์ PDF, Word หรือ ZIP ขนาดไม่เกิน 10 MB</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">หลักฐานการแสกนโค้ด (ถ้ามี แนบไฟล์แต่บังคับให้ส่งภายหลังก่อนขึ้นโฮสต์)</label>
                        <div class="doc-drop"><input type="file" name="screenshot_evidence" class="form-control" accept=".pdf,.jpg,.jpeg,.png"></div>
                    </div>

                    <div class="sub-title">การรับรองค่าใช้จ่าย</div>
                    <div class="form-check mb-1"><input class="form-check-input" type="checkbox" name="agree_to_pay" value="1" id="agreePay" {{ old('agree_to_pay') ? 'checked' : '' }}><label class="form-check-label" for="agreePay">ยินยอมชำระค่าบริการตามอัตราที่มหาวิทยาลัยกำหนด</label></div>
                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="request_fee_waiver" value="1" id="requestWaiver" {{ old('request_fee_waiver') ? 'checked' : '' }}><label class="form-check-label" for="requestWaiver">ขอรับการยกเว้นค่าธรรมเนียม</label></div>
                    <label class="form-label">เหตุผลประกอบการขอยกเว้นค่าธรรมเนียม</label>
                    <textarea name="waiver_reason" class="form-control" rows="2">{{ old('waiver_reason') }}</textarea>
                </div>

                {{-- ส่วนที่ 5: ยอมรับข้อกำหนด --}}
                <div class="form-card" id="sec5">
                    <div class="section-head">
                        <div class="sec-num">5</div>
                        <h2>ข้อกำหนด นโยบาย และการยืนยัน</h2>
                    </div>
                    <div class="accept-box mb-3">
                        ผู้ขอใช้บริการต้องปฏิบัติตาม พ.ร.บ. ว่าด้วยการกระทำความผิดเกี่ยวกับคอมพิวเตอร์ พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล (PDPA)
                        และระเบียบด้านเทคโนโลยีสารสนเทศของมหาวิทยาลัยอย่างเคร่งครัด รวมถึงมีหน้าที่สำรองข้อมูลและดูแลความปลอดภัยของระบบด้วยตนเอง
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">แนบรูปลายเซ็นผู้ขอใช้บริการ</label>
                        <div class="doc-drop"><input type="file" name="signature_image" class="form-control" accept="image/*" required></div>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accepted" value="1" id="accepted" {{ old('accepted') ? 'checked' : '' }} required>
                        <label class="form-check-label" for="accepted">
                            ข้าพเจ้าได้รับทราบและยินยอมปฏิบัติตามข้อกำหนดและแนวปฏิบัติในการใช้บริการของสำนักคอมพิวเตอร์
                        </label>
                    </div>
                </div>

                <div class="submit-bar">
                    <div class="container px-0">
                        <div class="inner">
                            <div class="hint">เมื่อกดส่งแล้ว ระบบจะบันทึกคำขอและรอการพิจารณา</div>
                            <button type="submit" class="btn btn-brand px-4">ส่งแบบฟอร์ม</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

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
                <div class="col-md-4">
                    <input type="text" name="developers[${devIndex}][full_name]" class="form-control" placeholder="ชื่อ-นามสกุล *" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="developers[${devIndex}][role_desc]" class="form-control" placeholder="บทบาท/หน้าที่">
                </div>
                <div class="col-md-2">
                    <input type="text" name="developers[${devIndex}][phone]" class="form-control" placeholder="เบอร์โทร">
                </div>
                <div class="col-md-2">
                    <input type="email" name="developers[${devIndex}][email]" class="form-control" placeholder="อีเมล">
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-dev" title="ลบแถวนี้">×</button>
                </div>
            </div>`;
        wrapper.appendChild(row);
        devIndex++;
    });

    document.getElementById('developersWrapper').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-dev')) {
            if (document.querySelectorAll('.dev-row').length > 1) {
                e.target.closest('.dev-row').remove();
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
                links.forEach(l => l.classList.toggle('active', l.dataset.target === id));
                links.forEach(l => l.classList.toggle('done', l.dataset.target < id));
                dots.forEach(d => {
                    d.classList.toggle('active', d.dataset.target === id);
                    d.classList.toggle('done', d.dataset.target < id);
                });
            }
        });
    }, { rootMargin: '-45% 0px -45% 0px', threshold: 0 });
    sections.forEach(s => s && spy.observe(s));

    // คลิกเพื่อเลื่อนไปส่วนนั้น
    document.querySelectorAll('.step-link, .ms-dot').forEach(el => {
        el.addEventListener('click', () => {
            const target = document.getElementById(el.dataset.target);
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
</script>
</body>
</html>
