<header class="report-heading">
    <div class="report-document-title">
        <span class="report-document-icon"><x-admin.icon name="reports" size="28" /></span>
        <div><h2>รายงานสรุปการให้บริการ</h2><p>Hosting และ Virtual Server · สำนักคอมพิวเตอร์ มหาวิทยาลัยราชภัฏนครราชสีมา</p></div>
    </div>
    <div class="report-context">
        <span><x-admin.icon name="calendar" size="17" /> {{ $start->format('d/m/').($start->year + 543) }} – {{ $end->format('d/m/').($end->year + 543) }}</span>
        <span><x-admin.icon name="server" size="17" /> {{ empty($filters['service_type']) ? 'ทุกประเภทบริการ' : $serviceLabels[$filters['service_type']] }}</span>
    </div>
    <div class="report-meta"><span>จัดทำเมื่อ {{ $generatedAt->format('d/m/').($generatedAt->year + 543) }} เวลา {{ $generatedAt->format('H:i') }} น. ({{ config('app.timezone') }})</span><span>ผู้จัดทำ {{ auth()->user()->name }}</span></div>
</header>

<section class="report-section report-overview" aria-labelledby="period-heading">
    <div class="report-section-heading"><h3 id="period-heading"><x-admin.icon name="requests" /> ภาพรวมคำขอในช่วงวันที่เลือก</h3></div>
    <p class="report-note">นับตามวันที่ยื่นคำขอ รวมคำขอที่สร้างบัญชีแล้วและข้อมูลนำเข้า โดยแสดงสถานะล่าสุด ณ วันจัดทำรายงาน</p>
    <dl class="report-kpis">
        <div class="report-kpi"><dt><span class="report-kpi-icon"><x-admin.icon name="requests" /></span>คำขอทั้งหมด</dt><dd>{{ number_format($totalRequests) }} <small>รายการ</small></dd></div>
        <div class="report-kpi"><dt><span class="report-kpi-icon"><x-admin.icon name="check" /></span>อนุมัติแล้ว</dt><dd>{{ number_format($statusCounts->get('approved', 0)) }} <small>รายการ</small></dd></div>
        <div class="report-kpi report-kpi--pending"><dt><span class="report-kpi-icon"><x-admin.icon name="clock" /></span>รอพิจารณา</dt><dd>{{ number_format($statusCounts->get('submitted', 0)) }} <small>รายการ</small></dd></div>
        <div class="report-kpi"><dt><span class="report-kpi-icon"><x-admin.icon name="domains" /></span>โดเมนในคำขอ</dt><dd>{{ number_format($domainCount) }} <small>รายการ</small></dd></div>
    </dl>
    @if($totalRequests === 0)
        <div class="report-empty"><x-admin.icon name="search" size="26" /><div><strong>ไม่พบคำขอในช่วงวันที่และประเภทบริการที่เลือก</strong><p>ปรับช่วงวันที่หรือเลือกทุกประเภทบริการเพื่อดูข้อมูลเพิ่มเติม</p></div></div>
    @endif
    <div class="report-grid">
        <section class="report-panel">
            <h3><x-admin.icon name="list" /> สถานะคำขอ</h3>
            <div class="report-table-wrap">
                <table class="report-table">
                    <thead><tr><th scope="col">สถานะล่าสุด</th><th scope="col" class="number">คำขอ (รายการ)</th></tr></thead>
                    <tbody>
                        @foreach($statusLabels as $status => $label)
                            <tr><th scope="row"><span class="report-status report-status--{{ $status }}">{{ $label }}</span></th><td class="number">{{ number_format($statusCounts->get($status, 0)) }}</td></tr>
                        @endforeach
                    </tbody>
                    <tfoot><tr><th scope="row">รวมทั้งหมด</th><td class="number">{{ number_format($totalRequests) }}</td></tr></tfoot>
                </table>
            </div>
        </section>
        <section class="report-panel">
            <h3><x-admin.icon name="server" /> ประเภทบริการ</h3>
            <div class="report-table-wrap">
                <table class="report-table">
                    <thead><tr><th scope="col">บริการ</th><th scope="col" class="number">คำขอ</th><th scope="col" class="number">สัดส่วน</th></tr></thead>
                    <tbody>
                        @foreach($serviceLabels as $type => $label)
                            <tr><th scope="row">{{ $label }}</th><td class="number">{{ number_format($serviceCounts->get($type, 0)) }}</td><td class="number">{{ number_format($totalRequests ? $serviceCounts->get($type, 0) / $totalRequests * 100 : 0, 1) }}%</td></tr>
                        @endforeach
                    </tbody>
                    <tfoot><tr><th scope="row">รวมทั้งหมด</th><td class="number">{{ number_format($totalRequests) }}</td><td class="number">{{ $totalRequests ? '100.0' : '0.0' }}%</td></tr></tfoot>
                </table>
            </div>
            <p class="report-note report-panel-note">สัดส่วนคำนวณจากคำขอทั้งหมดในช่วงวันที่และตัวกรองที่เลือก</p>
        </section>
    </div>
