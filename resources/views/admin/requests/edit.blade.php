@extends('admin.layout')

@section('title', 'แก้ไขคำขอ ' . $serviceRequest->form_no)
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'แก้ไขรายละเอียดคำขอใช้บริการ')
@section('page-description', 'ตรวจสอบและปรับข้อมูลในคำขอ แล้วบันทึกการเปลี่ยนแปลง')

@section('content')

    @php
        $oldServices = old('enabled_services', $serviceRequest->enabled_services ?? []);
        $selectedPlanId = old('plan_id', $serviceRequest->plan_id);
        $purposeLabels = [
            '1.1_teaching' => 'การเรียนการสอน',
            '1.2_academic_research_community' => 'บริการวิชาการ/วิจัย/บริการชุมชน',
            '1.3_internal_admin' => 'บริหารจัดการภายในหน่วยงาน',
            '1.4_other' => 'อื่น ๆ',
        ];
    @endphp
    @push('styles')
        <link rel="stylesheet" href="{{ versioned_asset('css/admin/pages/requests-edit.css') }}">
    @endpush


    <div class="request-edit-page">
        <div class="edit-intro">
            <div>
                <a href="{{ route('admin.requests.show', $serviceRequest->request_id) }}" class="back-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                    กลับไปยังรายละเอียดคำขอ
                </a>
                <h2>คำขอ {{ $serviceRequest->form_no }}</h2>
                <p>แก้ไขข้อมูลตามหัวข้อด้านล่าง ระบบจะบันทึกทุกส่วนพร้อมกันเมื่อกดบันทึก</p>
            </div>
            <a href="{{ route('admin.requests.show', $serviceRequest->request_id) }}" target="_blank" rel="noopener" class="btn btn-outline-soft btn-sm">เปิดคำขอในแท็บใหม่</a>
        </div>

        <nav class="edit-section-nav" aria-label="หัวข้อในแบบฟอร์ม">
            <a href="#acc-identity"><span>1</span> ผู้ขอใช้บริการ</a>
            <a href="#acc-purpose"><span>2</span> วัตถุประสงค์และระยะเวลา</a>
            <a href="#acc-resource"><span>3</span> ทรัพยากรและบริการ</a>
        </nav>

        @if($errors->any())
            <div class="edit-errors" role="alert">
                <strong>ยังบันทึกไม่ได้</strong>
                <span>กรุณาตรวจสอบข้อมูลที่แสดงข้อความสีแดงด้านล่าง ({{ $errors->count() }} รายการ)</span>
            </div>
        @endif

    <form action="{{ route('admin.requests.update', $serviceRequest->request_id) }}" method="POST" id="editRequestForm">
        @csrf
        @method('PUT')

        <div class="edit-layout">

            {{-- ข้อมูลสำคัญของคำขอ อัปเดตตามการแก้ไขในฟอร์ม --}}
            <aside class="summary-card" id="summaryCard" aria-labelledby="summary-heading">
                <div class="summary-heading">
                    <h2 id="summary-heading">สรุปคำขอ</h2>
                    <p>{{ $serviceRequest->form_no }} · อัปเดตตามที่แก้ไข</p>
                </div>
                <div class="summary-item"><span>ผู้ขอใช้บริการ</span><strong id="sumFullName" data-original="{{ $serviceRequest->applicant->full_name }}">{{ old('full_name', $serviceRequest->applicant->full_name) ?: '—' }}</strong></div>
                <div class="summary-item"><span>หน่วยงาน</span><strong id="sumUnitName">{{ old('unit_name', $serviceRequest->applicant->unit_name) ?: '—' }}</strong></div>
                <div class="summary-item"><span>Customer Name</span><strong id="sumCustomerName">{{ old('customer_name', $serviceRequest->applicant->customer_name) ?: '—' }}</strong></div>
                <div class="summary-item"><span>วัตถุประสงค์</span><strong id="sumPurpose">{{ $purposeLabels[old('purpose_type', $serviceRequest->purpose_type)] ?? '—' }}</strong></div>
                <div class="summary-item summary-dates">
                    <div><span>วันเริ่มใช้งาน</span><strong id="sumStartDate">—</strong></div>
                    <div><span>วันสิ้นสุด</span><strong id="sumEndDate">—</strong></div>
                </div>
                <div class="summary-item"><span>ประเภทบริการ</span><strong id="sumServiceType">{{ old('service_type', $serviceRequest->service_type) == 'web_hosting' ? 'Web Hosting' : 'Virtual Server' }}</strong></div>
                <div class="summary-item"><span>ทรัพยากร</span><strong id="sumPlan">—</strong></div>
                <div class="summary-item"><span>บริการที่เปิดใช้</span><strong id="sumServices">—</strong></div>
                <div class="summary-item summary-item--price"><span>ค่าบริการต่อปี</span><strong id="sumPrice">—</strong></div>
            </aside>

            <div class="edit-form-sections">

                    <div class="alert-important">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg>
                        <div class="a-text">
                            ข้อมูลผู้ขอที่แก้ไขจะมีผลกับคำขอนี้และบัญชีบริการที่ผูกอยู่เท่านั้น
                            หากต้องแก้ไขโดเมน ให้ไปที่หน้า <a href="{{ route('admin.domains.index') }}">จัดการโดเมน</a>
                        </div>
                    </div>

                    {{-- ============ 1. ผู้ขอใช้บริการ ============ --}}
                    <section class="acc sec-identity" id="acc-identity" aria-labelledby="identity-heading">
                        <div class="acc-head">
                            <div class="acc-head-l">
                                <span class="sec-num">1</span>
                                <div>
                                    <h2 id="identity-heading">ข้อมูลผู้ขอใช้บริการ</h2>
                                    <div class="sec-sub">ชื่อ หน่วยงาน และช่องทางติดต่อ</div>
                                </div>
                                @if ($errors->hasAny(['full_name', 'staff_or_student_id', 'unit_name', 'affiliation', 'email']))
                                    <span class="acc-error-dot" title="มีข้อผิดพลาดในหมวดนี้"></span>
                                @endif
                            </div>
                        </div>
                        <div class="acc-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">ชื่อ-สกุลผู้ขอใช้บริการ</label>
                                    <input type="text" name="full_name" class="form-control live-field" data-summary="sumFullName" value="{{ old('full_name', $serviceRequest->applicant->full_name) }}" required>
                                    @error('full_name') <div class="form-text text-danger">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Customer Name</label>
                                    <input type="text" name="customer_name" class="form-control live-field" data-summary="sumCustomerName" value="{{ old('customer_name', $serviceRequest->applicant->customer_name) }}" placeholder="ชื่อบัญชีที่ใช้ในระบบ Plesk (ถ้ามี)">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required" for="staff_or_student_id">รหัสบุคลากร/รหัสนักศึกษา</label>
                                    <input type="text" name="staff_or_student_id" id="staff_or_student_id" class="form-control @error('staff_or_student_id') is-invalid @enderror" value="{{ is_string(old('staff_or_student_id', $serviceRequest->applicant->staff_or_student_id)) ? old('staff_or_student_id', $serviceRequest->applicant->staff_or_student_id) : '' }}" maxlength="30" required aria-describedby="staff-id-help @error('staff_or_student_id') staff-id-error @enderror" @error('staff_or_student_id') aria-invalid="true" @enderror>
                                    <div class="field-hint" id="staff-id-help">แก้ไขรหัสได้ไม่เกิน 30 ตัวอักษร โดยชื่อบัญชีบริการ (Username) จะคงเดิม</div>
                                    @error('staff_or_student_id') <div class="form-text text-danger" id="staff-id-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">หน่วยงาน</label>
                                    <input type="text" name="unit_name" class="form-control live-field" data-summary="sumUnitName" value="{{ old('unit_name', $serviceRequest->applicant->unit_name) }}" required>
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
                    </section>

                    {{-- ============ 2. วัตถุประสงค์และระยะเวลา ============ --}}
                    <section class="acc sec-purpose" id="acc-purpose" aria-labelledby="purpose-heading">
                        <div class="acc-head">
                            <div class="acc-head-l">
                                <span class="sec-num">2</span>
                                <div>
                                    <h2 id="purpose-heading">วัตถุประสงค์และระยะเวลา</h2>
                                    <div class="sec-sub">เหตุผลการขอใช้งาน และช่วงเวลาที่ต้องการ</div>
                                </div>
                                @if ($errors->hasAny(['purpose_type', 'purpose_other_detail', 'project_start_date', 'project_end_date']))
                                    <span class="acc-error-dot" title="มีข้อผิดพลาดในหมวดนี้"></span>
                                @endif
                            </div>
                        </div>
                        <div class="acc-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">วัตถุประสงค์การใช้งาน</label>
                                    <select name="purpose_type" class="form-select live-field" id="purposeSelect" data-summary="sumPurpose" required>
                                        @foreach ($purposeLabels as $val => $label)
                                            <option value="{{ $val }}" {{ old('purpose_type', $serviceRequest->purpose_type) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('purpose_type') <div class="form-text text-danger">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6" id="purposeOtherField">
                                    <label class="form-label" for="purposeOtherInput">รายละเอียดกรณีอื่น ๆ</label>
                                    <input type="text" name="purpose_other_detail" id="purposeOtherInput" class="form-control @error('purpose_other_detail') is-invalid @enderror" value="{{ old('purpose_other_detail', $serviceRequest->purpose_other_detail) }}">
                                    @error('purpose_other_detail') <div class="form-text text-danger">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">วันเริ่มโครงการ/การขอใช้งาน</label>
                                    <input type="date" name="project_start_date" class="form-control live-field" data-summary="sumStartDate" data-fmt="date" value="{{ old('project_start_date', optional($serviceRequest->project_start_date)->format('Y-m-d')) }}" required>
                                    @error('project_start_date') <div class="form-text text-danger">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">วันสิ้นสุด</label>
                                    <input type="date" name="project_end_date" class="form-control live-field" data-summary="sumEndDate" data-fmt="date" value="{{ old('project_end_date', optional($serviceRequest->project_end_date)->format('Y-m-d')) }}" required>
                                    @error('project_end_date') <div class="form-text text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- ============ 3. ทรัพยากรและบริการ ============ --}}
                    <section class="acc sec-resource" id="acc-resource" aria-labelledby="resource-heading">
                        <div class="acc-head">
                            <div class="acc-head-l">
                                <span class="sec-num">3</span>
                                <div>
                                    <h2 id="resource-heading">ทรัพยากรและบริการที่ต้องการ</h2>
                                    <div class="sec-sub">สเปกเครื่อง บริการที่เปิดใช้ และรายละเอียดทางเทคนิค</div>
                                </div>
                                @if ($errors->hasAny(['service_type', 'plan_id', 'custom_cpu_vcpu', 'custom_ram_gb', 'custom_storage_gb', 'custom_fee', 'enabled_services', 'enabled_services_other_detail']))
                                    <span class="acc-error-dot" title="มีข้อผิดพลาดในหมวดนี้"></span>
                                @endif
                            </div>
                        </div>
                        <div class="acc-body">

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">ประเภทบริการ</label>
                                    <select name="service_type" class="form-select live-field" id="serviceTypeSelect" data-summary="sumServiceType" required>
                                        <option value="virtual_server" {{ old('service_type', $serviceRequest->service_type) == 'virtual_server' ? 'selected' : '' }}>Virtual Server</option>
                                        <option value="web_hosting" {{ old('service_type', $serviceRequest->service_type) == 'web_hosting' ? 'selected' : '' }}>Web Hosting</option>
                                    </select>
                                    @error('service_type') <div class="form-text text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="field-heading">
                                <h3>เลือกทรัพยากร</h3>
                                <p>เลือกแพ็กเกจตามประเภทบริการ หรือเลือกกำหนดสเปกเอง</p>
                            </div>
                            <div class="row g-2 mb-3" id="planOptions">
                                @foreach ($plans as $plan)
                                    <div class="col-md-4 plan-group" data-service-type="{{ $plan->service_type }}">
                                        <label class="plan-pick {{ $selectedPlanId == $plan->plan_id ? 'active' : '' }}">
                                            <input type="radio" name="plan_id" value="{{ $plan->plan_id }}" class="plan-radio" data-label="{{ $plan->size_label }}" data-fee="{{ $plan->fee_per_year }}" {{ $selectedPlanId == $plan->plan_id ? 'checked' : '' }}>
                                            <div class="po-title">{{ $plan->size_label }}</div>
                                            <div class="po-meta">
                                                @if($plan->cpu_vcpu) {{ $plan->cpu_vcpu }} vCPU / {{ $plan->ram_gb }} GB RAM /@endif
                                                {{ $plan->storage_gb }} GB — {{ number_format($plan->fee_per_year, 0) }} บาท/ปี
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                                <div class="col-md-4">
                                    <label class="plan-pick {{ $selectedPlanId ? '' : 'active' }}">
                                        <input type="radio" name="plan_id" value="" class="plan-radio" id="customPlanRadio" data-label="กำหนดสเปกเอง" data-fee="" {{ $selectedPlanId ? '' : 'checked' }}>
                                        <div class="po-title">กำหนดสเปกเอง</div>
                                        <div class="po-meta">กรอกทรัพยากรและค่าบริการด้านล่าง</div>
                                    </label>
                                </div>
                            </div>
                            @error('plan_id') <div class="form-text text-danger mb-2">{{ $message }}</div> @enderror

                            <div class="custom-specs" id="customSpecs">
                                <div class="field-heading">
                                    <h3>สเปกที่กำหนดเอง</h3>
                                    <p>กรอกเฉพาะค่าที่ต้องการระบุในคำขอ</p>
                                </div>
                                <div class="row g-3">
                                    <div class="col-sm-6 col-lg-3">
                                        <label class="form-label" for="customCpu">vCPU</label>
                                        <input type="number" name="custom_cpu_vcpu" id="customCpu" min="1" class="form-control @error('custom_cpu_vcpu') is-invalid @enderror" value="{{ old('custom_cpu_vcpu', $serviceRequest->custom_cpu_vcpu) }}">
                                        @error('custom_cpu_vcpu') <div class="form-text text-danger">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-sm-6 col-lg-3">
                                        <label class="form-label" for="customRam">RAM (GB)</label>
                                        <input type="number" name="custom_ram_gb" id="customRam" min="1" class="form-control @error('custom_ram_gb') is-invalid @enderror" value="{{ old('custom_ram_gb', $serviceRequest->custom_ram_gb) }}">
                                        @error('custom_ram_gb') <div class="form-text text-danger">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-sm-6 col-lg-3">
                                        <label class="form-label" for="customStorage">พื้นที่จัดเก็บ (GB)</label>
                                        <input type="number" name="custom_storage_gb" id="customStorage" min="1" class="form-control @error('custom_storage_gb') is-invalid @enderror" value="{{ old('custom_storage_gb', $serviceRequest->custom_storage_gb) }}">
                                        @error('custom_storage_gb') <div class="form-text text-danger">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-sm-6 col-lg-3">
                                        <label class="form-label" for="customFeeInput">ค่าบริการ (บาท/ปี)</label>
                                        <input type="number" step="0.01" min="0" name="custom_fee" id="customFeeInput" class="form-control live-field @error('custom_fee') is-invalid @enderror" data-summary="sumPrice" data-fmt="fee" value="{{ old('custom_fee', $serviceRequest->custom_fee) }}">
                                        @error('custom_fee') <div class="form-text text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="field-heading field-heading--divided">
                                <h3>บริการที่ต้องการเปิดใช้งาน <span class="required-mark">*</span></h3>
                                <p>เลือกอย่างน้อย 1 รายการ</p>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-md-6"><div class="form-check"><input class="form-check-input svc-checkbox" type="checkbox" name="enabled_services[]" value="ssh" id="svcSsh" data-label="SSH" {{ in_array('ssh', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcSsh">SSH — ภายในเครือข่ายหรือผ่าน VPN</label></div></div>
                                <div class="col-md-6"><div class="form-check"><input class="form-check-input svc-checkbox" type="checkbox" name="enabled_services[]" value="http_https" id="svcHttp" data-label="HTTP / HTTPS" {{ in_array('http_https', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcHttp">HTTP / HTTPS สำหรับ Web Service</label></div></div>
                                <div class="col-md-6"><div class="form-check"><input class="form-check-input svc-checkbox" type="checkbox" name="enabled_services[]" value="database_access" id="svcDb" data-label="Database Access" {{ in_array('database_access', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcDb">Database Access ภายในระบบที่อนุญาต</label></div></div>
                                <div class="col-md-6"><div class="form-check"><input class="form-check-input svc-checkbox" type="checkbox" name="enabled_services[]" value="other" id="svcOther" data-label="อื่น ๆ" {{ in_array('other', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcOther">อื่น ๆ</label></div></div>
                            </div>
                            @error('enabled_services') <div class="form-text text-danger mb-2">{{ $message }}</div> @enderror
                            <div class="other-service-field mb-3" id="otherServiceField">
                                <label class="form-label" for="otherServiceInput">รายละเอียดบริการอื่น ๆ</label>
                                <input type="text" name="enabled_services_other_detail" id="otherServiceInput" class="form-control @error('enabled_services_other_detail') is-invalid @enderror" value="{{ old('enabled_services_other_detail', $serviceRequest->enabled_services_other_detail) }}">
                                @error('enabled_services_other_detail') <div class="form-text text-danger">{{ $message }}</div> @enderror
                            </div>

                            <div class="field-heading field-heading--divided">
                                <h3>รายละเอียดทางเทคนิค</h3>
                                <p>ข้อมูลเพิ่มเติมสำหรับการตรวจคำขอ</p>
                            </div>
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
                    </section>

                    <div class="save-bar">
                        <div style="display:flex; align-items:center; gap:14px;">
                            <a href="{{ route('admin.requests.show', $serviceRequest->request_id) }}" class="btn btn-outline-soft">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:5px;"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                                ยกเลิก
                            </a>
                            <span class="unsaved-flag" id="unsavedFlag"><span class="dt"></span>มีการแก้ไขที่ยังไม่ได้บันทึก</span>
                        </div>
                        <button type="submit" class="btn btn-amber">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                            บันทึกการแก้ไข
                        </button>
                    </div>
            </div>
        </div>
    </form>
    </div>

    <script>
        (function () {
            var form = document.getElementById('editRequestForm');

            var planRadios = document.querySelectorAll('.plan-radio');
            var customPlanRadio = document.getElementById('customPlanRadio');
            var customSpecs = document.getElementById('customSpecs');
            var serviceTypeSelect = document.getElementById('serviceTypeSelect');

            function syncPlanSummary() {
                var checked = document.querySelector('.plan-radio:checked');
                var planEl = document.getElementById('sumPlan');
                var priceEl = document.getElementById('sumPrice');
                if (checked && checked.value) {
                    planEl.textContent = checked.dataset.label;
                    priceEl.textContent = Number(checked.dataset.fee).toLocaleString('th-TH') + ' บาท';
                } else {
                    planEl.textContent = 'กำหนดสเปกเอง';
                    var customFee = document.getElementById('customFeeInput').value;
                    priceEl.textContent = customFee ? Number(customFee).toLocaleString('th-TH') + ' บาท' : '—';
                }
            }

            function syncPlanSelection() {
                document.querySelectorAll('.plan-pick').forEach(function (pick) {
                    pick.classList.toggle('active', pick.querySelector('.plan-radio').checked);
                });
                customSpecs.hidden = !customPlanRadio.checked;
                customSpecs.querySelectorAll('input').forEach(function (input) {
                    input.disabled = !customPlanRadio.checked;
                });
                syncPlanSummary();
            }

            planRadios.forEach(function (radio) {
                radio.addEventListener('change', syncPlanSelection);
            });

            function syncPlanVisibility() {
                var type = serviceTypeSelect.value;
                document.querySelectorAll('.plan-group').forEach(function (el) {
                    el.hidden = el.dataset.serviceType !== type;
                });
                var checked = document.querySelector('.plan-radio:checked');
                if (checked && checked.value && checked.closest('.plan-group').hidden) {
                    customPlanRadio.checked = true;
                    syncPlanSelection();
                }
            }
            serviceTypeSelect.addEventListener('change', function () {
                syncPlanVisibility();
            });

            function updateSummaryField(el) {
                var targetId = el.dataset.summary;
                if (!targetId) return;
                var target = document.getElementById(targetId);
                if (!target) return;

                var value = el.value;
                if (el.tagName === 'SELECT') {
                    value = el.options[el.selectedIndex] ? el.options[el.selectedIndex].text : value;
                }
                if (el.dataset.fmt === 'date' && value) {
                    var parts = value.split('-');
                    value = parts[2] + '/' + parts[1] + '/' + parts[0];
                }
                if (target.id === 'sumPrice') {
                    var checkedPlan = document.querySelector('.plan-radio:checked');
                    if (checkedPlan && checkedPlan.value) return;
                    target.textContent = value ? Number(value).toLocaleString('th-TH') + ' บาท' : '—';
                    return;
                }
                target.textContent = value ? value : '—';
            }

            document.querySelectorAll('.live-field').forEach(function (el) {
                updateSummaryField(el);
                el.addEventListener('input', function () { updateSummaryField(el); });
                el.addEventListener('change', function () { updateSummaryField(el); });
            });

            var purposeSelect = document.getElementById('purposeSelect');
            var purposeOtherField = document.getElementById('purposeOtherField');
            var purposeOtherInput = document.getElementById('purposeOtherInput');
            function syncPurposeOther() {
                var isOther = purposeSelect.value === '1.4_other';
                purposeOtherField.hidden = !isOther;
                purposeOtherInput.disabled = !isOther;
                purposeOtherInput.required = isOther;
            }
            purposeSelect.addEventListener('change', syncPurposeOther);

            var otherServiceField = document.getElementById('otherServiceField');
            var otherServiceInput = document.getElementById('otherServiceInput');
            function syncOtherService() {
                var isOther = document.getElementById('svcOther').checked;
                otherServiceField.hidden = !isOther;
                otherServiceInput.disabled = !isOther;
            }
            document.getElementById('svcOther').addEventListener('change', syncOtherService);

            function syncServicesSummary() {
                var selected = Array.from(form.querySelectorAll('.svc-checkbox:checked'));
                document.getElementById('sumServices').textContent = selected.map(function (input) {
                    return input.dataset.label;
                }).join(', ') || '—';
            }
            form.querySelectorAll('.svc-checkbox').forEach(function (input) {
                input.addEventListener('change', syncServicesSummary);
            });

            document.querySelectorAll('.edit-section-nav a').forEach(function (link) {
                link.addEventListener('click', function () {
                    document.querySelectorAll('.edit-section-nav a').forEach(function (item) {
                        item.classList.remove('active');
                    });
                    link.classList.add('active');
                });
            });

            var unsavedFlag = document.getElementById('unsavedFlag');
            form.addEventListener('input', function () { unsavedFlag.classList.add('show'); });
            form.addEventListener('change', function () { unsavedFlag.classList.add('show'); });
            form.addEventListener('submit', function () { unsavedFlag.classList.remove('show'); });

            syncPlanVisibility();
            syncPlanSelection();
            syncPurposeOther();
            syncOtherService();
            syncServicesSummary();
        })();
    </script>

@endsection
