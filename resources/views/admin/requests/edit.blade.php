@extends('admin.layout')

@section('title', 'แก้ไขคำขอ ' . $serviceRequest->form_no)
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'แก้ไขรายละเอียดคำขอใช้บริการ')

@section('content')

    @php
        $oldServices = old('enabled_services', $serviceRequest->enabled_services ?? []);
    @endphp

    <style>
        .form-section { margin-bottom: 22px; }
        .form-section:last-child { margin-bottom: 0; }
        .form-section h3 {
            font-size: 14.5px; font-weight: 700; margin-bottom: 14px;
            display: flex; align-items: center; gap: 8px;
        }
        .form-section h3 .sec-icon {
            width: 26px; height: 26px; border-radius: 8px; background: var(--moss-light, #e3efe7);
            color: var(--moss, #2f6b4a); display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .required::after { content: ' *'; color: var(--rust, #b3542c); }
        .plan-pick { border: 1px solid var(--line, #e4e0d3); border-radius: 10px; padding: 10px 12px; cursor: pointer; display: block; }
        .plan-pick input { display: none; }
        .plan-pick.active { border-color: var(--moss, #2f6b4a); background: var(--moss-light, #e3efe7); }
        .plan-pick .po-title { font-weight: 600; font-size: 13.5px; }
        .plan-pick .po-meta { font-size: 12px; color: var(--ink-soft, #837c6c); margin-top: 2px; }
        .info-banner {
            font-size: 12.5px; color: var(--ink-soft, #837c6c); background: var(--surface-2, #f6f4ec);
            border-radius: 8px; padding: 9px 12px; margin-bottom: 18px;
        }
    </style>

    <a href="{{ route('admin.requests.show', $serviceRequest->request_id) }}" class="back-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        กลับไปยังรายละเอียดคำขอ
    </a>

    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="panel">

                <div class="info-banner">
                    กำลังแก้ไขคำขอ <strong>{{ $serviceRequest->form_no }}</strong> —
                    การแก้ไขข้อมูลผู้ขอใช้บริการจะมีผลกับคำขออื่นของรหัสประจำตัวเดียวกันนี้ด้วย
                    (โดเมนและเอกสารแนบแก้ไขแยกที่หน้า
                    <a href="{{ route('admin.domains.index') }}">จัดการโดเมน</a>)
                </div>

                <form action="{{ route('admin.requests.update', $serviceRequest->request_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- ============ ผู้ขอใช้บริการ ============ --}}
                    <div class="form-section">
                        <h3>
                            <span class="sec-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></span>
                            ข้อมูลผู้ขอใช้บริการ
                        </h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">ชื่อ-สกุลผู้ขอใช้บริการ</label>
                                <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $serviceRequest->applicant->full_name) }}" required>
                                @error('full_name') <div class="form-text text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Customer Name</label>
                                <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $serviceRequest->applicant->customer_name) }}" placeholder="ชื่อบัญชีที่ใช้ในระบบ Plesk (ถ้ามี)">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">รหัสบุคลากร/รหัสนักศึกษา</label>
                                <input type="text" class="form-control" value="{{ $serviceRequest->applicant->staff_or_student_id }}" disabled>
                                <div class="form-text" style="font-size:12px;">แก้ไขรหัสประจำตัวไม่ได้ที่หน้านี้ เพราะเป็นตัวระบุตัวตนหลักของผู้ขอ</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">หน่วยงาน</label>
                                <input type="text" name="unit_name" class="form-control" value="{{ old('unit_name', $serviceRequest->applicant->unit_name) }}" required>
                                @error('unit_name') <div class="form-text text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">สังกัด</label>
                                <input type="text" name="affiliation" class="form-control" value="{{ old('affiliation', $serviceRequest->applicant->affiliation) }}" required>
                                @error('affiliation') <div class="form-text text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ตำแหน่ง</label>
                                <input type="text" name="position_title" class="form-control" value="{{ old('position_title', $serviceRequest->applicant->position_title) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">โทรศัพท์</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $serviceRequest->applicant->phone) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">อีเมล</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $serviceRequest->applicant->email) }}">
                                @error('email') <div class="form-text text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- ============ วัตถุประสงค์และระยะเวลา ============ --}}
                    <div class="form-section">
                        <h3>
                            <span class="sec-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19V5h6l2 3h8v11Z"/></svg></span>
                            วัตถุประสงค์และระยะเวลา
                        </h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">วัตถุประสงค์การใช้งาน</label>
                                <select name="purpose_type" class="form-select" required>
                                    @foreach ([
                                        '1.1_teaching' => 'การเรียนการสอน',
                                        '1.2_academic_research_community' => 'บริการวิชาการ/วิจัย/บริการชุมชน',
                                        '1.3_internal_admin' => 'บริหารจัดการภายในหน่วยงาน',
                                        '1.4_other' => 'อื่น ๆ',
                                    ] as $val => $label)
                                        <option value="{{ $val }}" {{ old('purpose_type', $serviceRequest->purpose_type) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('purpose_type') <div class="form-text text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">รายละเอียดกรณีอื่น ๆ</label>
                                <input type="text" name="purpose_other_detail" class="form-control" value="{{ old('purpose_other_detail', $serviceRequest->purpose_other_detail) }}">
                                @error('purpose_other_detail') <div class="form-text text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">วันเริ่มโครงการ/การขอใช้งาน</label>
                                <input type="date" name="project_start_date" class="form-control" value="{{ old('project_start_date', optional($serviceRequest->project_start_date)->format('Y-m-d')) }}" required>
                                @error('project_start_date') <div class="form-text text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">วันสิ้นสุด</label>
                                <input type="date" name="project_end_date" class="form-control" value="{{ old('project_end_date', optional($serviceRequest->project_end_date)->format('Y-m-d')) }}" required>
                                @error('project_end_date') <div class="form-text text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- ============ ทรัพยากรและบริการ ============ --}}
                    <div class="form-section">
                        <h3>
                            <span class="sec-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="6" rx="1.5"/><rect x="4" y="14" width="16" height="6" rx="1.5"/></svg></span>
                            ทรัพยากรและบริการที่ต้องการ
                        </h3>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label required">ประเภทบริการ</label>
                                <select name="service_type" class="form-select" id="serviceTypeSelect" required>
                                    <option value="virtual_server" {{ old('service_type', $serviceRequest->service_type) == 'virtual_server' ? 'selected' : '' }}>Virtual Server</option>
                                    <option value="web_hosting" {{ old('service_type', $serviceRequest->service_type) == 'web_hosting' ? 'selected' : '' }}>Web Hosting</option>
                                </select>
                                @error('service_type') <div class="form-text text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <label class="form-label">แพ็กเกจ (เลือกได้ 1 รายการ หรือปล่อยว่างแล้วกำหนดสเปกเองด้านล่าง)</label>
                        <div class="row g-2 mb-3" id="planOptions">
                            @foreach ($plans as $plan)
                                <div class="col-md-4 plan-group" data-service-type="{{ $plan->service_type }}">
                                    <label class="plan-pick {{ old('plan_id', $serviceRequest->plan_id) == $plan->plan_id ? 'active' : '' }}">
                                        <input type="radio" name="plan_id" value="{{ $plan->plan_id }}" class="plan-radio" {{ old('plan_id', $serviceRequest->plan_id) == $plan->plan_id ? 'checked' : '' }}>
                                        <div class="po-title">{{ $plan->size_label }}</div>
                                        <div class="po-meta">
                                            @if($plan->cpu_vcpu) {{ $plan->cpu_vcpu }} vCPU / {{ $plan->ram_gb }} GB RAM /@endif
                                            {{ $plan->storage_gb }} GB — {{ number_format($plan->fee_per_year, 0) }} บาท/ปี
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                            <div class="col-md-4">
                                <label class="plan-pick {{ old('plan_id', $serviceRequest->plan_id) ? '' : 'active' }}">
                                    <input type="radio" name="plan_id" value="" class="plan-radio" {{ old('plan_id', $serviceRequest->plan_id) ? '' : 'checked' }}>
                                    <div class="po-title">ไม่ใช้แพ็กเกจสำเร็จรูป</div>
                                    <div class="po-meta">กำหนดสเปกเองด้านล่าง</div>
                                </label>
                            </div>
                        </div>
                        @error('plan_id') <div class="form-text text-danger mb-2">{{ $message }}</div> @enderror

                        <div class="row g-2 mb-3">
                            <div class="col-md-3">
                                <input type="number" name="custom_cpu_vcpu" class="form-control form-control-sm" placeholder="vCPU (กำหนดเอง)" value="{{ old('custom_cpu_vcpu', $serviceRequest->custom_cpu_vcpu) }}">
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="custom_ram_gb" class="form-control form-control-sm" placeholder="RAM (GB)" value="{{ old('custom_ram_gb', $serviceRequest->custom_ram_gb) }}">
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="custom_storage_gb" class="form-control form-control-sm" placeholder="Storage (GB)" value="{{ old('custom_storage_gb', $serviceRequest->custom_storage_gb) }}">
                            </div>
                            <div class="col-md-3">
                                <input type="number" step="0.01" name="custom_fee" class="form-control form-control-sm" placeholder="ค่าบริการ (บาท/ปี)" value="{{ old('custom_fee', $serviceRequest->custom_fee) }}">
                            </div>
                        </div>
                        <div class="form-text mb-3" style="font-size:12px;">หากเลือกแพ็กเกจสำเร็จรูปด้านบน ค่าที่กรอกในช่อง "กำหนดเอง" จะถูกล้างทิ้งตอนบันทึก</div>

                        <label class="form-label">บริการที่ต้องการเปิดใช้งาน</label>
                        <div class="row g-2 mb-2">
                            <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="enabled_services[]" value="ssh" id="svcSsh" {{ in_array('ssh', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcSsh">SSH — ภายในเครือข่ายหรือผ่าน VPN</label></div></div>
                            <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="enabled_services[]" value="http_https" id="svcHttp" {{ in_array('http_https', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcHttp">HTTP / HTTPS สำหรับ Web Service</label></div></div>
                            <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="enabled_services[]" value="database_access" id="svcDb" {{ in_array('database_access', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcDb">Database Access ภายในระบบที่อนุญาต</label></div></div>
                            <div class="col-md-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="enabled_services[]" value="other" id="svcOther" {{ in_array('other', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcOther">อื่น ๆ</label></div></div>
                        </div>
                        @error('enabled_services') <div class="form-text text-danger mb-2">{{ $message }}</div> @enderror
                        <input type="text" name="enabled_services_other_detail" class="form-control mb-3" placeholder="ระบุบริการอื่น ๆ" value="{{ old('enabled_services_other_detail', $serviceRequest->enabled_services_other_detail) }}">

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">ภาษา/Framework ที่ใช้</label>
                                <input type="text" name="language_framework" class="form-control" value="{{ old('language_framework', $serviceRequest->language_framework) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ฐานข้อมูลที่ใช้</label>
                                <input type="text" name="database_used" class="form-control" value="{{ old('database_used', $serviceRequest->database_used) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">พอร์ต/บริการที่ต้องเปิดเพิ่มเติม</label>
                                <input type="text" name="port_service_needed" class="form-control" value="{{ old('port_service_needed', $serviceRequest->port_service_needed) }}">
                            </div>
                        </div>
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="needs_external_connection" value="1" id="extConn" {{ old('needs_external_connection', $serviceRequest->needs_external_connection) ? 'checked' : '' }}>
                            <label class="form-check-label" for="extConn">ต้องการเชื่อมต่อกับระบบภายนอก</label>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.requests.show', $serviceRequest->request_id) }}" class="btn btn-outline-soft">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:5px;"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                            ยกเลิก
                        </a>
                        <button type="submit" class="btn btn-amber">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                            บันทึกการแก้ไข
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            // สลับ active state ของการ์ดเลือกแพ็กเกจตอนคลิก
            document.querySelectorAll('.plan-radio').forEach(function (radio) {
                radio.addEventListener('change', function () {
                    document.querySelectorAll('.plan-pick').forEach(function (el) { el.classList.remove('active'); });
                    radio.closest('.plan-pick').classList.add('active');
                });
            });

            // กรองการ์ดแพ็กเกจให้ตรงกับประเภทบริการที่เลือก
            var serviceTypeSelect = document.getElementById('serviceTypeSelect');
            function syncPlanVisibility() {
                var type = serviceTypeSelect.value;
                document.querySelectorAll('.plan-group').forEach(function (el) {
                    el.style.display = (el.dataset.serviceType === type) ? '' : 'none';
                });
            }
            serviceTypeSelect.addEventListener('change', syncPlanVisibility);
            syncPlanVisibility();
        })();
    </script>

@endsection