@extends('admin.layout')

@section('title', 'โดเมนผู้ใช้บริการ')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'โดเมนผู้ใช้บริการ')

@section('content')

    <style>
        .dm-stats { display: flex; flex-wrap: wrap; gap: 14px; margin-bottom: 20px; }
        .dm-stat {
            flex: 1; min-width: 160px;
            background: #fff; border: 1px solid var(--line); border-radius: var(--radius-md);
            padding: 16px 18px; display: flex; align-items: center; gap: 12px;
        }
        .dm-stat__icon {
            width: 40px; height: 40px; border-radius: 11px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .dm-stat--total  .dm-stat__icon { background: #e3efe7; color: #2f6b4a; }
        .dm-stat--linked .dm-stat__icon { background: var(--moss-light); color: var(--forest); }
        .dm-stat--pending .dm-stat__icon { background: #fdf1cf; color: #8a6408; }
        .dm-stat__num { font-family: 'Kanit', sans-serif; font-size: 21px; font-weight: 700; line-height: 1.2; }
        .dm-stat__label { font-size: 12.5px; color: var(--ink-soft); }

        .dm-toolbar {
            display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 18px;
        }
        .dm-toolbar input[type="text"], .dm-toolbar select {
            border: 1px solid var(--line); border-radius: var(--radius-sm);
            padding: 10px 14px; font-size: 14px; font-family: inherit;
        }
        .dm-toolbar input[type="text"] { flex: 1 1 260px; min-width: 220px; }
        .dm-toolbar .dm-clear { font-size: 13px; color: var(--ink-soft); }
        .dm-toolbar .dm-clear:hover { color: var(--rust); }

        .dm-table { width: 100%; border-collapse: collapse; }
        .dm-table th {
            text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .04em;
            color: var(--ink-soft); font-weight: 700; padding: 12px 14px; border-bottom: 2px solid var(--line);
        }
        .dm-table td { padding: 15px 14px; font-size: 14px; border-bottom: 1px solid var(--line); vertical-align: middle; }
        .dm-table tbody tr:hover { background: var(--moss-light); }
        .dm-table tbody tr:last-child td { border-bottom: none; }

        .dm-domain { display: flex; align-items: center; gap: 10px; }
        .dm-domain__icon {
            width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: #e3efe7; color: #2f6b4a;
        }

        .dm-badge {
            display: inline-block; font-size: 12px; font-weight: 600; padding: 4px 9px;
            border-radius: 7px; background: var(--moss-light); color: var(--forest);
        }
        .dm-badge + .dm-badge { margin-left: 4px; margin-top: 4px; }
        .dm-badge--none { background: #fdf1cf; color: #8a6408; }
        .dm-badge--disabled { background: #eeeadc; color: var(--ink-soft); }
        .dm-badge--expired { background: var(--rust-light); color: var(--rust); }

        .dm-actions { display: flex; gap: 6px; justify-content: flex-end; flex-wrap: wrap; }
        .dm-actions form { margin: 0; }
        .dm-btn {
            display: inline-flex; align-items: center; gap: 5px;
            border: 1px solid var(--line); background: #fff; border-radius: 8px;
            padding: 7px 12px; font-size: 13px; font-weight: 600; color: var(--ink);
            cursor: pointer; text-decoration: none;
        }
        .dm-btn:hover { background: var(--moss-light); border-color: var(--moss); color: var(--forest); }
        .dm-btn--danger:hover { background: var(--rust-light); border-color: var(--rust); color: var(--rust); }

        .dm-empty { text-align: center; padding: 50px 16px; color: var(--ink-soft); }

        @media (max-width: 760px) {
            .dm-table thead { display: none; }
            .dm-table, .dm-table tbody, .dm-table tr, .dm-table td { display: block; width: 100%; }
            .dm-table tbody tr { border: 1px solid var(--line); border-radius: 12px; margin-bottom: 12px; padding: 8px 14px; }
            .dm-table tbody td { border: none; padding: 8px 0; }
            .dm-table tbody td[data-label]::before {
                content: attr(data-label); display: block; font-size: 11px; color: #999;
                text-transform: uppercase; margin-bottom: 2px;
            }
            .dm-actions { justify-content: flex-start; }
        }
    </style>

    <div class="dm-stats">
        <div class="dm-stat dm-stat--total">
            <span class="dm-stat__icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg>
            </span>
            <div>
                <div class="dm-stat__num">{{ $domains->total() }}</div>
                <div class="dm-stat__label">โดเมนทั้งหมด</div>
            </div>
        </div>
        <div class="dm-stat dm-stat--linked">
            <span class="dm-stat__icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg>
            </span>
            <div>
                <div class="dm-stat__num">{{ $domains->filter(fn($d) => $d->serviceRequest->serviceAccounts->isNotEmpty())->count() }}</div>
                <div class="dm-stat__label">มีบัญชีแล้ว (หน้านี้)</div>
            </div>
        </div>
        <div class="dm-stat dm-stat--pending">
            <span class="dm-stat__icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
            </span>
            <div>
                <div class="dm-stat__num">{{ $domains->filter(fn($d) => $d->serviceRequest->serviceAccounts->isEmpty())->count() }}</div>
                <div class="dm-stat__label">รอสร้างบัญชี (หน้านี้)</div>
            </div>
        </div>
    </div>

    <div class="panel">

        <form method="GET" class="dm-toolbar">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหาชื่อโดเมน, ชื่อผู้ใช้ หรือ username...">

            <select name="has_account" onchange="this.form.submit()">
                <option value="">ทั้งหมด</option>
                <option value="yes" {{ request('has_account') == 'yes' ? 'selected' : '' }}>มีบัญชีแล้ว</option>
                <option value="no"  {{ request('has_account') == 'no'  ? 'selected' : '' }}>ยังไม่มีบัญชี</option>
            </select>

            <button class="btn btn-outline-soft" type="submit">ค้นหา</button>

            @if (request('q') || request('has_account'))
                <a href="{{ route('admin.domains.index') }}" class="dm-clear">ล้างตัวกรอง ✕</a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="dm-table">
                <thead>
                    <tr>
                        <th>ชื่อโดเมน</th>
                        <th>หน่วยงาน</th>
                        <th>ผู้ขอใช้บริการ</th>
                        <th>บัญชี (username)</th>
                        <th>คำขอ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($domains as $domain)
                        @php $accounts = $domain->serviceRequest->serviceAccounts; @endphp
                        <tr>
                            <td data-label="ชื่อโดเมน">
                                <div class="dm-domain">
                                    <span class="dm-domain__icon">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg>
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
                                @forelse ($accounts as $acc)
                                    <span class="dm-badge {{ $acc->status === 'disabled' ? 'dm-badge--disabled' : ($acc->status === 'expired' ? 'dm-badge--expired' : '') }}">
                                        {{ $acc->username }}
                                    </span>
                                @empty
                                    <span class="dm-badge dm-badge--none">ยังไม่มีบัญชี</span>
                                @endforelse
                            </td>
                            <td data-label="คำขอ">
                                {{ $domain->serviceRequest->form_no }}
                            </td>
                            <td data-label="">
                                <div class="dm-actions">
                                    <a href="{{ route('admin.domains.show', $domain->domain_id) }}" class="dm-btn">ดู</a>
                                    <a href="{{ route('admin.domains.edit', $domain->domain_id) }}" class="dm-btn">แก้ไข</a>
                                    <form action="{{ route('admin.domains.destroy', $domain->domain_id) }}" method="POST"
                                          data-confirm="ยืนยันลบโดเมน {{ $domain->domain_name }}? การลบไม่สามารถย้อนกลับได้">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dm-btn dm-btn--danger">ลบ</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="dm-empty">
                                    ยังไม่มีโดเมนในระบบ{{ (request('q') || request('has_account')) ? 'ที่ตรงกับตัวกรอง' : '' }}
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