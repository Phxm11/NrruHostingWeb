<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>สร้างบัญชีผู้ใช้บริการ — สำนักคอมพิวเตอร์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --accent: #185fa5; --accent-dark: #0c447c; }
        body { font-family: 'Sarabun', sans-serif; background: #f1eee7; color: #2c2c2a; }
        .page-header { background: var(--accent-dark); color: #fff; padding: 24px 0; }
        .page-header h1 { font-size: 19px; font-weight: 600; margin: 0; }
        .form-card { background: #fff; border-radius: 12px; padding: 26px 30px; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .info-box { background: #eef4fa; border-radius: 8px; padding: 14px 18px; font-size: 14px; margin-bottom: 22px; }
        .info-box dt { color: #5f5e5a; font-weight: 500; font-size: 12.5px; }
        .info-box dd { margin-bottom: 8px; }
        label.form-label { font-weight: 500; font-size: 14px; }
        .btn-primary { background: var(--accent); border-color: var(--accent); }
        .btn-primary:hover { background: var(--accent-dark); border-color: var(--accent-dark); }
        .helper-text { font-size: 12.5px; color: #5f5e5a; }
    </style>
</head>
<body>

<div class="page-header">
    <div class="container">
        <h1>สร้างบัญชี Username / Password สำหรับผู้ขอใช้บริการ</h1>
    </div>
</div>

<div class="container py-4" style="max-width: 700px;">

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <div class="info-box">
            <dl class="row mb-0">
                <dt class="col-4">ผู้ขอใช้บริการ</dt>
                <dd class="col-8">{{ $serviceRequest->applicant->full_name }} ({{ $serviceRequest->applicant->staff_or_student_id }})</dd>
                <dt class="col-4">หน่วยงาน</dt>
                <dd class="col-8">{{ $serviceRequest->applicant->unit_name }}</dd>
                <dt class="col-4">เลขที่คำขอ</dt>
                <dd class="col-8">{{ $serviceRequest->form_no }}</dd>
                <dt class="col-4">โดเมน</dt>
                <dd class="col-8">{{ $serviceRequest->domains->pluck('domain_name')->join(', ') ?: '-' }}</dd>
                <dt class="col-4">ระยะเวลาโครงการ</dt>
                <dd class="col-8 mb-0">
                    {{ \Carbon\Carbon::parse($serviceRequest->project_start_date)->format('d/m/Y') }} —
                    {{ \Carbon\Carbon::parse($serviceRequest->project_end_date)->format('d/m/Y') }}
                </dd>
            </dl>
        </div>

        @if ($serviceRequest->serviceAccounts->isNotEmpty())
            <div class="alert alert-warning py-2" style="font-size: 13.5px;">
                คำขอนี้มีบัญชีที่สร้างไว้แล้ว {{ $serviceRequest->serviceAccounts->count() }} บัญชี
                ({{ $serviceRequest->serviceAccounts->pluck('username')->join(', ') }}) — สามารถสร้างเพิ่มได้หากต้องการ
            </div>
        @endif

        <form action="{{ route('admin.accounts.store', $serviceRequest->request_id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="{{ old('username', $suggestedUsername) }}" required>
                <div class="helper-text">แนะนำจากรหัสบุคลากร แก้ไขได้ตามต้องการ (ใช้ตัวอักษร ตัวเลข - หรือ _ เท่านั้น)</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="text" name="password" id="passwordField" class="form-control"
                           value="{{ old('password', $suggestedPassword) }}" required minlength="8">
                    <button type="button" class="btn btn-outline-secondary" onclick="regeneratePassword()">สุ่มใหม่</button>
                </div>
                <div class="helper-text">ระบบสุ่มรหัสผ่านให้อัตโนมัติ กรุณาคัดลอกเก็บไว้ก่อนกดบันทึก เพราะจะแสดงอีกครั้งเดียวหลังบันทึกสำเร็จ</div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">ประเภทบัญชี</label>
                    <select name="account_type" class="form-select" required>
                        <option value="control_panel">Control Panel</option>
                        <option value="ssh">SSH</option>
                        <option value="database">Database</option>
                        <option value="ftp">FTP</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">วันหมดอายุบัญชี</label>
                    <input type="date" name="expire_date" class="form-control"
                           value="{{ old('expire_date', $serviceRequest->project_end_date) }}">
                    <div class="helper-text">ค่าเริ่มต้นตามวันสิ้นสุดโครงการ</div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">ผู้สร้างบัญชี (เจ้าหน้าที่)</label>
                <input type="text" name="created_by" class="form-control" value="{{ old('created_by') }}" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.requests.index') }}" class="btn btn-outline-secondary">ย้อนกลับ</a>
                <button type="submit" class="btn btn-primary px-4">บันทึกและสร้างบัญชี</button>
            </div>
        </form>
    </div>
</div>

<script>
    function regeneratePassword() {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
        let pass = '';
        const array = new Uint32Array(12);
        crypto.getRandomValues(array);
        for (let i = 0; i < 12; i++) {
            pass += chars[array[i] % chars.length];
        }
        document.getElementById('passwordField').value = pass;
    }
</script>
</body>
</html>
