<div class="institution-bar">
    <div class="container"><span>มหาวิทยาลัยราชภัฏนครราชสีมา</span><span class="service-label">บริการ Data Center และ Web Hosting</span></div>
</div>
<nav class="navbar-custom" id="navbar" aria-label="เมนูหลัก">
    <div class="container navbar-inner">
        <a href="{{ route('home') }}" class="brand" aria-label="สำนักคอมพิวเตอร์ หน้าหลัก">
            <img src="{{ versioned_asset('images/logo.png') }}" class="brand-mark" alt="">
            <div class="brand-name">สำนักคอมพิวเตอร์<small>มหาวิทยาลัยราชภัฏนครราชสีมา</small></div>
        </a>
        <div class="nav-desktop">
            <div class="nav-links-group">
                <a href="{{ request()->routeIs('home') ? '' : route('home') }}#top" class="nav-link-custom" @if(request()->routeIs('home')) aria-current="location" @endif><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m3 10 9-7 9 7M5 9v12h5v-7h4v7h5V9"/></svg><span>หน้าหลัก</span></a>
                    <a href="{{ request()->routeIs('home') ? '' : route('home') }}#services" class="nav-link-custom"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="3" width="18" height="7" rx="2"/><rect x="3" y="14" width="18" height="7" rx="2"/><path d="M7 6.5h.01M7 17.5h.01M11 6.5h6M11 17.5h6"/></svg><span>บริการของเรา</span></a>
                    <a href="{{ request()->routeIs('home') ? '' : route('home') }}#steps" class="nav-link-custom"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="4" y="4" width="16" height="17" rx="2"/><path d="M9 3h6v3H9ZM8 11h.01M12 11h5M8 16h.01M12 16h5"/></svg><span>ขั้นตอนการขอใช้</span></a>
                    <a href="{{ route('domains.index') }}" class="nav-link-custom" @if(request()->routeIs('domains.index')) aria-current="page" @endif><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="10" cy="10" r="7"/><path d="m15 15 6 6M3 10h14M10 3c3 3 3 11 0 14M10 3c-3 3-3 11 0 14"/></svg><span>ตรวจสอบโดเมน</span></a>
                    <a href="{{ request()->routeIs('home') ? '' : route('home') }}#policy" class="nav-link-custom"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m12 3 8 4v5c0 5-8 9-8 9s-8-4-8-9V7Z"/><path d="m8 12 3 3 5-6"/></svg><span>ข้อกำหนด</span></a>
            </div>
            <div class="nav-actions">
                <a href="{{ route('admin.requests.index') }}" class="staff-link"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/></svg> เจ้าหน้าที่</a>
                <a href="{{ route('service-requests.create') }}" class="btn-request">ยื่นคำขอใช้บริการ <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a>
            </div>
        </div>
        <button type="button" class="mobile-toggle" id="mobileToggle" aria-label="เปิดเมนูหลัก" aria-expanded="false" aria-controls="mobileMenu">
            <svg class="ui-icon menu-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg class="ui-icon close-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M6 18 18 6"/></svg>
            <span class="toggle-label">เมนู</span>
        </button>
    </div>
    <div class="mobile-menu" id="mobileMenu" hidden>
        <div class="container">
            <a href="{{ request()->routeIs('home') ? '' : route('home') }}#top" class="nav-link-custom" @if(request()->routeIs('home')) aria-current="location" @endif><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m3 10 9-7 9 7M5 9v12h5v-7h4v7h5V9"/></svg><span>หน้าหลัก</span></a>
                    <a href="{{ request()->routeIs('home') ? '' : route('home') }}#services" class="nav-link-custom"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="3" width="18" height="7" rx="2"/><rect x="3" y="14" width="18" height="7" rx="2"/><path d="M7 6.5h.01M7 17.5h.01M11 6.5h6M11 17.5h6"/></svg><span>บริการของเรา</span></a>
                    <a href="{{ request()->routeIs('home') ? '' : route('home') }}#steps" class="nav-link-custom"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="4" y="4" width="16" height="17" rx="2"/><path d="M9 3h6v3H9ZM8 11h.01M12 11h5M8 16h.01M12 16h5"/></svg><span>ขั้นตอนการขอใช้</span></a>
                    <a href="{{ route('domains.index') }}" class="nav-link-custom" @if(request()->routeIs('domains.index')) aria-current="page" @endif><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="10" cy="10" r="7"/><path d="m15 15 6 6M3 10h14M10 3c3 3 3 11 0 14M10 3c-3 3-3 11 0 14"/></svg><span>ตรวจสอบโดเมน</span></a>
                    <a href="{{ request()->routeIs('home') ? '' : route('home') }}#policy" class="nav-link-custom"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m12 3 8 4v5c0 5-8 9-8 9s-8-4-8-9V7Z"/><path d="m8 12 3 3 5-6"/></svg><span>ข้อกำหนด</span></a>
            <div class="nav-actions">
                <a href="{{ route('admin.requests.index') }}" class="staff-link"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/></svg> สำหรับเจ้าหน้าที่</a>
                <a href="{{ route('service-requests.create') }}" class="btn-request">ยื่นคำขอใช้บริการ <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a>
            </div>
        </div>
    </div>
</nav>
