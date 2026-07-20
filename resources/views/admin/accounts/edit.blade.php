@extends('admin.layout')

@section('title', 'แก้ไขบัญชีผู้ใช้บริการ')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'แก้ไขบัญชี Username / Password')

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
                    <dd class="col-8">{{ $account->applicant->full_name }} ({{ $account->applicant->staff_or_student_id }})</dd>
                    <dt class="col-4" style="font-size:12.5px;color:var(--ink-soft);">เลขที่คำขอ</dt>
                    <dd class="col-8">{{ $account->serviceRequest->form_no }}</dd>
                    <dt class="col-4" style="font-size:12.5px;color:var(--ink-soft);">โดเมน</dt>
                    <dd class="col-8 mb-0">{{ $account->serviceRequest->domains->pluck('domain_name')->join(', ') ?: '-' }}</dd>
                </dl>
            </div>

            <div class="panel">
                <form action="{{ route('admin.accounts.update', $account->account_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-medium">Username</label>
                        <input type="text" name="username" class="form-control" value="{{ old('username', $account->username) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Password ใหม่</label>
                        <div class="input-group">
                            <input type="text" name="password" id="passwordField" class="form-control"
                                   placeholder="ปล่อยว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน" minlength="8">
                            <button type="button" class="btn btn-outline-soft" onclick="regeneratePassword()">สุ่มใหม่</button>
                        </div>
                        <div class="form-text" style="font-size:12.5px;">กรอกเฉพาะเมื่อต้องการเปลี่ยนรหัสผ่าน หากบันทึกด้วยช่องว่างจะคงรหัสผ่านเดิมไว้</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">ประเภทบัญชี</label>
                            <select name="account_type" class="form-select" required>
                                @foreach (['control_panel' => 'Control Panel', 'ssh' => 'SSH', 'database' => 'Database', 'ftp' => 'FTP'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('account_type', $account->account_type) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">สถานะบัญชี</label>
                            <select name="status" class="form-select" required>
                                @foreach (['active' => 'ใช้งานอยู่', 'disabled' => 'ระงับการใช้งาน', 'expired' => 'หมดอายุ'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('status', $account->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">วันหมดอายุบัญชี</label>
                        <input type="date" name="expire_date" class="form-control"
                               value="{{ old('expire_date', $account->expire_date) }}">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-soft">ย้อนกลับ</a>
                        <button type="submit" class="btn btn-amber">บันทึกการแก้ไข</button>
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
