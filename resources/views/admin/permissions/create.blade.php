@extends('admin.layout')

@section('title', 'สร้างสิทธิ์ใหม่')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'สร้างสิทธิ์ใหม่')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="panel">
                <form action="{{ route('admin.permissions.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-medium">ชื่อสิทธิ์ (ระบบ)</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="เช่น users.create">
                        <div class="form-text" style="font-size:12.5px;">ใช้ขีดจุดคั่น เช่น users.create, requests.approve</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">ชื่อแสดงผล</label>
                        <input type="text" name="label" class="form-control" value="{{ old('label') }}" required placeholder="เช่น สร้างผู้ใช้">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">คำอธิบาย</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">กลุ่ม</label>
                        <input type="text" name="group" class="form-control" value="{{ old('group') }}" placeholder="เช่น users, requests">
                        <div class="form-text" style="font-size:12.5px;">ใช้จัดกลุ่มสิทธิ์ในหน้าจัดการ</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-soft">ย้อนกลับ</a>
                        <button type="submit" class="btn btn-amber">บันทึกสิทธิ์</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
