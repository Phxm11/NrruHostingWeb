<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceReportRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return $this->user()?->isActive() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'start_date' => $this->input('start_date', today()->startOfYear()->toDateString()),
            'end_date' => $this->input('end_date', today()->toDateString()),
        ]);
    }

    public function rules(): array
    {
        return [
            'start_date' => ['bail', 'required', 'string', 'date_format:Y-m-d', 'after_or_equal:1900-01-01', 'before_or_equal:2100-12-31'],
            'end_date' => ['bail', 'required', 'string', 'date_format:Y-m-d', 'after_or_equal:start_date', 'before_or_equal:2100-12-31'],
            'service_type' => ['nullable', Rule::in(['web_hosting', 'virtual_server'])],
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => 'กรุณาระบุวันที่ให้ครบถ้วน',
            '*.date_format' => 'กรุณาระบุวันที่ให้ถูกต้อง',
            '*.string' => 'กรุณาระบุวันที่ให้ถูกต้อง',
            'start_date.after_or_equal' => 'วันที่เริ่มต้นต้องไม่ก่อนปี ค.ศ. 1900',
            '*.before_or_equal' => 'กรุณาระบุวันที่ไม่เกินปี ค.ศ. 2100',
            'end_date.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',
            'service_type.in' => 'กรุณาเลือกประเภทบริการจากรายการ',
        ];
    }
}
