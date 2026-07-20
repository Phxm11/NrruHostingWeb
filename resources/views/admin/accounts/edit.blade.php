@extends('admin.layout')

@section('title', 'แก้ไขบัญชีผู้ใช้บริการ')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'แก้ไขบัญชี Username / Password')

@section('content')

    <style>
        :root {
            --acc-blue-bg: #e3efe7;   --acc-blue-fg: #2f6b4a;
            --acc-violet-bg: #f4e8dd; --acc-violet-fg: #a1592f;
            --acc-teal-bg: #eef1da;   --acc-teal-fg: #6a7a2c;
        }
        @keyframes popIn { from { opacity:0; transform: scale(.92); } to { opacity:1; transform: scale(1); } }
        @keyframes bob   { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-3px); } }

        /* ---------- account summary card ---------- */
        .acc-summary {
            position: relative; overflow: hidden;
            background: linear-gradient(120deg, var(--moss-light), #fff);
        }
        .acc-summary__head { display: flex; align-items: center; gap: 11px; margin-bottom: 14px; }
        .acc-summary__icon {
            width: 38px; height: 38px; border-radius: 11px; flex-shrink: 0;
            display: inline-flex; align-items: center; justify-content: center;
            background: #fff; color: var(--moss); box-shadow: 0 2px 6px rgba(0,0,0,.08);
        }
        .acc-summary__head strong { font-size: 15px; }
        .acc-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px 20px; }
        @media (max-width: 640px) { .acc-grid { grid-template-columns: 1fr; } }
        .acc-item { display: flex; align-items: flex-start; gap: 10px; animation: bob 4s ease-in-out infinite; }
        .acc-item__icon {
            width: 28px; height: 28px; border-radius: 8px; flex-shrink: 0; margin-top: 1px;
            display: inline-flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,.7); color: var(--moss);
        }
        .acc-item__label { font-size: 12px; color: var(--ink-soft); margin-bottom: 1px; }
        .acc-item__value { font-size: 14px; font-weight: 500; }
        .acc-item.span-2 { grid-column: 1 / -1; }

        /* ---------- form polish ---------- */
        .field-group { margin-bottom: 22px; }
        .field-label {
            display: flex; align-items: center; gap: 7px; font-weight: 600; margin-bottom: 6px; font-size: 14px;
        }
        .field-label .field-icon {
            width: 22px; height: 22px; border-radius: 6px; display: inline-flex;
            align-items: center; justify-content: center; flex-shrink: 0;
        }
        .field-label--username .field-icon { background: var(--acc-blue-bg);   color: var(--acc-blue-fg); }
        .field-label--password .field-icon { background: var(--amber-light, #fbf1d6); color: var(--amber-deep, #b9840f); }
        .field-label--type     .field-icon { background: var(--acc-violet-bg); color: var(--acc-violet-fg); }
        .field-label--status   .field-icon { background: var(--acc-teal-bg);   color: var(--acc-teal-fg); }
        .field-label--expire   .field-icon { background: var(--acc-blue-bg);   color: var(--acc-blue-fg); }

        .form-control, .form-select { transition: box-shadow .15s, border-color .15s; }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 3px var(--moss-light); border-color: var(--moss);
        }

        #copyPwBtn { display: inline-flex; align-items: center; gap: 6px; }
        #copyPwBtn.copied { color: var(--acc-teal-fg); }

        .banner-danger { display: flex; align-items: flex-start; gap: 10px; animation: popIn .25s ease; }
        .banner-danger .banner-icon {
            width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0; margin-top: 1px;
            display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,.6);
        }

        .btn-amber { display: inline-flex; align-items: center; gap: 7px; }
    </style>

    @if ($errors->any())
        <div class="banner-danger">
            <span class="banner-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.6 1.9 18a2 2 0 0 0 1.7 3h16.8a2 2 0 0 0 1.7-3L14.7 3.6a2 2 0 0 0-3.4 0Z"/></svg>
            </span>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- ============================================================
                 Account summary
            ============================================================ --}}
            <div class="panel mb-3 acc-summary">
                <div class="acc-summary__head">
                    <span class="acc-summary__icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <strong>ข้อมูลบัญชีผู้ใช้บริการ</strong>
                </div>

                <div class="acc-grid">
                    <div class="acc-item">
                        <span class="acc-item__icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                        </span>
                        <div>
                            <div class="acc-item__label">ผู้ขอใช้บริการ</div>
                            <div class="acc-item__value">{{ $account->applicant->full_name }} ({{ $account->applicant->staff_or_student_id }})</div>
                        </div>
                    </div>

                    <div class="acc-item">
                        <span class="acc-item__icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 2v4M16 2v4"/></svg>
                        </span>
                        <div>
                            <div class="acc-item__label">เลขที่คำขอ</div>
                            <div class="acc-item__value">{{ $account->serviceRequest->form_no }}</div>
                        </div>
                    </div>

                    <div class="acc-item span-2">
                        <span class="acc-item__icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg>
                        </span>
                        <div>
                            <div class="acc-item__label">โดเมน</div>
                            <div class="acc-item__value">{{ $account->serviceRequest->domains->pluck('domain_name')->join(', ') ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================
                 Edit-account form
            ============================================================ --}}
            <div class="panel">
                <form action="{{ route('admin.accounts.update', $account->account_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="field-group">
                        <label class="field-label field-label--username">
                            <span class="field-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            Username
                        </label>
                        <input type="text" name="username" class="form-control" value="{{ old('username', $account->username) }}" required>
                    </div>

                    <div class="field-group">
                        <label class="field-label field-label--password">
                            <span class="field-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                            </span>
                            Password ใหม่
                        </label>
                        <div class="input-group">
                            <input type="text" name="password" id="passwordField" class="form-control"
                                   placeholder="ปล่อยว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน" minlength="8">
                            <button type="button" class="btn btn-outline-soft" id="copyPwBtn" onclick="copyPassword()">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
                                <span id="copyPwLabel">คัดลอก</span>
                            </button>
                            <button type="button" class="btn btn-outline-soft" onclick="regeneratePassword()">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-2.6-6.4"/><path d="M21 4v5h-5"/></svg>
                                สุ่มใหม่
                            </button>
                        </div>
                        <div class="form-text" style="font-size:12.5px;">กรอกเฉพาะเมื่อต้องการเปลี่ยนรหัสผ่าน หากบันทึกด้วยช่องว่างจะคงรหัสผ่านเดิมไว้</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="field-label field-label--type">
                                <span class="field-icon">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 3 6v6c0 5 4 9 9 10 5-1 9-5 9-10V6l-9-4Z"/></svg>
                                </span>
                                ประเภทบัญชี
                            </label>
                            <select name="account_type" class="form-select" required>
                                @foreach (['control_panel' => 'Control Panel', 'ssh' => 'SSH', 'database' => 'Database', 'ftp' => 'FTP'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('account_type', $account->account_type) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="field-label field-label--status">
                                <span class="field-icon">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-5"/></svg>
                                </span>
                                สถานะบัญชี
                            </label>
                            <select name="status" class="form-select" required>
                                @foreach (['active' => 'ใช้งานอยู่', 'disabled' => 'ระงับการใช้งาน', 'expired' => 'หมดอายุ'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('status', $account->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="field-group mb-4">
                        <label class="field-label field-label--expire">
                            <span class="field-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 3v3M16 3v3"/></svg>
                            </span>
                            วันหมดอายุบัญชี
                        </label>
                        <input type="date" name="expire_date" class="form-control"
                               value="{{ old('expire_date', $account->expire_date) }}">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-soft">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:5px;"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                            ย้อนกลับ
                        </a>
                        <button type="submit" class="btn btn-amber">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                            บันทึกการแก้ไข
                        </button>
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

        function copyPassword() {
            const pw = document.getElementById('passwordField').value;
            if (!pw) return;
            navigator.clipboard.writeText(pw).then(function () {
                const btn = document.getElementById('copyPwBtn');
                const label = document.getElementById('copyPwLabel');
                const original = label.textContent;
                label.textContent = 'คัดลอกแล้ว ✓';
                btn.classList.add('copied');
                setTimeout(function () {
                    label.textContent = original;
                    btn.classList.remove('copied');
                }, 2000);
            });
        }
    </script>

@endsection
