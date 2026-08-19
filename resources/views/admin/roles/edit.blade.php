@extends('admin.layout')

@section('title', 'แก้ไขบทบาท')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'แก้ไขบทบาท')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="panel">
                <form action="{{ route('admin.roles.update', $role->role_id) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-medium">ชื่อบทบาท (ระบบ)</label>
                        <input type="text" class="form-control" value="{{ $role->name }}" disabled>
                        @if ($role->is_system)
                            <div class="form-text">บทบาทระบบไม่สามารถเปลี่ยนชื่อได้</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">ชื่อแสดงผล</label>
                        <input type="text" name="label" class="form-control" value="{{ old('label', $role->label) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">คำอธิบาย</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $role->description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">สิทธิ์ที่มอบหมาย</label>
                        @php
                            $groupedPermissions = \App\Models\Permission::orderBy('group')->orderBy('label')->get()->groupBy('group');
                            $currentPermissions = $role->permissions->pluck('permission_id')->toArray();
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
                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->permission_id }}" id="perm_{{ $perm->permission_id }}" {{ in_array($perm->permission_id, $currentPermissions) ? 'checked' : '' }}>
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
                        <button type="submit" class="btn btn-amber">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
