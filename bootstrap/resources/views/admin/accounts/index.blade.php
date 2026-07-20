<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>บัญชีผู้ใช้บริการ — สำนักคอมพิวเตอร์</title>
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
        .credential-box {
            background: #fff8e1; border: 1px solid #f0c14b; border-radius: 8px;
            padding: 16px 20px; font-size: 14px; margin-bottom: 20px;
        }
        .credential-box code { font-size: 15px; font-weight: 600; }
    </style>
</head>
<body>

<div class="page-header">
    <div class="container">
        <h1 class="mb-2">ระบบจัดการคำขอใช้บริการ Data Center / Web Hosting</h1>
        <div class="nav-tabs-custom">
            <a href="{{ url('/admin/requests') }}">รายการคำขอ</a>
            <a href="{{ url('/admin/accounts') }}" class="active">บัญชีผู้ใช้บริการ</a>
        </div>
    </div>
</div>

<div class="container py-4">

    @if (session('new_username'))
        <div class="credential-box">
            <strong>สร้างบัญชีสำเร็จ — กรุณาคัดลอกและส่งให้ผู้ใช้บริการทันที (จะไม่แสดงรหัสผ่านนี้อีก)</strong><br>
            Username: <code>{{ session('new_username') }}</code> &nbsp;|&nbsp;
            Password: <code>{{ session('new_password') }}</code>
        </div>
    @elseif (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="content-card">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">สถานะทั้งหมด</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>ใช้งานอยู่</option>
                    <option value="disabled" {{ request('status') == 'disabled' ? 'selected' : '' }}>ระงับการใช้งาน</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>หมดอายุ</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" type="submit">กรอง</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>ผู้ขอใช้บริการ</th>
                        <th>โดเมน/คำขอ</th>
                        <th>ประเภทบัญชี</th>
                        <th>สถานะ</th>
                        <th>วันหมดอายุ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accounts as $acc)
                        <tr>
                            <td><code>{{ $acc->username }}</code></td>
                            <td>
                                {{ $acc->applicant->full_name }}<br>
                                <span class="text-muted" style="font-size:12px;">{{ $acc->applicant->staff_or_student_id }}</span>
                            </td>
                            <td>{{ $acc->serviceRequest->form_no }} — {{ $acc->serviceRequest->domains->pluck('domain_name')->join(', ') ?: '-' }}</td>
                            <td>{{ $acc->account_type }}</td>
                            <td>
                                @php
                                    $color = ['active' => 'success', 'disabled' => 'secondary', 'expired' => 'danger'][$acc->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }} badge-status">{{ $acc->status }}</span>
                            </td>
                            <td style="font-size:13px;">
                                {{ $acc->expire_date ? \Carbon\Carbon::parse($acc->expire_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-end">
                                <form action="{{ url('/admin/accounts/' . $acc->account_id . '/toggle-status') }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                        {{ $acc->status === 'active' ? 'ระงับการใช้งาน' : 'เปิดใช้งาน' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">ยังไม่มีบัญชีในระบบ</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $accounts->links() }}
    </div>
</div>

</body>
</html>
