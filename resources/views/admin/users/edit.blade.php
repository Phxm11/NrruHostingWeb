@extends('admin.layout')

@section('title', 'แก้ไขผู้ใช้')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'แก้ไขผู้ใช้เจ้าหน้าที่')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="panel">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

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
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">อีเมล</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">รหัสผ่านใหม่</label>
                        <input type="password" name="password" class="form-control" autocomplete="new-password" minlength="8">
                        <div class="form-text" style="font-size:12.5px;">ปล่อยว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน (อย่างน้อย 8 ตัวอักษรหากกรอก)</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">ยืนยันรหัสผ่านใหม่</label>
                        <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">เปิดการใช้งานบัญชีนี้ (ยกเลิกการเลือกเพื่อปิดการใช้งาน)</label>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-soft">ย้อนกลับ</a>
                        <button type="submit" class="btn btn-amber">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
