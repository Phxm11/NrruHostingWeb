@extends('admin.layout')

@section('title', 'รายการคำขอ')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'รายการคำขอใช้บริการ')

@section('content')

    @if (session('success'))
        <div class="banner-success">{{ session('success') }}</div>
    @endif

    <div class="stat-row">
        <div class="stat-card">
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 2h6l1 3h4v17H4V5h4l1-3Z"/></svg>
            </div>
            <div>
                <div class="stat-number">{{ $serviceRequests->total() }}</div>
                <div class="stat-label">คำขอทั้งหมด</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="9"/></svg>
            </div>
            <div>
                <div class="stat-number">{{ $serviceRequests->where('status', 'submitted')->count() }}</div>
                <div class="stat-label">รอพิจารณา (หน้านี้)</div>
            </div>
        </div>
        <div class="stat-card">
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
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อ หรือรหัสบุคลากร"
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">สถานะทั้งหมด</option>
                    @foreach (['submitted' => 'รอพิจารณา', 'approved' => 'อนุมัติแล้ว', 'rejected' => 'ไม่อนุมัติ', 'expired' => 'หมดอายุ'] as $val => $label)
                        <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-soft w-100" type="submit">ค้นหา</button>
            </div>
        </form>

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
                        <th>บัญชีที่สร้างแล้ว</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($serviceRequests as $req)
                        @php
                            $avatarClasses = ['avatar-a', 'avatar-b', 'avatar-c'];
                            $avatarClass = $avatarClasses[ord(mb_substr($req->applicant->full_name, 0, 1)) % 3];
                            $initial = mb_substr($req->applicant->full_name, 0, 1);
                        @endphp
                        <tr>
                            <td>{{ $req->form_no }}</td>
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
                            <td>{{ $req->domains->pluck('domain_name')->join(', ') ?: '-' }}</td>
                            <td style="font-size:13px;">
                                {{ \Carbon\Carbon::parse($req->project_start_date)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($req->project_end_date)->format('d/m/Y') }}
                            </td>
                            <td><span class="pill pill-{{ $req->status }}">{{ $req->status }}</span></td>
                            <td>{{ $req->service_accounts_count }} บัญชี</td>
                            <td class="text-end">
                                <a href="{{ route('admin.accounts.create', $req->request_id) }}"
                                   class="btn btn-brand btn-sm">+ สร้างบัญชี</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">ยังไม่มีคำขอในระบบ</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $serviceRequests->links() }}</div>
    </div>

@endsection
