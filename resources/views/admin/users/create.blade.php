@extends('admin.layout')

@section('title', 'เพิ่มผู้ใช้')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'เพิ่มผู้ใช้เจ้าหน้าที่')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="panel">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium">ชื่อ-สกุลผู้ใช้</label>
                        <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium">อีเมล</label>
                        <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="ชื่อ@nrru.ac.th">
                        <div class="form-text" style="font-size:12.5px;">ใช้เป็นชื่อเข้าสู่ระบบ</div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-medium">รหัสผ่าน</label>
                        <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password" minlength="8">
                        <div class="form-text" style="font-size:12.5px;">อย่างน้อย 8 ตัวอักษร</div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-medium">ยืนยันรหัสผ่าน</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-soft"><x-admin.icon name="back" size="17" /> ย้อนกลับ</a>
                        <button type="submit" class="btn btn-amber"><x-admin.icon name="save" size="17" /> บันทึกผู้ใช้</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
