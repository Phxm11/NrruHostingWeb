@extends('admin.layout')

@section('title', 'บัญชีผู้ใช้บริการ')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'บัญชีผู้ใช้บริการ')

@push('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

    {{-- ============================================================
         Scoped styles. Class names are intentionally namespaced
         (avatar/av-*/type-tag/acc-table/...) so they don't collide
         with the global .pill / .avatar-circle / table.modern-table
         rules already defined in admin.layout.
    ============================================================ --}}
    <style>
        .acc2-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 18px; flex-wrap: wrap; }
        .acc2-head p { margin: 2px 0 0; color: var(--ink-soft); font-size: 13.5px; }

        @keyframes rowIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

        /* ---------- segmented status filter ---------- */
        .filter-row { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; margin-bottom: 16px; }
        .segmented { display: inline-flex; background: var(--surface); border: 1px solid var(--line); border-radius: 10px; padding: 3px; gap: 2px; }
        .seg-btn {
            display: flex; align-items: center; gap: 7px; text-decoration: none;
            padding: 8px 14px; border-radius: 8px; font-size: 13.5px; font-weight: 500;
            color: var(--ink-soft); white-space: nowrap; transition: background .15s, color .15s;
        }
        .seg-btn .count {
            font-family: 'Kanit', sans-serif; font-size: 12px; font-weight: 600; padding: 1px 7px;
            border-radius: 999px; background: var(--moss-light); color: var(--ink-soft);
        }
        .seg-btn.is-active { background: var(--forest); color: #fff; }
        .seg-btn.is-active .count { background: rgba(255,255,255,.18); color: #fff; }
        .seg-btn:not(.is-active):hover { background: var(--moss-light); color: var(--ink); }

        .search-wrap { position: relative; flex: 1 1 220px; max-width: 320px; }
        .search-wrap svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--ink-soft); opacity: .6; pointer-events: none; }
        .search-wrap input {
            width: 100%; padding: 9px 12px 9px 36px; border-radius: 10px; border: 1px solid var(--line); font-size: 13.5px;
        }
        .search-wrap input:focus { outline: none; border-color: var(--moss); box-shadow: 0 0 0 3px var(--moss-light); }

        .sort-wrap { margin-left: auto; display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--ink-soft); }
        .sort-wrap select { border: 1px solid var(--line); border-radius: 9px; padding: 8px 10px; font-size: 13.5px; background: #fff; color: var(--ink); }

        .filter-clear { font-size: 13px; color: #888; text-decoration: none; }
        .filter-clear:hover { color: var(--rust); }

        /* ---------- bulk action bar ---------- */
        .bulk-bar {
            display: none; align-items: center; gap: 14px; background: var(--forest); color: #fff;
            padding: 11px 16px; border-radius: 10px; margin-bottom: 14px; font-size: 13.5px;
        }
        .bulk-bar.is-visible { display: flex; }
        .bulk-bar strong { font-family: 'Kanit', sans-serif; font-weight: 600; }
        .bulk-actions { display: flex; gap: 8px; margin-left: auto; }
        .bulk-btn {
            border: 1px solid rgba(255,255,255,.35); background: rgba(255,255,255,.08); color: #fff;
            padding: 7px 13px; border-radius: 8px; font-size: 13px; cursor: pointer; font-family: 'Sarabun', sans-serif;
        }
        .bulk-btn:hover { background: rgba(255,255,255,.18); }
        .bulk-btn.danger { border-color: rgba(214,120,95,.6); background: rgba(174,72,48,.35); }
        .bulk-close { background: none; border: none; color: rgba(255,255,255,.7); cursor: pointer; font-size: 18px; line-height: 1; }

        /* ---------- table ---------- */
        .acc-table-wrap { overflow-x: auto; }
        table.acc-table { border-collapse: separate; border-spacing: 0; width: 100%; }
        table.acc-table thead th {
            text-align: left; font-size: 12.5px; font-weight: 600; color: var(--ink-soft);
            padding: 12px 14px; border-bottom: 1px solid var(--line); white-space: nowrap;
        }
        table.acc-table thead th.sortable { cursor: pointer; }
        table.acc-table thead th.sortable a { color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
        table.acc-table thead th.sortable.is-sorted { color: var(--forest); }
        table.acc-table thead th.col-check { width: 34px; }
        table.acc-table tbody td { padding: 12px 14px; font-size: 14px; border-bottom: 1px solid #f0eee7; vertical-align: middle; }
        table.acc-table tbody tr { animation: rowIn .25s ease both; transition: background .12s; }
        table.acc-table tbody tr:hover { background: #fbfaf7; }
        table.acc-table tbody tr:last-child td { border-bottom: none; }
        table.acc-table tbody tr.is-selected { background: var(--moss-light); }
        /* rowIn's end-state keeps transform:translateY(0), which creates a stacking
           context per <tr> (transform != none, even at 0). Without this, an open
           dropdown menu paints *underneath* the next row instead of above it,
           because z-index only resolves within a row's own stacking context.
           Bump the whole row above its siblings while its menu is open. */
        table.acc-table tbody tr.row-menu-open { position: relative; z-index: 30; }

        .row-check { width: 17px; height: 17px; accent-color: var(--forest); cursor: pointer; }

        .identity { display: flex; align-items: center; gap: 11px; }
        .avatar {
            width: 36px; height: 36px; min-width: 36px; border-radius: 50%; display: flex; align-items: center;
            justify-content: center; font-family: 'Kanit', sans-serif; font-weight: 600; font-size: 14px; color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,.12);
        }
        .av-1 { background: linear-gradient(135deg, var(--forest), var(--forest-2)); }
        .av-2 { background: linear-gradient(135deg, var(--moss), #82a862); }
        .av-3 { background: linear-gradient(135deg, var(--amber-deep), var(--amber)); }
        .id-name { font-weight: 600; color: var(--ink); font-size: 14px; }
        .id-sub { font-size: 12px; color: var(--ink-soft); margin-top: 1px; }
        .id-sub code { background: none; padding: 0; color: inherit; }

        .org-line1 { color: var(--ink); font-size: 13.5px; }
        .org-line2 { color: var(--ink-soft); font-size: 12px; margin-top: 1px; }

        code.uname { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; background: var(--moss-light); padding: 3px 7px; border-radius: 6px; font-size: 12.5px; color: var(--forest); }

        .domain-cell { max-width: 220px; }
        .domain-chip { display: inline-flex; align-items: center; font-size: 12px; background: #ece9dc; color: var(--ink-soft); padding: 3px 8px; border-radius: 7px; margin: 1px 4px 1px 0; }
        .req-link { font-size: 12px; color: var(--moss); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; margin-top: 4px; }
        .req-link:hover { text-decoration: underline; }

        .type-tag { display: inline-block; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 7px; }
        .type-ssh { background: #e3efe7; color: #2f6b4a; }
        .type-database { background: var(--amber-light); color: var(--amber-deep); }
        .type-control_panel { background: #eef1da; color: #6a7a2c; }
        .type-ftp { background: #f4e8dd; color: #a1592f; }

        .status-cell { display: flex; align-items: center; gap: 8px; }
        .status-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .dot-active { background: var(--moss); }
        .dot-disabled { background: var(--ink-soft); }
        .dot-expired { background: var(--rust); }
        .status-label { font-weight: 600; font-size: 13px; }
        .status-active { color: var(--forest); }
        .status-disabled { color: var(--ink-soft); }
        .status-expired { color: var(--rust); }

        .expiry-main { font-size: 13.5px; color: var(--ink); }
        .expiry-soon { color: var(--rust); font-weight: 600; }
        .expiry-track { width: 64px; height: 4px; border-radius: 99px; background: #ece9dc; margin-top: 6px; overflow: hidden; }
        .expiry-fill { height: 100%; border-radius: 99px; background: var(--rust); }

        .row-actions { display: flex; align-items: center; justify-content: flex-end; gap: 4px; }
        .row-actions form { margin: 0; }
        .icon-btn {
            width: 30px; height: 30px; border-radius: 8px; border: 1px solid transparent; background: none;
            display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: var(--ink-soft);
            transition: background .12s, color .12s; text-decoration: none;
        }
        .icon-btn:hover, .icon-btn.menu-open { background: var(--moss-light); color: var(--forest); }

        .menu-wrap { position: relative; }
        .dropdown {
            position: absolute; right: 0; top: calc(100% + 4px); background: #fff; border: 1px solid var(--line);
            border-radius: 10px; box-shadow: var(--shadow-md); min-width: 172px; padding: 6px; z-index: 10; display: none;
        }
        .dropdown.is-open { display: block; }
        .dropdown button, .dropdown a {
            width: 100%; text-align: left; background: none; border: none; padding: 9px 10px; border-radius: 7px;
            font-size: 13px; color: var(--ink); display: flex; align-items: center; gap: 9px; cursor: pointer;
            font-family: 'Sarabun', sans-serif; text-decoration: none;
        }
        .dropdown button:hover, .dropdown a:hover { background: var(--moss-light); }
        .dropdown button.danger { color: var(--rust); }
        .dropdown button.danger:hover { background: var(--rust-light); }
        .dropdown hr { border: none; border-top: 1px solid #f0eee7; margin: 5px 2px; }

        /* ---------- empty state ---------- */
        .empty-state { text-align: center; padding: 56px 16px; color: var(--ink-soft); }
        .empty-state .empty-icon-wrap {
            display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px;
            border-radius: 50%; background: var(--moss-light); color: var(--forest); margin-bottom: 14px;
        }
        .empty-state p { margin: 0; font-size: 14px; }

        /* ---------- responsive: collapse table to cards ---------- */
        @media (max-width: 760px) {
            table.acc-table thead { display: none; }
            table.acc-table, table.acc-table tbody, table.acc-table tr, table.acc-table td { display: block; width: 100%; }
            table.acc-table tbody tr { border: 1px solid #eee; border-radius: 12px; margin-bottom: 10px; padding: 10px 12px; }
            table.acc-table tbody td { padding: 6px 0; border: none; }
            table.acc-table tbody td[data-label]::before {
                content: attr(data-label); display: block; font-size: 11px; text-transform: uppercase;
                letter-spacing: .03em; color: var(--ink-soft); margin-bottom: 2px;
            }
            table.acc-table td.text-end { text-align: left !important; }
            table.acc-table .row-actions { justify-content: flex-start; }
        }
    </style>

    <div class="acc2-head">
        <div>
            <p>บัญชีที่สร้างไปแล้วทั้งหมด {{ $statusCounts['all'] }} รายการ</p>
        </div>
        <a href="{{ route('admin.requests.index', ['status' => 'approved']) }}" class="btn btn-brand" style="display:inline-flex;align-items:center;gap:8px;text-decoration:none;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M12 5v14M5 12h14"/></svg>
            สร้างบัญชีจากคำขอที่อนุมัติแล้ว
        </a>
    </div>

    <div class="panel">

        {{-- ============================================================
             Filters: status as segmented links (real GET navigation,
             counts computed server-side in the controller) + search.
        ============================================================ --}}
        <div class="filter-row">
            <div class="segmented">
                @php
                    $currentStatus = request('status');
                    // base query params (search + sort) preserved across every filter link below.
                    // array_filter drops empty ones so URLs stay clean (no "?q=&sort=").
                    $qp = ['q' => request('q'), 'sort' => request('sort')];
                @endphp
                <a href="{{ route('admin.accounts.index', array_filter($qp)) }}" class="seg-btn {{ ! $currentStatus ? 'is-active' : '' }}">
                    ทั้งหมด <span class="count">{{ $statusCounts['all'] }}</span>
                </a>
                <a href="{{ route('admin.accounts.index', array_filter(array_merge($qp, ['status' => 'active']))) }}" class="seg-btn {{ $currentStatus === 'active' ? 'is-active' : '' }}">
                    ใช้งานอยู่ <span class="count">{{ $statusCounts['active'] }}</span>
                </a>
                <a href="{{ route('admin.accounts.index', array_filter(array_merge($qp, ['status' => 'disabled']))) }}" class="seg-btn {{ $currentStatus === 'disabled' ? 'is-active' : '' }}">
                    ระงับ <span class="count">{{ $statusCounts['disabled'] }}</span>
                </a>
                <a href="{{ route('admin.accounts.index', array_filter(array_merge($qp, ['status' => 'expired']))) }}" class="seg-btn {{ $currentStatus === 'expired' ? 'is-active' : '' }}">
                    หมดอายุ <span class="count">{{ $statusCounts['expired'] }}</span>
                </a>
            </div>

            <form method="GET" class="search-wrap" style="margin:0;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหาชื่อผู้ใช้ หรือ username...">
                @if ($currentStatus) <input type="hidden" name="status" value="{{ $currentStatus }}"> @endif
                @if (request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
            </form>

            <form method="GET" class="sort-wrap">
                เรียงตาม
                @if ($currentStatus) <input type="hidden" name="status" value="{{ $currentStatus }}"> @endif
                @if (request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                <select name="sort" onchange="this.form.submit()">
                    <option value="" {{ ! request('sort') ? 'selected' : '' }}>สร้างล่าสุด</option>
                    <option value="expire_soon" {{ request('sort') === 'expire_soon' ? 'selected' : '' }}>วันหมดอายุ (ใกล้สุดก่อน)</option>
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>ชื่อผู้ใช้บริการ (ก-ฮ)</option>
                </select>
            </form>

            @if (request('q') || $currentStatus || request('sort'))
                <a href="{{ route('admin.accounts.index') }}" class="filter-clear">ล้างตัวกรอง ✕</a>
            @endif
        </div>

        {{-- Bulk action bar — appears once a row is selected --}}
        <div class="bulk-bar" id="bulkBar">
            <strong id="bulkCount">0</strong> รายการที่เลือก
            <div class="bulk-actions">
                <button type="button" class="bulk-btn" data-act="enable">เปิดใช้งาน</button>
                <button type="button" class="bulk-btn" data-act="disable">ระงับ</button>
                <button type="button" class="bulk-btn danger" data-act="delete">ลบ</button>
            </div>
            <button type="button" class="bulk-close" id="bulkClose">✕</button>
        </div>

        <div class="acc-table-wrap">
            <table class="acc-table">
                <thead>
                    <tr>
                        <th class="col-check"><input type="checkbox" id="checkAll" class="row-check"></th>
                        <th>ผู้ขอใช้บริการ</th>
                        <th>สังกัด / หน่วยงาน</th>
                        <th>โดเมน / คำขอ</th>
                        <th>ประเภทบัญชี</th>
                        <th class="sortable {{ ! request('sort') ? 'is-sorted' : '' }}">สถานะ</th>
                        <th class="sortable {{ request('sort') === 'expire_soon' ? 'is-sorted' : '' }}">
                            <a href="{{ route('admin.accounts.index', array_filter(array_merge($qp, ['status' => $currentStatus, 'sort' => 'expire_soon']))) }}">วันหมดอายุ</a>
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accounts as $acc)
                        @php
                            $avatarClass = ['av-1', 'av-2', 'av-3'][ord(mb_substr($acc->applicant->full_name, 0, 1)) % 3];
                            $initial = mb_substr($acc->applicant->full_name, 0, 1);

                            $expireDate = $acc->expire_date ? \Carbon\Carbon::parse($acc->expire_date) : null;
                            $daysLeft = $expireDate ? (int) now()->startOfDay()->diffInDays($expireDate, false) : null;
                            $expiresSoon = $acc->status === 'active' && $daysLeft !== null && $daysLeft >= 0 && $daysLeft <= 14;

                            $typeLabels = ['ssh' => 'SSH', 'database' => 'ฐานข้อมูล', 'control_panel' => 'Control Panel', 'ftp' => 'FTP'];
                            $statusLabels = ['active' => 'ใช้งานอยู่', 'disabled' => 'ระงับ', 'expired' => 'หมดอายุ'];
                        @endphp
                        <tr>
                            <td><input type="checkbox" class="row-check"
                                data-status="{{ $acc->status }}"
                                data-toggle-url="{{ route('admin.accounts.toggle-status', $acc->account_id) }}"
                                data-destroy-url="{{ route('admin.accounts.destroy', $acc->account_id) }}"></td>
                            <td data-label="ผู้ขอใช้บริการ">
                                <div class="identity">
                                    <span class="avatar {{ $avatarClass }}">{{ $initial }}</span>
                                    <div>
                                        <div class="id-name">{{ $acc->applicant->full_name }}</div>
                                        <div class="id-sub"><code class="uname">{{ $acc->username }}</code> · {{ $acc->applicant->staff_or_student_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td data-label="สังกัด / หน่วยงาน">
                                <div class="org-line1">{{ $acc->applicant->affiliation ?: '-' }}</div>
                                <div class="org-line2">{{ $acc->applicant->unit_name ?: '-' }}</div>
                            </td>
                            <td data-label="โดเมน / คำขอ" class="domain-cell">
                                @forelse ($acc->serviceRequest->domains as $domain)
                                    <span class="domain-chip">{{ $domain->domain_name }}</span>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                                <div><a class="req-link" href="{{ route('admin.requests.show', $acc->request_id) }}">{{ $acc->serviceRequest->form_no }} ↗</a></div>
                            </td>
                            <td data-label="ประเภทบัญชี"><span class="type-tag type-{{ $acc->account_type }}">{{ $typeLabels[$acc->account_type] ?? $acc->account_type }}</span></td>
                            <td data-label="สถานะ">
                                <div class="status-cell">
                                    <span class="status-dot dot-{{ $acc->status }}"></span>
                                    <span class="status-label status-{{ $acc->status }}">{{ $statusLabels[$acc->status] ?? $acc->status }}</span>
                                </div>
                            </td>
                            <td data-label="วันหมดอายุ">
                                @if (! $expireDate)
                                    <span class="expiry-main">-</span>
                                @elseif ($acc->status === 'expired' || $daysLeft < 0)
                                    <span class="expiry-main expiry-soon">หมดอายุแล้ว {{ abs($daysLeft) }} วันก่อน</span>
                                @elseif ($expiresSoon)
                                    <div class="expiry-main expiry-soon">อีก {{ $daysLeft }} วัน</div>
                                    <div class="expiry-track"><div class="expiry-fill" style="width: {{ max(8, (14 - $daysLeft) / 14 * 100) }}%"></div></div>
                                @else
                                    <span class="expiry-main">{{ $expireDate->format('d/m/Y') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="row-actions">
                                    <a href="{{ route('admin.requests.show', $acc->request_id) }}" class="icon-btn" title="ดูคำขอ">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <div class="menu-wrap">
                                        <button type="button" class="icon-btn menu-btn" title="เพิ่มเติม">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="12" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="19" cy="12" r="1.6"/></svg>
                                        </button>
                                        <div class="dropdown">
                                            <a href="{{ route('admin.accounts.edit', $acc->account_id) }}">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                                แก้ไขบัญชี
                                            </a>
                                            <form action="{{ route('admin.accounts.toggle-status', $acc->account_id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit">
                                                    @if ($acc->status === 'active')
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                                        ระงับการใช้งาน
                                                    @else
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg>
                                                        เปิดใช้งาน
                                                    @endif
                                                </button>
                                            </form>
                                            <hr>
                                            <form action="{{ route('admin.accounts.destroy', $acc->account_id) }}" method="POST"
                                                  data-confirm="ยืนยันลบบัญชี {{ $acc->username }}? การลบไม่สามารถย้อนกลับได้">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="danger">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                                    ลบบัญชี
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
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

    <script>
        (function () {
            const tbody = document.querySelector('table.acc-table tbody');
            const checkAll = document.getElementById('checkAll');
            const bulkBar = document.getElementById('bulkBar');
            const bulkCount = document.getElementById('bulkCount');

            function rowChecks() {
                return Array.from(document.querySelectorAll('table.acc-table tbody .row-check'));
            }

            function updateBulkBar() {
                const checked = rowChecks().filter(cb => cb.checked);
                bulkCount.textContent = checked.length;
                bulkBar.classList.toggle('is-visible', checked.length > 0);
                rowChecks().forEach(cb => cb.closest('tr').classList.toggle('is-selected', cb.checked));
            }

            if (checkAll) {
                checkAll.addEventListener('change', function () {
                    rowChecks().forEach(cb => { cb.checked = checkAll.checked; });
                    updateBulkBar();
                });
            }

            if (tbody) {
                tbody.addEventListener('change', function (e) {
                    if (e.target.classList.contains('row-check')) updateBulkBar();
                });
            }

            document.getElementById('bulkClose').addEventListener('click', function () {
                rowChecks().forEach(cb => { cb.checked = false; });
                if (checkAll) checkAll.checked = false;
                updateBulkBar();
            });

            // dropdown open/close per row
            document.addEventListener('click', function (e) {
                const menuBtn = e.target.closest('.menu-btn');
                document.querySelectorAll('.dropdown.is-open').forEach(dd => {
                    if (!menuBtn || dd !== menuBtn.nextElementSibling) {
                        dd.classList.remove('is-open');
                        dd.previousElementSibling.classList.remove('menu-open');
                        const openRow = dd.closest('tr');
                        if (openRow) openRow.classList.remove('row-menu-open');
                    }
                });
                if (menuBtn) {
                    const dd = menuBtn.nextElementSibling;
                    const isOpen = dd.classList.toggle('is-open');
                    menuBtn.classList.toggle('menu-open', isOpen);
                    const row = menuBtn.closest('tr');
                    if (row) row.classList.toggle('row-menu-open', isOpen);
                }
            });

            // bulk actions — reuse the existing single-account routes (toggle-status / destroy)
            document.querySelectorAll('.bulk-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    bulkAction(btn.dataset.act);
                });
            });

            async function bulkAction(kind) {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const checked = rowChecks().filter(cb => cb.checked && cb !== checkAll);

                if (!checked.length) return;

                if (kind === 'delete') {
                    const ok = confirm('ยืนยันลบบัญชีที่เลือกไว้ ' + checked.length + ' รายการ? การลบไม่สามารถย้อนกลับได้');
                    if (!ok) return;
                }

                const targets = checked.filter(cb => {
                    if (kind === 'enable') return cb.dataset.status !== 'active';
                    if (kind === 'disable') return cb.dataset.status === 'active';
                    return true;
                });

                if (!targets.length) {
                    alert('ไม่มีรายการที่ต้องเปลี่ยนสถานะตามที่เลือก');
                    return;
                }

                for (const cb of targets) {
                    const url = kind === 'delete' ? cb.dataset.destroyUrl : cb.dataset.toggleUrl;
                    const method = kind === 'delete' ? 'DELETE' : 'PATCH';
                    try {
                        await fetch(url, {
                            method: method,
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                        });
                    } catch (err) {
                        console.error('bulk action failed for', url, err);
                    }
                }

                window.location.reload();
            }
        })();
    </script>

@endsection