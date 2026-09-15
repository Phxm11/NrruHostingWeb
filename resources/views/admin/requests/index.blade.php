@extends('admin.layout')

@section('title', 'รายการคำขอ')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'รายการคำขอใช้บริการ')

@section('content')


    <div class="stat-row">
        <div class="stat-card stat-card--total">
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 2h6l1 3h4v17H4V5h4l1-3Z"/></svg>
            </div>
            <div>
                <div class="stat-number">{{ $statusCounts['all'] }}</div>
                <div class="stat-label">คำขอทั้งหมด</div>
            </div>
        </div>
        <div class="stat-card stat-card--pending">
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="9"/></svg>
            </div>
            <div>
                <div class="stat-number">{{ $statusCounts['submitted'] }}</div>
                <div class="stat-label">รอพิจารณา</div>
            </div>
        </div>
        <div class="stat-card stat-card--approved">
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <div>
                <div class="stat-number">{{ $statusCounts['approved'] }}</div>
                <div class="stat-label">อนุมัติแล้ว</div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="list-heading"><div><h2>รายการคำขอ</h2><p>ตรวจสอบข้อมูลและเลือกดำเนินการแต่ละคำขอ</p></div><span class="list-total">{{ number_format($serviceRequests->total()) }} รายการ</span></div>

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
                <input aria-label="ค้นหาข้อมูล" type="text" name="search" placeholder="ค้นหาชื่อ หรือรหัสบุคลากร" value="{{ request('search') }}">
                @if ($currentStatus) <input type="hidden" name="status" value="{{ $currentStatus }}"> @endif
            <button type="submit" class="btn btn-outline-soft">ค้นหา</button>
            </form>

            @if (request('search') || $currentStatus)
                <a href="{{ route('admin.requests.index') }}" class="filter-clear">ล้างตัวกรอง ✕</a>
            @endif
        </div>

        <p class="table-hint"><x-admin.icon name="list" size="15" /> เลื่อนตารางในแนวนอนเพื่อดูข้อมูลครบทุกคอลัมน์</p>
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th scope="col">เลขที่คำขอ</th>
                        <th scope="col">ผู้ขอใช้บริการ</th>
                        <th scope="col">หน่วยงาน</th>
                        <th scope="col">โดเมน</th>
                        <th scope="col">ระยะเวลาโครงการ</th>
                        <th scope="col">สถานะ</th>
                        <th scope="col" class="text-end">จัดการ</th>
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
                            <td class="cell-unit">{{ $req->applicant->unit_name }}</td>
                            <td>
                                @forelse ($domains as $d)
                                    <span class="domain-chip">{{ $d }}</span>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </td>
                            <td class="cell-date">
                                {{ \Carbon\Carbon::parse($req->project_start_date)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($req->project_end_date)->format('d/m/Y') }}
                            </td>
                            <td>
                                <x-admin.status :status="$req->status" />
                            </td>
                            <td class="text-end">
                                <div class="row-actions">
                                    <a href="{{ route('admin.requests.show', $req->request_id) }}" class="btn-view" title="ดูรายละเอียดคำขอ">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        ดูรายละเอียด
                                    </a>
                                    @if ($req->status === 'submitted')
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
    </script>

@endsection