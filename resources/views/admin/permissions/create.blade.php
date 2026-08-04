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
