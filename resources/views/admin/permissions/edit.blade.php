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
