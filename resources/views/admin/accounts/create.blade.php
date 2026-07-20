@extends('admin.layout')

@section('title', 'สร้างบัญชีผู้ใช้บริการ')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'สร้างบัญชี Username / Password')

@section('content')

    @if ($errors->any())
        <div class="banner-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="panel mb-3" style="background: linear-gradient(120deg, var(--moss-light), #fff);">
                <dl class="row mb-0">
                    <dt class="col-4" style="font-size:12.5px;color:var(--ink-soft);">ผู้ขอใช้บริการ</dt>
                    <dd class="col-8">{{ $serviceRequest->applicant->full_name }} ({{ $serviceRequest->applicant->staff_or_student_id }})</dd>
                    <dt class="col-4" style="font-size:12.5px;color:var(--ink-soft);">หน่วยงาน</dt>
                    <dd class="col-8">{{ $serviceRequest->applicant->unit_name }}</dd>
                    <dt class="col-4" style="font-size:12.5px;color:var(--ink-soft);">เลขที่คำขอ</dt>
                    <dd class="col-8">{{ $serviceRequest->form_no }}</dd>
                    <dt class="col-4" style="font-size:12.5px;color:var(--ink-soft);">โดเมน</dt>
                    <dd class="col-8">{{ $serviceRequest->domains->pluck('domain_name')->join(', ') ?: '-' }}</dd>
                    <dt class="col-4" style="font-size:12.5px;color:var(--ink-soft);">ระยะเวลาโครงการ</dt>
                    <dd class="col-8 mb-0">
                        {{ \Carbon\Carbon::parse($serviceRequest->project_start_date)->format('d/m/Y') }} —
                        {{ \Carbon\Carbon::parse($serviceRequest->project_end_date)->format('d/m/Y') }}
                    </dd>
                </dl>
            </div>

            @if ($serviceRequest->serviceAccounts->isNotEmpty())
                <div class="banner-success" style="background: var(--amber-light); border-color:#eecf88; color:#6b4c05;">
                    คำขอนี้มีบัญชีที่สร้างไว้แล้ว {{ $serviceRequest->serviceAccounts->count() }} บัญชี
                    ({{ $serviceRequest->serviceAccounts->pluck('username')->join(', ') }}) — สามารถสร้างเพิ่มได้หากต้องการ
                </div>
            @endif

            <div class="panel">
                <form action="{{ route('admin.accounts.store', $serviceRequest->request_id) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-medium">Username</label>
                        <input type="text" name="username" class="form-control" value="{{ old('username', $suggestedUsername) }}" required>
                        <div class="form-text" style="font-size:12.5px;">แนะนำจากรหัสบุคลากร แก้ไขได้ตามต้องการ (ใช้ตัวอักษร ตัวเลข - หรือ _ เท่านั้น)</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Password</label>
                        <div class="input-group">
                            <input type="text" name="password" id="passwordField" class="form-control"
                                   value="{{ old('password', $suggestedPassword) }}" required minlength="8">
                            <button type="button" class="btn btn-outline-soft" onclick="regeneratePassword()">สุ่มใหม่</button>
                        </div>
                        <div class="form-text" style="font-size:12.5px;">ระบบสุ่มรหัสผ่านให้อัตโนมัติ กรุณาคัดลอกเก็บไว้ก่อนกดบันทึก เพราะจะแสดงอีกครั้งเดียวหลังบันทึกสำเร็จ</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">ประเภทบัญชี</label>
                            <select name="account_type" class="form-select" required>
                                <option value="control_panel">Control Panel</option>
                                <option value="ssh">SSH</option>
                                <option value="database">Database</option>
                                <option value="ftp">FTP</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">วันหมดอายุบัญชี</label>
                            <input type="date" name="expire_date" class="form-control"
                                   value="{{ old('expire_date', $serviceRequest->project_end_date) }}">
                            <div class="form-text" style="font-size:12.5px;">ค่าเริ่มต้นตามวันสิ้นสุดโครงการ</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">ผู้สร้างบัญชี (เจ้าหน้าที่)</label>
                        <input type="text" name="created_by" class="form-control" value="{{ old('created_by') }}" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.requests.index') }}" class="btn btn-outline-soft">ย้อนกลับ</a>
                        <button type="submit" class="btn btn-amber">บันทึกและสร้างบัญชี</button>
                    </div>
                </form>
            </div>

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

@endsection
