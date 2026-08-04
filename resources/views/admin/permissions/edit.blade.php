@extends('admin.layout')

@section('title', 'แก้ไขสิทธิ์')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'แก้ไขสิทธิ์')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="panel">
                <form action="{{ route('admin.permissions.update', $permission->permission_id) }}" method="POST">
                    @csrf @method('PUT')

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
                        <label class="form-label fw-medium">ชื่อสิทธิ์ (ระบบ)</label>
                        <input type="text" class="form-control" value="{{ $permission->name }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">ชื่อแสดงผล</label>
                        <input type="text" name="label" class="form-control" value="{{ old('label', $permission->label) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">คำอธิบาย</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $permission->description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">กลุ่ม</label>
                        <input type="text" name="group" class="form-control" value="{{ old('group', $permission->group) }}" placeholder="เช่น users, requests">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-soft">ย้อนกลับ</a>
                        <button type="submit" class="btn btn-amber">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
