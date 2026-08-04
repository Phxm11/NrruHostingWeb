@extends('admin.layout')

@section('title', 'จัดการบทบาท')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'จัดการบทบาท')

@section('topbar-action')
    <a href="{{ route('admin.roles.create') }}" class="btn btn-amber btn-sm">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
        สร้างบทบาท
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
                        <th>บทบาท</th>
                        <th>คำอธิบาย</th>
                        <th>จำนวนผู้ใช้</th>
                        <th>จำนวนสิทธิ์</th>
                        <th>ระบบ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td>
                                <span class="fw-medium">{{ $role->label }}</span>
                                <br><small class="text-muted">{{ $role->name }}</small>
                            </td>
                            <td>{{ $role->description ?? '-' }}</td>
                            <td>{{ $role->users_count }}</td>
                            <td>{{ $role->permissions_count }}</td>
                            <td>
                                @if ($role->is_system)
                                    <span class="badge text-bg-secondary">ใช่</span>
                                @else
                                    <span class="badge text-bg-light">ไม่ใช่</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('admin.roles.edit', $role->role_id) }}" class="btn btn-outline-soft btn-sm">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                        แก้ไข
                                    </a>
                                    @if (! $role->is_system)
                                        <form action="{{ route('admin.roles.destroy', $role->role_id) }}" method="POST" onsubmit="return confirm('ต้องการลบบทบาท {{ $role->label }} หรือไม่?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger-soft btn-sm" title="ลบบทบาท">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                ลบ
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <span class="empty-icon-wrap">
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                    </span>
                                    <p>ยังไม่มีบทบาทในระบบ</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
