@extends('admin.layout')

@section('title', $domain->domain_name)
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'รายละเอียดโดเมน')

@section('topbar-action')
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <a href="{{ route('admin.domains.index') }}" class="btn btn-outline-soft" style="display:inline-flex;align-items:center;gap:6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            กลับไปรายการโดเมน
        </a>
        <a href="{{ route('admin.domains.edit', $domain->domain_id) }}" class="btn btn-amber" style="display:inline-flex;align-items:center;gap:6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
            แก้ไขโดเมน
        </a>
    </div>
@endsection

@section('content')

    <style>
        .dm-header {
            display: flex; align-items: center; gap: 16px; margin-bottom: 22px;
        }
        .dm-header .domain-icon-wrap {
            width: 56px; height: 56px; border-radius: 16px; flex-shrink: 0;
            display: inline-flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, var(--moss-light), var(--amber-light)); color: var(--forest);
        }
        .dm-header h2 { margin: 0; font-family: 'Kanit', sans-serif; font-size: 22px; }
        .dm-header .dm-sub { font-size: 13px; color: var(--ink-soft); margin-top: 2px; }

        .info-grid {
            display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 24px;
        }
        .info-grid.span-full { grid-template-columns: 1fr; }
        .info-item__label { font-size: 12px; color: var(--ink-soft); margin-bottom: 3px; }
        .info-item__value { font-size: 14.5px; color: var(--ink); }
        .info-item.span-2 { grid-column: span 2; }

        .section-title {
            font-family: 'Kanit', sans-serif; font-size: 15px; font-weight: 600;
            display: flex; align-items: center; gap: 8px; margin: 0 0 14px;
            color: var(--forest);
        }

        .account-card {
            border: 1px solid var(--line); border-radius: var(--radius-md);
            padding: 14px 18px; display: flex; align-items: center; justify-content: space-between;
            gap: 12px; margin-bottom: 10px;
        }
        .account-card:last-child { margin-bottom: 0; }
        .account-card__main { display: flex; align-items: center; gap: 12px; }
        .account-card__avatar {
            width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
            display: inline-flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, var(--forest), var(--forest-2)); color: #fff;
            font-family: 'Kanit', sans-serif; font-weight: 600; font-size: 14px;
        }
        .account-card__username { font-weight: 600; font-size: 14.5px; }
        .account-card__meta { font-size: 12.5px; color: var(--ink-soft); }

        .empty-accounts {
            text-align: center; padding: 32px 16px; color: var(--ink-soft);
            border: 1px dashed var(--line); border-radius: var(--radius-md);
        }
        .empty-accounts p { margin: 8px 0 0; font-size: 14px; }

        .copy-btn {
            border: none; background: transparent; padding: 3px; cursor: pointer;
            color: #aaa; display: inline-flex; align-items: center; vertical-align: -3px; margin-left: 4px;
            border-radius: 5px; transition: color .15s ease, background .15s ease;
        }
        .copy-btn:hover { background: var(--moss-light); color: var(--forest); }
        .copy-btn.copied { color: var(--forest); }
        .info-item__value a { color: var(--forest); border-bottom: 1px dashed var(--line); }
        .info-item__value a:hover { border-bottom-color: var(--forest); }
    </style>

    <div class="panel" style="margin-bottom:20px;">
        <div class="dm-header">
            <span class="domain-icon-wrap">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg>
            </span>
            <div>
                <h2>
                    <code>{{ $domain->domain_name }}</code>
                    <button type="button" class="copy-btn js-copy" data-copy="{{ $domain->domain_name }}" title="คัดลอกชื่อโดเมน" aria-label="คัดลอกชื่อโดเมน">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
                    </button>
                </h2>
                <div class="dm-sub">คำขอ {{ $domain->serviceRequest->form_no }} — ยื่นเมื่อ {{ \Carbon\Carbon::parse($domain->serviceRequest->request_date)->format('d/m/Y') }}</div>
            </div>
        </div>

        <div class="info-grid">
            @if ($domain->domain_format)
                <div class="info-item">
                    <div class="info-item__label">รูปแบบโดเมน</div>
                    <div class="info-item__value">{{ $domain->domain_format }}</div>
                </div>
            @endif
            <div class="info-item">
                <div class="info-item__label">หน่วยงานเจ้าของโดเมน</div>
                <div class="info-item__value">{{ $domain->departmentCode?->department_name ?? $domain->department_other ?? 'ไม่ได้ระบุ' }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">สถานะคำขอ</div>
                <div class="info-item__value">
                    <span class="pill pill-{{ $domain->serviceRequest->status }}">{{ $domain->serviceRequest->status }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-bottom:20px;">
        <div class="section-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
            ผู้ขอใช้บริการ
        </div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-item__label">ชื่อ-นามสกุล</div>
                <div class="info-item__value">{{ $domain->serviceRequest->applicant->full_name }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">รหัสบุคลากร/นักศึกษา</div>
                <div class="info-item__value">{{ $domain->serviceRequest->applicant->staff_or_student_id ?: '-' }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">หน่วยงาน/สังกัด</div>
                <div class="info-item__value">{{ $domain->serviceRequest->applicant->unit_name ?: '-' }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">ตำแหน่ง</div>
                <div class="info-item__value">{{ $domain->serviceRequest->applicant->position_title ?: '-' }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">เบอร์โทรศัพท์</div>
                <div class="info-item__value">
                    @if ($domain->serviceRequest->applicant->phone)
                        <a href="tel:{{ $domain->serviceRequest->applicant->phone }}">{{ $domain->serviceRequest->applicant->phone }}</a>
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="info-item">
                <div class="info-item__label">อีเมล</div>
                <div class="info-item__value">
                    @if ($domain->serviceRequest->applicant->email)
                        <a href="mailto:{{ $domain->serviceRequest->applicant->email }}">{{ $domain->serviceRequest->applicant->email }}</a>
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="section-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            บัญชี (username) ที่ออกให้สำหรับคำขอนี้
        </div>

        @forelse ($domain->serviceRequest->serviceAccounts as $acc)
            <div class="account-card">
                <div class="account-card__main">
                    <span class="account-card__avatar">{{ mb_substr($acc->username, 0, 1) }}</span>
                    <div>
                        <div class="account-card__username">
                            <code>{{ $acc->username }}</code>
                            <button type="button" class="copy-btn js-copy" data-copy="{{ $acc->username }}" title="คัดลอก username" aria-label="คัดลอก username">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
                            </button>
                        </div>
                        <div class="account-card__meta">
                            {{ $acc->account_type }}
                            @if ($acc->expire_date)
                                · หมดอายุ {{ \Carbon\Carbon::parse($acc->expire_date)->format('d/m/Y') }}
                            @endif
                        </div>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <span class="pill pill-{{ $acc->status }}">{{ $acc->status }}</span>
                    <a href="{{ route('admin.accounts.edit', $acc->account_id) }}" class="btn btn-outline-soft" style="padding:6px 12px; font-size:13px;">
                        จัดการบัญชี
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-accounts">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" style="opacity:.5;"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <p>ยังไม่มีบัญชีที่ออกให้สำหรับโดเมนนี้</p>
                <a href="{{ route('admin.requests.show', $domain->serviceRequest->request_id) }}" class="btn btn-brand" style="margin-top:12px; display:inline-block;">
                    ไปที่คำขอเพื่อสร้างบัญชี
                </a>
            </div>
        @endforelse
    </div>

    <script>
        (function () {
            document.querySelectorAll('.js-copy').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var text = btn.getAttribute('data-copy');
                    var showCopied = function () {
                        var original = btn.innerHTML;
                        btn.classList.add('copied');
                        btn.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg>';
                        setTimeout(function () {
                            btn.innerHTML = original;
                            btn.classList.remove('copied');
                        }, 1200);
                    };
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(text).then(showCopied);
                    } else {
                        showCopied();
                    }
                });
            });
        })();
    </script>

@endsection