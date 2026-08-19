@extends('admin.layout')

@section('title', 'แก้ไขโดเมน')
@section('eyebrow', 'แดชบอร์ดเจ้าหน้าที่')
@section('page-title', 'แก้ไขโดเมน')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="panel">

                <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap" style="gap:8px; font-size:13px;color:#8a8a8a;">
                    <div>
                        คำขอต้นทาง: <strong>{{ $domain->serviceRequest->form_no }}</strong>
                        · ผู้ขอใช้บริการ: <strong>{{ $domain->serviceRequest->applicant->full_name }}</strong>
                    </div>
                    <a href="{{ route('admin.domains.show', $domain->domain_id) }}" style="font-size:13px; color:var(--forest); white-space:nowrap;">
                        ดูรายละเอียดโดเมน &rarr;
                    </a>
                </div>

                <form action="{{ route('admin.domains.update', $domain->domain_id) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-medium">ชื่อโดเมน <span style="color:var(--rust);">*</span></label>
                        <input type="text" name="domain_name" class="form-control"
                               value="{{ old('domain_name', $domain->domain_name) }}" required autofocus>
                        @error('domain_name')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">รูปแบบโดเมน (ถ้ามี)</label>
                        <input type="text" name="domain_format" class="form-control"
                               value="{{ old('domain_format', $domain->domain_format) }}"
                               placeholder="เช่น test.nrru.ac.th">
                        @error('domain_format')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">หน่วยงาน (เลือกจากรายการ)</label>
                        <select name="department_code" class="form-select" id="departmentCodeSelect">
                            <option value="">— ไม่ระบุ / พิมพ์ชื่อหน่วยงานเอง —</option>
                            @foreach ($departmentCodes as $dc)
                                <option value="{{ $dc->code }}"
                                    {{ old('department_code', $domain->department_code) === $dc->code ? 'selected' : '' }}>
                                    {{ $dc->department_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_code')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="departmentOtherWrap">
                        <label class="form-label fw-medium">หรือระบุชื่อหน่วยงานเอง</label>
                        <input type="text" name="department_other" class="form-control"
                               value="{{ old('department_other', $domain->department_other) }}"
                               placeholder="กรอกเมื่อไม่พบหน่วยงานในรายการด้านบน">
                        @error('department_other')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.domains.index') }}" class="btn btn-outline-soft">ย้อนกลับ</a>
                        <button type="submit" class="btn btn-amber">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var select = document.getElementById('departmentCodeSelect');
            var otherWrap = document.getElementById('departmentOtherWrap');
            function sync() {
                otherWrap.style.display = select.value ? 'none' : 'block';
            }
            select.addEventListener('change', sync);
            sync();
        })();
    </script>

@endsection
