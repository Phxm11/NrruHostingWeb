@extends('admin.layout')

@section('title', 'ต่ออายุบริการ')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'ต่ออายุบริการ')

@section('content')
<style>
    .renew-page .renew-summary { background: linear-gradient(120deg, var(--moss-light), #fff); }
    .renew-page .renew-heading { display: flex; align-items: center; gap: 11px; margin-bottom: 18px; }
    .renew-page .renew-heading h2 { font-size: 15px; font-weight: 600; margin: 0; }
    .renew-page .renew-icon { width: 38px; height: 38px; border-radius: 11px; flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center; background: var(--moss-light); color: var(--moss); }
    .renew-page .renew-summary .renew-heading > .renew-icon { background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,.08); }
    .renew-page .renew-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px 20px; }
    .renew-page .renew-item { display: flex; align-items: flex-start; gap: 10px; min-width: 0; }
    .renew-page .renew-item .renew-icon { width: 28px; height: 28px; border-radius: 8px; background: rgba(255,255,255,.7); }
    .renew-page .renew-label { font-size: 12px; color: var(--ink-soft); margin-bottom: 3px; }
    .renew-page .renew-value { font-size: 14px; font-weight: 500; overflow-wrap: anywhere; }
    .renew-page .renew-field { margin-bottom: 22px; }
    .renew-page .renew-field label { display: flex; align-items: center; gap: 7px; font-size: 14px; font-weight: 600; margin-bottom: 6px; }
    .renew-page .renew-field .renew-icon { width: 22px; height: 22px; border-radius: 6px; background: #e3efe7; color: #2f6b4a; }
    .renew-page .renew-field--note .renew-icon { background: var(--amber-light); color: var(--amber-deep); }
    .renew-page .renew-help { font-size: 12.5px; color: var(--ink-soft); margin-top: 7px; }
    .renew-page .renew-notice { display: flex; align-items: flex-start; gap: 9px; font-size: 13px; }
    .renew-page .renew-notice svg { flex-shrink: 0; margin-top: 2px; }
    .renew-page .renew-actions { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .renew-page .renew-actions .btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; }
    .renew-page .renew-count { margin-left: auto; white-space: nowrap; }
    .renew-page .renew-date { white-space: nowrap; }
    .renew-page .renew-note { min-width: 140px; max-width: 300px; white-space: pre-wrap; overflow-wrap: anywhere; }
    .renew-page .renew-empty { text-align: center; padding: 32px 14px; color: var(--ink-soft); }
    .renew-page .renew-empty .renew-icon { margin-bottom: 10px; }
    .renew-page .renew-empty p { margin: 0; font-size: 13px; }
    @media (max-width: 640px) {
        .renew-page .renew-grid { grid-template-columns: 1fr; }
        .renew-page .renew-actions .btn { width: 100%; }
    }
</style>
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
