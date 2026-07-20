<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>แบบฟอร์มขอใช้บริการระบบ Data Center และ Web Hosting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&family=Kanit:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --leaf: #2e7d32;
            --leaf-dark: #1b5e20;
            --leaf-light: #e8f5e9;
            --sun: #fbc02d;
            --sun-dark: #f9a825;
            --ink: #263228;
            --paper: #f7f8f0;
            --line: #dde3d3;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Sarabun', sans-serif;
            background: var(--paper);
            color: var(--ink);
        }
        h1, h2, .brand, .step-num { font-family: 'Kanit', 'Sarabun', sans-serif; }

        /* ---------- Top utility bar ---------- */
        .topbar {
            background: var(--leaf-dark);
            color: #fff;
            font-size: 12.5px;
            padding: 6px 0;
        }
        .topbar a { color: #fff; opacity: .85; text-decoration: none; }
        .topbar a:hover { opacity: 1; text-decoration: underline; }

        /* ---------- Site header / nav ---------- */
        .site-header {
            background: linear-gradient(120deg, var(--leaf) 0%, var(--leaf-dark) 100%);
            color: #fff;
            padding: 26px 0 60px;
            position: relative;
            overflow: hidden;
        }
        .site-header::after {
            content: "";
            position: absolute;
            right: -60px; top: -80px;
            width: 260px; height: 260px;
            border-radius: 50%;
            background: var(--sun);
            opacity: .18;
        }
        .site-header::before {
            content: "";
            position: absolute;
            left: 10%; bottom: -100px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: var(--sun);
            opacity: .12;
        }
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 15px; }
        .brand .bi { font-size: 20px; color: var(--sun); }
        .header-content { position: relative; z-index: 2; margin-top: 26px; }
        .eyebrow {
            display: inline-block;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.3);
            color: #fff;
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 999px;
            margin-bottom: 12px;
        }
        .site-header h1 { font-size: 26px; font-weight: 700; margin: 0 0 6px; }
        .site-header p.lead-text { font-size: 14.5px; margin: 0; opacity: .9; max-width: 640px; }

        /* ---------- Progress / steps strip ---------- */
        .steps-strip {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(27,94,32,.08);
            margin-top: -40px;
            position: relative;
            z-index: 3;
            padding: 18px 22px;
        }
        .step-item { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #5c6559; }
        .step-num {
            width: 26px; height: 26px;
            border-radius: 50%;
            background: var(--leaf-light);
            color: var(--leaf-dark);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 12.5px;
            flex: 0 0 auto;
        }
        .step-item.done .step-num { background: var(--leaf); color: #fff; }
        .step-divider { flex: 1; height: 2px; background: var(--line); margin: 0 6px; }

        /* ---------- Alerts ---------- */
        .alert-success { background: var(--leaf-light); border-color: var(--leaf); color: var(--leaf-dark); }

        /* ---------- Form cards ---------- */
        .form-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 28px 32px;
            margin-bottom: 22px;
            box-shadow: 0 2px 10px rgba(38,50,40,.05);
        }
        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 17px;
            font-weight: 700;
            color: var(--leaf-dark);
            padding-bottom: 12px;
            margin-bottom: 22px;
            border-bottom: 2px solid var(--leaf-light);
        }
        .section-title .badge-num {
            background: var(--leaf);
            color: #fff;
            width: 28px; height: 28px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700;
        }
        .sub-title {
            font-size: 14.5px;
            font-weight: 600;
            color: var(--ink);
            margin: 22px 0 12px;
            padding-left: 10px;
            border-left: 3px solid var(--sun);
        }
        label.form-label { font-weight: 500; font-size: 14px; color: #3d473f; }
        .required::after { content: " *"; color: #c0392b; }

        .form-control, .form-select {
            border-color: var(--line);
            font-size: 14px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--leaf);
            box-shadow: 0 0 0 .2rem rgba(46,125,50,.15);
        }
        .form-check-input:checked {
            background-color: var(--leaf);
            border-color: var(--leaf);
        }
        .form-check-input:focus {
            border-color: var(--leaf);
            box-shadow: 0 0 0 .2rem rgba(46,125,50,.15);
        }

        .plan-option {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 14px 16px;
            cursor: pointer;
            height: 100%;
            transition: all .15s ease;
            background: #fcfdf9;
        }
        .plan-option:hover { border-color: var(--leaf); box-shadow: 0 2px 8px rgba(46,125,50,.1); }
        .form-check-input:checked ~ .plan-option,
        .plan-option.selected {
            border-color: var(--leaf);
            background: var(--leaf-light);
            box-shadow: 0 0 0 2px rgba(46,125,50,.15);
        }
        .plan-option .fee-tag {
            display: inline-block;
            margin-top: 6px;
            background: #fff8e1;
            color: var(--sun-dark);
            border: 1px solid #ffe082;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .dev-row {
            border: 1px dashed var(--line);
            background: #fbfcf7;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 12px;
        }

        .btn-primary { background: var(--leaf); border-color: var(--leaf); font-weight: 600; }
        .btn-primary:hover { background: var(--leaf-dark); border-color: var(--leaf-dark); }
        .btn-outline-secondary { border-color: var(--line); color: var(--leaf-dark); }
        .btn-outline-secondary:hover { background: var(--leaf-light); border-color: var(--leaf); color: var(--leaf-dark); }
        .btn-accent-outline {
            border: 1px solid var(--sun-dark);
            color: #8a6d00;
            background: #fffdf5;
        }
        .btn-accent-outline:hover { background: #fff3cd; }

        .helper-text { font-size: 12.5px; color: #6b756c; }
        .policy-box {
            background: var(--leaf-light);
            border: 1px solid #c8e6c9;
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 13px;
            color: #2b3d2c;
        }
        .policy-box .bi { color: var(--leaf-dark); }

        .submit-bar {
            position: sticky;
            bottom: 0;
            background: linear-gradient(180deg, rgba(247,248,240,0) 0%, var(--paper) 30%);
            padding: 20px 0 16px;
        }
        .submit-note { font-size: 12.5px; color: #6b756c; }

        /* ---------- Footer ---------- */
        .site-footer {
            background: var(--leaf-dark);
            color: rgba(255,255,255,.85);
            font-size: 13px;
            padding: 26px 0;
            margin-top: 40px;
        }
        .site-footer a { color: #fff; }
        .footer-accent { height: 4px; background: linear-gradient(90deg, var(--sun) 0%, var(--leaf) 100%); }
    </style>
</head>
<body>

<div class="topbar">
    <div class="container d-flex justify-content-between">
        <span><i class="bi bi-building"></i> มหาวิทยาลัยราชภัฏนครราชสีมา</span>
        <span><a href="#"><i class="bi bi-question-circle"></i> ต้องการความช่วยเหลือ?</a></span>
    </div>
</div>

<header class="site-header">
    <div class="container">
        <div class="brand"><i class="bi bi-hdd-network"></i> สำนักคอมพิวเตอร์ | ระบบบริการ Data Center</div>
        <div class="header-content">
            <span class="eyebrow"><i class="bi bi-clipboard-check"></i> แบบฟอร์มออนไลน์</span>
            <h1>ขอใช้บริการระบบ Data Center และ Web Hosting</h1>
            <p class="lead-text">กรอกข้อมูลให้ครบถ้วนเพื่อยื่นคำขอใช้ทรัพยากรเครื่องแม่ข่ายและพื้นที่โฮสติ้งของมหาวิทยาลัย ระบบจะตรวจสอบและแจ้งผลกลับทางอีเมล</p>
        </div>
    </div>
</header>

<div class="container" style="max-width: 900px;">
    <div class="steps-strip d-none d-md-flex align-items-center">
        <div class="step-item done"><span class="step-num">1</span> ผู้ขอใช้บริการ</div>
        <div class="step-divider"></div>
        <div class="step-item done"><span class="step-num">2</span> ความต้องการ</div>
        <div class="step-divider"></div>
        <div class="step-item done"><span class="step-num">3</span> โดเมน</div>
        <div class="step-divider"></div>
        <div class="step-item done"><span class="step-num">4</span> เอกสารแนบ</div>
        <div class="step-divider"></div>
        <div class="step-item done"><span class="step-num">5</span> ยืนยัน & ส่ง</div>
    </div>
</div>

<div class="container py-4" style="max-width: 900px;">

    @if (session('success'))
        <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong><i class="bi bi-exclamation-triangle-fill"></i> กรุณาตรวจสอบข้อมูล:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('service-requests.store') }}" method="POST" enctype="multipart/form-data" id="serviceRequestForm">
        @csrf

        {{-- ส่วนที่ 1: ข้อมูลผู้ขอรับบริการ --}}
        <div class="form-card">
            <div class="section-title"><span class="badge-num">1</span> ข้อมูลผู้ขอรับบริการและหน่วยงาน</div>

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
                <div class="col-md-6">
                    <label class="form-label">โทรศัพท์</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">อีเมล</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
            </div>

            <div class="sub-title">หลักการและวัตถุประสงค์</div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="purpose_type" id="purpose1" value="1.1_teaching" {{ old('purpose_type') == '1.1_teaching' ? 'checked' : '' }} required>
                <label class="form-check-label" for="purpose1">เพื่อสนับสนุนการจัดการเรียนการสอนของมหาวิทยาลัย</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="purpose_type" id="purpose2" value="1.2_academic_research_community" {{ old('purpose_type') == '1.2_academic_research_community' ? 'checked' : '' }}>
                <label class="form-check-label" for="purpose2">เพื่อสนับสนุนการบริการวิชาการ วิจัย และท้องถิ่น</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="purpose_type" id="purpose3" value="1.3_internal_admin" {{ old('purpose_type') == '1.3_internal_admin' ? 'checked' : '' }}>
                <label class="form-check-label" for="purpose3">เพื่อสนับสนุนการบริหารจัดการองค์กรภายในมหาวิทยาลัย</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="purpose_type" id="purpose4" value="1.4_other" {{ old('purpose_type') == '1.4_other' ? 'checked' : '' }}>
                <label class="form-check-label" for="purpose4">อื่น ๆ (ต้องได้รับการพิจารณา)</label>
            </div>
            <input type="text" name="purpose_other_detail" class="form-control mt-1 mb-3" placeholder="ระบุกรณีอื่น ๆ" value="{{ old('purpose_other_detail') }}">

            <div class="row g-3">
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
            <button type="button" class="btn btn-sm btn-accent-outline" id="addDeveloper"><i class="bi bi-plus-circle"></i> เพิ่มผู้รับผิดชอบ</button>
        </div>

        {{-- ส่วนที่ 2: ความต้องการพื้นฐาน --}}
        <div class="form-card">
            <div class="section-title"><span class="badge-num">2</span> ข้อมูลความต้องการพื้นฐานสำหรับการพิจารณาการให้บริการ</div>

            <div class="sub-title">2.1 ประเภทของบริการ</div>
            <div class="form-check mb-1">
                <input class="form-check-input service-type-radio" type="radio" name="service_type" id="stVirtual" value="virtual_server" {{ old('service_type') == 'virtual_server' ? 'checked' : '' }} required>
                <label class="form-check-label" for="stVirtual">เครื่องแม่ข่ายเสมือน</label>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input service-type-radio" type="radio" name="service_type" id="stHosting" value="web_hosting" {{ old('service_type') == 'web_hosting' ? 'checked' : '' }}>
                <label class="form-check-label" for="stHosting">บริการ Web Hosting</label>
            </div>

            <div class="sub-title">2.2 ขนาดทรัพยากรที่ต้องการ</div>
            <div class="row g-2" id="planOptions">
                @foreach ($resourcePlans as $plan)
                    <div class="col-md-4 plan-group" data-service-type="{{ $plan->service_type }}">
                        <label class="w-100">
                            <input type="radio" name="plan_id" value="{{ $plan->plan_id }}" class="d-none plan-radio" {{ old('plan_id') == $plan->plan_id ? 'checked' : '' }}>
                            <div class="plan-option">
                                <div class="fw-semibold">{{ $plan->size_label }}</div>
                                <div class="helper-text">
                                    @if($plan->cpu_vcpu) {{ $plan->cpu_vcpu }} vCPU / {{ $plan->ram_gb }} GB RAM /@endif
                                    {{ $plan->storage_gb }} GB Storage
                                </div>
                                <div class="helper-text">{{ $plan->suitable_for }}</div>
                                <span class="fee-tag">{{ number_format($plan->fee_per_year, 0) }} บาท/ปี</span>
                            </div>
                        </label>
                    </div>
                @endforeach
                <div class="col-md-4">
                    <div class="plan-option">
                        <div class="fw-semibold mb-1">อื่น ๆ (ระบุเอง)</div>
                        <input type="number" name="custom_cpu_vcpu" class="form-control form-control-sm mb-1" placeholder="vCPU">
                        <input type="number" name="custom_ram_gb" class="form-control form-control-sm mb-1" placeholder="RAM (GB)">
                        <input type="number" name="custom_storage_gb" class="form-control form-control-sm mb-1" placeholder="Storage (GB)">
                        <input type="number" step="0.01" name="custom_fee" class="form-control form-control-sm" placeholder="ค่าบริการ (บาท/ปี)">
                    </div>
                </div>
            </div>

            <div class="sub-title">บริการที่ต้องการเปิดใช้งาน</div>
            @php $oldServices = old('enabled_services', []); @endphp
            <div class="form-check mb-1">
                <input class="form-check-input" type="checkbox" name="enabled_services[]" value="ssh" id="svcSsh" {{ in_array('ssh', $oldServices) ? 'checked' : '' }}>
                <label class="form-check-label" for="svcSsh">SSH — ภายในเครือข่ายมหาวิทยาลัยหรือผ่าน VPN และ Port ที่กำหนดเท่านั้น</label>
            </div>
            <div class="form-check mb-1">
                <input class="form-check-input" type="checkbox" name="enabled_services[]" value="http_https" id="svcHttp" {{ in_array('http_https', $oldServices) ? 'checked' : '' }}>
                <label class="form-check-label" for="svcHttp">HTTP / HTTPS สำหรับ Web Service</label>
            </div>
            <div class="form-check mb-1">
                <input class="form-check-input" type="checkbox" name="enabled_services[]" value="database_access" id="svcDb" {{ in_array('database_access', $oldServices) ? 'checked' : '' }}>
                <label class="form-check-label" for="svcDb">Database Access เฉพาะภายในระบบหรือเครือข่ายที่ได้รับอนุญาต</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="enabled_services[]" value="other" id="svcOther" {{ in_array('other', $oldServices) ? 'checked' : '' }}>
                <label class="form-check-label" for="svcOther">อื่น ๆ</label>
            </div>
            <input type="text" name="enabled_services_other_detail" class="form-control mb-3" placeholder="ระบุบริการอื่น ๆ" value="{{ old('enabled_services_other_detail') }}">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">ภาษา/Framework ที่ใช้</label>
                    <input type="text" name="language_framework" class="form-control" placeholder="เช่น PHP 8.3" value="{{ old('language_framework') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ฐานข้อมูลที่ใช้</label>
                    <input type="text" name="database_used" class="form-control" placeholder="เช่น MySQL" value="{{ old('database_used') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">พอร์ตหรือบริการที่ต้องเปิดใช้งาน</label>
                    <input type="text" name="port_service_needed" class="form-control" value="{{ old('port_service_needed') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label d-block">ระบบเชื่อมต่อภายนอก</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="needs_external_connection" value="1" id="extConn" {{ old('needs_external_connection') ? 'checked' : '' }}>
                        <label class="form-check-label" for="extConn">ต้องการเชื่อมต่อกับระบบภายนอก</label>
                    </div>
                </div>
            </div>
        </div>

        {{-- ส่วนที่ 3: โดเมน --}}
        <div class="form-card">
            <div class="section-title"><span class="badge-num">3</span> ชื่อโดเมนและบริการที่ต้องการเปิดใช้งาน</div>
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
            <div class="row">
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
        <div class="form-card">
            <div class="section-title"><span class="badge-num">4</span> เอกสารแนบและการรับรอง</div>

            <div class="mb-3">
                <label class="form-label required"><i class="bi bi-file-earmark-arrow-up"></i> เอกสารรายละเอียดระบบ / โครงสร้างระบบ (บังคับแนบไฟล์)</label>
                <input type="file" name="system_detail_doc" class="form-control" accept=".pdf,.doc,.docx,.zip" required>
                <div class="helper-text">รองรับไฟล์ PDF, Word หรือ ZIP ขนาดไม่เกิน 10 MB</div>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="bi bi-file-earmark-image"></i> หลักฐานการแสกนโค้ด (ถ้ามี แนบไฟล์แต่บังคับให้ส่งภายหลังก่อนขึ้นโฮสต์)</label>
                <input type="file" name="screenshot_evidence" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <div class="sub-title">การรับรองค่าใช้จ่าย</div>
            <div class="form-check mb-1">
                <input class="form-check-input" type="checkbox" name="agree_to_pay" value="1" id="agreePay" {{ old('agree_to_pay') ? 'checked' : '' }}>
                <label class="form-check-label" for="agreePay">ยินยอมชำระค่าบริการตามอัตราที่มหาวิทยาลัยกำหนด</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="request_fee_waiver" value="1" id="requestWaiver" {{ old('request_fee_waiver') ? 'checked' : '' }}>
                <label class="form-check-label" for="requestWaiver">ขอรับการยกเว้นค่าธรรมเนียม</label>
            </div>
            <label class="form-label">เหตุผลประกอบการขอยกเว้นค่าธรรมเนียม</label>
            <textarea name="waiver_reason" class="form-control" rows="2">{{ old('waiver_reason') }}</textarea>
        </div>

        {{-- ส่วนที่ 5: ยอมรับข้อกำหนด --}}
        <div class="form-card">
            <div class="section-title"><span class="badge-num">5</span> ข้อกำหนด นโยบาย และการยืนยัน</div>
            <div class="policy-box mb-3">
                <i class="bi bi-shield-check"></i>
                ผู้ขอใช้บริการต้องปฏิบัติตาม พ.ร.บ. ว่าด้วยการกระทำความผิดเกี่ยวกับคอมพิวเตอร์ พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล (PDPA)
                และระเบียบด้านเทคโนโลยีสารสนเทศของมหาวิทยาลัยอย่างเคร่งครัด รวมถึงมีหน้าที่สำรองข้อมูลและดูแลความปลอดภัยของระบบด้วยตนเอง
            </div>

            <div class="mb-3">
                <label class="form-label required">แนบรูปลายเซ็นผู้ขอใช้บริการ</label>
                <input type="file" name="signature_image" class="form-control" accept="image/*" required>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="accepted" value="1" id="accepted" {{ old('accepted') ? 'checked' : '' }} required>
                <label class="form-check-label" for="accepted">
                    ข้าพเจ้าได้รับทราบและยินยอมปฏิบัติตามข้อกำหนดและแนวปฏิบัติในการใช้บริการของสำนักคอมพิวเตอร์
                </label>
            </div>
        </div>

        <div class="submit-bar d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="submit-note"><i class="bi bi-info-circle"></i> เมื่อกดส่งแล้ว ระบบจะแจ้งผลการพิจารณาไปยังอีเมลที่ระบุไว้</span>
            <button type="submit" class="btn btn-primary btn-lg px-4"><i class="bi bi-send-check"></i> ส่งแบบฟอร์ม</button>
        </div>
    </form>
</div>

<footer class="site-footer">
    <div class="footer-accent"></div>
    <div class="container pt-3 d-flex flex-wrap justify-content-between gap-2">
        <div><i class="bi bi-building"></i> สำนักคอมพิวเตอร์ มหาวิทยาลัยราชภัฏนครราชสีมา</div>
        <div><a href="#">นโยบายความเป็นส่วนตัว</a> · <a href="#">ติดต่อเจ้าหน้าที่</a></div>
    </div>
</footer>

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
    document.querySelectorAll('.plan-radio').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.plan-option').forEach(el => el.classList.remove('selected'));
            this.closest('label').querySelector('.plan-option').classList.add('selected');
        });
    });
</script>
</body>
</html>