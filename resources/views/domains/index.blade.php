<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ตรวจสอบโดเมน — สำนักคอมพิวเตอร์ มหาวิทยาลัยราชภัฏนครราชสีมา</title>
    @include('partials.site-icons')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700&family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/public-site.css') }}?v={{ filemtime(public_path('css/public-site.css')) }}" rel="stylesheet">
    <link href="{{ asset('css/domain-directory.css') }}?v={{ filemtime(public_path('css/domain-directory.css')) }}" rel="stylesheet">
</head>
<body class="domain-directory-page">
<a class="skip-link" href="#mainContent">ข้ามไปยังเนื้อหา</a>
@include('partials.public-navigation')
<main id="mainContent" tabindex="-1">
    <header class="domain-hero">
        <div class="container">
            <a class="domain-back" href="{{ route('home') }}"><x-admin.icon name="back" class="ui-icon" size="16" /> กลับหน้าหลัก</a>
            <div class="domain-hero-title"><span class="domain-hero-icon"><x-admin.icon name="domains" size="30" /></span><div><h1>ตรวจสอบโดเมนของคุณ</h1><p>ค้นหาโดเมนเพื่อดูว่ามีบัญชีบริการเปิดใช้งานแล้วหรือยัง</p></div></div>
        </div>
    </header>
    <div class="container domain-content">
        <form method="GET" action="{{ route('domains.index') }}" class="domain-search" role="search" aria-label="ค้นหาโดเมน">
            <label for="domain-query">ชื่อโดเมนที่ต้องการตรวจสอบ</label>
            <div class="domain-search-row">
                <div class="domain-search-input"><x-admin.icon name="search" size="21" /><input id="domain-query" type="search" name="q" value="{{ is_string(old('q', $search)) ? old('q', $search) : '' }}" maxlength="255" placeholder="เช่น department.nrru.ac.th" autocomplete="off" autocapitalize="none" spellcheck="false" aria-describedby="domain-search-help @error('q') domain-search-error @enderror" @error('q') aria-invalid="true" @enderror></div>
                <button class="btn-amber" type="submit"><x-admin.icon name="search" size="18" /> ค้นหาโดเมน</button>
            </div>
            <p class="domain-search-help" id="domain-search-help">ค้นหาได้ด้วยชื่อโดเมนทั้งหมดหรือบางส่วน หรือวาง URL ของเว็บไซต์</p>
            @error('q')<p class="domain-error" id="domain-search-error" role="alert">{{ $message }}</p>@enderror
            @error('page')<p class="domain-error" role="alert">{{ $message }}</p>@enderror
        </form>

        <section class="domain-results" aria-labelledby="domain-results-heading">
            <div class="domain-results-heading">
                <div><h2 id="domain-results-heading">{{ $search !== '' ? 'ผลการค้นหาโดเมน' : 'โดเมนที่มีบัญชีเปิดใช้งานแล้ว' }}</h2><p>@if($search !== '')คำค้น “{{ $search }}” · @endifพบ {{ number_format($totalDomains) }} โดเมน · {{ number_format($groups->total()) }} ผู้ดูแล <span>จัดกลุ่มตามผู้ดูแล · โดเมนในกลุ่มเรียง A–Z</span></p></div>
                @if($search !== '')<a href="{{ route('domains.index') }}" class="domain-reset"><x-admin.icon name="renew" size="16" /> ดูโดเมนทั้งหมด</a>@endif
            </div>

            @if($groups->isNotEmpty())
                @foreach($groups as $group)
                    <section class="domain-owner-group" aria-labelledby="domain-owner-{{ $loop->iteration }}">
                        <header class="domain-owner-heading">
                            <div class="domain-owner-title"><span class="domain-owner-icon"><x-admin.icon name="users" size="22" /></span><div><p>ผู้ดูแลโดเมน</p><h3 id="domain-owner-{{ $loop->iteration }}">{{ $group->owner_name }}</h3></div></div>
                            <div class="domain-owner-count"><strong>ดูแล {{ number_format($group->domain_count) }} โดเมน</strong>@if($search !== '')<span>ตรงกับคำค้น {{ number_format($group->matching_count) }} โดเมน</span>@endif</div>
                        </header>
                        <div class="domain-table-wrap">
                            <table class="domain-table">
                                <caption class="visually-hidden">โดเมนที่เปิดใช้งานและยังไม่หมดอายุของ {{ $group->owner_name }}</caption>
                                <thead><tr><th scope="col">ชื่อโดเมน / ผู้ดูแล</th><th scope="col">สถานะบริการ</th></tr></thead>
                                <tbody>
                                    @foreach($group->domains as $domain)
                                        <tr><td><div class="domain-name"><span class="domain-row-icon"><x-admin.icon name="domains" size="19" /></span><div class="domain-name-details"><strong>{{ $domain }}</strong><span class="domain-row-owner"><span aria-hidden="true">— </span><span class="visually-hidden">ผู้ดูแล </span>{{ $group->owner_name }}</span></div></div></td><td><span class="domain-active"><x-admin.icon name="check" size="16" /> บัญชีเปิดใช้งานแล้ว</span></td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endforeach
                @if($groups->hasPages())<p class="domain-pagination-help">แสดงหน้าละ 10 กลุ่มผู้ดูแล</p>@endif
                {{ $groups->links('vendor.pagination.custom') }}
            @else
                <div class="domain-empty">
                    <span class="domain-empty-icon"><x-admin.icon name="search" size="30" /></span>
                    <h3>{{ $search !== '' ? 'ไม่พบโดเมนที่มีบัญชีเปิดใช้งานตรงกับคำค้น' : 'ยังไม่มีโดเมนที่มีบัญชีเปิดใช้งานในขณะนี้' }}</h3>
                    <p>ลองตรวจสอบการสะกดหรือค้นหาด้วยชื่อโดเมนบางส่วน<br>หากยื่นคำขอแล้วและยังไม่พบโดเมน กรุณาติดต่อเจ้าหน้าที่สำนักคอมพิวเตอร์</p>
                    @if($search !== '')<a href="{{ route('domains.index') }}" class="domain-reset">ล้างคำค้นและดูโดเมนทั้งหมด <x-admin.icon name="arrow" size="16" /></a>@endif
                </div>
            @endif
        </section>
        <aside class="domain-status-note"><x-admin.icon name="info" size="20" /><div><strong>สถานะนี้หมายถึงอะไร?</strong><p>แสดงโดเมนที่มีบัญชีบริการเปิดใช้งานอย่างน้อยหนึ่งบัญชีและยังไม่หมดอายุ อ้างอิงข้อมูลในระบบของสำนักคอมพิวเตอร์ ไม่ใช่การตรวจสอบว่าเว็บไซต์ออนไลน์อยู่ในขณะนี้</p></div></aside>
    </div>
</main>
@include('partials.public-footer')
<script src="{{ asset('js/public-navigation.js') }}?v={{ filemtime(public_path('js/public-navigation.js')) }}" defer></script>
</body>
</html>
