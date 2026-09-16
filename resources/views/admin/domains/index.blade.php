@extends('admin.layout')

@section('title', 'โดเมนผู้ใช้บริการ')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'โดเมนผู้ใช้บริการ')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ versioned_asset('css/admin/pages/domains-index.css') }}">
    @endpush

    <div class="dm-stats">
        <div class="dm-stat dm-stat--total">
            <span class="dm-stat__icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"/></svg>
            </span>
            <div>
                <div class="dm-stat__num">{{ $domainCounts['linked'] + $domainCounts['pending'] }}</div>
                <div class="dm-stat__label">โดเมนทั้งหมด</div>
            </div>
        </div>
        <div class="dm-stat dm-stat--linked">
            <span class="dm-stat__icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg>
            </span>
            <div>
                <div class="dm-stat__num">{{ $domainCounts['linked'] }}</div>
                <div class="dm-stat__label">มีบัญชีแล้ว</div>
            </div>
        </div>
        <div class="dm-stat dm-stat--pending">
            <span class="dm-stat__icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
            </span>
            <div>
                <div class="dm-stat__num">{{ $domainCounts['pending'] }}</div>
                <div class="dm-stat__label">รอสร้างบัญชี</div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="list-heading"><div><h2>ทะเบียนโดเมน</h2><p>ชื่อโดเมน เครื่อง Server หน่วยงาน และบัญชีบริการที่เกี่ยวข้อง</p></div><span class="list-total">{{ number_format($domains->total()) }} รายการ</span></div>

        <form method="GET" class="dm-toolbar">
            <input aria-label="ค้นหาโดเมน ผู้ขอใช้บริการ หรือ Server" type="text" name="q" value="{{ request('q') }}" placeholder="ค้นหาโดเมน, ผู้ใช้ หรือ Server...">

            <select aria-label="กรองตามการสร้างบัญชี" name="has_account" onchange="this.form.submit()">
                <option value="">ทั้งหมด</option>
                <option value="yes" {{ request('has_account') == 'yes' ? 'selected' : '' }}>มีบัญชีแล้ว</option>
                <option value="no"  {{ request('has_account') == 'no'  ? 'selected' : '' }}>ยังไม่มีบัญชี</option>
            </select>

            <select name="sort" aria-label="เรียงลำดับโดเมน">
                <option value="newest" @selected(request('sort', 'newest') === 'newest')>ล่าสุดก่อน</option>
                <option value="name_asc" @selected(request('sort') === 'name_asc')>ชื่อโดเมน A–Z</option>
                <option value="name_desc" @selected(request('sort') === 'name_desc')>ชื่อโดเมน Z–A</option>
            </select>
            <button class="btn btn-outline-soft" type="submit"><x-admin.icon name="search" size="16" /> ค้นหา</button>

            @if (request('q') || request('has_account'))
                <a href="{{ route('admin.domains.index') }}" class="dm-clear">ล้างตัวกรอง ✕</a>
            @endif
        </form>

        <p class="table-hint"><x-admin.icon name="list" size="15" /> เลื่อนตารางในแนวนอนเพื่อดูข้อมูลครบทุกคอลัมน์</p>
        <div class="table-responsive">
            <table class="dm-table dm-table--compact">
                <colgroup><col class="dm-col-domain"><col class="dm-col-server"><col class="dm-col-owner"><col class="dm-col-account"><col class="dm-col-actions"></colgroup>
                <thead>
                    <tr>
                        <th scope="col">โดเมน / คำขอ</th>
                        <th scope="col"><x-admin.icon name="server" size="16" /> Server</th>
                        <th scope="col">ผู้ขอใช้บริการ / หน่วยงาน</th>
                        <th scope="col">บัญชี (username)</th>
                        <th scope="col" class="text-end">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($domains as $domain)
                        @php $accounts = $domain->serviceRequest->serviceAccounts; @endphp
                        <tr>
                            <td data-label="ชื่อโดเมน">
                                <a href="https://{{ $domain->domain_name }}" target="_blank" rel="noopener noreferrer" class="dm-domain__link" title="เปิดเว็บไซต์ {{ $domain->domain_name }} ในแท็บใหม่">
                                    <code>{{ $domain->domain_name }}</code>
                                    <svg class="dm-domain__external" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14 21 3"/></svg>
                                </a>
                                <a class="dm-secondary" href="{{ route('admin.requests.show', $domain->request_id) }}">{{ $domain->serviceRequest->form_no }}</a>
                            </td>
                            <td data-label="Server">
                                <span class="dm-server {{ $domain->server_name ? '' : 'text-muted' }}">{{ $domain->server_name ?: '—' }}</span>
                            </td>
                            <td data-label="ผู้ขอใช้บริการ">
                                <div class="dm-owner-name">{{ $domain->serviceRequest->applicant->full_name }}</div>
                                <span class="dm-secondary dm-owner-meta" title="{{ $domain->serviceRequest->applicant->staff_or_student_id }} · {{ $domain->departmentCode?->department_name ?? $domain->department_other ?? '-' }}">{{ $domain->serviceRequest->applicant->staff_or_student_id }} · {{ $domain->departmentCode?->department_name ?? $domain->department_other ?? '-' }}</span>
                            </td>
                            <td data-label="บัญชี (username)">
                                @forelse ($accounts as $acc)
                                    <span class="dm-badge {{ $acc->status === 'disabled' ? 'dm-badge--disabled' : ($acc->status === 'expired' ? 'dm-badge--expired' : '') }}">
                                        {{ $acc->username }}
                                    </span>
                                @empty
                                    <span class="dm-badge dm-badge--none">ยังไม่มีบัญชี</span>
                                @endforelse
                            </td>
                            <td data-label="">
                                <div class="dm-actions">
                                    <a href="{{ route('admin.domains.show', $domain->domain_id) }}" class="dm-btn" title="ดูรายละเอียด" aria-label="ดูรายละเอียด {{ $domain->domain_name }}">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a href="{{ route('admin.domains.edit', $domain->domain_id) }}" class="dm-btn" title="แก้ไขโดเมน" aria-label="แก้ไข {{ $domain->domain_name }}">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.domains.destroy', $domain->domain_id) }}" method="POST"
                                          data-confirm="ยืนยันลบโดเมน {{ $domain->domain_name }}? การลบไม่สามารถย้อนกลับได้">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dm-btn dm-btn--danger" title="ลบโดเมน" aria-label="ลบ {{ $domain->domain_name }}">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="dm-empty">
                                    ยังไม่มีโดเมนในระบบ{{ (request('q') || request('has_account')) ? 'ที่ตรงกับตัวกรอง' : '' }}
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $domains->appends(request()->query())->links() }}</div>
    </div>

@endsection
