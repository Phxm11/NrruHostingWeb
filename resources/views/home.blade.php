<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>บริการ Data Center และ Web Hosting — สำนักคอมพิวเตอร์ มหาวิทยาลัยราชภัฏนครราชสีมา</title>
    @include('partials.site-icons')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600;700&family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ versioned_asset('css/public-site.css') }}" rel="stylesheet">
    <link href="{{ versioned_asset('css/motion.css') }}" rel="stylesheet">
</head>
<body>
<a class="skip-link" href="#mainContent">ข้ามไปยังเนื้อหา</a>
@include('partials.public-navigation')
<main id="mainContent" tabindex="-1">
    <header class="hero" id="top">
        <div class="container hero-layout">
            <div>
                <div class="hero-context"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="3" width="18" height="7" rx="2"/><rect x="3" y="14" width="18" height="7" rx="2"/><path d="M7 6.5h.01M7 17.5h.01M11 6.5h6M11 17.5h6"/></svg> Data Center &amp; Web Hosting</div>
                <h2>ระบบการจัดการคำขอใช้บริการ<br>Web Hosting และ Virtual Server</h2>
                <p class="lead">ขอใช้บริการ Web Hosting และเครื่องแม่ข่ายเสมือนสำหรับบุคลากรและหน่วยงาน มหาวิทยาลัยราชภัฏนครราชสีมา ผ่านแบบฟอร์มออนไลน์ในที่เดียว</p>
                <div class="hero-cta">
                    <a href="{{ route('domains.index') }}" class="btn-outline-brand"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="m15 15 6 6"/></svg> ตรวจสอบโดเมนของคุณ</a>
                    <a href="{{ route('service-requests.create') }}" class="btn-amber"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m15 5 4 4M4 20l4-1L21 6a2.8 2.8 0 0 0-4-4L4 15ZM3 22h18"/></svg> ยื่นคำขอใช้บริการ</a>
                    <a href="#steps" class="btn-outline-brand">ดูขั้นตอนการขอใช้ <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a>
                </div>
                <p class="hero-note"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m12 3 8 4v5c0 5-8 9-8 9s-8-4-8-9V7Z"/><path d="m8 12 3 3 5-6"/></svg> พิจารณาคำขอและดูแลบริการโดยสำนักคอมพิวเตอร์</p>
            </div>
            <section class="service-directory" id="services" aria-labelledby="servicesTitle">
                <div class="directory-head"><h2 id="servicesTitle">เลือกบริการให้เหมาะกับงาน</h2><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="3" width="18" height="7" rx="2"/><rect x="3" y="14" width="18" height="7" rx="2"/><path d="M7 6.5h.01M7 17.5h.01M11 6.5h6M11 17.5h6"/></svg></div>
                <div class="service-row">
                    <span class="service-icon"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><ellipse cx="12" cy="12" rx="4" ry="9"/><path d="M3 12h18"/></svg></span>
                    <div><h3>Web Hosting</h3><p>พื้นที่สำหรับเว็บไซต์ของหน่วยงาน โครงการ และงานประชาสัมพันธ์</p></div>
                </div>
                <div class="service-row">
                    <span class="service-icon"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="3" width="18" height="7" rx="2"/><rect x="3" y="14" width="18" height="7" rx="2"/><path d="M7 6.5h.01M7 17.5h.01M11 6.5h6M11 17.5h6"/></svg></span>
                    <div><h3>Virtual Server</h3><p>เครื่องแม่ข่ายเสมือนสำหรับระบบงานที่ต้องการระบุ CPU, RAM และพื้นที่จัดเก็บ</p></div>
                </div>
                <a href="{{ route('service-requests.create') }}" class="directory-link"><span>ระบุบริการและทรัพยากรในแบบฟอร์ม</span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a>
            </section>
        </div>
    </header>
    <!--<div class="purpose-strip">
        <div class="container purpose-inner">
            <strong>รองรับการใช้งานเพื่อ</strong>
            <span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m5 12 4 4L19 6"/></svg> การเรียนการสอน</span>
            <span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m5 12 4 4L19 6"/></svg> งานวิจัย</span>
            <span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m5 12 4 4L19 6"/></svg> บริการวิชาการ</span>
            <span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m5 12 4 4L19 6"/></svg> งานบริหารหน่วยงาน</span>
        </div>
    </div>!-->
    <section id="steps">
        <div class="container">
            <div class="section-heading">
                <div><h2 class="section-title">ขั้นตอนการขอใช้บริการ</h2><p class="section-desc">เตรียมข้อมูลให้ครบ แล้วส่งคำขอให้เจ้าหน้าที่พิจารณา</p></div>
                <a href="{{ route('service-requests.create') }}" class="section-link">ไปที่แบบฟอร์ม <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a>
            </div>
            <div class="steps-grid">
                <div class="step-item"><div class="step-top"><span class="step-num">1</span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m15 5 4 4M4 20l4-1L21 6a2.8 2.8 0 0 0-4-4L4 15ZM3 22h18"/></svg></div><div class="step-title">กรอกแบบฟอร์ม</div><div class="step-desc">ระบุข้อมูลผู้ขอ วัตถุประสงค์ และทรัพยากรที่ต้องการใช้งาน</div></div>
                <div class="step-item"><div class="step-top"><span class="step-num">2</span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8ZM14 2v6h6M8 13h8M8 17h5"/></svg></div><div class="step-title">แนบเอกสาร</div><div class="step-desc">เตรียมรายละเอียดระบบหรือโครงสร้างระบบ พร้อมรูปลายเซ็นผู้ขอ</div></div>
                <div class="step-item"><div class="step-top"><span class="step-num">3</span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m12 3 8 4v5c0 5-8 9-8 9s-8-4-8-9V7Z"/><path d="m8 12 3 3 5-6"/></svg></div><div class="step-title">รอเจ้าหน้าที่พิจารณา</div><div class="step-desc">เจ้าหน้าที่ตรวจสอบข้อมูลและพิจารณาอนุมัติคำขอใช้บริการ</div></div>
                <div class="step-item"><div class="step-top"><span class="step-num">4</span><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="8" cy="8" r="5"/><path d="m12 12 9 9m-4-4 3-3m-6 0 3-3"/></svg></div><div class="step-title">รับข้อมูลบัญชีบริการ</div><div class="step-desc">เจ้าหน้าที่จัดการบัญชีบริการหลังคำขอได้รับการอนุมัติ</div></div>
            </div>
        </div>
    </section>
    <section id="policy" class="policy-section">
        <div class="container policy-layout">
            <div>
                <h2 class="section-title">ก่อนเริ่มใช้บริการ</h2>
                <p class="section-desc">อ่านข้อกำหนดและเตรียมเอกสารประกอบคำขอ เพื่อให้เจ้าหน้าที่ตรวจสอบข้อมูลได้ครบถ้วน</p>
                <div class="prepare-note">
                    <strong>สิ่งที่ต้องเตรียม</strong>
                    <p><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/></svg> ข้อมูลผู้ขอและผู้รับผิดชอบระบบ</p>
                    <p><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8ZM14 2v6h6M8 13h8M8 17h5"/></svg> เอกสารรายละเอียดหรือโครงสร้างระบบ</p>
                    <p><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m15 5 4 4M4 20l4-1L21 6a2.8 2.8 0 0 0-4-4L4 15ZM3 22h18"/></svg> รูปลายเซ็นผู้ขอใช้บริการ</p>
                </div>
            </div>
            <ul class="policy-list">
                    <li><span class="check"><svg aria-hidden="true" focusable="false" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg></span> ปฏิบัติตาม พ.ร.บ. ว่าด้วยการกระทำความผิดเกี่ยวกับคอมพิวเตอร์ และ พ.ร.บ. การรักษาความมั่นคงปลอดภัยไซเบอร์</li>
                    <li><span class="check"><svg aria-hidden="true" focusable="false" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg></span> ปฏิบัติตามนโยบายคุ้มครองข้อมูลส่วนบุคคล (PDPA) ของมหาวิทยาลัย</li>
                    <li><span class="check"><svg aria-hidden="true" focusable="false" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg></span> ผู้ใช้บริการมีหน้าที่สำรองข้อมูล (Backup) และดูแลความปลอดภัยระบบของตนเองอย่างสม่ำเสมอ</li>
                    <li><span class="check"><svg aria-hidden="true" focusable="false" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg></span> ระยะเวลาการใช้งานไม่เกิน 1 ปีต่อครั้ง สามารถยื่นขอต่ออายุได้ก่อนวันหมดอายุ</li>
                    <li><span class="check"><svg aria-hidden="true" focusable="false" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg></span> สำนักคอมพิวเตอร์มีสิทธิ์ระงับหรือยกเลิกบริการทันทีหากพบการใช้งานที่กระทบต่อระบบส่วนรวม</li>
                </ul>
        </div>
    </section>
    <section class="cta-section">
        <div class="container cta-band">
            <div><h2 class="section-title">เริ่มต้นคำขอใช้บริการของคุณ</h2><p class="section-desc">กรอกข้อมูล เลือกบริการ และแนบเอกสารผ่านแบบฟอร์มออนไลน์</p></div>
            <a href="{{ route('service-requests.create') }}" class="btn-request">ยื่นคำขอใช้บริการ <svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 12h16m-6-6 6 6-6 6"/></svg></a>
        </div>
    </section>
</main>
@include('partials.public-footer')
<script src="{{ versioned_asset('js/public-navigation.js') }}" defer></script>
<script src="{{ versioned_asset('js/motion.js') }}" defer></script>
</body>
</html>
