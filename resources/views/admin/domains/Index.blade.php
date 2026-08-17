@extends('admin.layout')

@section('title', 'โดเมนผู้ใช้บริการ')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'โดเมนผู้ใช้บริการ')

@section('content')

    <style>
        :root {
            --dm-active-bg: var(--moss-light, #e8f0dc);
            --dm-active-fg: var(--forest, #1a3323);
            --dm-pending-bg: #fdf1cf;
            --dm-pending-fg: #8a6408;
            --dm-blue-bg: #e3efe7;   --dm-blue-fg: #2f6b4a;
        }

        @keyframes rowIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes bob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }

        /* ---------- toolbar ---------- */
        .domains-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-bottom: 16px; }
        .domains-toolbar .search-field { position: relative; flex: 1 1 240px; min-width: 200px; }
        .domains-toolbar .search-field svg {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%); opacity: .45; pointer-events: none;
        }
        .domains-toolbar input[type="text"] { width: 100%; padding: 9px 12px 9px 36px; border-radius: 10px; border: 1px solid #ddd; font-size: 14px; }
        .domains-toolbar select { border-radius: 10px; border: 1px solid #ddd; padding: 9px 12px; font-size: 14px; }
        .domains-toolbar .filter-clear { font-size: 13px; color: #888; text-decoration: none; }
        .domains-toolbar .filter-clear:hover { color: var(--rust); }
        .domains-toolbar .search-field:focus-within { box-shadow: 0 0 0 3px var(--dm-blue-bg); border-radius: 10px; }

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
        .summary-chip--total .chip-icon   { background: var(--dm-blue-bg);   color: var(--dm-blue-fg); }
        .summary-chip--linked .chip-icon  { background: var(--dm-active-bg); color: var(--dm-active-fg); }
        .summary-chip--pending .chip-icon { background: var(--dm-pending-bg); color: var(--dm-pending-fg); }

        .modern-table tbody tr { animation: rowIn .3s ease both; }
        .modern-table tbody tr:hover { background: var(--dm-active-bg); }

        .domain-name-cell { display: flex; align-items: center; gap: 10px; }
        .domain-icon-wrap {
            width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
            display: inline-flex; align-items: center; justify-content: center;
            background: var(--dm-blue-bg); color: var(--dm-blue-fg);
        }

        .account-chip-list { display: flex; flex-wrap: wrap; gap: 6px; }
        .account-chip {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 12px; font-weight: 600; padding: 4px 9px; border-radius: 8px;
            background: var(--dm-active-bg); color: var(--dm-active-fg);
        }
        .account-chip.is-disabled { background: #eeeadc; color: var(--ink-soft); }
        .account-chip.is-expired  { background: var(--rust-light); color: var(--rust); }
        .no-account-tag {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 12px; font-weight: 600; padding: 4px 9px; border-radius: 8px;
            background: var(--dm-pending-bg); color: var(--dm-pending-fg);
        }

        .row-btn {
            display: inline-flex; align-items: center; gap: 6px;
            border-radius: 8px; border: 1px solid #e6e6e6; background: #fff;
            padding: 7px 11px; font-size: 12.5px; font-weight: 600; color: #444;
            cursor: pointer; text-decoration: none; transition: background .15s, border-color .15s, color .15s, transform .1s;
            white-space: nowrap;
        }
        .row-btn:hover { transform: translateY(-1px); background: var(--dm-blue-bg); border-color: var(--dm-blue-bg); color: var(--dm-blue-fg); }

        .empty-state { text-align: center; padding: 56px 16px; color: #8a8a8a; }
        .empty-state .empty-icon-wrap {
            display: inline-flex; align-items: center; justify-content: center;
            width: 64px; height: 64px; border-radius: 50%; background: var(--dm-blue-bg);
            color: var(--dm-blue-fg); margin-bottom: 14px; animation: bob 2.4s ease-in-out infinite;
        }
        .empty-state p { margin: 0; font-size: 14px; }

        @media (max-width: 760px) {
            .modern-table thead { display: none; }
            .modern-table, .modern-table tbody, .modern-table tr, .modern-table td { display: block; width: 100%; }
            .modern-table tbody tr { border: 1px solid #eee; border-radius: 12px; margin-bottom: 10px; padding: 10px 12px; }
            .modern-table tbody td { padding: 6px 0; border: none; }
            .modern-table tbody td[data-label]::before {
                content: attr(data-label); display: block; font-size: 11px;
                text-transform: uppercase; letter-spacing: .03em; color: #999; margin-bottom: 2px;
            }
            .modern-table td.text-end { text-align: left !important; }
        }
    </style>

    @if (session('success'))
        <div class="banner-success">{{ session('success') }}</div>
    @endif

    <div class="panel">

        <form method="GET" class="domains-toolbar">
            <div class="search-field">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหาชื่อโดเมน, ชื่อผู้ใช้ หรือ username...">
            </div>

            <select name="has_account" class="form-select" onchange="this.form.submit()">
                <option value="">ทั้งหมด</option>
                <option value="yes" {{ request('has_account') == 'yes' ? 'selected' : '' }}>มีบัญชีแล้ว</option>
                <option value="no"  {{ request('has_account') == 'no'  ? 'selected' : '' }}>ยังไม่มีบัญชี</option>
            </select>

            <button class="btn btn-outline-soft" type="submit" style="display:inline-flex;align-items:center;gap:6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16l-6 8v6l-4 2v-8L4 4Z"/></svg>
                กรอง
            </button>

            @if (request('q') || request('has_account'))
                <a href="{{ route('admin.domains.index') }}" class="filter-clear">ล้างตัวกรอง ✕</a>
            @endif
        </form>

        <div class="summary-chips">
            <span class="summary-chip summary-chip--total">
                <span class="chip-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg></span>
                <strong>{{ $domains->total() }}</strong> โดเมนทั้งหมด
            </span>
            <span class="summary-chip summary-chip--linked">
                <span class="chip-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg></span>
                <strong>{{ $domains->filter(fn($d) => $d->serviceRequest->serviceAccounts->isNotEmpty())->count() }}</strong> มีบัญชีแล้ว (หน้านี้)
            </span>
            <span class="summary-chip summary-chip--pending">
                <span class="chip-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
                <strong>{{ $domains->filter(fn($d) => $d->serviceRequest->serviceAccounts->isEmpty())->count() }}</strong> รอสร้างบัญชี (หน้านี้)
            </span>
        </div>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th><span class="th-flex">ชื่อโดเมน</span></th>
                        <th><span class="th-flex">หน่วยงาน</span></th>
                        <th><span class="th-flex">ผู้ขอใช้บริการ</span></th>
                        <th><span class="th-flex">บัญชี (username)</span></th>
                        <th><span class="th-flex">คำขอ</span></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($domains as $domain)
                        @php
                            $accounts = $domain->serviceRequest->serviceAccounts;
                        @endphp
                        <tr style="animation-delay: {{ min($loop->index, 12) * 35 }}ms;">
                            <td data-label="ชื่อโดเมน">
                                <div class="domain-name-cell">
                                    <span class="domain-icon-wrap">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg>
                                    </span>
                                    <code>{{ $domain->domain_name }}</code>
                                </div>
                            </td>
                            <td data-label="หน่วยงาน">{{ $domain->departmentCode?->department_name ?? $domain->department_other ?? '-' }}</td>
                            <td data-label="ผู้ขอใช้บริการ">
                                {{ $domain->serviceRequest->applicant->full_name }}<br>
                                <span class="text-muted" style="font-size:12px;">{{ $domain->serviceRequest->applicant->staff_or_student_id }}</span>
                            </td>
                            <td data-label="บัญชี (username)">
                                @if ($accounts->isNotEmpty())
                                    <div class="account-chip-list">
                                        @foreach ($accounts as $acc)
                                            <span class="account-chip {{ $acc->status === 'disabled' ? 'is-disabled' : ($acc->status === 'expired' ? 'is-expired' : '') }}">
                                                <code>{{ $acc->username }}</code>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="no-account-tag">ยังไม่มีบัญชี</span>
                                @endif
                            </td>
                            <td data-label="คำขอ">{{ $domain->serviceRequest->form_no }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.domains.show', $domain->domain_id) }}" class="row-btn" title="ดูรายละเอียด">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    ดูรายละเอียด
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <span class="empty-icon-wrap">
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg>
                                    </span>
                                    <p>ยังไม่มีโดเมนในระบบ{{ (request('q') || request('has_account')) ? 'ที่ตรงกับตัวกรอง' : '' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $domains->appends(request()->query())->links() }}</div>
    </div>

@endsection