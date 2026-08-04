@extends('admin.layout')

@section('title', 'สร้างบทบาทใหม่')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'สร้างบทบาทใหม่')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="panel">
                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf

                    @if ($errors->any())
                        <div class="banner-danger" style="display:flex;align-items:flex-start;gap:10px;">
                            <span style="flex-shrink:0;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.6 1.9 18a2 2 0 0 0 1.7 3h16.8a2 2 0 0 0 1.7-3L14.7 3.6a2 2 0 0 0-3.4 0Z"/></svg>
                            </span>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-medium">ชื่อบทบาท (ระบบ)</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="เช่น admin, staff, viewer">
                        <div class="form-text" style="font-size:12.5px;">ใช้เฉพาะอักษรตัวเล็ก ตัวเลข และขีดล่าง เท่านั้น</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">ชื่อแสดงผล</label>
                        <input type="text" name="label" class="form-control" value="{{ old('label') }}" required placeholder="เช่น ผู้ดูแลระบบ, เจ้าหน้าที่">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">คำอธิบาย</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">สิทธิ์ที่มอบหมาย</label>
                        @php
                            $groupedPermissions = \App\Models\Permission::orderBy('group')->orderBy('label')->get()->groupBy('group');
                        @endphp
                        @foreach ($groupedPermissions as $group => $perms)
                            <div class="mb-2">
                                <div class="fw-medium mb-1" style="font-size:13px;">
                                    {{ $group ?? 'ทั่วไป' }}
                                </div>
                                <div class="row g-1">
                                    @foreach ($perms as $perm)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->permission_id }}" id="perm_{{ $perm->permission_id }}">
                                                <label class="form-check-label" for="perm_{{ $perm->permission_id }}" style="font-size:13px;">
                                                    {{ $perm->label }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-soft">ย้อนกลับ</a>
                        <button type="submit" class="btn btn-amber">บันทึกบทบาท</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
