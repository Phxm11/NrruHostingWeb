<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เข้าสู่ระบบ — สำนักคอมพิวเตอร์ มรภ.นครราชสีมา</title>
    @include('partials.site-icons')
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700&family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ versioned_asset('css/login.css') }}" rel="stylesheet">
</head>
<body>
    <a class="skip-link" href="#login-form">ข้ามไปยังแบบฟอร์มเข้าสู่ระบบ</a>
    <header class="login-header">
        <a class="login-brand" href="{{ route('home') }}" aria-label="สำนักคอมพิวเตอร์ หน้าหลัก">
            <img src="{{ versioned_asset('images/logo.png') }}" width="52" height="52" alt="">
            <span>สำนักคอมพิวเตอร์<small>มหาวิทยาลัยราชภัฏนครราชสีมา</small></span>
        </a>
        <a class="home-link" href="{{ route('home') }}"><x-admin.icon name="back" size="18" /> กลับหน้าเว็บบริการ</a>
    </header>

    <main class="login-main">
        <section class="login-intro" aria-labelledby="service-heading">
            <span class="intro-rule" aria-hidden="true"></span>
            <p class="intro-label">ระบบสำหรับเจ้าหน้าที่</p>
            <h1 id="service-heading">จัดการบริการ<br> Data Center และ<br> Web Hosting</h1>
            <p class="intro-description">ดูแลคำขอใช้บริการ บัญชีผู้ใช้งาน และโดเมน<br class="desktop-break">ของมหาวิทยาลัยได้ในที่เดียว</p>
            <ul class="service-list" aria-label="งานที่จัดการได้ในระบบ">
                <li><x-admin.icon name="requests" size="21" /><span>ตรวจสอบและจัดการคำขอใช้บริการ</span></li>
                <li><x-admin.icon name="domains" size="21" /><span>ดูแลบัญชีบริการและโดเมน</span></li>
                <li><x-admin.icon name="reports" size="21" /><span>สรุปรายงานสำหรับผู้บริหาร</span></li>
            </ul>
        </section>

        <section class="login-panel" aria-labelledby="login-heading">
            <div class="login-panel-heading">
                <span class="login-panel-icon"><x-admin.icon name="accounts" size="25" /></span>
                <h2 id="login-heading">เข้าสู่ระบบเจ้าหน้าที่</h2>
                <p>กรอกอีเมลและรหัสผ่านเพื่อเข้าใช้งาน</p>
            </div>

            @if($errors->any())
                <div class="login-alert" id="login-error" role="alert">
                    <x-admin.icon name="info" size="20" />
                    <div><strong>ไม่สามารถเข้าสู่ระบบได้</strong><ul>@foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul></div>
                </div>
            @endif
            @if(session('status'))<p class="login-status" role="status">{{ session('status') }}</p>@endif

            <form id="login-form" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="login-field">
                    <label for="email">อีเมล</label>
                    <input id="email" type="email" name="email" value="{{ is_string(old('email')) ? old('email') : '' }}" required autocomplete="username" inputmode="email" autocapitalize="none" spellcheck="false" placeholder="name@nrru.ac.th" @error('email') aria-invalid="true" aria-describedby="login-error" @enderror>
                </div>
                <div class="login-field">
                    <label for="password">รหัสผ่าน</label>
                    <div class="password-field">
                        <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="กรอกรหัสผ่าน" @error('password') aria-invalid="true" aria-describedby="login-error" @enderror>
                        <button class="password-toggle" type="button" id="password-toggle" aria-controls="password" aria-label="แสดงรหัสผ่าน" hidden>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/><path class="eye-slash" d="m3 3 18 18"/></svg>
                        </button>
                    </div>
                </div>
                <label class="remember-field" for="remember"><input type="checkbox" name="remember" id="remember" value="1" @checked(old('remember'))><span>จดจำการเข้าสู่ระบบ</span></label>
                <button type="submit" class="login-submit">เข้าสู่ระบบ <x-admin.icon name="arrow" size="20" /></button>
            </form>
            <p class="login-help"><x-admin.icon name="info" size="17" /><span>หากเข้าสู่ระบบไม่ได้ กรุณาติดต่อผู้ดูแลระบบ</span></p>
        </section>
    </main>
    <footer class="login-footer">สำนักคอมพิวเตอร์ · มหาวิทยาลัยราชภัฏนครราชสีมา</footer>
    <script src="{{ versioned_asset('js/login.js') }}" defer></script>
</body>
</html>
