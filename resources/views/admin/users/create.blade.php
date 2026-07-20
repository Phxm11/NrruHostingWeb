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
                        <label class="form-label fw-medium">ชื่อ-สกุลผู้ใช้</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">อีเมล</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="ชื่อ@nrru.ac.th">
                        <div class="form-text" style="font-size:12.5px;">ใช้เป็นชื่อเข้าสู่ระบบ</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">รหัสผ่าน</label>
                        <input type="password" name="password" class="form-control" required autocomplete="new-password" minlength="8">
                        <div class="form-text" style="font-size:12.5px;">อย่างน้อย 8 ตัวอักษร</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">ยืนยันรหัสผ่าน</label>
                        <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-soft">ย้อนกลับ</a>
                        <button type="submit" class="btn btn-amber">บันทึกผู้ใช้</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
