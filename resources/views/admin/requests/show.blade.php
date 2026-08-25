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
        $fileAttachments = $attachments->diff($imageAttachments);
    @endphp

    <style>
        @keyframes popIn { from { opacity: 0; transform: scale(.94); } to { opacity: 1; transform: scale(1); } }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

        .back-link {
            display: inline-flex; align-items: center; gap: 6px; font-size: 13.5px;
            color: var(--ink-soft); margin-bottom: 14px; transition: color .15s ease;
        }
        .back-link:hover { color: var(--forest); }

        /* ---------- Detail header ---------- */
        .detail-header {
            position: relative; overflow: hidden;
            background: radial-gradient(130% 160% at 90% -20%, var(--forest-2) 0%, var(--forest) 60%, #142a1a 100%);
            color: #fff; padding: 26px 28px; margin-bottom: 20px;
        }
        .detail-header::after {
            content: ''; position: absolute; width: 260px; height: 260px;
            background: radial-gradient(circle, rgba(224,165,38,.28), transparent 70%); top: -100px; right: -60px; pointer-events: none;
        }
        .detail-header-top { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 16px; position: relative; z-index: 1; }
        .detail-header .avatar-circle { width: 52px; height: 52px; font-size: 19px; }
        .detail-header h1 { font-size: 20px; font-weight: 700; margin: 0 0 3px; color: #fff; }
        .detail-header .sub { font-size: 13px; color: #cbd6c1; }
        .detail-header .form-no-tag {
            font-family: 'Kanit', sans-serif; font-size: 12.5px; background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.22); border-radius: 999px; padding: 4px 12px; display: inline-flex; align-items: center; gap: 6px;
        }
        .detail-header .pill { background: rgba(255,255,255,.92); }
        .detail-actions { display: flex; flex-wrap: wrap; gap: 8px; position: relative; z-index: 1; margin-top: 16px; }
        .detail-actions form { display: inline; }
        .detail-actions .btn { display: inline-flex; align-items: center; gap: 6px; }
        .btn-header-outline {
            border: 1px solid rgba(255,255,255,.35); background: rgba(255,255,255,.08); color: #fff;
            border-radius: var(--radius-sm); font-size: 13.5px; padding: 8px 15px; transition: background .15s ease;
        }
        .btn-header-outline:hover { background: rgba(255,255,255,.18); color: #fff; }

        /* ---------- Section cards ---------- */
        .detail-section { margin-bottom: 18px; scroll-margin-top: 20px; }
        .detail-section .section-head { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
        .detail-section .sec-num {
            width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
            font-family: 'Kanit', sans-serif; font-weight: 700; font-size: 14px; color: #fff;
            background: linear-gradient(135deg, var(--amber), var(--amber-deep)); box-shadow: 0 6px 14px -6px rgba(185,132,15,.6);
            flex-shrink: 0;
        }
        .detail-section h2 { font-size: 15.5px; font-weight: 600; color: var(--forest); margin: 0; }

        .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px 24px; }
        @media (max-width: 640px) { .info-grid { grid-template-columns: 1fr; } }
        .info-item { animation: fadeIn .3s ease both; }
        .info-item.span-2 { grid-column: 1 / -1; }
        .info-item__label {
            font-size: 11.5px; text-transform: uppercase; letter-spacing: .04em; color: var(--ink-soft);
            display: flex; align-items: center; gap: 6px; margin-bottom: 4px;
        }
        .info-item__label svg { opacity: .6; flex-shrink: 0; }
        .info-item__value { font-size: 14.5px; font-weight: 500; color: var(--ink); word-break: break-word; }
        .info-item__value.muted { color: var(--ink-soft); font-weight: 400; font-style: italic; }

        .sub-divider { font-size: 13px; font-weight: 600; color: var(--ink); margin: 4px 0 12px; display: flex; align-items: center; gap: 8px; }
        .sub-divider::before { content: ''; width: 4px; height: 14px; border-radius: 4px; background: var(--moss); display: inline-block; }

        .dev-card {
            border: 1px solid var(--line); border-radius: var(--radius-md); padding: 14px 16px; margin-bottom: 10px;
            background: var(--surface-2); display: flex; align-items: flex-start; gap: 12px;
        }
        .dev-card:last-child { margin-bottom: 0; }
        .dev-card .avatar-circle { width: 34px; height: 34px; font-size: 13px; margin-right: 0; }
        .dev-card .dev-name { font-weight: 600; font-size: 14px; }
        .dev-card .dev-meta { font-size: 12.5px; color: var(--ink-soft); margin-top: 2px; }
        .dev-card .dev-meta span { margin-right: 12px; display: inline-flex; align-items: center; gap: 4px; }

        .chip-list { display: flex; flex-wrap: wrap; gap: 8px; }
        .service-chip {
            display: inline-flex; align-items: center; gap: 7px; font-size: 13px; font-weight: 500;
            background: var(--moss-light); color: var(--forest); border-radius: 999px; padding: 7px 14px;
        }
        .service-chip svg { flex-shrink: 0; }

        .plan-highlight {
            border: 1.5px solid #d6e3c6; background: var(--moss-light); border-radius: var(--radius-md);
            padding: 16px 18px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
        }
        .plan-highlight .po-title { font-weight: 700; font-size: 15.5px; color: var(--forest); }
        .plan-highlight .po-meta { font-size: 13px; color: var(--ink-soft); margin-top: 3px; }
        .plan-highlight .po-fee { font-family: 'Kanit', sans-serif; font-weight: 700; font-size: 17px; color: var(--forest); }

        .doc-card {
            display: flex; align-items: center; gap: 12px; border: 1px solid var(--line); border-radius: var(--radius-md);
            padding: 12px 14px; margin-bottom: 10px; transition: border-color .15s ease, background .15s ease;
        }
        .doc-card:last-child { margin-bottom: 0; }
        .doc-card:hover { border-color: var(--moss); background: var(--moss-light); }
        .doc-card .doc-icon {
            width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
            background: var(--amber-light); color: var(--amber-deep);
        }
        .doc-card .doc-thumb { width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0; object-fit: cover; }
        .doc-card .doc-name { font-weight: 500; font-size: 13.5px; }
        .doc-card .doc-sub { font-size: 12px; color: var(--ink-soft); }
        .doc-card .doc-action { margin-left: auto; flex-shrink: 0; }

        /* ---------- uploaded image gallery ---------- */
        .img-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 14px; margin-bottom: 18px; }
        .img-gallery-item {
            border: 1px solid var(--line); border-radius: var(--radius-md); overflow: hidden; cursor: zoom-in;
            background: var(--surface-2); transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
            position: relative;
        }
        .img-gallery-item:hover { border-color: var(--moss); box-shadow: var(--shadow-md); transform: translateY(-2px); }
        .img-gallery-item img { width: 100%; height: 150px; object-fit: cover; display: block; background: #eee; }
        .img-gallery-item .img-caption {
            padding: 8px 10px; font-size: 12px; color: var(--ink-soft); display: flex; align-items: center; gap: 6px;
            border-top: 1px solid var(--line);
        }
        .img-gallery-item .img-zoom-hint {
            position: absolute; top: 8px; right: 8px; width: 26px; height: 26px; border-radius: 50%;
            background: rgba(21,35,26,.55); color: #fff; display: flex; align-items: center; justify-content: center;
            opacity: 0; transition: opacity .15s ease;
        }
        .img-gallery-item:hover .img-zoom-hint { opacity: 1; }

        /* lightbox */
        .lightbox-overlay {
            display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(15,22,17,.86);
            align-items: center; justify-content: center; padding: 32px; cursor: zoom-out;
            animation: popIn .18s ease;
        }
        .lightbox-overlay.open { display: flex; }
        .lightbox-overlay img { max-width: 92vw; max-height: 86vh; border-radius: 10px; box-shadow: 0 20px 60px rgba(0,0,0,.5); }
        .lightbox-overlay .lightbox-caption {
            position: absolute; bottom: 22px; left: 0; right: 0; text-align: center; color: #eef2e6; font-size: 13.5px;
        }
        .lightbox-close {
            position: absolute; top: 18px; right: 22px; width: 38px; height: 38px; border-radius: 50%;
            background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.28); color: #fff;
            display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background .15s ease;
        }
        .lightbox-close:hover { background: rgba(255,255,255,.24); }

        .sig-box { border: 1px dashed var(--line); border-radius: var(--radius-md); padding: 14px; background: var(--surface-2); display: inline-block; }
        .sig-box img { max-width: 220px; max-height: 110px; display: block; }

        .yn-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 13px; font-weight: 600; padding: 4px 11px; border-radius: 999px; }
        .yn-badge.yes { background: var(--moss-light); color: var(--forest); }
        .yn-badge.no  { background: #ece9dc; color: var(--ink-soft); }

        .empty-note { font-size: 13.5px; color: var(--ink-soft); font-style: italic; padding: 6px 0; }

        .approval-row {
            display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--line);
        }
        .approval-row:last-child { border-bottom: none; }

        .account-row {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            border: 1px solid var(--line); border-radius: var(--radius-md); padding: 11px 14px; margin-bottom: 8px;
        }
        .account-row:last-child { margin-bottom: 0; }
        .account-row code { font-size: 13.5px; font-weight: 600; }

        .bottom-actions {
            display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
            margin-top: 4px;
        }
    </style>

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
                                <span class="pill pill-{{ $serviceRequest->status }}">
                                    @switch($serviceRequest->status)
                                        @case('approved')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                                            @break
                                        @case('rejected')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                            @break
                                        @case('expired')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                            @break
                                        @default
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="9"/></svg>
                                    @endswitch
                                    {{ $serviceRequest->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-end" style="position:relative;z-index:1;">
                        <div class="sub">วันที่ยื่นคำขอ</div>
                        <div style="font-weight:600;">{{ \Carbon\Carbon::parse($serviceRequest->request_date)->format('d/m/Y') }}</div>
                    </div>
                </div>

                <div class="detail-actions">
                    @if ($serviceRequest->status !== 'approved')
                        <form action="{{ route('admin.requests.approve', $serviceRequest->request_id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-approve btn-sm">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg>
                                อนุมัติคำขอ
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('admin.accounts.create', $serviceRequest->request_id) }}" class="btn btn-amber btn-sm">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
                        สร้างบัญชีให้ผู้ขอใช้บริการ
                    </a>
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
                            รหัสบุคลากร/นักศึกษา
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
                            @php $url = asset('storage/' . $file->file_path); @endphp
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
                            $url = asset('storage/' . $file->file_path);
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
                    <div class="sig-box" style="cursor:zoom-in;" onclick="openLightbox('{{ asset('/Storage/' . $serviceRequest->signature_image_path) }}', 'ลายเซ็นผู้ขอใช้บริการ')">
                        <img src="{{ asset('storage/' . $serviceRequest->signature_image_path) }}" alt="ลายเซ็นผู้ขอใช้บริการ" loading="lazy">
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
                        <div class="info-item__label">เลขที่ใบเสร็จ</div>
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
                        <span class="pill pill-{{ $approval->decision === 'approved' ? 'approved' : ($approval->decision === 'rejected' ? 'rejected' : 'submitted') }}">
                            {{ $approval->decision }}
                        </span>
                        <div>
                            <div style="font-weight:600;font-size:13.5px;">{{ $approval->approver_name }} <span class="text-muted" style="font-weight:400;">({{ $approval->approver_level }})</span></div>
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
                            <span class="pill pill-{{ $account->status }}">{{ $account->status }}</span>
                            <a href="{{ route('admin.accounts.edit', $account->account_id) }}" class="btn btn-outline-soft btn-sm">แก้ไข</a>
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
