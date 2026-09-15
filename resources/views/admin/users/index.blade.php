@extends('admin.layout')

@section('title', 'จัดการผู้ใช้')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'จัดการผู้ใช้เจ้าหน้าที่')

@section('topbar-action')
    <a href="{{ url('admin/users/create') }}" class="btn btn-amber btn-sm">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
        เพิ่มผู้ใช้
    </a>
@endsection

@section('content')


    <div class="stat-row">
        <div class="stat-card stat-card--total">
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="stat-number">{{ $statusCounts['all'] }}</div>
                <div class="stat-label">ผู้ใช้ทั้งหมด</div>
            </div>
        </div>
        <div class="stat-card stat-card--active">
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <div>
                <div class="stat-number">{{ $statusCounts['active'] }}</div>
                <div class="stat-label">ใช้งานอยู่</div>
            </div>
        </div>
        <div class="stat-card stat-card--disabled">
            <div class="stat-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>
            </div>
            <div>
                <div class="stat-number">{{ $statusCounts['disabled'] }}</div>
                <div class="stat-label">ปิดการใช้งาน</div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="list-heading"><div><h2>รายชื่อเจ้าหน้าที่</h2><p>บัญชีเจ้าหน้าที่ที่เข้าใช้งานระบบ ADMIN</p></div><span class="list-total">{{ number_format($users->total()) }} รายการ</span></div>

        {{-- ============================================================
             Filters: status as segmented links (real GET navigation,
             counts computed server-side in the controller) + search.
        ============================================================ --}}
        <div class="filter-row">
            <div class="segmented">
                @php
                    $currentStatus = request('status');
                    $qp = ['q' => request('q')];
                @endphp
                <a href="{{ route('admin.users.index', array_filter($qp)) }}" class="seg-btn {{ ! $currentStatus ? 'is-active' : '' }}">
                    ทั้งหมด <span class="count">{{ $statusCounts['all'] }}</span>
                </a>
                <a href="{{ route('admin.users.index', array_filter(array_merge($qp, ['status' => 'active']))) }}" class="seg-btn {{ $currentStatus === 'active' ? 'is-active' : '' }}">
                    ใช้งานอยู่ <span class="count">{{ $statusCounts['active'] }}</span>
                </a>
                <a href="{{ route('admin.users.index', array_filter(array_merge($qp, ['status' => 'disabled']))) }}" class="seg-btn {{ $currentStatus === 'disabled' ? 'is-active' : '' }}">
                    ปิดการใช้งาน <span class="count">{{ $statusCounts['disabled'] }}</span>
                </a>
            </div>

            <form method="GET" class="search-wrap">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                <input aria-label="ค้นหาข้อมูล" type="text" name="q" placeholder="ค้นหาชื่อ หรืออีเมล..." value="{{ request('q') }}">
                @if ($currentStatus) <input type="hidden" name="status" value="{{ $currentStatus }}"> @endif
            <button type="submit" class="btn btn-outline-soft">ค้นหา</button>
            </form>

            @if (request('q') || $currentStatus)
                <a href="{{ route('admin.users.index') }}" class="filter-clear">ล้างตัวกรอง ✕</a>
            @endif
        </div>

        <p class="table-hint"><x-admin.icon name="list" size="15" /> เลื่อนตารางในแนวนอนเพื่อดูข้อมูลครบทุกคอลัมน์</p>
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th scope="col">ชื่อผู้ใช้</th>
                        <th scope="col">อีเมล</th>
                        <th scope="col">สถานะ</th>
                        <th scope="col">สร้างเมื่อ</th>
                        <th scope="col" class="text-end">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar-circle avatar-a">{{ mb_substr($user->name, 0, 1) }}</span>
                                    <div>
                                        {{ $user->name }}
                                        @if ($user->id === auth()->id())
                                            <span class="badge text-bg-light ms-1" style="font-size:11px;">(คุณ)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if ($user->is_active)
                                    <span class="pill pill-active">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                                        ใช้งานอยู่
                                    </span>
                                @else
                                    <span class="pill pill-disabled">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>
                                        ปิดการใช้งาน
                                    </span>
                                @endif
                            </td>
                            <td style="font-size:13px;">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ url('admin/users/' . $user->id . '/edit') }}" class="btn btn-outline-soft btn-sm">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                        แก้ไข
                                    </a>
                                    @if ($user->id === auth()->id())
                                        <span class="btn btn-outline-soft btn-sm" style="opacity:.6;cursor:not-allowed;">บัญชีนี้</span>
                                    @else
                                        <form action="{{ route('admin.users.toggle-active', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-danger-soft btn-sm" title="{{ $user->is_active ? 'ปิดการใช้งาน' : 'เปิดการใช้งาน' }}">
                                                @if ($user->is_active)
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.6 1.9 18a2 2 0 0 0 1.7 3h16.8a2 2 0 0 0 1.7-3L14.7 3.6a2 2 0 0 0-3.4 0Z"/></svg>
                                                    ปิดใช้งาน
                                                @else
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                                                    เปิดใช้งาน
                                                @endif
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <span class="empty-icon-wrap">
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                    </span>
                                    <p>ยังไม่มีผู้ใช้ในระบบ{{ (request('q') || request('status')) ? 'ที่ตรงกับตัวกรอง' : '' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $users->appends(request()->query())->links() }}</div>
    </div>

@endsection