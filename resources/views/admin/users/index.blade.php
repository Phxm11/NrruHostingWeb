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

    <style>
        /* ---------- distinct color per stat card ---------- */
        .stat-card--total    .stat-icon { background: linear-gradient(135deg, #e3efe7, #d3e6d8); color: var(--forest); }
        .stat-card--active   .stat-icon { background: linear-gradient(135deg, var(--moss-light), #cfe3b7); color: var(--forest); }
        .stat-card--active   .stat-number { color: var(--forest); }
        .stat-card--disabled .stat-icon { background: #eeeadc; color: var(--ink-soft); }

        /* ---------- filters: segmented status + search ---------- */
        .filter-row { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; margin-bottom: 18px; }
        .segmented { display: inline-flex; background: var(--surface); border: 1px solid var(--line); border-radius: 10px; padding: 3px; gap: 2px; }
        .seg-btn {
            display: flex; align-items: center; gap: 7px; text-decoration: none;
            padding: 8px 14px; border-radius: 8px; font-size: 13.5px; font-weight: 500;
            color: var(--ink-soft); white-space: nowrap; transition: background .15s, color .15s;
        }
        .seg-btn .count {
            font-family: 'Kanit', sans-serif; font-size: 12px; font-weight: 600; padding: 1px 7px;
            border-radius: 999px; background: var(--moss-light); color: var(--ink-soft);
        }
        .seg-btn.is-active { background: var(--forest); color: #fff; }
        .seg-btn.is-active .count { background: rgba(255,255,255,.18); color: #fff; }
        .seg-btn:not(.is-active):hover { background: var(--moss-light); color: var(--ink); }

        .search-wrap { position: relative; flex: 1 1 220px; max-width: 320px; }
        .search-wrap svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--ink-soft); opacity: .6; pointer-events: none; }
        .search-wrap input {
            width: 100%; padding: 9px 12px 9px 36px; border-radius: 10px; border: 1px solid var(--line); font-size: 13.5px;
        }
        .search-wrap input:focus { outline: none; border-color: var(--moss); box-shadow: 0 0 0 3px var(--moss-light); }

        .filter-clear { font-size: 13px; color: #888; text-decoration: none; }
        .filter-clear:hover { color: var(--rust); }
    </style>

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
                <input type="text" name="q" placeholder="ค้นหาชื่อ หรืออีเมล..." value="{{ request('q') }}">
                @if ($currentStatus) <input type="hidden" name="status" value="{{ $currentStatus }}"> @endif
            </form>

            @if (request('q') || $currentStatus)
                <a href="{{ route('admin.users.index') }}" class="filter-clear">ล้างตัวกรอง ✕</a>
            @endif
        </div>

        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>ชื่อผู้ใช้</th>
                        <th>อีเมล</th>
                        <th>สถานะ</th>
                        <th>สร้างเมื่อ</th>
                        <th></th>
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