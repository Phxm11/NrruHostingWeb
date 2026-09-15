@extends('admin.layout')

@section('title', 'บัญชีผู้ใช้บริการ')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'บัญชีผู้ใช้บริการ')



@section('content')

    {{-- ============================================================
         Scoped styles. Class names are intentionally namespaced
         (avatar/av-*/type-tag/acc-table/...) so they don't collide
         with the global .pill / .avatar-circle / table.modern-table
         rules already defined in admin.layout.
    ============================================================ --}}


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
        <div class="list-heading"><div><h2>ทะเบียนบัญชีบริการ</h2><p>ตรวจสอบสถานะและวันหมดอายุก่อนจัดการบัญชี</p></div><span class="list-total">{{ number_format($accounts->total()) }} รายการ</span></div>

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
                <input aria-label="ค้นหาข้อมูล" type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหาชื่อผู้ใช้ หรือ username...">
                @if ($currentStatus) <input type="hidden" name="status" value="{{ $currentStatus }}"> @endif
                @if (request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
            <button type="submit" class="btn btn-outline-soft">ค้นหา</button>
            </form>

            <form method="GET" class="sort-wrap">
                เรียงตาม
                @if ($currentStatus) <input type="hidden" name="status" value="{{ $currentStatus }}"> @endif
                @if (request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                <select aria-label="เรียงลำดับบัญชี" name="sort" onchange="this.form.submit()">
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
            <button type="button" class="bulk-close" id="bulkClose" aria-label="ยกเลิกการเลือกทั้งหมด">✕</button>
        </div>

        <p class="table-hint"><x-admin.icon name="list" size="15" /> เลื่อนตารางในแนวนอนเพื่อดูข้อมูลครบทุกคอลัมน์</p>
        <div class="acc-table-wrap">
            <table class="acc-table">
                <thead>
                    <tr>
                        <th class="col-check"><input type="checkbox" id="checkAll" class="row-check" aria-label="เลือกบัญชีทั้งหมดในหน้านี้"></th>
                        <th scope="col">ผู้ขอใช้บริการ</th>
                        <th scope="col">สังกัด / หน่วยงาน</th>
                        <th scope="col">โดเมน / คำขอ</th>
                        <th scope="col">ประเภทบัญชี</th>
                        <th scope="col">สถานะ</th>
                        <th class="sortable {{ request('sort') === 'expire_soon' ? 'is-sorted' : '' }}">
                            <a href="{{ route('admin.accounts.index', array_filter(array_merge($qp, ['status' => $currentStatus, 'sort' => 'expire_soon']))) }}">วันหมดอายุ</a>
                        </th>
                        <th scope="col" class="text-end">จัดการ</th>
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
                            <td><input type="checkbox" class="row-check" aria-label="เลือกบัญชี {{ $acc->username }}"
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
                                            <a href="{{ route('admin.accounts.renew', $acc->account_id) }}">
                                                @include('admin.accounts.partials.renew-icon', ['name' => 'renew', 'size' => 14])
                                                ต่ออายุบริการ / ประวัติ
                                            </a>
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
                checkAll.checked = checked.length > 0 && checked.length === rowChecks().length;
                checkAll.indeterminate = checked.length > 0 && checked.length < rowChecks().length;
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
