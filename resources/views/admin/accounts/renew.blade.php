@extends('admin.layout')

@section('title', 'ต่ออายุบริการ')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'ต่ออายุบริการ')

@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ versioned_asset('css/admin/pages/accounts-renew.css') }}">
    @endpush

<div class="row justify-content-center renew-page">
    <div class="col-lg-8">
        <div class="panel mb-3 renew-summary">
            <div class="renew-heading">
                <span class="renew-icon">@include('admin.accounts.partials.renew-icon', ['name' => 'account', 'size' => 18])</span>
                <h2>ข้อมูลบัญชีผู้ใช้บริการ</h2>
            </div>
            <div class="renew-grid">
                <div class="renew-item">
                    <span class="renew-icon">@include('admin.accounts.partials.renew-icon', ['name' => 'user', 'size' => 14])</span>
                    <div><div class="renew-label">ผู้ขอใช้บริการ</div><div class="renew-value">{{ $account->applicant?->full_name ?? '—' }}</div></div>
                </div>
                <div class="renew-item">
                    <span class="renew-icon">@include('admin.accounts.partials.renew-icon', ['name' => 'account', 'size' => 14])</span>
                    <div><div class="renew-label">Username</div><div class="renew-value">{{ $account->username }}</div></div>
                </div>
                <div class="renew-item">
                    <span class="renew-icon">@include('admin.accounts.partials.renew-icon', ['name' => 'calendar', 'size' => 14])</span>
                    <div><div class="renew-label">วันหมดอายุปัจจุบัน</div><div class="renew-value">{{ $account->expire_date ? \Carbon\Carbon::parse($account->expire_date)->format('d/m/Y') : 'ไม่ระบุ' }}</div></div>
                </div>
                <div class="renew-item">
                    <span class="renew-icon">@include('admin.accounts.partials.renew-icon', ['name' => 'info', 'size' => 14])</span>
                    <div><div class="renew-label">สถานะบัญชี</div><span class="pill pill-{{ $account->status }}">{{ ['active' => 'ใช้งานอยู่', 'disabled' => 'ระงับการใช้งาน', 'expired' => 'หมดอายุ'][$account->status] ?? $account->status }}</span></div>
                </div>
            </div>
        </div>
        <div class="panel mb-3">
            <div class="renew-heading">
                <span class="renew-icon">@include('admin.accounts.partials.renew-icon', ['name' => 'renew', 'size' => 18])</span>
                <h2>กำหนดระยะเวลาต่ออายุ</h2>
            </div>
            @if ($account->status === 'disabled')
                <div class="alert alert-warning renew-notice">@include('admin.accounts.partials.renew-icon', ['name' => 'info', 'size' => 16]) <span>บัญชีนี้ถูกระงับ การต่ออายุจะคงสถานะระงับไว้ เจ้าหน้าที่สามารถเปิดใช้งานแยกต่างหากได้</span></div>
            @elseif ($account->status === 'expired')
                <div class="alert alert-success renew-notice">@include('admin.accounts.partials.renew-icon', ['name' => 'info', 'size' => 16]) <span>หลังต่ออายุ บัญชีจะเปลี่ยนเป็นสถานะเปิดใช้งาน</span></div>
            @endif
            <form method="POST" action="{{ route('admin.accounts.renew.store', $account->account_id) }}">
                @csrf
                <input type="hidden" name="previous_expire_date" value="{{ $account->expire_date }}">
                <div class="renew-field">
                    <label for="expire_date"><span class="renew-icon">@include('admin.accounts.partials.renew-icon', ['name' => 'calendar', 'size' => 13])</span>วันหมดอายุใหม่ <span class="text-danger">*</span></label>
                    <input id="expire_date" type="date" name="expire_date" class="form-control @error('expire_date') is-invalid @enderror" required min="{{ $minimumDate }}" value="{{ old('expire_date', $suggestedDate) }}">
                    @error('expire_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @error('previous_expire_date') <div class="text-danger">{{ $message }}</div> @enderror
                    <div class="renew-help">แนะนำต่อเพิ่ม 1 ปีจากวันหมดอายุเดิม หรือจากวันนี้หากหมดอายุแล้ว สามารถเปลี่ยนวันที่ได้ตามต้องการ</div>
                </div>
                <div class="renew-field renew-field--note">
                    <label for="note"><span class="renew-icon">@include('admin.accounts.partials.renew-icon', ['name' => 'note', 'size' => 13])</span>หมายเหตุ / เลขที่เอกสารอ้างอิง</label>
                    <textarea id="note" name="note" class="form-control" rows="3" maxlength="2000">{{ old('note') }}</textarea>
                    @error('note') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="renew-actions">
                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-soft">@include('admin.accounts.partials.renew-icon', ['name' => 'back', 'size' => 14]) ย้อนกลับ</a>
                    <button type="submit" class="btn btn-amber">@include('admin.accounts.partials.renew-icon', ['name' => 'renew', 'size' => 14]) ยืนยันต่ออายุบริการ</button>
                </div>
            </form>
        </div>
        <div class="panel">
            <div class="renew-heading">
                <span class="renew-icon">@include('admin.accounts.partials.renew-icon', ['name' => 'history', 'size' => 18])</span>
                <h2>ประวัติการต่ออายุ</h2>
                <span class="pill pill-draft renew-count">{{ number_format($renewals->total()) }} รายการ</span>
            </div>
            <div class="table-responsive">
                <table class="modern-table">
                    <thead><tr><th>วันที่ดำเนินการ</th><th>วันหมดอายุเดิม</th><th>วันหมดอายุใหม่</th><th>ผู้ดำเนินการ</th><th>หมายเหตุ</th></tr></thead>
                    <tbody>
                    @forelse ($renewals as $renewal)
                        <tr>
                            <td class="renew-date">{{ \Carbon\Carbon::parse($renewal->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="renew-date">{{ $renewal->previous_expire_date ? \Carbon\Carbon::parse($renewal->previous_expire_date)->format('d/m/Y') : 'ไม่ระบุ' }}</td>
                            <td class="renew-date"><span class="pill pill-active">{{ \Carbon\Carbon::parse($renewal->expire_date)->format('d/m/Y') }}</span></td>
                            <td>{{ $renewal->renewed_by_name }}</td>
                            <td class="renew-note">{{ $renewal->note ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="renew-empty"><span class="renew-icon">@include('admin.accounts.partials.renew-icon', ['name' => 'history', 'size' => 20])</span><p>ยังไม่มีประวัติการต่ออายุ</p><p class="renew-help">ประวัติจะแสดงที่นี่หลังจากต่ออายุบริการครั้งแรก</p></div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $renewals->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>
@endsection
