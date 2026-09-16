@extends('admin.layout')

@section('title', 'รายละเอียดคำขอ ' . $serviceRequest->form_no)
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'รายละเอียดคำขอใช้บริการ')

@section('content')

    @php
        $purposeLabels = [
            '1.1_teaching' => 'การเรียนการสอน',
            '1.2_academic_research_community' => 'บริการวิชาการ/วิจัย/บริการชุมชน',
            '1.3_internal_admin' => 'บริหารจัดการภายในหน่วยงาน',
            '1.4_other' => 'อื่น ๆ',
        ];
        $serviceTypeLabels = [
            'virtual_server' => 'Virtual Server',
            'web_hosting' => 'Web Hosting',
        ];
        $enabledServiceLabels = [
            'ssh' => 'SSH — ภายในเครือข่ายหรือผ่าน VPN',
            'http_https' => 'HTTP / HTTPS สำหรับ Web Service',
            'database_access' => 'Database Access ภายในระบบที่อนุญาต',
            'other' => 'อื่น ๆ',
        ];
        $accountTypeLabels = [
            'control_panel' => 'Control Panel',
            'ssh' => 'SSH',
            'database' => 'Database',
            'ftp' => 'FTP',
        ];
        $fileTypeLabels = [
            'system_detail_doc' => 'เอกสารรายละเอียดระบบ/โครงสร้างระบบ',
            'screenshot_evidence' => 'ภาพหน้าจอ / หลักฐานประกอบ',
        ];
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        // Compact schema: fixed request details are columns on service_requests.
        $resource = $serviceRequest;
        $avatarClasses = ['avatar-a', 'avatar-b', 'avatar-c'];
        $avatarClass = $avatarClasses[ord(mb_substr($serviceRequest->applicant->full_name, 0, 1)) % 3];
        $attachments = collect([
            $serviceRequest->system_detail_doc_path ? (object) ['file_type' => 'system_detail_doc', 'file_path' => $serviceRequest->system_detail_doc_path] : null,
            $serviceRequest->screenshot_evidence_path ? (object) ['file_type' => 'screenshot_evidence', 'file_path' => $serviceRequest->screenshot_evidence_path] : null,
        ])->filter();
        $imageAttachments = $attachments->filter(function ($f) use ($imageExts) {
            return in_array(strtolower(pathinfo($f->file_path, PATHINFO_EXTENSION)), $imageExts);
        });
        $fileAttachments = $attachments->reject(function ($f) use ($imageExts) {
            return in_array(strtolower(pathinfo($f->file_path, PATHINFO_EXTENSION)), $imageExts);
        });
    @endphp
    @push('styles')
        <link rel="stylesheet" href="{{ versioned_asset('css/admin/pages/requests-show.css') }}">
    @endpush


    <a href="{{ route('admin.requests.index') }}" class="back-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        กลับไปยังรายการคำขอ
    </a>

    <div class="row justify-content-center">
        <div class="col-xl-9">

            {{-- ============================================================
                 Header — ผู้ขอใช้บริการ / สถานะ / ปุ่มดำเนินการหลัก
            ============================================================ --}}
            <div class="panel detail-header">
                <div class="detail-header-top">
                    <div class="d-flex align-items-center">
                        <span class="avatar-circle {{ $avatarClass }}">{{ mb_substr($serviceRequest->applicant->full_name, 0, 1) }}</span>
                        <div>
                            <h1>{{ $serviceRequest->applicant->full_name }}</h1>
                            <div class="sub">{{ $serviceRequest->applicant->position_title ?: 'ผู้ขอใช้บริการ' }} · {{ $serviceRequest->applicant->unit_name }}</div>
                            <div class="mt-2 d-flex align-items-center gap-2 flex-wrap">
                                <span class="form-no-tag">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 2v4M16 2v4"/></svg>
                                    เลขที่คำขอ {{ $serviceRequest->form_no }}
                                </span>
                                <x-admin.status :status="$serviceRequest->status" />
                            </div>
                        </div>
                    </div>
                    <div class="text-end" style="position:relative;z-index:1;">
                        <div class="sub">วันที่ยื่นคำขอ</div>
                        <div style="font-weight:600;">{{ \Carbon\Carbon::parse($serviceRequest->request_date)->format('d/m/Y') }}</div>
                    </div>
                </div>

                <div class="detail-actions">
                    @if ($serviceRequest->status === 'submitted')
                        <form action="{{ route('admin.requests.approve', $serviceRequest->request_id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-approve btn-sm">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg>
                                อนุมัติคำขอ
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.requests.edit', $serviceRequest->request_id) }}" class="btn btn-header-outline btn-sm">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                        แก้ไขคำขอ
                    </a>
                    @if ($serviceRequest->status === 'approved' && $serviceRequest->serviceAccounts->isEmpty())
                        <a href="{{ route('admin.accounts.create', $serviceRequest->request_id) }}" class="btn btn-amber btn-sm">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
                            สร้างบัญชีให้ผู้ขอใช้บริการ
                        </a>
                    @endif
                    <form action="{{ route('admin.requests.destroy', $serviceRequest->request_id) }}" method="POST"
                          data-confirm="ยืนยันลบคำขอ {{ $serviceRequest->form_no }}?{{ $serviceRequest->serviceAccounts->count() > 0 ? ' คำขอนี้มีบัญชีที่สร้างแล้ว ' . $serviceRequest->serviceAccounts->count() . ' บัญชี ซึ่งจะถูกลบด้วย' : '' }} การลบไม่สามารถย้อนกลับได้">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-header-outline btn-sm">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                            ลบคำขอ
                        </button>
                    </form>
                </div>
            </div>

            {{-- ============================================================
                 1. ข้อมูลผู้ขอใช้บริการและหน่วยงาน
            ============================================================ --}}
            <div class="panel detail-section" id="secApplicant">
                <div class="section-head">
                    <span class="sec-num">1</span>
                    <h2>ข้อมูลผู้ขอใช้บริการและหน่วยงาน</h2>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-item__label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                            ชื่อ-นามสกุล
                        </div>
                        <div class="info-item__value">{{ $serviceRequest->applicant->full_name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item__label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h.01M11 8h6M7 12h.01M11 12h6M7 16h.01M11 16h6"/></svg>
                            รหัสบุคลากร
                        </div>
                        <div class="info-item__value">{{ $serviceRequest->applicant->staff_or_student_id }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item__label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21V7l9-4 9 4v14"/><path d="M9 21v-6h6v6"/></svg>
                            หน่วยงาน
                        </div>
                        <div class="info-item__value">{{ $serviceRequest->applicant->unit_name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item__label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21V7l9-4 9 4v14"/></svg>
                            สังกัด
                        </div>
                        <div class="info-item__value">{{ $serviceRequest->applicant->affiliation }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-item__label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                            ตำแหน่ง
                        </div>
                        <div class="info-item__value {{ $serviceRequest->applicant->position_title ? '' : 'muted' }}">
                            {{ $serviceRequest->applicant->position_title ?: 'ไม่ได้ระบุ' }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-item__label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92Z"/></svg>
                            เบอร์โทรศัพท์
                        </div>
                        <div class="info-item__value {{ $serviceRequest->applicant->phone ? '' : 'muted' }}">
                            {{ $serviceRequest->applicant->phone ?: 'ไม่ได้ระบุ' }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-item__label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/></svg>
                            อีเมล
                        </div>
                        <div class="info-item__value {{ $serviceRequest->applicant->email ? '' : 'muted' }}">
                            {{ $serviceRequest->applicant->email ?: 'ไม่ได้ระบุ' }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-item__label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/></svg>
                            หลักการและวัตถุประสงค์
                        </div>
                        <div class="info-item__value">{{ $purposeLabels[$serviceRequest->purpose_type] ?? $serviceRequest->purpose_type }}</div>
                    </div>
                    @if ($serviceRequest->purpose_type === '1.4_other' && $serviceRequest->purpose_other_detail)
                        <div class="info-item span-2">
                            <div class="info-item__label">รายละเอียดกรณีอื่น ๆ</div>
                            <div class="info-item__value">{{ $serviceRequest->purpose_other_detail }}</div>
                        </div>
                    @endif
                    <div class="info-item span-2">
                        <div class="info-item__label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 3v3M16 3v3"/></svg>
                            ระยะเวลาโครงการ/การขอใช้งาน
                        </div>
                        <div class="info-item__value">
                            {{ \Carbon\Carbon::parse($serviceRequest->project_start_date)->format('d/m/Y') }}
                            <span style="color:var(--ink-soft);">—</span>
                            {{ \Carbon\Carbon::parse($serviceRequest->project_end_date)->format('d/m/Y') }}
                        </div>
                    </div>
                </div>

                @if ($serviceRequest->developers->isNotEmpty())
                    <div class="sub-divider">ผู้รับผิดชอบพัฒนาระบบ ({{ $serviceRequest->developers->count() }} คน)</div>
                    @foreach ($serviceRequest->developers as $dev)
                        <div class="dev-card">
                            <span class="avatar-circle {{ $avatarClasses[$loop->index % 3] }}">{{ mb_substr($dev->full_name, 0, 1) }}</span>
                            <div>
                                <div class="dev-name">{{ $dev->full_name }}</div>
                                <div class="dev-meta">
                                    @if ($dev->role_desc)<span>{{ $dev->role_desc }}</span>@endif
                                    @if ($dev->phone)<span>📞 {{ $dev->phone }}</span>@endif
                                    @if ($dev->email)<span>✉ {{ $dev->email }}</span>@endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- ============================================================
                 2. ทรัพยากรและบริการที่ขอใช้
            ============================================================ --}}
            <div class="panel detail-section" id="secResources">
                <div class="section-head">
                    <span class="sec-num">2</span>
                    <h2>ทรัพยากรและบริการที่ต้องการ</h2>
                </div>

                <div class="info-grid mb-3">
                    <div class="info-item">
                        <div class="info-item__label">ประเภทบริการ</div>
                        <div class="info-item__value">{{ $serviceTypeLabels[$resource?->service_type ?? ''] ?? ($resource ? '-' : 'ไม่ได้ระบุ') }}</div>
                    </div>
                </div>

                @if ($resource?->plan)
                    <div class="plan-highlight mb-3">
                        <div>
                            <div class="po-title">{{ $resource->plan->size_label }}</div>
                            <div class="po-meta">{{ $resource->plan->cpu_vcpu }} vCPU · {{ $resource->plan->ram_gb }} GB RAM · {{ $resource->plan->storage_gb }} GB Storage</div>
                            @if ($resource->plan->suitable_for)
                                <div class="po-meta">เหมาะสำหรับ: {{ $resource->plan->suitable_for }}</div>
                            @endif
                        </div>
                        <div class="po-fee">{{ number_format($resource->plan->fee_per_year, 2) }} บาท/ปี</div>
                    </div>
                @elseif ($resource && ($resource->custom_cpu_vcpu || $resource->custom_ram_gb || $resource->custom_storage_gb))
                    <div class="plan-highlight mb-3">
                        <div>
                            <div class="po-title">สเปกกำหนดเอง (Custom)</div>
                            <div class="po-meta">
                                {{ $resource->custom_cpu_vcpu ?? '-' }} vCPU ·
                                {{ $resource->custom_ram_gb ?? '-' }} GB RAM ·
                                {{ $resource->custom_storage_gb ?? '-' }} GB Storage
                            </div>
                        </div>
                        @if ($resource->custom_fee)
                            <div class="po-fee">{{ number_format($resource->custom_fee, 2) }} บาท/ปี</div>
                        @endif
                    </div>
                @else
                    <p class="empty-note">ไม่ได้ระบุแพ็กเกจหรือสเปกทรัพยากร</p>
                @endif

                <div class="sub-divider">บริการที่ต้องการเปิดใช้งาน</div>
                @if (!empty($serviceRequest->enabled_services))
                    <div class="chip-list mb-3">
                        @foreach ($serviceRequest->enabled_services as $serviceName)
                            <span class="service-chip">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg>
                                {{ $enabledServiceLabels[$serviceName] ?? $serviceName }}
                                @if ($serviceName === 'other' && $serviceRequest->enabled_services_other_detail)
                                    — {{ $serviceRequest->enabled_services_other_detail }}
                                @endif
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="empty-note">ไม่ได้เลือกบริการ</p>
                @endif

                <div class="sub-divider">รายละเอียดทางเทคนิค</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-item__label">ภาษา/เฟรมเวิร์กที่ใช้พัฒนา</div>
                        <div class="info-item__value {{ $serviceRequest->language_framework ? '' : 'muted' }}">
                            {{ $serviceRequest->language_framework ?: 'ไม่ได้ระบุ' }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-item__label">ฐานข้อมูลที่ใช้</div>
                        <div class="info-item__value {{ $serviceRequest->database_used ? '' : 'muted' }}">
                            {{ $serviceRequest->database_used ?: 'ไม่ได้ระบุ' }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-item__label">พอร์ต/บริการที่ต้องการ</div>
                        <div class="info-item__value {{ $serviceRequest->port_service_needed ? '' : 'muted' }}">
                            {{ $serviceRequest->port_service_needed ?: 'ไม่ได้ระบุ' }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-item__label">ต้องการเชื่อมต่อจากภายนอก</div>
                        <div class="info-item__value">
                            @if ($serviceRequest->needs_external_connection)
                                <span class="yn-badge yes">✓ ต้องการ</span>
                            @else
                                <span class="yn-badge no">ไม่ต้องการ</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================
                 3. โดเมนที่ขอเปิดใช้งาน
            ============================================================ --}}
            <div class="panel detail-section" id="secDomains">
                <div class="section-head">
                    <span class="sec-num">3</span>
                    <h2>ชื่อโดเมนที่ขอเปิดใช้งาน</h2>
                </div>
                @forelse ($serviceRequest->domains as $domain)
                    <div class="info-grid @unless ($loop->last) mb-3 pb-3 @endunless" @unless ($loop->last) style="border-bottom:1px solid var(--line);" @endunless>
                        <div class="info-item span-2">
                            <div class="info-item__label">ชื่อโดเมน</div>
                            <div class="info-item__value"><code>{{ $domain->domain_name }}</code></div>
                        </div>
                        @if ($domain->domain_format)
                            <div class="info-item">
                                <div class="info-item__label">รูปแบบโดเมน</div>
                                <div class="info-item__value">{{ $domain->domain_format }}</div>
                            </div>
                        @endif
                        <div class="info-item">
                            <div class="info-item__label">หน่วยงานเจ้าของโดเมน</div>
                            <div class="info-item__value">
                                {{ $domain->departmentCode?->department_name ?? $domain->department_other ?? 'ไม่ได้ระบุ' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="empty-note">ยังไม่มีข้อมูลโดเมนสำหรับคำขอนี้</p>
                @endforelse
            </div>

            {{-- ============================================================
                 4. เอกสารแนบ และการรับรองค่าใช้จ่าย
            ============================================================ --}}
            <div class="panel detail-section" id="secDocs">
                <div class="section-head">
                    <span class="sec-num">4</span>
                    <h2>เอกสารแนบและการรับรองค่าใช้จ่าย</h2>
                </div>

                @if ($imageAttachments->isNotEmpty())
                    <div class="sub-divider">รูปภาพที่แนบมา ({{ $imageAttachments->count() }})</div>
                    <div class="img-gallery">
                        @foreach ($imageAttachments as $file)
                            @php $url = route('admin.requests.files.show', [$serviceRequest->request_id, $file->file_type]); @endphp
                            <div class="img-gallery-item" onclick="openLightbox('{{ $url }}', '{{ addslashes($fileTypeLabels[$file->file_type] ?? $file->file_type) }}')">
                                <img src="{{ $url }}" loading="lazy" alt="{{ $fileTypeLabels[$file->file_type] ?? $file->file_type }}">
                                <span class="img-zoom-hint">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/><path d="M11 8v6M8 11h6"/></svg>
                                </span>
                                <div class="img-caption">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                    {{ $fileTypeLabels[$file->file_type] ?? $file->file_type }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($fileAttachments->isNotEmpty())
                    <div class="sub-divider">เอกสารอื่น ๆ ที่แนบมา</div>
                    @foreach ($fileAttachments as $file)
                        @php
                            $ext = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
                            $url = route('admin.requests.files.show', [$serviceRequest->request_id, $file->file_type]);
                        @endphp
                        <div class="doc-card">
                            <span class="doc-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/></svg>
                            </span>
                            <div>
                                <div class="doc-name">{{ $fileTypeLabels[$file->file_type] ?? $file->file_type }}</div>
                                <div class="doc-sub">{{ strtoupper($ext) ?: 'ไฟล์' }}</div>
                            </div>
                            <a href="{{ $url }}" target="_blank" rel="noopener" class="btn btn-outline-soft btn-sm doc-action">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:4px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>
                                เปิด/ดาวน์โหลด
                            </a>
                        </div>
                    @endforeach
                @endif

                @if ($attachments->isEmpty())
                    <p class="empty-note">ไม่มีเอกสารแนบ</p>
                @endif

                <div class="sub-divider mt-3">การรับรองค่าใช้จ่าย</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-item__label">ยินยอมชำระค่าบริการ</div>
                        <div class="info-item__value">
                            @if ($serviceRequest->agree_to_pay)
                                <span class="yn-badge yes">✓ ยินยอม</span>
                            @else
                                <span class="yn-badge no">ไม่ยินยอม</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-item__label">ขอรับการยกเว้นค่าธรรมเนียม</div>
                        <div class="info-item__value">
                            @if ($serviceRequest->request_fee_waiver)
                                <span class="yn-badge yes">✓ ขอยกเว้น</span>
                            @else
                                <span class="yn-badge no">ไม่ขอยกเว้น</span>
                            @endif
                        </div>
                    </div>
                    @if ($serviceRequest->waiver_reason)
                        <div class="info-item span-2">
                            <div class="info-item__label">เหตุผลที่ขอยกเว้นค่าธรรมเนียม</div>
                            <div class="info-item__value">{{ $serviceRequest->waiver_reason }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ============================================================
                 5. การยอมรับข้อกำหนดและลายเซ็น
            ============================================================ --}}
            <div class="panel detail-section" id="secPolicy">
                <div class="section-head">
                    <span class="sec-num">5</span>
                    <h2>การยอมรับข้อกำหนดและลายเซ็น</h2>
                </div>
                <div class="info-grid mb-3">
                    <div class="info-item">
                        <div class="info-item__label">การยอมรับข้อกำหนดและนโยบาย</div>
                        <div class="info-item__value">
                            @if ($serviceRequest->accepted)
                                <span class="yn-badge yes">✓ ยอมรับแล้ว</span>
                            @else
                                <span class="yn-badge no">ยังไม่ยอมรับ</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-item__label">วันที่ยอมรับ</div>
                        <div class="info-item__value {{ $serviceRequest->accepted_date ? '' : 'muted' }}">
                            {{ $serviceRequest->accepted_date ? \Carbon\Carbon::parse($serviceRequest->accepted_date)->format('d/m/Y') : 'ไม่ได้ระบุ' }}
                        </div>
                    </div>
                </div>

                @if ($serviceRequest->signature_image_path)
                    <div class="sub-divider">ลายเซ็นผู้ขอใช้บริการ</div>
                    <div class="sig-box" style="cursor:zoom-in;" onclick="openLightbox('{{ route('admin.requests.files.show', [$serviceRequest->request_id, 'signature_image']) }}', 'ลายเซ็นผู้ขอใช้บริการ')">
                        <img src="{{ route('admin.requests.files.show', [$serviceRequest->request_id, 'signature_image']) }}" alt="ลายเซ็นผู้ขอใช้บริการ" loading="lazy">
                    </div>
                @else
                    <p class="empty-note">ไม่มีไฟล์ลายเซ็นแนบมา</p>
                @endif
            </div>

            {{-- ============================================================
                 6. ประวัติการอนุมัติ
            ============================================================ --}}
            <div class="panel detail-section" id="secApprovals">
                <div class="section-head">
                    <span class="sec-num">6</span>
                    <h2>ประวัติการอนุมัติ</h2>
                </div>
                @if ($serviceRequest->receipt_no)
                    <div class="info-item" style="margin-bottom:12px;">
                        <div class="info-item__label">เลขที่ใบเสร็จจากข้อมูลเดิม (ยังไม่ได้ตรวจสอบการชำระ)</div>
                        <div class="info-item__value">
                            {{ $serviceRequest->receipt_no }}
                            @if ($serviceRequest->receipt_date)
                                — {{ \Carbon\Carbon::parse($serviceRequest->receipt_date)->format('d/m/Y') }}
                                {{ $serviceRequest->receipt_time ? \Carbon\Carbon::parse($serviceRequest->receipt_time)->format('H:i') : '' }}
                            @endif
                        </div>
                    </div>
                @endif
                @forelse ($serviceRequest->approvals as $approval)
                    <div class="approval-row">
                        <span class="pill pill-{{ $approval->decision === 'rejected' ? 'rejected' : 'approved' }}">
                            {{ ['certify_info_only' => 'รับรองข้อมูล / อนุมัติบริการ', 'certify_and_waive_fee' => 'รับรองและยกเว้นค่าธรรมเนียม', 'acknowledge_assign_web_team' => 'รับทราบและมอบหมายงาน', 'rejected' => 'ไม่อนุมัติ'][$approval->decision] ?? $approval->decision }}
                        </span>
                        <div>
                            <div style="font-weight:600;font-size:13.5px;">{{ $approval->approver_name }} <span class="text-muted" style="font-weight:400;">({{ ['staff' => 'เจ้าหน้าที่', 'unit_head' => 'หัวหน้าหน่วยงาน', 'computer_center_deputy_director' => 'รองผู้อำนวยการ', 'computer_center_director' => 'ผู้อำนวยการ'][$approval->approver_level] ?? $approval->approver_level }})</span></div>
                            <div class="doc-sub">{{ $approval->decision_date ? \Carbon\Carbon::parse($approval->decision_date)->format('d/m/Y') : '-' }}</div>
                        </div>
                    </div>
                @empty
                    <p class="empty-note">ยังไม่มีประวัติการอนุมัติสำหรับคำขอนี้</p>
                @endforelse
            </div>

            {{-- ============================================================
                 7. บัญชีที่สร้างให้แล้ว
            ============================================================ --}}
            <div class="panel detail-section" id="secAccounts">
                <div class="section-head">
                    <span class="sec-num">7</span>
                    <h2>บัญชีที่สร้างให้แล้ว ({{ $serviceRequest->serviceAccounts->count() }})</h2>
                </div>
                @forelse ($serviceRequest->serviceAccounts as $account)
                    <div class="account-row">
                        <div class="d-flex align-items-center gap-2">
                            <code>{{ $account->username }}</code>
                            <span class="text-muted" style="font-size:12.5px;">{{ $accountTypeLabels[$account->account_type] ?? $account->account_type }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <x-admin.status :status="$account->status" />
                            <a href="{{ route('admin.accounts.edit', $account->account_id) }}" class="btn btn-outline-soft btn-sm">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                แก้ไข
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="empty-note">ยังไม่มีการสร้างบัญชีให้คำขอนี้</p>
                @endforelse
            </div>

            <div class="bottom-actions">
                <a href="{{ route('admin.requests.index') }}" class="btn btn-outline-soft">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:5px;"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                    กลับไปยังรายการคำขอ
                </a>
                <a href="{{ route('admin.accounts.create', $serviceRequest->request_id) }}" class="btn btn-brand">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" style="vertical-align:-2px;margin-right:5px;"><path d="M12 5v14M5 12h14"/></svg>
                    สร้างบัญชีให้คำขอนี้
                </a>
            </div>

        </div>
    </div>

    {{-- ============================================================
         Lightbox — สำหรับดูรูปภาพที่ผู้ใช้แนบมาแบบเต็มขนาด
    ============================================================ --}}
    <div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightbox(event)">
        <span class="lightbox-close" onclick="closeLightbox(event)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </span>
        <img id="lightboxImg" src="" alt="">
        <div class="lightbox-caption" id="lightboxCaption"></div>
    </div>

    <script>
        function openLightbox(url, caption) {
            document.getElementById('lightboxImg').src = url;
            document.getElementById('lightboxCaption').textContent = caption || '';
            document.getElementById('lightboxOverlay').classList.add('open');
        }
        function closeLightbox(e) {
            if (e.target.id === 'lightboxOverlay' || e.target.closest('.lightbox-close')) {
                document.getElementById('lightboxOverlay').classList.remove('open');
                document.getElementById('lightboxImg').src = '';
            }
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.getElementById('lightboxOverlay').classList.remove('open');
                document.getElementById('lightboxImg').src = '';
            }
        });
    </script>

@endsection
