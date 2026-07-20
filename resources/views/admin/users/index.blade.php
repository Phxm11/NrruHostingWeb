@extends('admin.layout')

@section('title', 'จัดการผู้ใช้')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'จัดการผู้ใช้เจ้าหน้าที่')

@section('topbar-action')
    <a href="{{ route('admin.users.create') }}" class="btn btn-amber btn-sm">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
        เพิ่มผู้ใช้
    </a>
@endsection

@section('content')

    @if (session('success'))
        <div class="banner-success" style="display:flex;align-items:center;gap:8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="banner-danger" style="display:flex;align-items:center;gap:8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.6 1.9 18a2 2 0 0 0 1.7 3h16.8a2 2 0 0 0 1.7-3L14.7 3.6a2 2 0 0 0-3.4 0Z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="panel">
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
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-soft btn-sm">
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
                                    <p>ยังไม่มีบัญชีผู้ใช้ในระบบ</p>
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
