<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>แบบฟอร์มขอใช้บริการระบบ Data Center และ Web Hosting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #185fa5;
            --accent-dark: #0c447c;
        }
        body {
            font-family: 'Sarabun', sans-serif;
            background: #f1eee7;
            color: #2c2c2a;
        }
        .page-header {
            background: var(--accent-dark);
            color: #fff;
            padding: 28px 0;
        }
        .page-header h1 { font-size: 20px; font-weight: 600; margin: 0; }
        .page-header p { font-size: 14px; margin: 4px 0 0; opacity: .85; }
        .form-card {
            background: #fff;
            border-radius: 12px;
            padding: 28px 32px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }
        .section-title {
            font-size: 17px;
            font-weight: 600;
            color: var(--accent-dark);
            border-bottom: 2px solid var(--accent);
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .sub-title { font-size: 15px; font-weight: 600; margin: 20px 0 10px; }
        label.form-label { font-weight: 500; font-size: 14px; }
        .required::after { content: " *"; color: #a32d2d; }
        .plan-option {
            border: 1px solid #d3d1c7;
            border-radius: 8px;
            padding: 12px 14px;
            cursor: pointer;
            height: 100%;
        }
        .plan-option:hover { border-color: var(--accent); }
        .form-check-input:checked ~ .plan-option,
        .plan-option.selected { border-color: var(--accent); background: #e6f1fb; }
        .dev-row { border: 1px dashed #d3d1c7; border-radius: 8px; padding: 14px; margin-bottom: 12px; }
        .btn-primary { background: var(--accent); border-color: var(--accent); }
        .btn-primary:hover { background: var(--accent-dark); border-color: var(--accent-dark); }
        .helper-text { font-size: 12.5px; color: #5f5e5a; }
        .submit-bar { position: sticky; bottom: 0; background: #f1eee7; padding: 16px 0; }
    </style>
</head>
<body>

<div class="page-header">
    <div class="container">
        <h1>แบบฟอร์มขอใช้บริการระบบ Data Center และ Web Hosting</h1>
        <p>สำนักคอมพิวเตอร์ มหาวิทยาลัยราชภัฏนครราชสีมา</p>
    </div>
</div>

<div class="container py-4" style="max-width: 900px;">

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

        {{-- ส่วนที่ 1: ข้อมูลผู้ขอรับบริการ --}}
        <div class="form-card">
            <div class="section-title">ส่วนที่ 1 ข้อมูลผู้ขอรับบริการและหน่วยงาน</div>

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
            <button type="button" class="btn btn-sm btn-outline-secondary" id="addDeveloper">+ เพิ่มผู้รับผิดชอบ</button>
        </div>

        {{-- ส่วนที่ 2: ความต้องการพื้นฐาน --}}
        <div class="form-card">
            <div class="section-title">ส่วนที่ 2 ข้อมูลความต้องการพื้นฐานสำหรับการพิจารณาการให้บริการ</div>

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
                                <div class="helper-text">{{ number_format($plan->fee_per_year, 0) }} บาท/ปี — {{ $plan->suitable_for }}</div>
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
            <div class="section-title">ส่วนที่ 3 ชื่อโดเมนและบริการที่ต้องการเปิดใช้งาน</div>
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
            <div class="section-title">ส่วนที่ 4 เอกสารแนบและการรับรอง</div>

            <div class="mb-3">
                <label class="form-label required">เอกสารรายละเอียดระบบ / โครงสร้างระบบ (บังคับแนบไฟล์)</label>
                <input type="file" name="system_detail_doc" class="form-control" accept=".pdf,.doc,.docx,.zip" required>
                <div class="helper-text">รองรับไฟล์ PDF, Word หรือ ZIP ขนาดไม่เกิน 10 MB</div>
            </div>
            <div class="mb-3">
                <label class="form-label">หลักฐานการแสกนโค้ด (ถ้ามี แนบไฟล์แต่บังคับให้ส่งภายหลังก่อนขึ้นโฮสต์)</label>
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
            <div class="section-title">ส่วนที่ 5 ข้อกำหนด นโยบาย และการยืนยัน</div>
            <div class="helper-text mb-3">
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

        <div class="submit-bar text-end">
            <button type="submit" class="btn btn-primary btn-lg px-4">ส่งแบบฟอร์ม</button>
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
    document.querySelectorAll('.plan-radio').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.plan-option').forEach(el => el.classList.remove('selected'));
            this.closest('label').querySelector('.plan-option').classList.add('selected');
        });
    });
</script>
</body>
</html>
