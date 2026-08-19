<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ตั้งรหัสผ่านใหม่ — สำนักคอมพิวเตอร์ มรภ.นครราชสีมา</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700&family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f5f4ec;
            --surface: #ffffff;
            --ink: #15231a;
            --ink-soft: #5c6659;
            --forest: #1a3323;
            --forest-2: #244430;
            --moss: #6c9752;
            --moss-light: #e8f0dc;
            --amber: #d79a2c;
            --amber-deep: #a6740e;
            --amber-light: #faf0d3;
            --rust: #ae4830;
            --rust-light: #fbebe6;
            --line: #e5e1d1;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Sarabun', sans-serif; margin: 0; min-height: 100vh; color: var(--ink);
            background:
                repeating-radial-gradient(circle at 85% 5%, rgba(255,255,255,.08) 0px, rgba(255,255,255,.08) 1px, transparent 1px, transparent 18px),
                radial-gradient(140% 120% at 80% -10%, var(--forest-2) 0%, var(--forest) 58%, #142a1a 100%);
            display: flex; align-items: center; justify-content: center; padding: 24px;
            -webkit-font-smoothing: antialiased;
        }
        .login-card {
            width: 100%; max-width: 410px; background: var(--surface);
            border-radius: 22px; padding: 34px 32px; box-shadow: 0 30px 70px -25px rgba(0,0,0,.5);
            animation: pop .35s ease;
        }
        @keyframes pop { from { opacity: 0; transform: translateY(12px) scale(.98); } to { opacity: 1; transform: none; } }
        .brand-mark {
            width: 48px; height: 48px; border-radius: 14px; margin: 0 auto 14px;
            background: linear-gradient(135deg, var(--amber), #f0c25c);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 6px 16px rgba(224,165,38,.4);
        }
        h1 { font-family: 'Kanit', sans-serif; font-size: 21px; font-weight: 600; text-align: center; margin: 0 0 4px; color: var(--forest); }
        .sub { text-align: center; color: var(--ink-soft); font-size: 14px; margin-bottom: 24px; }
        .form-label { font-weight: 600; font-size: 14px; margin-bottom: 6px; }
        .form-control {
            border: 1px solid var(--line); border-radius: 11px; font-size: 14.5px; padding: 10px 13px;
            transition: box-shadow .15s ease, border-color .15s ease;
        }
        .form-control:focus { border-color: var(--moss); box-shadow: 0 0 0 3px rgba(95,139,70,.16); }
        .form-control[readonly] { background: var(--bg); color: var(--ink-soft); }
        .btn-amber {
            width: 100%; background: linear-gradient(135deg, var(--amber), var(--amber-deep)); border: none;
            color: #2c1e05; font-weight: 600; font-size: 15.5px; border-radius: 12px; padding: 12px; margin-top: 6px;
            box-shadow: 0 6px 16px -6px rgba(185,132,15,.6); transition: filter .15s ease, transform .15s ease;
        }
        .btn-amber:hover { filter: brightness(1.05); color: #2c1e05; transform: translateY(-1px); }
        .alert-danger-c { background: var(--rust-light); border: 1px solid #e6b6a4; color: var(--rust); border-radius: 12px; padding: 11px 14px; font-size: 13.5px; margin-bottom: 16px; }
        .back-link { display: block; text-align: center; margin-top: 18px; font-size: 13px; color: var(--ink-soft); text-decoration: none; }
        .back-link:hover { color: var(--forest); }
        .hint { font-size: 12.5px; color: var(--ink-soft); margin-top: 4px; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-mark">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M12 2C7 2 3 6 3 11c0 5 4.5 9.5 9 11 4.5-1.5 9-6 9-11 0-5-4-9-9-9Z" fill="#24422b"/>
                <path d="M12 6v10M9 9l3-3 3 3" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1>ตั้งรหัสผ่านใหม่</h1>
        <div class="sub">กรอกรหัสผ่านใหม่ของคุณ</div>

        @include('partials.alert-popup', ['errorTitle' => 'ไม่สามารถดำเนินการได้'])

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label class="form-label" for="email">อีเมล</label>
                <input id="email" type="email" name="email" class="form-control" value="{{ old('email', $email) }}" required autofocus>
            </div>

            <div class="mb-1">
                <label class="form-label" for="password">รหัสผ่านใหม่</label>
                <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password">
                <div class="hint">อย่างน้อย 8 ตัวอักษร</div>
            </div>

            <div class="mb-3 mt-3">
                <label class="form-label" for="password_confirmation">ยืนยันรหัสผ่านใหม่</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-amber">บันทึกรหัสผ่านใหม่</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">← กลับไปหน้าเข้าสู่ระบบ</a>
    </div>
</body>
</html>
