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
        .stat-card--total  .stat-icon { background: linear-gradient(135deg, #e3efe7, #d3e6d8); color: var(--forest); }
        .stat-card--pending .stat-icon { background: linear-gradient(135deg, var(--amber-light), #f6dfa0); color: #8a6408; }
        .stat-card--pending .stat-number { color: #8a6408; }
        .stat-card--approved .stat-icon { background: linear-gradient(135deg, var(--moss-light), #cfe3b7); color: var(--forest); }
        .stat-card--approved .stat-number { color: var(--forest); }
        .stat-card--pending .stat-icon { animation: bob 2.4s ease-in-out infinite; }

        /* ---------- filters: segmented status + search ---------- */
        .filter-row { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; margin-bottom: 18px; }
        .segmented { display: inline-flex; background: var(--surface); border: 1px solid var(--line); border-radius: 10px; padding: 3px; gap: 2px; flex-wrap: wrap; }
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

        .filter-clear { font-size: 13px; color: #888; text-decoration: none; }
        .filter-clear:hover { color: var(--rust); }

        /* ---------- table polish ---------- */
        table.modern-table tbody tr { animation: rowIn .3s ease both; }
        /* rowIn's end-state keeps transform:translateY(0), which creates a stacking
           context per <tr> (transform != none, even at 0). Without this, an open
           dropdown menu paints *underneath* the next row instead of above it,
           because z-index only resolves within a row's own stacking context.
           Bump the whole row above its siblings while its menu is open. */
        table.modern-table tbody tr.row-menu-open { position: relative; z-index: 30; }

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

        /* view-details button */
        .btn-view {
            display: inline-flex; align-items: center; gap: 6px;
            border: 1px solid var(--line); background: #fff; color: var(--forest);
            border-radius: 9px; font-size: 13.5px; padding: 8px 14px; font-weight: 500;
            transition: background .15s ease, border-color .15s ease, transform .15s ease;
        }
        .btn-view:hover { background: var(--moss-light); border-color: var(--moss); color: var(--forest); transform: translateY(-1px); }

        /* row + applicant link polish for easier navigation */
        table.modern-table tbody tr.request-row { cursor: pointer; }
        .applicant-link { color: inherit; display: block; }
        .applicant-link:hover .applicant-link__name { color: var(--forest); text-decoration: underline; }
        code.form-no-link { transition: color .15s ease; }
        a.form-no-link:hover code { color: var(--forest); text-decoration: underline; }

        /* approve button */
        .btn-approve {
            display: inline-flex; align-items: center; gap: 6px;
            background: linear-gradient(135deg, var(--moss), #4f7a3a);
            border: none; color: #fff; border-radius: 9px; font-size: 13.5px; font-weight: 600;
            padding: 8px 14px; transition: filter .15s ease, transform .15s ease, box-shadow .15s ease;
        }
        .btn-approve:hover { filter: brightness(1.06); color: #fff; transform: translateY(-1px); box-shadow: var(--shadow-sm); }

        /* ---------- row actions: main buttons + "···" dropdown ---------- */
        .row-actions { display: flex; align-items: center; justify-content: flex-end; gap: 6px; flex-wrap: nowrap; }
        .row-actions form { margin: 0; }
        .icon-btn {
            width: 34px; height: 34px; border-radius: 9px; border: 1px solid var(--line); background: #fff;
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
            display: inline-flex; align-items: center; justify-content: center;
            width: 64px; height: 64px; border-radius: 50%; background: var(--moss-light); color: var(--forest);
            margin-bottom: 14px; animation: bob 2.4s ease-in-out infinite;
        }
        .empty-state p { margin: 0; font-size: 14px; }
    </style>

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

        {{-- ============================================================
             Filters: status as segmented links (real GET navigation,
             counts computed server-side in the controller) + search.
        ============================================================ --}}
        <div class="filter-row">
            <div class="segmented">
                @php
                    $currentStatus = request('status');
                    // base query params (search) preserved across every filter link below.
                    $qp = ['search' => request('search')];
                @endphp
                <a href="{{ route('admin.requests.index', array_filter($qp)) }}" class="seg-btn {{ ! $currentStatus ? 'is-active' : '' }}">
                    ทั้งหมด <span class="count">{{ $statusCounts['all'] }}</span>
                </a>
                <a href="{{ route('admin.requests.index', array_filter(array_merge($qp, ['status' => 'submitted']))) }}" class="seg-btn {{ $currentStatus === 'submitted' ? 'is-active' : '' }}">
                    รอพิจารณา <span class="count">{{ $statusCounts['submitted'] }}</span>
                </a>
                <a href="{{ route('admin.requests.index', array_filter(array_merge($qp, ['status' => 'approved']))) }}" class="seg-btn {{ $currentStatus === 'approved' ? 'is-active' : '' }}">
                    อนุมัติแล้ว <span class="count">{{ $statusCounts['approved'] }}</span>
                </a>
                <a href="{{ route('admin.requests.index', array_filter(array_merge($qp, ['status' => 'rejected']))) }}" class="seg-btn {{ $currentStatus === 'rejected' ? 'is-active' : '' }}">
                    ไม่อนุมัติ <span class="count">{{ $statusCounts['rejected'] }}</span>
                </a>
                <a href="{{ route('admin.requests.index', array_filter(array_merge($qp, ['status' => 'expired']))) }}" class="seg-btn {{ $currentStatus === 'expired' ? 'is-active' : '' }}">
                    หมดอายุ <span class="count">{{ $statusCounts['expired'] }}</span>
                </a>
            </div>

            <form method="GET" class="search-wrap">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="search" placeholder="ค้นหาชื่อ หรือรหัสบุคลากร" value="{{ request('search') }}">
                @if ($currentStatus) <input type="hidden" name="status" value="{{ $currentStatus }}"> @endif
            </form>

            @if (request('search') || $currentStatus)
                <a href="{{ route('admin.requests.index') }}" class="filter-clear">ล้างตัวกรอง ✕</a>
            @endif
        </div>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>เลขที่คำขอ</th>
                        <th>ผู้ขอใช้บริการ</th>
                        <th>หน่วยงาน</th>
                        <th>โดเมน</th>
                        <th>ระยะเวลาโครงการ</th>
                        <th>สถานะ</th>
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
                        <tr class="request-row" data-href="{{ route('admin.requests.show', $req->request_id) }}">
                            <td>
                                <a href="{{ route('admin.requests.show', $req->request_id) }}" class="form-no-link" title="ดูรายละเอียดคำขอ">
                                    <code>{{ $req->form_no }}</code>
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.requests.show', $req->request_id) }}" class="applicant-link" title="ดูรายละเอียดคำขอ">
                                    <div class="d-flex align-items-center">
                                        <span class="avatar-circle {{ $avatarClass }}">{{ $initial }}</span>
                                        <div>
                                            <span class="applicant-link__name">{{ $req->applicant->full_name }}</span><br>
                                            <span class="text-muted" style="font-size:12px;">{{ $req->applicant->staff_or_student_id }}</span>
                                        </div>
                                    </div>
                                </a>
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
                            <td class="text-end">
                                <div class="row-actions">
                                    <a href="{{ route('admin.requests.show', $req->request_id) }}" class="btn-view" title="ดูรายละเอียดคำขอ">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        ดูรายละเอียด
                                    </a>
                                    @if ($req->status !== 'approved')
                                        <form action="{{ route('admin.requests.approve', $req->request_id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-approve" title="อนุมัติคำขอ">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg>
                                                อนุมัติ
                                            </button>
                                        </form>
                                    @endif
                                    <div class="menu-wrap">
                                        <button type="button" class="icon-btn menu-btn" title="เพิ่มเติม">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="12" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="19" cy="12" r="1.6"/></svg>
                                        </button>
                                        <div class="dropdown">
                                            <a href="{{ route('admin.accounts.create', $req->request_id) }}">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
                                                สร้างบัญชี
                                            </a>
                                            <hr>
                                            <form action="{{ route('admin.requests.destroy', $req->request_id) }}" method="POST"
                                                  data-confirm="ยืนยันลบคำขอ {{ $req->form_no }}?{{ $req->service_accounts_count > 0 ? ' คำขอนี้มีบัญชีที่สร้างแล้ว ' . $req->service_accounts_count . ' บัญชี ซึ่งจะถูกลบด้วย' : '' }} การลบไม่สามารถย้อนกลับได้">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="danger">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                                    ลบคำขอ
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <span class="empty-icon-wrap">
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M9 2h6l1 3h4v17H4V5h4l1-3Z"/><path d="M9 12h6M9 16h6"/></svg>
                                    </span>
                                    <p>ยังไม่มีคำขอที่รอสร้างบัญชี{{ (request('search') || request('status')) ? 'ที่ตรงกับตัวกรอง' : '' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $serviceRequests->appends(request()->query())->links() }}</div>
    </div>

    <script>
        // ทำให้คลิกที่แถวไหนก็ได้ (ยกเว้นปุ่ม/ลิงก์/ฟอร์ม/เมนู "···" ด้านใน) เพื่อเปิดหน้ารายละเอียดคำขอ
        document.querySelectorAll('tr.request-row[data-href]').forEach(function (row) {
            row.addEventListener('click', function (e) {
                if (e.target.closest('a, button, form, input, select, textarea')) {
                    return;
                }
                window.location = row.dataset.href;
            });
        });

        // เมนู "···" ต่อแถว — เปิด/ปิดแบบเดียวกับหน้าบัญชี
        document.addEventListener('click', function (e) {
            const menuBtn = e.target.closest('.menu-btn');
            document.querySelectorAll('.dropdown.is-open').forEach(function (dd) {
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
    </script>

@endsection