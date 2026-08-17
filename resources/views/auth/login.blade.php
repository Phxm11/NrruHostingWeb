<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เข้าสู่ระบบ — สำนักคอมพิวเตอร์ มรภ.นครราชสีมา</title>
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
            width: 56px; height: 56px; border-radius: 14px; margin: 0 auto 14px;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 6px 16px rgba(0,0,0,.16);
            padding: 6px;
        }
        .brand-mark img { width: 100%; height: 100%; object-fit: contain; display: block; }
        h1 { font-family: 'Kanit', sans-serif; font-size: 21px; font-weight: 600; text-align: center; margin: 0 0 4px; color: var(--forest); }
        .sub { text-align: center; color: var(--ink-soft); font-size: 14px; margin-bottom: 24px; }
        .form-label { font-weight: 600; font-size: 14px; margin-bottom: 6px; }
        .form-control {
            border: 1px solid var(--line); border-radius: 11px; font-size: 14.5px; padding: 10px 13px;
            transition: box-shadow .15s ease, border-color .15s ease;
        }
        .form-control:focus { border-color: var(--moss); box-shadow: 0 0 0 3px rgba(95,139,70,.16); }
        .btn-amber {
            width: 100%; background: linear-gradient(135deg, var(--amber), var(--amber-deep)); border: none;
            color: #2c1e05; font-weight: 600; font-size: 15.5px; border-radius: 12px; padding: 12px; margin-top: 6px;
            box-shadow: 0 6px 16px -6px rgba(185,132,15,.6); transition: filter .15s ease, transform .15s ease;
        }
        .btn-amber:hover { filter: brightness(1.05); color: #2c1e05; transform: translateY(-1px); }
        .form-check-input:checked { background-color: var(--forest); border-color: var(--forest); }
        .alert-danger-c { background: var(--rust-light); border: 1px solid #e6b6a4; color: var(--rust); border-radius: 12px; padding: 11px 14px; font-size: 13.5px; margin-bottom: 16px; }
        .alert-status-c { background: var(--moss-light); border: 1px solid #c9dcb4; color: var(--forest-2); border-radius: 12px; padding: 11px 14px; font-size: 13.5px; margin-bottom: 16px; }
        .back-link { display: block; text-align: center; margin-top: 18px; font-size: 13px; color: var(--ink-soft); text-decoration: none; }
        .back-link:hover { color: var(--forest); }
        .forgot-link { display: block; text-align: right; font-size: 13px; color: var(--forest-2); text-decoration: none; margin: -8px 0 16px; }
        .forgot-link:hover { color: var(--amber-deep); text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-mark">
            <img src="{{ asset('images/logo.png') }}" alt="โลโก้ระบบ">
        </div>
        <h1>เข้าสู่ระบบเจ้าหน้าที่</h1>
        <div class="sub">ระบบจัดการคำขอใช้บริการ Data Center และ Web Hosting</div>

        @if (session('status'))
            <div class="alert-status-c">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-danger-c">
                <strong>ไม่สามารถเข้าสู่ระบบได้:</strong>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label" for="email">อีเมล</label>
                <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="admin@nrru.ac.th">
            </div>

            <div class="mb-2">
                <label class="form-label" for="password">รหัสผ่าน</label>
                <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password">
            </div>

            <a href="{{ route('password.request') }}" class="forgot-link">ลืมรหัสผ่าน?</a>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember" style="font-size:13.5px;">จดจำการเข้าสู่ระบบ</label>
            </div>

            <button type="submit" class="btn btn-amber">เข้าสู่ระบบ</button>
        </form>

        <a href="{{ url('/') }}" class="back-link">← กลับหน้าเว็บบริการ</a>
    </div>
</body>
</html>
