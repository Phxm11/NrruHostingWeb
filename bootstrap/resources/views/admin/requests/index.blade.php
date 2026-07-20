<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>รายการคำขอใช้บริการ — สำนักคอมพิวเตอร์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --accent: #185fa5; --accent-dark: #0c447c; }
        body { font-family: 'Sarabun', sans-serif; background: #f1eee7; color: #2c2c2a; }
        .page-header { background: var(--accent-dark); color: #fff; padding: 24px 0; }
        .page-header h1 { font-size: 19px; font-weight: 600; margin: 0; }
        .nav-tabs-custom a { color: #fff; opacity: .75; text-decoration: none; margin-right: 20px; font-size: 14px; }
        .nav-tabs-custom a.active { opacity: 1; border-bottom: 2px solid #fff; padding-bottom: 6px; font-weight: 600; }
        .content-card { background: #fff; border-radius: 12px; padding: 20px 24px; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        table th { font-size: 13px; color: #5f5e5a; font-weight: 600; }
        table td { font-size: 14px; vertical-align: middle; }
        .badge-status { font-size: 12px; padding: 5px 10px; border-radius: 20px; }
        .btn-primary { background: var(--accent); border-color: var(--accent); }
        .btn-primary:hover { background: var(--accent-dark); border-color: var(--accent-dark); }
    </style>
</head>
<body>

<div class="page-header">
    <div class="container">
        <h1 class="mb-2">ระบบจัดการคำขอใช้บริการ Data Center / Web Hosting</h1>
        <div class="nav-tabs-custom">
            <a href="{{ route('admin.requests.index') }}" class="active">รายการคำขอ</a>
            <a href="{{ route('admin.accounts.index') }}">บัญชีผู้ใช้บริการ</a>
        </div>
    </div>
</div>

<div class="container py-4">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="content-card">
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
                <button class="btn btn-outline-secondary w-100" type="submit">ค้นหา</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
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
                        <tr>
                            <td>{{ $req->form_no }}</td>
                            <td>
                                {{ $req->applicant->full_name }}<br>
                                <span class="text-muted" style="font-size:12px;">{{ $req->applicant->staff_or_student_id }}</span>
                            </td>
                            <td>{{ $req->applicant->unit_name }}</td>
                            <td>{{ $req->domains->pluck('domain_name')->join(', ') ?: '-' }}</td>
                            <td style="font-size:13px;">
                                {{ \Carbon\Carbon::parse($req->project_start_date)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($req->project_end_date)->format('d/m/Y') }}
                            </td>
                            <td>
                                @php
                                    $statusColor = [
                                        'submitted' => 'warning', 'approved' => 'success',
                                        'rejected' => 'danger', 'expired' => 'secondary', 'draft' => 'light',
                                    ][$req->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusColor }} badge-status">{{ $req->status }}</span>
                            </td>
                            <td>{{ $req->service_accounts_count }} บัญชี</td>
                            <td class="text-end">
                                <a href="{{ route('admin.accounts.create', $req->request_id) }}"
                                   class="btn btn-sm btn-primary">+ สร้างบัญชี</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">ยังไม่มีคำขอในระบบ</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $serviceRequests->links() }}
    </div>
</div>

</body>
</html>
