@extends('admin.layout')

@section('title', 'บัญชีผู้ใช้บริการ')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'บัญชีผู้ใช้บริการ')

@section('content')

    {{-- ============================================================
         Scoped styles. These reuse the color tokens already defined
         in admin.layout (--rust / --rust-light / etc). Fallback values
         are provided only so this view still renders sensibly if
         viewed in isolation — the fallback is ignored whenever the
         layout's own variable is present.
    ============================================================ --}}
    <style>
        :root {
            --acc-active-bg: var(--moss-light, #e8f0dc);
            --acc-active-fg: var(--forest, #1a3323);
            --acc-disabled-bg: #eeeadc;
            --acc-disabled-fg: var(--ink-soft, #5c6659);
            --acc-expired-bg: var(--rust-light, #f6e1d8);
            --acc-expired-fg: var(--rust, #ae4830);
            --acc-gold: #8a6408;
            /* brand-aligned accent trio (sage / clay / olive) */
            --acc-blue-bg: #e3efe7;   --acc-blue-fg: #2f6b4a;
            --acc-violet-bg: #f4e8dd; --acc-violet-fg: #a1592f;
            --acc-teal-bg: #eef1da;   --acc-teal-fg: #6a7a2c;
        }

        @keyframes rowIn {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes shimmer {
            0%   { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }
        @keyframes bob {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(-4px); }
        }
        @keyframes popIn {
            from { opacity: 0; transform: scale(.85); }
            to   { opacity: 1; transform: scale(1); }
        }

        /* ---------- toolbar ---------- */
        .accounts-toolbar {
            display: flex; flex-wrap: wrap; align-items: center;
            gap: 10px; margin-bottom: 16px;
        }
        .accounts-toolbar .search-field {
            position: relative; flex: 1 1 240px; min-width: 200px;
        }
        .accounts-toolbar .search-field svg {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            opacity: .45; pointer-events: none;
        }
        .accounts-toolbar input[type="text"] {
            width: 100%; padding: 9px 12px 9px 36px; border-radius: 10px;
            border: 1px solid #ddd; font-size: 14px;
        }
        .accounts-toolbar select {
            border-radius: 10px; border: 1px solid #ddd; padding: 9px 12px; font-size: 14px;
        }
        .accounts-toolbar .btn-outline-soft { border-radius: 10px; }
        .accounts-toolbar .filter-clear {
            font-size: 13px; color: #888; text-decoration: none;
        }
        .accounts-toolbar .filter-clear:hover { color: var(--acc-expired-fg); }

        /* ---------- summary chips (reflects current page only) ---------- */
        .summary-chips { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 16px; }
        .summary-chip {
            display: inline-flex; align-items: center; gap: 7px;
            font-size: 13px; padding: 7px 14px 7px 8px; border-radius: 999px;
            background: #f4f4f2; color: #555; border: 1px solid #eaeaea;
        }
        .summary-chip strong { color: #222; font-size: 14px; }
        .summary-chip .chip-icon {
            width: 24px; height: 24px; border-radius: 50%; display: inline-flex;
            align-items: center; justify-content: center; flex-shrink: 0;
        }
        .summary-chip--total .chip-icon   { background: var(--acc-blue-bg);   color: var(--acc-blue-fg); }
        .summary-chip--active .chip-icon  { background: var(--acc-active-bg); color: var(--acc-active-fg); }
        .summary-chip--expired .chip-icon { background: var(--acc-expired-bg); color: var(--acc-expired-fg); }

        /* ---------- colorful account-type tag ---------- */
        .type-tag {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 12.5px; font-weight: 600; padding: 4px 10px; border-radius: 8px;
        }
        .type-tag-0 { background: var(--acc-blue-bg);   color: var(--acc-blue-fg); }
        .type-tag-1 { background: var(--acc-violet-bg); color: var(--acc-violet-fg); }
        .type-tag-2 { background: var(--acc-teal-bg);   color: var(--acc-teal-fg); }
        .type-tag-3 { background: var(--acc-active-bg); color: var(--acc-active-fg); }
        .type-tag-4 { background: var(--acc-expired-bg);color: var(--acc-expired-fg); }
        .type-tag-5 { background: #fdf1cf;              color: var(--acc-gold); }

        /* ---------- gradient avatars (brand tones) ---------- */
        .avatar-a { background: linear-gradient(135deg, var(--forest, #1a3323), var(--forest-2, #244430)); color: #fff; }
        .avatar-b { background: linear-gradient(135deg, var(--moss, #6c9752), #7ba15b); color: #fff; }
        .avatar-c { background: linear-gradient(135deg, var(--amber-deep, #a6740e), var(--amber, #d79a2c)); color: #fff; }

        /* ---------- table polish ---------- */
        .modern-table thead th {
            font-size: 12.5px; text-transform: uppercase; letter-spacing: .03em;
            color: #8a8a8a; font-weight: 600; border-bottom: 1px solid #ececec;
            padding-bottom: 10px;
        }
        .modern-table thead th .th-flex { display: inline-flex; align-items: center; gap: 6px; }
        .modern-table thead th svg { opacity: .55; }
        .modern-table tbody tr {
            border-bottom: 1px solid #f2f2f2; transition: background .15s, transform .15s;
            animation: rowIn .3s ease both;
        }
        .modern-table tbody tr:hover { background: #fafafa; transform: translateX(2px); }
        .modern-table tbody td { vertical-align: middle; padding-top: 12px; padding-bottom: 12px; }

        .avatar-circle {
            width: 34px; height: 34px; min-width: 34px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; margin-right: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,.12);
        }

        .pill {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 12.5px; font-weight: 600; padding: 4px 10px; border-radius: 999px;
        }
        .pill svg { width: 12px; height: 12px; }
        .pill-active   { background: var(--acc-active-bg);   color: var(--acc-active-fg); }
        .pill-disabled { background: var(--acc-disabled-bg); color: var(--acc-disabled-fg); }
        .pill-expired  { background: var(--acc-expired-bg);  color: var(--acc-expired-fg); animation: bob 2.4s ease-in-out infinite; }

        .expire-soon { color: var(--acc-expired-fg); font-weight: 600; }
        .expire-soon .warn-icon { display: inline-flex; animation: bob 1.6s ease-in-out infinite; }

        /* ---------- search focus glow ---------- */
        .accounts-toolbar .search-field:focus-within {
            box-shadow: 0 0 0 3px var(--acc-blue-bg); border-radius: 10px;
        }
        .accounts-toolbar .search-field:focus-within svg { color: var(--acc-blue-fg); opacity: 1; }
        .accounts-toolbar input[type="text"]:focus, .accounts-toolbar select:focus {
            outline: none; border-color: var(--acc-blue-fg);
        }

        /* ---------- row actions as visible inline buttons ---------- */
        .row-actions-inline { display: flex; align-items: center; justify-content: flex-end; gap: 6px; flex-wrap: wrap; }
        .row-actions-inline form { margin: 0; }
        .row-btn {
            display: inline-flex; align-items: center; gap: 6px;
            border-radius: 8px; border: 1px solid #e6e6e6; background: #fff;
            padding: 7px 11px; font-size: 12.5px; font-weight: 600; color: #444;
            cursor: pointer; text-decoration: none; transition: background .15s, border-color .15s, color .15s, transform .1s;
            white-space: nowrap;
        }
        .row-btn:hover { transform: translateY(-1px); }
        .row-btn svg { flex-shrink: 0; }
        .row-btn--edit:hover   { background: var(--acc-blue-bg);   border-color: var(--acc-blue-bg);   color: var(--acc-blue-fg); }
        .row-btn--toggle:hover { background: #fdf1cf;              border-color: #fdf1cf;              color: var(--acc-gold); }
        .row-btn--delete       { color: var(--acc-expired-fg); }
        .row-btn--delete:hover { background: var(--acc-expired-bg); border-color: var(--acc-expired-bg); }

        /* on narrow screens, keep labels but let buttons wrap under each other's row */
        @media (max-width: 480px) {
            .row-actions-inline { justify-content: flex-start; }
        }

        /* ---------- empty state ---------- */
        .empty-state { text-align: center; padding: 56px 16px; color: #8a8a8a; }
        .empty-state .empty-icon-wrap {
            display: inline-flex; align-items: center; justify-content: center;
            width: 64px; height: 64px; border-radius: 50%; background: var(--acc-blue-bg);
            color: var(--acc-blue-fg); margin-bottom: 14px; animation: bob 2.4s ease-in-out infinite;
        }
        .empty-state p { margin: 0; font-size: 14px; }

        /* ---------- responsive: collapse table to cards ---------- */
        @media (max-width: 760px) {
            .modern-table thead { display: none; }
            .modern-table, .modern-table tbody, .modern-table tr, .modern-table td { display: block; width: 100%; }
            .modern-table tbody tr {
                border: 1px solid #eee; border-radius: 12px; margin-bottom: 10px; padding: 10px 12px;
            }
            .modern-table tbody td {
                padding: 6px 0; border: none;
            }
            .modern-table tbody td[data-label]::before {
                content: attr(data-label); display: block; font-size: 11px;
                text-transform: uppercase; letter-spacing: .03em; color: #999; margin-bottom: 2px;
            }
            .modern-table td.text-end { text-align: left !important; }
        }
    </style>

    <div class="panel">

        {{-- ============================================================
             Toolbar: search + status filter
             Note: the "q" field searches by name/username. Wire this up
             on the controller side (e.g. WHERE username LIKE / whereHas
             applicant name) if it isn't already — the view now sends it.
        ============================================================ --}}
        <form method="GET" class="accounts-toolbar">
            <div class="search-field">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหาชื่อผู้ใช้ หรือ username...">
            </div>

            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">สถานะทั้งหมด</option>
                <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>ใช้งานอยู่</option>
                <option value="disabled" {{ request('status') == 'disabled' ? 'selected' : '' }}>ระงับการใช้งาน</option>
                <option value="expired"  {{ request('status') == 'expired'  ? 'selected' : '' }}>หมดอายุ</option>
            </select>

            <button class="btn btn-outline-soft" type="submit" style="display:inline-flex;align-items:center;gap:6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16l-6 8v6l-4 2v-8L4 4Z"/></svg>
                กรอง
            </button>

            @if (request('q') || request('status'))
                <a href="{{ route('admin.accounts.index') }}" class="filter-clear">ล้างตัวกรอง ✕</a>
            @endif
        </form>

        {{-- Quick summary of what's on this page --}}
        <div class="summary-chips">
            <span class="summary-chip summary-chip--total">
                <span class="chip-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/></svg></span>
                <strong>{{ $accounts->total() }}</strong> รายการทั้งหมด
            </span>
            <span class="summary-chip summary-chip--active">
                <span class="chip-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg></span>
                <strong>{{ $accounts->where('status', 'active')->count() }}</strong> ใช้งานอยู่ (หน้านี้)
            </span>
            <span class="summary-chip summary-chip--expired">
                <span class="chip-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
                <strong>{{ $accounts->where('status', 'expired')->count() }}</strong> หมดอายุ (หน้านี้)
            </span>
        </div>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th><span class="th-flex"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Username</span></th>
                        <th><span class="th-flex"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>ผู้ขอใช้บริการ</span></th>
                        <th><span class="th-flex"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg>โดเมน/คำขอ</span></th>
                        <th><span class="th-flex"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 3 6v6c0 5 4 9 9 10 5-1 9-5 9-10V6l-9-4Z"/></svg>ประเภทบัญชี</span></th>
                        <th><span class="th-flex"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-5"/></svg>สถานะ</span></th>
                        <th><span class="th-flex"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 3v3M16 3v3"/></svg>วันหมดอายุ</span></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accounts as $acc)
                        @php
                            $avatarClasses = ['avatar-a', 'avatar-b', 'avatar-c'];
                            $avatarClass = $avatarClasses[ord(mb_substr($acc->applicant->full_name, 0, 1)) % 3];
                            $initial = mb_substr($acc->applicant->full_name, 0, 1);

                            $expireDate = $acc->expire_date ? \Carbon\Carbon::parse($acc->expire_date) : null;
                            $expiresSoon = $expireDate && $expireDate->isFuture() && $expireDate->diffInDays(now()) <= 14;

                            $typeTagIndex = crc32($acc->account_type) % 6;
                        @endphp
                        <tr style="animation-delay: {{ min($loop->index, 12) * 35 }}ms;">
                            <td data-label="Username"><code>{{ $acc->username }}</code></td>
                            <td data-label="ผู้ขอใช้บริการ">
                                <div class="d-flex align-items-center">
                                    <span class="avatar-circle {{ $avatarClass }}">{{ $initial }}</span>
                                    <div>
                                        {{ $acc->applicant->full_name }}<br>
                                        <span class="text-muted" style="font-size:12px;">{{ $acc->applicant->staff_or_student_id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td data-label="โดเมน/คำขอ">{{ $acc->serviceRequest->form_no }} — {{ $acc->serviceRequest->domains->pluck('domain_name')->join(', ') ?: '-' }}</td>
                            <td data-label="ประเภทบัญชี"><span class="type-tag type-tag-{{ $typeTagIndex }}">{{ $acc->account_type }}</span></td>
                            <td data-label="สถานะ">
                                <span class="pill pill-{{ $acc->status }}">
                                    @if ($acc->status === 'active')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                                    @elseif ($acc->status === 'disabled')
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.6 1.9 18a2 2 0 0 0 1.7 3h16.8a2 2 0 0 0 1.7-3L14.7 3.6a2 2 0 0 0-3.4 0Z"/></svg>
                                    @endif
                                    {{ $acc->status }}
                                </span>
                            </td>
                            <td data-label="วันหมดอายุ" style="font-size:13px;" class="{{ $expiresSoon ? 'expire-soon' : '' }}">
                                {{ $expireDate ? $expireDate->format('d/m/Y') : '-' }}
                                @if ($expiresSoon)
                                    <span class="warn-icon" title="ใกล้หมดอายุ">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.6 1.9 18a2 2 0 0 0 1.7 3h16.8a2 2 0 0 0 1.7-3L14.7 3.6a2 2 0 0 0-3.4 0Z"/></svg>
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="row-actions-inline">
                                    <a href="{{ route('admin.accounts.edit', $acc->account_id) }}" class="row-btn row-btn--edit" title="แก้ไข">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                        แก้ไข
                                    </a>
                                    <form action="{{ route('admin.accounts.toggle-status', $acc->account_id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="row-btn row-btn--toggle" title="{{ $acc->status === 'active' ? 'ระงับการใช้งาน' : 'เปิดใช้งาน' }}">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                            {{ $acc->status === 'active' ? 'ระงับ' : 'เปิดใช้งาน' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.accounts.destroy', $acc->account_id) }}" method="POST"
                                          data-confirm="ยืนยันลบบัญชี {{ $acc->username }}? การลบไม่สามารถย้อนกลับได้">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="row-btn row-btn--delete" title="ลบบัญชี">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                            ลบ
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <span class="empty-icon-wrap">
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/><path d="M9 15h6"/></svg>
                                    </span>
                                    <p>ยังไม่มีบัญชีในระบบ{{ (request('q') || request('status')) ? 'ที่ตรงกับตัวกรอง' : '' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $accounts->appends(request()->query())->links() }}</div>
    </div>

@endsection