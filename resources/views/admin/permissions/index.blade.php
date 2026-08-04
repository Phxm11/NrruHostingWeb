@extends('admin.layout')

@section('title', 'จัดการสิทธิ์')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'จัดการสิทธิ์')

@section('topbar-action')
    <a href="{{ route('admin.permissions.create') }}" class="btn btn-amber btn-sm">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
        สร้างสิทธิ์
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
        @if ($groups->isNotEmpty())
            @foreach ($groups as $group)
                @php
                    $groupPerms = $permissions->where('group', $group);
                @endphp
                <h3 class="h6 fw-medium mb-3 mt-4">{{ $group }}</h3>
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>ชื่อสิทธิ์</th>
                                <th>ชื่อแสดงผล</th>
                                <th>คำอธิบาย</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupPerms as $perm)
                                <tr>
                                    <td><code>{{ $perm->name }}</code></td>
                                    <td>{{ $perm->label }}</td>
                                    <td>{{ $perm->description ?? '-' }}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('admin.permissions.edit', $perm->permission_id) }}" class="btn btn-outline-soft btn-sm">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                                แก้ไข
                                            </a>
                                            <form action="{{ route('admin.permissions.destroy', $perm->permission_id) }}" method="POST" onsubmit="return confirm('ต้องการลบสิทธิ์ {{ $perm->label }} หรือไม่?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger-soft btn-sm" title="ลบสิทธิ์">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                                    ลบ
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @else
            <div class="empty-state text-center py-4">
                <p>ยังไม่มีสิทธิ์ในระบบ</p>
            </div>
        @endif
    </div>

@endsection
