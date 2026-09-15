@extends('admin.layout')

@section('title', 'รายงานผู้บริหาร')
@section('page-title', 'รายงานผู้บริหาร')

@section('topbar-action')
    <a class="btn btn-amber" href="{{ route('admin.reports.print', $filters) }}" target="_blank" rel="noopener"><x-admin.icon name="print" size="18" /> พิมพ์ / บันทึก PDF</a>
@endsection

@push('styles')
    @include('admin.reports.styles')
@endpush

@section('content')
<div class="report">
    <form method="GET" action="{{ route('admin.reports.index') }}" class="report-filters">
        <div class="report-filter-heading"><h2><x-admin.icon name="filter" /> กำหนดข้อมูลรายงาน</h2><span>เลือกช่วงวันที่และประเภทบริการ แล้วกดแสดงรายงาน</span></div>
        <div class="report-filter-grid">
            <div>
                <label for="start_date"><x-admin.icon name="calendar" size="15" /> วันที่เริ่มต้น</label>
                <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $filters['start_date']) }}" class="form-control" min="1900-01-01" max="2100-12-31" required>
                @error('start_date')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="end_date"><x-admin.icon name="calendar" size="15" /> วันที่สิ้นสุด</label>
                <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $filters['end_date']) }}" class="form-control" min="1900-01-01" max="2100-12-31" required>
                @error('end_date')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="service_type"><x-admin.icon name="server" size="15" /> ประเภทบริการ</label>
                <select id="service_type" name="service_type" class="form-select">
                    <option value="">ทุกประเภทบริการ</option>
                    @foreach($serviceLabels as $type => $label)
                        @if($type !== '')<option value="{{ $type }}" @selected(old('service_type', $filters['service_type'] ?? '') === $type)>{{ $label }}</option>@endif
                    @endforeach
                </select>
                @error('service_type')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-brand"><x-admin.icon name="search" size="17" /> แสดงรายงาน</button>
        </div>
        <div class="report-actions mt-3">
            <span class="report-note">ช่วงด่วน:</span>
            <button type="button" class="report-range" data-report-start="{{ today()->startOfMonth()->toDateString() }}" data-report-end="{{ today()->toDateString() }}">เดือนนี้</button>
            <button type="button" class="report-range" data-report-start="{{ today()->startOfYear()->toDateString() }}" data-report-end="{{ today()->toDateString() }}">ปีนี้</button>
            <a class="report-reset" href="{{ route('admin.reports.index') }}"><x-admin.icon name="renew" size="14" /> ล้างตัวกรอง</a>
            <span class="report-export-note">PDF ใช้ตัวกรองที่แสดงผลแล้ว</span>
        </div>
    </form>

    @include('admin.reports.summary')

    <details class="report-detail-disclosure" @if(request()->integer('page', 1) > 1) open @endif>
        <summary id="details-heading"><span><x-admin.icon name="list" /> รายการคำขอประกอบรายงาน <small>{{ number_format($serviceRequests->total()) }} รายการ</small></span><x-admin.icon name="chevron" size="18" /></summary>
        <section class="report-details" aria-labelledby="details-heading">
        <p class="report-note">ใช้ตรวจสอบข้อมูลในระบบ ส่วนหน้าพิมพ์จะแสดงรายงานสรุปสำหรับผู้บริหาร <span class="report-mobile-hint">เลื่อนตารางในแนวนอนเพื่อดูข้อมูลครบทุกคอลัมน์</span></p>
        <div class="report-table-wrap" role="region" aria-labelledby="details-heading" tabindex="0">
            <table class="report-table report-detail-table">
                <thead><tr><th scope="col">เลขที่คำขอ</th><th scope="col">วันที่ยื่น</th><th scope="col">หน่วยงาน</th><th scope="col">บริการ</th><th scope="col">สถานะล่าสุด</th></tr></thead>
                <tbody>
                    @forelse($serviceRequests as $serviceRequest)
                        <tr>
                            <td><a href="{{ route('admin.requests.show', $serviceRequest) }}">{{ $serviceRequest->form_no }}</a></td>
                            <td>{{ $serviceRequest->request_date->format('d/m/').($serviceRequest->request_date->year + 543) }}</td>
                            <td class="report-unit">{{ $serviceRequest->applicant?->unit_name ?: 'ไม่ระบุหน่วยงาน' }}<small>{{ $serviceRequest->applicant?->affiliation }}</small></td>
                            <td>{{ $serviceLabels[$serviceRequest->service_type ?? ''] ?? 'ไม่ระบุประเภท' }}</td>
                            <td><span class="report-status report-status--{{ $serviceRequest->status }}">{{ $statusLabels[$serviceRequest->status] ?? $serviceRequest->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5">ไม่พบคำขอตามตัวกรองที่เลือก</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $serviceRequests->links('vendor.pagination.custom') }}</div>
        </section>
    </details>
</div>
<script>
    document.querySelectorAll('[data-report-start]').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('start_date').value = button.dataset.reportStart;
            document.getElementById('end_date').value = button.dataset.reportEnd;
            document.getElementById('start_date').focus();
        });
    });
</script>
@endsection
