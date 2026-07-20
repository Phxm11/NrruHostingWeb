@extends('admin.layout')

@section('title', 'รายการคำขอ')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'รายการคำขอใช้บริการ')

@section('content')

    <style>
        @keyframes rowIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes bob   { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-3px); } }
        @keyframes popIn { from { opacity: 0; transform: scale(.9); } to { opacity: 1; transform: scale(1); } }

        /* ---------- distinct color per stat card ---------- */
        .stat-card--total  .stat-icon { background: linear-gradient(135deg, #e6f0fb, #d3e4fa); color: #2660b0; }
        .stat-card--pending .stat-icon { background: linear-gradient(135deg, var(--amber-light), #f6dfa0); color: #8a6408; }
        .stat-card--pending .stat-number { color: #8a6408; }
        .stat-card--approved .stat-icon { background: linear-gradient(135deg, var(--moss-light), #cfe3b7); color: var(--forest); }
        .stat-card--approved .stat-number { color: var(--forest); }
        .stat-card--pending .stat-icon { animation: bob 2.4s ease-in-out infinite; }

        /* ---------- toolbar ---------- */
        .requests-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-bottom: 18px; }
        .requests-toolbar .search-field { position: relative; flex: 1 1 240px; min-width: 200px; }
        .requests-toolbar .search-field svg {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            opacity: .45; pointer-events: none; transition: opacity .15s, color .15s;
        }
        .requests-toolbar .search-field input { padding-left: 36px; }
        .requests-toolbar .search-field:focus-within svg { opacity: 1; color: #2660b0; }
        .requests-toolbar .search-field:focus-within { box-shadow: 0 0 0 3px #e6f0fb; border-radius: 9px; }
        .requests-toolbar select { min-width: 160px; }
        .requests-toolbar button[type="submit"] { display: inline-flex; align-items: center; gap: 6px; }
        .requests-toolbar .filter-clear { font-size: 13px; color: #888; }
        .requests-toolbar .filter-clear:hover { color: var(--rust); }

        /* ---------- table polish ---------- */
        table.modern-table thead th .th-flex { display: inline-flex; align-items: center; gap: 6px; }
        table.modern-table thead th svg { opacity: .5; }
        table.modern-table tbody tr { animation: rowIn .3s ease both; }

        .pill svg { width: 11px; height: 11px; }

        .domain-chip {
            display: inline-flex; align-items: center; gap: 4px; font-size: 12.5px;
            background: #f4f4f2; border: 1px solid #eaeaea; border-radius: 999px; padding: 2px 9px; margin: 1px 2px 1px 0;
        }

        .account-count {
            display: inline-flex; align-items: center; gap: 5px; font-size: 13px;
        }
        .account-count .count-icon {
            width: 22px; height: 22px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;
            background: var(--moss-light); color: var(--forest); flex-shrink: 0;
        }
        .account-count.zero .count-icon { background: #ece9dc; color: var(--ink-soft); }

        .btn-brand.btn-sm { display: inline-flex; align-items: center; gap: 6px; }

        /* ---------- empty state ---------- */
        .empty-state { text-align: center; padding: 56px 16px; color: var(--ink-soft); }
        .empty-state .empty-icon-wrap {
            display: inline-flex; align-items: center; justify-content: center;
            width: 64px; height: 64px; border-radius: 50%; background: #e6f0fb; color: #2660b0;
            margin-bottom: 14px; animation: bob 2.4s ease-in-out infinite;
        }
        .empty-state p { margin: 0; font-size: 14px; }
    </style>

    @if (session('success'))
        <div class="banner-success" style="display:flex;align-items:center;gap:8px;animation:popIn .25s ease;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="stat-row">
        <div class="stat-card stat-card--total">
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 2h6l1 3h4v17H4V5h4l1-3Z"/></svg>
            </div>
            <div>
                <div class="stat-number">{{ $serviceRequests->total() }}</div>
                <div class="stat-label">คำขอทั้งหมด</div>
            </div>
        </div>
        <div class="stat-card stat-card--pending">
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="9"/></svg>
            </div>
            <div>
                <div class="stat-number">{{ $serviceRequests->where('status', 'submitted')->count() }}</div>
                <div class="stat-label">รอพิจารณา (หน้านี้)</div>
            </div>
        </div>
        <div class="stat-card stat-card--approved">
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <div>
                <div class="stat-number">{{ $serviceRequests->where('status', 'approved')->count() }}</div>
                <div class="stat-label">อนุมัติแล้ว (หน้านี้)</div>
            </div>
        </div>
    </div>

    <div class="panel">
        <form method="GET" class="requests-toolbar">
            <div class="search-field">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อ หรือรหัสบุคลากร"
                       value="{{ request('search') }}">
            </div>

            <select name="status" class="form-select">
                <option value="">สถานะทั้งหมด</option>
                @foreach (['submitted' => 'รอพิจารณา', 'approved' => 'อนุมัติแล้ว', 'rejected' => 'ไม่อนุมัติ', 'expired' => 'หมดอายุ'] as $val => $label)
                    <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <button class="btn btn-outline-soft" type="submit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                ค้นหา
            </button>

            @if (request('search') || request('status'))
                <a href="{{ route('admin.requests.index') }}" class="filter-clear">ล้างตัวกรอง ✕</a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th><span class="th-flex"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 2v4M16 2v4"/></svg>เลขที่คำขอ</span></th>
                        <th><span class="th-flex"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>ผู้ขอใช้บริการ</span></th>
                        <th><span class="th-flex"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21V7l9-4 9 4v14"/><path d="M9 21v-6h6v6"/></svg>หน่วยงาน</span></th>
                        <th><span class="th-flex"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg>โดเมน</span></th>
                        <th><span class="th-flex"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 3v3M16 3v3"/></svg>ระยะเวลาโครงการ</span></th>
                        <th><span class="th-flex"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-5"/></svg>สถานะ</span></th>
                        <th><span class="th-flex"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>บัญชีที่สร้างแล้ว</span></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($serviceRequests as $req)
                        @php
                            $avatarClasses = ['avatar-a', 'avatar-b', 'avatar-c'];
                            $avatarClass = $avatarClasses[ord(mb_substr($req->applicant->full_name, 0, 1)) % 3];
                            $initial = mb_substr($req->applicant->full_name, 0, 1);
                            $domains = $req->domains->pluck('domain_name');
                        @endphp
                        <tr style="animation-delay: {{ min($loop->index, 12) * 35 }}ms;">
                            <td><code>{{ $req->form_no }}</code></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar-circle {{ $avatarClass }}">{{ $initial }}</span>
                                    <div>
                                        {{ $req->applicant->full_name }}<br>
                                        <span class="text-muted" style="font-size:12px;">{{ $req->applicant->staff_or_student_id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $req->applicant->unit_name }}</td>
                            <td>
                                @forelse ($domains as $d)
                                    <span class="domain-chip">{{ $d }}</span>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </td>
                            <td style="font-size:13px;">
                                {{ \Carbon\Carbon::parse($req->project_start_date)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($req->project_end_date)->format('d/m/Y') }}
                            </td>
                            <td>
                                <span class="pill pill-{{ $req->status }}">
                                    @switch($req->status)
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
                                    {{ $req->status }}
                                </span>
                            </td>
                            <td>
                                <span class="account-count {{ $req->service_accounts_count == 0 ? 'zero' : '' }}">
                                    <span class="count-icon">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    </span>
                                    {{ $req->service_accounts_count }} บัญชี
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.accounts.create', $req->request_id) }}" class="btn btn-brand btn-sm">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
                                    สร้างบัญชี
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <span class="empty-icon-wrap">
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M9 2h6l1 3h4v17H4V5h4l1-3Z"/><path d="M9 12h6M9 16h6"/></svg>
                                    </span>
                                    <p>ยังไม่มีคำขอในระบบ{{ (request('search') || request('status')) ? 'ที่ตรงกับตัวกรอง' : '' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $serviceRequests->appends(request()->query())->links() }}</div>
    </div>

@endsection