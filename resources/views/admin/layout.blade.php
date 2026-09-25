@php
    $adminSection = explode('.', request()->route()->getName())[1] ?? 'requests';
    $adminNavigation = [
        'requests' => ['label' => 'คำขอใช้บริการ', 'description' => 'ตรวจสอบและดำเนินการคำขอที่ยังไม่ได้สร้างบัญชีบริการ'],
        'accounts' => ['label' => 'บัญชีบริการ', 'description' => 'ดูแลบัญชีผู้ใช้บริการ สถานะ และระยะเวลาการใช้งาน'],
        'domains' => ['label' => 'โดเมนบริการ', 'description' => 'ตรวจสอบโดเมน หน่วยงาน และบัญชีที่เกี่ยวข้อง'],
        'reports' => ['label' => 'รายงานผู้บริหาร', 'description' => 'สรุปข้อมูลบริการและจัดเตรียมรายงานสำหรับผู้บริหาร'],
        'users' => ['label' => 'เจ้าหน้าที่', 'description' => 'จัดการบัญชีเจ้าหน้าที่และการเข้าถึงระบบ'],
    ];
    $adminPage = $adminNavigation[$adminSection] ?? $adminNavigation['requests'];
@endphp
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ระบบจัดการบริการ') — สำนักคอมพิวเตอร์ NRRU</title>
    @include('partials.site-icons')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700&family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ versioned_asset('css/admin/admin.css') }}" rel="stylesheet">
    @stack('styles')
    <link href="{{ versioned_asset('css/motion.css') }}" rel="stylesheet">
</head>
<body class="admin-app" data-admin-section="{{ $adminSection }}">
<a href="#main-content" class="skip-link">ข้ามไปยังเนื้อหา</a>
<div class="sidebar-backdrop" id="sidebarBackdrop" hidden></div>
<div class="app-shell">
    <aside class="sidebar" id="sidebar" aria-label="เมนู ADMIN">
        <a href="{{ route('admin.requests.index') }}" class="brand">
            <img src="{{ versioned_asset('images/logo.png') }}" width="42" height="42" alt="NRRU">
            <span class="brand-text">NRRU Hosting<small>ระบบจัดการบริการ</small></span>
        </a>
        <button type="button" class="sidebar-close icon-btn" id="sidebarClose" aria-label="ปิดเมนู"><x-admin.icon name="close" /></button>
        <nav class="sidebar-navigation" aria-label="เมนูหลัก">
            @foreach(['งานบริการ' => ['requests', 'accounts', 'domains'], 'การบริหาร' => ['reports', 'users']] as $groupLabel => $sections)
                <div class="nav-group">
                    <p class="nav-label">{{ $groupLabel }}</p>
                    @foreach($sections as $section)
                        <a href="{{ route('admin.'.$section.'.index') }}" class="nav-item {{ $adminSection === $section ? 'active' : '' }}" @if($adminSection === $section) aria-current="page" @endif>
                            <x-admin.icon :name="$section" />
                            <span>{{ $adminNavigation[$section]['label'] }}</span>
                            @if($adminSection === $section)<span class="nav-current-mark" aria-hidden="true"></span>@endif
                        </a>
                    @endforeach
                </div>
            @endforeach
            <div class="nav-group nav-group--secondary">
                <p class="nav-label">ลิงก์บริการ</p>
                <a href="{{ route('home') }}" class="nav-item" target="_blank" rel="noopener"><x-admin.icon name="external" /><span>หน้าเว็บไซต์บริการ</span></a>
                <a href="{{ route('service-requests.create') }}" class="nav-item" target="_blank" rel="noopener"><x-admin.icon name="plus" /><span>แบบฟอร์มขอใช้บริการ</span></a>
            </div>
        </nav>
        <div class="sidebar-footer">
            <span class="staff-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
            <div class="footer-user"><strong>{{ auth()->user()->name }}</strong><span>เจ้าหน้าที่สำนักคอมพิวเตอร์</span></div>
            <form action="{{ route('logout') }}" method="POST">@csrf
                <button type="submit" class="icon-btn" title="ออกจากระบบ" aria-label="ออกจากระบบ"><x-admin.icon name="logout" size="18" /></button>
            </form>
        </div>
    </aside>
    <div class="main-col">
        <header class="topbar">
            <div class="topbar-heading">
                <button type="button" class="sidebar-toggle icon-btn" id="menuBtn" aria-label="เปิดเมนู" aria-controls="sidebar" aria-expanded="false"><x-admin.icon name="menu" /></button>
                <span class="page-icon"><x-admin.icon :name="$adminSection" size="24" /></span>
                <div>
                    <nav class="breadcrumb-line" aria-label="ตำแหน่งปัจจุบัน"><span>ADMIN</span><x-admin.icon name="chevron" size="12" /><a href="{{ route('admin.'.$adminSection.'.index') }}">{{ $adminPage['label'] }}</a></nav>
                    <h1>@yield('page-title', $adminPage['label'])</h1>
                </div>
            </div>
            @hasSection('topbar-action')<div class="topbar-right">@yield('topbar-action')</div>@endif
        </header>
        <main class="content-area" id="main-content" tabindex="-1">
            <p class="page-description">@yield('page-description', $adminPage['description'])</p>
            @yield('content')
        </main>
        <footer class="app-footer">สำนักคอมพิวเตอร์ มหาวิทยาลัยราชภัฏนครราชสีมา</footer>
    </div>
</div>
@include('partials.alert-popup')
@include('partials.confirm-modal')
<script src="{{ versioned_asset('js/admin.js') }}" defer></script>
<script src="{{ versioned_asset('js/motion.js') }}" defer></script>
@stack('scripts')
</body>
</html>
