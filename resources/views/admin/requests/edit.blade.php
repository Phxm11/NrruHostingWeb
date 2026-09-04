@extends('admin.layout')

@section('title', 'แก้ไขคำขอ ' . $serviceRequest->form_no)
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'แก้ไขรายละเอียดคำขอใช้บริการ')

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

    <style>
        /* ---------- Back link ---------- */
        .back-link {
            display: inline-flex; align-items: center; gap: 6px; font-size: 13.5px;
            color: var(--ink-soft, #837c6c); margin-bottom: 14px; transition: color .15s ease;
        }
        .back-link:hover { color: var(--forest, #1a3323); }

        /* ---------- Layout ---------- */
        .edit-layout { display: grid; grid-template-columns: 300px 1fr; gap: 20px; align-items: start; }
        @media (max-width: 960px) { .edit-layout { grid-template-columns: 1fr; } .summary-card { position: static !important; } }

        /* ---------- Sticky live summary ---------- */
        .summary-card {
            position: sticky; top: 20px; background: var(--surface, #fff); border: 1px solid var(--line, #e4e0d3);
            border-radius: var(--radius-lg, 22px); padding: 20px; box-shadow: var(--shadow-sm, 0 1px 2px rgba(21,35,26,.05));
        }
        .summary-card .s-head { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; padding-bottom: 14px; border-bottom: 1px solid var(--line, #e4e0d3); }
        .summary-card .s-icon {
            width: 38px; height: 38px; border-radius: 11px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, var(--forest, #1a3323), var(--forest-2, #244430)); color: #fff;
        }
        .summary-card .s-title { font-family: 'Kanit', sans-serif; font-weight: 600; font-size: 14.5px; }
        .summary-card .s-sub { font-size: 11.5px; color: var(--ink-soft, #837c6c); }
        .sum-group { margin-bottom: 14px; }
        .sum-group .g-label { font-size: 10.5px; text-transform: uppercase; letter-spacing: .05em; color: var(--ink-soft, #837c6c); font-weight: 700; margin-bottom: 7px; }
        .sum-row { display: flex; justify-content: space-between; gap: 8px; font-size: 12.5px; padding: 4px 0; }
        .sum-row .k { color: var(--ink-soft, #837c6c); flex-shrink: 0; }
        .sum-row .v { font-weight: 600; text-align: right; word-break: break-word; }
        .sum-row .v.changed { color: var(--amber-deep, #a6740e); }
        .sum-row .v.changed::after { content: ' ●'; font-size: 8px; vertical-align: middle; }
        .sum-row .v.empty { color: var(--ink-soft, #837c6c); font-weight: 400; font-style: italic; }
        .price-total { background: var(--moss-light, #e8f0dc); border-radius: var(--radius-sm, 14px); padding: 12px 14px; margin-top: 4px; display: flex; justify-content: space-between; align-items: center; }
        .price-total .p-lbl { font-size: 12px; color: var(--forest, #1a3323); }
        .price-total .p-val { font-family: 'Kanit', sans-serif; font-weight: 700; font-size: 17px; color: var(--forest, #1a3323); }

        /* ---------- Alert banner ---------- */
        .alert-important {
            display: flex; gap: 12px; align-items: flex-start; background: #fdf3e2; border: 1px solid #eed3a0;
            border-left: 4px solid var(--amber-deep, #b9840f); border-radius: 10px; padding: 13px 15px; margin-bottom: 16px;
        }
        .alert-important svg { flex-shrink: 0; color: var(--amber-deep, #b9840f); margin-top: 1px; }
        .alert-important strong { color: #7a5a0d; }
        .alert-important .a-text { font-size: 13px; color: #5c4a1e; line-height: 1.55; }

        /* ---------- Accordion sections ---------- */
        .acc { border: 1px solid var(--line, #e4e0d3); border-radius: var(--radius-md, 16px); margin-bottom: 12px; background: var(--surface, #fff); overflow: hidden; }
        .acc-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; cursor: pointer; gap: 12px; user-select: none; background: none; border: none; width: 100%; text-align: left; font: inherit; color: inherit; }
        .acc-head-l { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .acc .sec-num { width: 30px; height: 30px; border-radius: 9px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12.5px; color: #fff; }
        .acc.sec-identity .sec-num { background: linear-gradient(135deg, #2f6b4a, #235238); }
        .acc.sec-purpose .sec-num  { background: linear-gradient(135deg, #3b6ea5, #2a4f79); }
        .acc.sec-resource .sec-num { background: linear-gradient(135deg, #b9840f, #8f6608); }
        .acc-head h3 { font-size: 14.5px; font-weight: 700; margin: 0; color: var(--ink, #2b2620); }
        .acc-head .sec-sub { font-size: 11.5px; color: var(--ink-soft, #837c6c); font-weight: 400; margin-top: 1px; }
        .acc-chev { transition: transform .2s ease; flex-shrink: 0; color: var(--ink-soft, #837c6c); }
        .acc.open .acc-chev { transform: rotate(180deg); }
        .acc-body { padding: 0 20px 20px; }
        .acc:not(.open) .acc-body { display: none; }
        .acc-error-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--rust, #ae4830); flex-shrink: 0; }

        .required::after { content: ' *'; color: var(--rust, #b3542c); font-weight: 700; }
        .locked-field { background: var(--surface-2, #f6f4ec); }

        .plan-pick { border: 1.5px solid var(--line, #e4e0d3); border-radius: 12px; padding: 12px; cursor: pointer; display: block; transition: all .12s ease; }
        .plan-pick input { display: none; }
        .plan-pick.active { border-color: var(--moss, #2f6b4a); background: var(--moss-light, #e3efe7); box-shadow: 0 0 0 1px var(--moss, #2f6b4a) inset; }
        .plan-pick .po-title { font-family: 'Kanit', sans-serif; font-weight: 600; font-size: 13.5px; }
        .plan-pick .po-meta { font-size: 12px; color: var(--ink-soft, #837c6c); margin-top: 2px; }

        .field-hint { font-size: 12px; color: var(--ink-soft, #837c6c); margin-top: 4px; }

        /* ---------- Sticky save bar ---------- */
        .save-bar {
            position: sticky; bottom: 0; margin-top: 16px; background: var(--surface, #fff);
            border-top: 1px solid var(--line, #e4e0d3); padding: 14px 0 4px; display: flex; justify-content: space-between;
            align-items: center; z-index: 5; gap: 12px; flex-wrap: wrap;
        }
        .save-bar .unsaved-flag { font-size: 12.5px; color: var(--amber-deep, #a6740e); font-weight: 600; display: none; align-items: center; gap: 6px; }
        .save-bar .unsaved-flag.show { display: flex; }
        .save-bar .unsaved-flag .dt { width: 7px; height: 7px; border-radius: 50%; background: var(--amber-deep, #a6740e); animation: pulseDot 1.6s infinite; }
        @keyframes pulseDot { 0%, 100% { opacity: 1; } 50% { opacity: .3; } }
    </style>

    <a href="{{ route('admin.requests.show', $serviceRequest->request_id) }}" class="back-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        กลับไปยังรายละเอียดคำขอ
    </a>

    <form action="{{ route('admin.requests.update', $serviceRequest->request_id) }}" method="POST" id="editRequestForm">
        @csrf
        @method('PUT')

        <div class="edit-layout">

            {{-- ============ ซ้าย: สรุปคำขอแบบสด (sticky) ============ --}}
            <aside class="summary-card" id="summaryCard">
                <div class="s-head">
                    <div class="s-icon">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 2h6l1 3h4v17H4V5h4l1-3Z"/><path d="M9 12h6M9 16h6"/></svg>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div class="s-title">{{ $serviceRequest->form_no }}</div>
                        <div class="s-sub">อัปเดตสดตามที่แก้ไข</div>
                    </div>
                    <a href="{{ route('admin.requests.show', $serviceRequest->request_id) }}" target="_blank" rel="noopener" class="btn btn-outline-soft btn-sm" style="flex-shrink:0;" title="เปิดรายละเอียดคำขอในแท็บใหม่">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14 21 3"/></svg>
                        ดูรายละเอียด
                    </a>
                </div>

                <div class="sum-group">
                    <div class="g-label">ผู้ขอใช้บริการ</div>
                    <div class="sum-row"><span class="k">ชื่อ</span><span class="v" id="sumFullName" data-original="{{ $serviceRequest->applicant->full_name }}">{{ $serviceRequest->applicant->full_name ?: '—' }}</span></div>
                    <div class="sum-row"><span class="k">หน่วยงาน</span><span class="v" id="sumUnitName" data-original="{{ $serviceRequest->applicant->unit_name }}">{{ $serviceRequest->applicant->unit_name ?: '—' }}</span></div>
                    <div class="sum-row"><span class="k">Customer Name</span><span class="v" id="sumCustomerName" data-original="{{ $serviceRequest->applicant->customer_name }}">{{ $serviceRequest->applicant->customer_name ?: '—' }}</span></div>
                </div>

                <div class="sum-group">
                    <div class="g-label">วัตถุประสงค์</div>
                    <div class="sum-row"><span class="k">ประเภท</span><span class="v" id="sumPurpose">{{ $purposeLabels[$serviceRequest->purpose_type] ?? '—' }}</span></div>
                    <div class="sum-row"><span class="k">เริ่ม</span><span class="v" id="sumStartDate">{{ optional($serviceRequest->project_start_date)->format('d/m/Y') ?: '—' }}</span></div>
                    <div class="sum-row"><span class="k">สิ้นสุด</span><span class="v" id="sumEndDate">{{ optional($serviceRequest->project_end_date)->format('d/m/Y') ?: '—' }}</span></div>
                </div>

                <div class="sum-group">
                    <div class="g-label">ทรัพยากร</div>
                    <div class="sum-row"><span class="k">บริการ</span><span class="v" id="sumServiceType">{{ $serviceRequest->service_type == 'web_hosting' ? 'Web Hosting' : 'Virtual Server' }}</span></div>
                    <div class="sum-row"><span class="k">แพ็กเกจ</span><span class="v" id="sumPlan">—</span></div>
                    <div class="sum-row"><span class="k">บริการที่เปิดใช้</span><span class="v" id="sumServices">—</span></div>
                </div>

                <div class="price-total">
                    <span class="p-lbl">ค่าบริการ/ปี</span>
                    <span class="p-val" id="sumPrice">—</span>
                </div>
            </aside>

            {{-- ============ ขวา: ฟอร์มแก้ไขแบบ Accordion ============ --}}
            <div>
                <div class="panel">

                    <div class="alert-important">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg>
                        <div class="a-text">
                            กำลังแก้ไขคำขอ <strong>{{ $serviceRequest->form_no }}</strong> —
                            การแก้ไข<strong>ข้อมูลผู้ขอใช้บริการ</strong>จะมีผลกับคำขออื่นของรหัสประจำตัวเดียวกันนี้ด้วย
                            (โดเมนและเอกสารแนบแก้ไขแยกที่หน้า
                            <a href="{{ route('admin.domains.index') }}">จัดการโดเมน</a>)
                        </div>
                    </div>

                    {{-- ============ 1. ผู้ขอใช้บริการ ============ --}}
                    <div class="acc sec-identity open" id="acc-identity">
                        <button type="button" class="acc-head" data-target="acc-identity">
                            <div class="acc-head-l">
                                <span class="sec-num">1</span>
                                <div>
                                    <h3>ข้อมูลผู้ขอใช้บริการ</h3>
                                    <div class="sec-sub">ชื่อ หน่วยงาน และช่องทางติดต่อ</div>
                                </div>
                                @if ($errors->hasAny(['full_name', 'unit_name', 'affiliation', 'email']))
                                    <span class="acc-error-dot" title="มีข้อผิดพลาดในหมวดนี้"></span>
                                @endif
                            </div>
                            <svg class="acc-chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
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
                                    <label class="form-label">รหัสบุคลากร/รหัสนักศึกษา</label>
                                    <input type="text" class="form-control locked-field" value="{{ $serviceRequest->applicant->staff_or_student_id }}" disabled>
                                    <div class="field-hint">🔒 แก้ไขรหัสประจำตัวไม่ได้ที่หน้านี้ เพราะเป็นตัวระบุตัวตนหลักของผู้ขอ</div>
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
                    </div>

                    {{-- ============ 2. วัตถุประสงค์และระยะเวลา ============ --}}
                    <div class="acc sec-purpose {{ $errors->hasAny(['purpose_type', 'purpose_other_detail', 'project_start_date', 'project_end_date']) ? 'open' : '' }}" id="acc-purpose">
                        <button type="button" class="acc-head" data-target="acc-purpose">
                            <div class="acc-head-l">
                                <span class="sec-num">2</span>
                                <div>
                                    <h3>วัตถุประสงค์และระยะเวลา</h3>
                                    <div class="sec-sub">เหตุผลการขอใช้งาน และช่วงเวลาที่ต้องการ</div>
                                </div>
                                @if ($errors->hasAny(['purpose_type', 'project_start_date', 'project_end_date']))
                                    <span class="acc-error-dot" title="มีข้อผิดพลาดในหมวดนี้"></span>
                                @endif
                            </div>
                            <svg class="acc-chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
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
                                <div class="col-md-6">
                                    <label class="form-label">รายละเอียดกรณีอื่น ๆ</label>
                                    <input type="text" name="purpose_other_detail" class="form-control" value="{{ old('purpose_other_detail', $serviceRequest->purpose_other_detail) }}">
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
                    </div>

                    {{-- ============ 3. ทรัพยากรและบริการ ============ --}}
                    <div class="acc sec-resource {{ $errors->hasAny(['service_type', 'plan_id', 'custom_cpu_vcpu', 'custom_ram_gb', 'custom_storage_gb', 'custom_fee', 'enabled_services', 'enabled_services_other_detail', 'language_framework', 'database_used', 'port_service_needed']) ? 'open' : '' }}" id="acc-resource">
                        <button type="button" class="acc-head" data-target="acc-resource">
                            <div class="acc-head-l">
                                <span class="sec-num">3</span>
                                <div>
                                    <h3>ทรัพยากรและบริการที่ต้องการ</h3>
                                    <div class="sec-sub">สเปกเครื่อง บริการที่เปิดใช้ และรายละเอียดทางเทคนิค</div>
                                </div>
                                @if ($errors->hasAny(['service_type', 'plan_id', 'enabled_services']))
                                    <span class="acc-error-dot" title="มีข้อผิดพลาดในหมวดนี้"></span>
                                @endif
                            </div>
                            <svg class="acc-chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
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

                            <label class="form-label">แพ็กเกจ (เลือกได้ 1 รายการ หรือปล่อยว่างแล้วกำหนดสเปกเองด้านล่าง)</label>
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
                                        <input type="radio" name="plan_id" value="" class="plan-radio" data-label="ไม่ใช้แพ็กเกจสำเร็จรูป" data-fee="" {{ $selectedPlanId ? '' : 'checked' }}>
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
                                    <input type="number" step="0.01" name="custom_fee" id="customFeeInput" class="form-control form-control-sm live-field" data-summary="sumPrice" data-fmt="fee" placeholder="ค่าบริการ (บาท/ปี)" value="{{ old('custom_fee', $serviceRequest->custom_fee) }}">
                                </div>
                            </div>
                            <div class="field-hint mb-3">หากเลือกแพ็กเกจสำเร็จรูปด้านบน ค่าที่กรอกในช่อง "กำหนดเอง" จะถูกล้างทิ้งตอนบันทึก</div>

                            <label class="form-label">บริการที่ต้องการเปิดใช้งาน</label>
                            <div class="row g-2 mb-2">
                                <div class="col-md-6"><div class="form-check"><input class="form-check-input svc-checkbox" type="checkbox" name="enabled_services[]" value="ssh" id="svcSsh" data-label="SSH" {{ in_array('ssh', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcSsh">SSH — ภายในเครือข่ายหรือผ่าน VPN</label></div></div>
                                <div class="col-md-6"><div class="form-check"><input class="form-check-input svc-checkbox" type="checkbox" name="enabled_services[]" value="http_https" id="svcHttp" data-label="HTTP / HTTPS" {{ in_array('http_https', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcHttp">HTTP / HTTPS สำหรับ Web Service</label></div></div>
                                <div class="col-md-6"><div class="form-check"><input class="form-check-input svc-checkbox" type="checkbox" name="enabled_services[]" value="database_access" id="svcDb" data-label="Database Access" {{ in_array('database_access', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcDb">Database Access ภายในระบบที่อนุญาต</label></div></div>
                                <div class="col-md-6"><div class="form-check"><input class="form-check-input svc-checkbox" type="checkbox" name="enabled_services[]" value="other" id="svcOther" data-label="อื่น ๆ" {{ in_array('other', $oldServices) ? 'checked' : '' }}><label class="form-check-label" for="svcOther">อื่น ๆ</label></div></div>
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
                    </div>

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
        </div>
    </form>

    <script>
        (function () {
            var form = document.getElementById('editRequestForm');

            // ---------- Accordion toggle ----------
            document.querySelectorAll('.acc-head').forEach(function (head) {
                head.addEventListener('click', function () {
                    document.getElementById(head.dataset.target).classList.toggle('open');
                });
            });

            // ---------- Plan pick active state + summary sync ----------
            var planRadios = document.querySelectorAll('.plan-radio');
            function syncPlanSummary() {
                var checked = document.querySelector('.plan-radio:checked');
                var planEl = document.getElementById('sumPlan');
                var priceEl = document.getElementById('sumPrice');
                if (checked && checked.value) {
                    planEl.textContent = checked.dataset.label;
                    planEl.classList.remove('empty');
                    priceEl.textContent = Number(checked.dataset.fee).toLocaleString('th-TH') + ' บาท';
                } else {
                    planEl.textContent = 'กำหนดสเปกเอง';
                    planEl.classList.add('empty');
                    var customFee = document.getElementById('customFeeInput').value;
                    priceEl.textContent = customFee ? Number(customFee).toLocaleString('th-TH') + ' บาท' : '—';
                }
            }
            planRadios.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    document.querySelectorAll('.plan-pick').forEach(function (el) { el.classList.remove('active'); });
                    radio.closest('.plan-pick').classList.add('active');
                    syncPlanSummary();
                });
            });

            // ---------- กรองการ์ดแพ็กเกจให้ตรงกับประเภทบริการที่เลือก ----------
            var serviceTypeSelect = document.getElementById('serviceTypeSelect');
            function syncPlanVisibility() {
                var type = serviceTypeSelect.value;
                document.querySelectorAll('.plan-group').forEach(function (el) {
                    el.style.display = (el.dataset.serviceType === type) ? '' : 'none';
                });
            }
            serviceTypeSelect.addEventListener('change', function () {
                syncPlanVisibility();
                updateSummaryField(serviceTypeSelect);
            });

            // ---------- Live summary text fields ----------
            function updateSummaryField(el) {
                var targetId = el.dataset.summary;
                if (!targetId) return;
                var target = document.getElementById(targetId);
                if (!target) return;

                var value = el.value;
                if (el.tagName === 'SELECT') {
                    if (el.id === 'serviceTypeSelect') {
                        value = el.value === 'web_hosting' ? 'Web Hosting' : 'Virtual Server';
                    } else {
                        value = el.options[el.selectedIndex] ? el.options[el.selectedIndex].text : value;
                    }
                }
                if (el.dataset.fmt === 'date' && value) {
                    var parts = value.split('-');
                    if (parts.length === 3) { value = parts[2] + '/' + parts[1] + '/' + parts[0]; }
                }

                if (target.id === 'sumPrice') {
                    // handled in syncPlanSummary when a package is selected
                    var checkedPlan = document.querySelector('.plan-radio:checked');
                    if (checkedPlan && checkedPlan.value) return;
                    target.textContent = value ? Number(value).toLocaleString('th-TH') + ' บาท' : '—';
                    return;
                }

                target.textContent = value ? value : '—';

                if (target.dataset.original !== undefined) {
                    if (value !== target.dataset.original) {
                        target.classList.add('changed');
                    } else {
                        target.classList.remove('changed');
                    }
                }
            }

            document.querySelectorAll('.live-field').forEach(function (el) {
                el.addEventListener('input', function () { updateSummaryField(el); });
                el.addEventListener('change', function () { updateSummaryField(el); });
            });

            // ---------- บริการที่เปิดใช้งาน ----------
            function syncServicesSummary() {
                var checked = Array.prototype.slice.call(document.querySelectorAll('.svc-checkbox:checked'));
                var el = document.getElementById('sumServices');
                if (checked.length === 0) {
                    el.textContent = '—';
                    el.classList.add('empty');
                } else {
                    el.textContent = checked.map(function (c) { return c.dataset.label; }).join(', ');
                    el.classList.remove('empty');
                }
            }
            document.querySelectorAll('.svc-checkbox').forEach(function (cb) {
                cb.addEventListener('change', syncServicesSummary);
            });

            // ---------- แจ้งเตือนมีการแก้ไขที่ยังไม่บันทึก ----------
            var unsavedFlag = document.getElementById('unsavedFlag');
            form.addEventListener('input', function () { unsavedFlag.classList.add('show'); });
            form.addEventListener('change', function () { unsavedFlag.classList.add('show'); });
            form.addEventListener('submit', function () { unsavedFlag.classList.remove('show'); });

            // ---------- ค่าเริ่มต้นตอนโหลดหน้า ----------
            syncPlanVisibility();
            syncPlanSummary();
            syncServicesSummary();
        })();
    </script>

@endsection