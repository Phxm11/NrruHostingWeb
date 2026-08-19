@extends('admin.layout')

@section('title', 'สร้างบัญชีผู้ใช้บริการ')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'สร้างบัญชี Username / Password')

@section('content')

    <style>
        :root {
            --acc-blue-bg: #e3efe7;   --acc-blue-fg: #2f6b4a;
            --acc-violet-bg: #f4e8dd; --acc-violet-fg: #a1592f;
            --acc-teal-bg: #eef1da;   --acc-teal-fg: #6a7a2c;
        }
        @keyframes popIn { from { opacity:0; transform: scale(.92); } to { opacity:1; transform: scale(1); } }
        @keyframes bob   { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-3px); } }
        @keyframes rowIn { from { opacity:0; transform: translateY(6px); } to { opacity:1; transform: translateY(0); } }

        /* ---------- request summary card ---------- */
        .req-summary {
            position: relative; overflow: hidden;
            background: linear-gradient(120deg, var(--moss-light), #fff);
        }
        .req-summary__head {
            display: flex; align-items: center; gap: 10px; margin-bottom: 14px;
        }
        .req-summary__icon {
            width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
            display: inline-flex; align-items: center; justify-content: center;
            background: #fff; color: var(--moss, #3f8a5f); box-shadow: 0 2px 6px rgba(0,0,0,.08);
        }
        .req-summary__head strong { font-size: 15px; }
        .req-grid {
            display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px 20px;
        }
        @media (max-width: 640px) { .req-grid { grid-template-columns: 1fr; } }
        .req-item { display: flex; align-items: flex-start; gap: 10px; animation: rowIn .3s ease both; }
        .req-item__icon {
            width: 28px; height: 28px; border-radius: 8px; flex-shrink: 0; margin-top: 1px;
            display: inline-flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,.7); color: var(--moss, #3f8a5f);
        }
        .req-item__label { font-size: 12px; color: var(--ink-soft); margin-bottom: 1px; }
        .req-item__value { font-size: 14px; font-weight: 500; }
        .req-item.span-2 { grid-column: 1 / -1; }

        /* ---------- existing-accounts note ---------- */
        .banner-success.has-accounts {
            display: flex; align-items: flex-start; gap: 10px; animation: popIn .3s ease;
        }
        .banner-success.has-accounts .banner-icon {
            width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0; margin-top: 1px;
            display: inline-flex; align-items: center; justify-content: center;
            background: #fff; animation: bob 2.2s ease-in-out infinite;
        }
        .existing-username-chip {
            display: inline-flex; align-items: center; font-size: 12.5px; font-weight: 600;
            background: #fff; border: 1px solid #eecf88; border-radius: 999px; padding: 2px 10px; margin: 2px 4px 0 0;
        }

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
        .field-label--password .field-icon { background: #fdf1cf;              color: var(--amber, #a3760a); }
        .field-label--type     .field-icon { background: var(--acc-violet-bg); color: var(--acc-violet-fg); }
        .field-label--expire   .field-icon { background: var(--acc-teal-bg);   color: var(--acc-teal-fg); }
        .field-label--officer  .field-icon { background: var(--moss-light, #e3f0e6); color: var(--moss, #3f8a5f); }

        .form-control, .form-select {
            transition: box-shadow .15s, border-color .15s;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 3px var(--moss-light); border-color: var(--moss);
        }

        /* password strength meter */
        .pw-meter { display: flex; gap: 4px; margin-top: 8px; }
        .pw-meter__bar { flex: 1; height: 4px; border-radius: 999px; background: #ececec; transition: background .2s; }
        .pw-strength-label { font-size: 12px; margin-top: 4px; font-weight: 600; }

        #regenBtn { display: inline-flex; align-items: center; gap: 6px; }
        #regenBtn svg { transition: transform .3s; }
        #regenBtn.spin svg { transform: rotate(180deg); }

        #copyPwBtn {
            display: inline-flex; align-items: center; gap: 6px;
        }
        #copyPwBtn.copied { color: var(--acc-teal-fg); }

        .btn-amber { display: inline-flex; align-items: center; gap: 7px; }
    </style>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- ============================================================
                 Request summary
            ============================================================ --}}
            <div class="panel mb-3 req-summary">
                <div class="req-summary__head">
                    <span class="req-summary__icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18"/><path d="M8 13h3M8 17h5"/></svg>
                    </span>
                    <strong>ข้อมูลคำขอใช้บริการ</strong>
                </div>

                <div class="req-grid">
                    <div class="req-item">
                        <span class="req-item__icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                        </span>
                        <div>
                            <div class="req-item__label">ผู้ขอใช้บริการ</div>
                            <div class="req-item__value">{{ $serviceRequest->applicant->full_name }} ({{ $serviceRequest->applicant->staff_or_student_id }})</div>
                        </div>
                    </div>

                    <div class="req-item">
                        <span class="req-item__icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21V7l9-4 9 4v14"/><path d="M9 21v-6h6v6"/></svg>
                        </span>
                        <div>
                            <div class="req-item__label">หน่วยงาน</div>
                            <div class="req-item__value">{{ $serviceRequest->applicant->unit_name }}</div>
                        </div>
                    </div>

                    <div class="req-item">
                        <span class="req-item__icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 2v4M16 2v4"/></svg>
                        </span>
                        <div>
                            <div class="req-item__label">เลขที่คำขอ</div>
                            <div class="req-item__value">{{ $serviceRequest->form_no }}</div>
                        </div>
                    </div>

                    <div class="req-item">
                        <span class="req-item__icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg>
                        </span>
                        <div>
                            <div class="req-item__label">โดเมน</div>
                            <div class="req-item__value">{{ $serviceRequest->domains->pluck('domain_name')->join(', ') ?: '-' }}</div>
                        </div>
                    </div>

                    <div class="req-item span-2">
                        <span class="req-item__icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 3v3M16 3v3"/></svg>
                        </span>
                        <div>
                            <div class="req-item__label">ระยะเวลาโครงการ</div>
                            <div class="req-item__value">
                                {{ \Carbon\Carbon::parse($serviceRequest->project_start_date)->format('d/m/Y') }}
                                <span style="color:var(--ink-soft);">—</span>
                                {{ \Carbon\Carbon::parse($serviceRequest->project_end_date)->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================
                 Existing accounts notice
            ============================================================ --}}
            @if ($serviceRequest->serviceAccounts->isNotEmpty())
                <div class="banner-success has-accounts" style="background: var(--amber-light); border-color:#eecf88; color:#6b4c05;">
                    <span class="banner-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#a3760a" stroke-width="2"><path d="M12 9v4M12 17h.01"/><circle cx="12" cy="12" r="9"/></svg>
                    </span>
                    <div>
                        คำขอนี้มีบัญชีที่สร้างไว้แล้ว <strong>{{ $serviceRequest->serviceAccounts->count() }}</strong> บัญชี — สามารถสร้างเพิ่มได้หากต้องการ
                        <div class="mt-1">
                            @foreach ($serviceRequest->serviceAccounts->pluck('username') as $u)
                                <span class="existing-username-chip"><code>{{ $u }}</code></span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- ============================================================
                 Create-account form
            ============================================================ --}}
            <div class="panel">
                <form action="{{ route('admin.accounts.store', $serviceRequest->request_id) }}" method="POST">
                    @csrf

                    <div class="field-group">
                        <label class="field-label field-label--username">
                            <span class="field-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            Username
                        </label>
                        <input type="text" name="username" class="form-control" value="{{ old('username', $suggestedUsername) }}" required>
                        <div class="form-text" style="font-size:12.5px;">แนะนำจากรหัสบุคลากร แก้ไขได้ตามต้องการ (ใช้ตัวอักษร ตัวเลข - หรือ _ เท่านั้น)</div>
                    </div>

                    <div class="field-group">
                        <label class="field-label field-label--password">
                            <span class="field-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                            </span>
                            Password
                        </label>
                        <div class="input-group">
                            <input type="text" name="password" id="passwordField" class="form-control"
                                   value="{{ old('password', $suggestedPassword) }}" required minlength="8"
                                   oninput="updatePwStrength()">
                            <button type="button" class="btn btn-outline-soft" id="copyPwBtn" onclick="copyPassword()">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
                                <span id="copyPwLabel">คัดลอก</span>
                            </button>
                            <button type="button" class="btn btn-outline-soft" id="regenBtn" onclick="regeneratePassword()">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-2.6-6.4"/><path d="M21 4v5h-5"/></svg>
                                สุ่มใหม่
                            </button>
                        </div>
                        <div class="pw-meter">
                            <span class="pw-meter__bar" id="pwBar1"></span>
                            <span class="pw-meter__bar" id="pwBar2"></span>
                            <span class="pw-meter__bar" id="pwBar3"></span>
                            <span class="pw-meter__bar" id="pwBar4"></span>
                        </div>
                        <div class="pw-strength-label" id="pwStrengthLabel"></div>
                        <div class="form-text" style="font-size:12.5px;">ระบบสุ่มรหัสผ่านให้อัตโนมัติ กรุณาคัดลอกเก็บไว้ก่อนกดบันทึก เพราะจะแสดงอีกครั้งเดียวหลังบันทึกสำเร็จ</div>
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
                                <option value="control_panel">Control Panel</option>
                                <option value="ssh">SSH</option>
                                <option value="database">Database</option>
                                <option value="ftp">FTP</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="field-label field-label--expire">
                                <span class="field-icon">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 3v3M16 3v3"/></svg>
                                </span>
                                วันหมดอายุบัญชี
                            </label>
                            <input type="date" name="expire_date" class="form-control"
                                   value="{{ old('expire_date', $serviceRequest->project_end_date) }}">
                            <div class="form-text" style="font-size:12.5px;">ค่าเริ่มต้นตามวันสิ้นสุดโครงการ</div>
                        </div>
                    </div>

                    <div class="field-group mb-4">
                        <label class="field-label field-label--officer">
                            <span class="field-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
                            </span>
                            ผู้สร้างบัญชี (เจ้าหน้าที่)
                        </label>
                        <input type="text" name="created_by" class="form-control" value="{{ old('created_by') }}" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.requests.index') }}" class="btn btn-outline-soft">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:5px;"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                            ย้อนกลับ
                        </a>
                        <button type="submit" class="btn btn-amber">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                            บันทึกและสร้างบัญชี
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
            updatePwStrength();

            const btn = document.getElementById('regenBtn');
            btn.classList.add('spin');
            setTimeout(() => btn.classList.remove('spin'), 300);
        }

        function copyPassword() {
            const pw = document.getElementById('passwordField').value;
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

        function updatePwStrength() {
            const val = document.getElementById('passwordField').value;
            const bars = [
                document.getElementById('pwBar1'),
                document.getElementById('pwBar2'),
                document.getElementById('pwBar3'),
                document.getElementById('pwBar4'),
            ];
            const label = document.getElementById('pwStrengthLabel');

            let score = 0;
            if (val.length >= 8) score++;
            if (val.length >= 12) score++;
            if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;

            const colors = ['#e0574c', '#e0574c', '#d4a017', '#2f9e5b', '#1f7a3d'];
            const labels = ['สั้นเกินไป', 'อ่อน', 'ปานกลาง', 'ดี', 'แข็งแรงมาก'];
            const color = colors[score] || '#ececec';

            bars.forEach((bar, i) => {
                bar.style.background = i < score ? color : '#ececec';
            });
            label.textContent = val.length ? labels[score] : '';
            label.style.color = color;
        }

        document.addEventListener('DOMContentLoaded', updatePwStrength);
    </script>

@endsection