</section>

<div class="report-analysis">
    <section class="report-section report-panel" aria-labelledby="monthly-heading">
        <h3 id="monthly-heading"><x-admin.icon name="chart" /> จำนวนคำขอรายเดือน</h3>
        <p class="report-note">เดือนแรกและเดือนสุดท้ายนับเฉพาะวันที่อยู่ในช่วงที่เลือก</p>
        <div class="report-table-wrap">
            <table class="report-table">
                <thead><tr><th scope="col">เดือน / ปี พ.ศ.</th><th scope="col" class="number">คำขอ</th><th scope="col" class="report-bar-cell">เปรียบเทียบ</th></tr></thead>
                <tbody>
                    @php($maximumMonthlyCount = $months->max('total'))
                    @foreach($months as $month)
                        <tr><th scope="row">{{ $month['label'] }}</th><td class="number">{{ number_format($month['total']) }}</td><td><span class="report-bar-track" aria-hidden="true"><span class="report-bar" style="width: {{ $maximumMonthlyCount ? $month['total'] / $maximumMonthlyCount * 100 : 0 }}%"></span></span></td></tr>
                    @endforeach
                </tbody>
                <tfoot><tr><th scope="row">รวมทั้งช่วง</th><td class="number">{{ number_format($totalRequests) }}</td><td></td></tr></tfoot>
            </table>
        </div>
    </section>
    <section class="report-section report-panel" aria-labelledby="departments-heading">
        <h3 id="departments-heading"><x-admin.icon name="building" /> คำขอแยกตามหน่วยงาน</h3>
        <p class="report-note">เรียงจากจำนวนคำขอมากไปน้อย อ้างอิงหน่วยงานและสังกัดที่ระบุในคำขอแต่ละครั้ง</p>
        <div class="report-table-wrap" role="region" aria-label="ตารางคำขอแยกตามหน่วยงาน" tabindex="0">
            <table class="report-table report-department-table">
                <thead><tr><th scope="col">หน่วยงาน / สังกัด</th><th scope="col" class="number">ทั้งหมด</th><th scope="col" class="number">อนุมัติ</th><th scope="col" class="number">รอพิจารณา</th></tr></thead>
                <tbody>
                    @forelse($departments as $department)
                        <tr><th scope="row" class="report-unit">{{ $department->unit_name ?: 'ไม่ระบุหน่วยงาน' }}<small>{{ $department->affiliation ?: 'ไม่ระบุสังกัด' }}</small></th><td class="number"><strong>{{ number_format($department->total) }}</strong></td><td class="number">{{ number_format($department->approved) }}</td><td class="number">{{ number_format($department->submitted) }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="report-empty-cell">ไม่พบข้อมูลหน่วยงานในช่วงวันที่เลือก</td></tr>
                    @endforelse
                </tbody>
                <tfoot><tr><th scope="row">รวมทั้งหมด</th><td class="number">{{ number_format($totalRequests) }}</td><td class="number">{{ number_format($statusCounts->get('approved', 0)) }}</td><td class="number">{{ number_format($statusCounts->get('submitted', 0)) }}</td></tr></tfoot>
            </table>
        </div>
    </section>
</div>

<section class="report-section report-renewal" aria-labelledby="renewals-heading">
    <span class="report-renewal-icon"><x-admin.icon name="renew" size="24" /></span>
    <div><h3 id="renewals-heading">การต่ออายุในช่วงวันที่เลือก</h3><p>ต่ออายุทั้งหมด <strong>{{ number_format($renewalCount) }} ครั้ง</strong> จาก <strong>{{ number_format($renewedAccountCount) }} บัญชี</strong></p><p class="report-note">นับตามวันที่บันทึกการต่ออายุและประเภทบริการที่เลือก รวมบัญชีจากคำขอที่ยื่นก่อนช่วงรายงาน บัญชีเดียวอาจต่ออายุได้หลายครั้ง</p></div>
</section>

<section class="report-section report-panel" aria-labelledby="accounts-heading">
    <div class="report-section-heading"><h3 id="accounts-heading"><x-admin.icon name="accounts" /> ภาพรวมบัญชีบริการปัจจุบัน</h3><span class="report-current-label"><x-admin.icon name="clock" size="14" /> ณ วันจัดทำรายงาน</span></div>
    <p class="report-note">ทุกบัญชีของประเภทบริการที่เลือก ไม่จำกัดวันที่ยื่นคำขอ รวม {{ number_format($accountSummary->total) }} บัญชี</p>
    <div class="report-grid report-inventory">
        <div class="report-table-wrap">
            <table class="report-table">
                <thead><tr><th scope="col">สถานะบัญชี</th><th scope="col" class="number">จำนวน (บัญชี)</th></tr></thead>
                <tbody>
                    <tr><th scope="row"><span class="report-status report-status--approved">ใช้งานได้</span></th><td class="number">{{ number_format($accountSummary->active ?? 0) }}</td></tr>
                    <tr><th scope="row"><span class="report-status report-status--expired">หมดอายุ</span></th><td class="number">{{ number_format($accountSummary->expired ?? 0) }}</td></tr>
                    <tr><th scope="row"><span class="report-status report-status--draft">ปิดใช้งาน</span></th><td class="number">{{ number_format($accountSummary->disabled ?? 0) }}</td></tr>
                </tbody>
                <tfoot><tr><th scope="row">รวมบัญชีทั้งหมด</th><td class="number">{{ number_format($accountSummary->total) }}</td></tr></tfoot>
            </table>
        </div>
        <div class="report-followup">
            <h4><x-admin.icon name="calendar" size="19" /> บัญชีที่ควรติดตามการต่ออายุ</h4>
            <strong class="report-followup-number">{{ number_format($accountSummary->expiring ?? 0) }} <small>บัญชี</small></strong>
            <p>ใช้งานได้และครบกำหนดตั้งแต่วันนี้ถึง {{ $generatedAt->addDays(30)->format('d/m/').($generatedAt->addDays(30)->year + 543) }} (อีก 30 วัน)</p>
            <p class="report-followup-note">ใช้งานได้แต่ไม่ระบุวันหมดอายุ {{ number_format($accountSummary->no_expiry ?? 0) }} บัญชี</p>
        </div>
    </div>
    <p class="report-note report-panel-note">บัญชีที่พ้นวันหมดอายุถือว่าหมดอายุ แม้สถานะที่บันทึกยังเป็นใช้งานได้ โดยบัญชีที่ปิดใช้งานนับในกลุ่มปิดใช้งานเท่านั้น</p>
</section>
<footer class="report-note report-footnote"><x-admin.icon name="info" size="17" /><span>แหล่งข้อมูล: ระบบจัดการคำขอใช้บริการ สำนักคอมพิวเตอร์ · รายงานอ้างอิงข้อมูลที่ยังอยู่ในระบบ ณ เวลาจัดทำ จำนวนโดเมนเป็นรายการที่ระบุในคำขอ ไม่ใช่จำนวนโดเมนที่เปิดใช้งานจริง</span></footer>
