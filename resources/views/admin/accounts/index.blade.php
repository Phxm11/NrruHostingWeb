@extends('admin.layout')

@section('title', 'บัญชีผู้ใช้บริการ')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'บัญชีผู้ใช้บริการ')

@section('content')

    @if (session('new_username'))
        <div class="banner-credential">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#8a6408" stroke-width="1.8" style="flex-shrink:0;margin-top:2px;">
                <path d="M12 2 3 6v6c0 5 4 9 9 10 5-1 9-5 9-10V6l-9-4Z"/><path d="M9 12l2 2 4-4"/>
            </svg>
            <div>
                <strong>สร้างบัญชีสำเร็จ</strong> — กรุณาคัดลอกและส่งให้ผู้ใช้บริการทันที (จะไม่แสดงรหัสผ่านนี้อีก)<br>
                Username: <code>{{ session('new_username') }}</code> &nbsp;&nbsp; Password: <code>{{ session('new_password') }}</code>
            </div>
        </div>
    @elseif (session('success'))
        <div class="banner-success">{{ session('success') }}</div>
    @endif

    <div class="panel">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">สถานะทั้งหมด</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>ใช้งานอยู่</option>
                    <option value="disabled" {{ request('status') == 'disabled' ? 'selected' : '' }}>ระงับการใช้งาน</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>หมดอายุ</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-soft w-100" type="submit">กรอง</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>ผู้ขอใช้บริการ</th>
                        <th>โดเมน/คำขอ</th>
                        <th>ประเภทบัญชี</th>
                        <th>สถานะ</th>
                        <th>วันหมดอายุ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accounts as $acc)
                        @php
                            $avatarClasses = ['avatar-a', 'avatar-b', 'avatar-c'];
                            $avatarClass = $avatarClasses[ord(mb_substr($acc->applicant->full_name, 0, 1)) % 3];
                            $initial = mb_substr($acc->applicant->full_name, 0, 1);
                        @endphp
                        <tr>
                            <td><code>{{ $acc->username }}</code></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar-circle {{ $avatarClass }}">{{ $initial }}</span>
                                    <div>
                                        {{ $acc->applicant->full_name }}<br>
                                        <span class="text-muted" style="font-size:12px;">{{ $acc->applicant->staff_or_student_id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $acc->serviceRequest->form_no }} — {{ $acc->serviceRequest->domains->pluck('domain_name')->join(', ') ?: '-' }}</td>
                            <td>{{ $acc->account_type }}</td>
                            <td><span class="pill pill-{{ $acc->status }}">{{ $acc->status }}</span></td>
                            <td style="font-size:13px;">
                                {{ $acc->expire_date ? \Carbon\Carbon::parse($acc->expire_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-end">
                                <form action="{{ route('admin.accounts.toggle-status', $acc->account_id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline-soft btn-sm">
                                        {{ $acc->status === 'active' ? 'ระงับการใช้งาน' : 'เปิดใช้งาน' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">ยังไม่มีบัญชีในระบบ</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $accounts->links() }}</div>
    </div>

@endsection
