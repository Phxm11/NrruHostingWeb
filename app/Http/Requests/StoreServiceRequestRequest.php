<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ส่วนที่ 1 ผู้ขอใช้บริการ
            'full_name' => ['required', 'string', 'max:150'],
            'customer_name' => ['nullable', 'string', 'max:150'],
            'staff_or_student_id' => ['required', 'string', 'max:30'],
            'unit_name' => ['required', 'string', 'max:150'],
            'affiliation' => ['required', 'string', 'max:150'],
            'position_title' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],

            'purpose_type' => ['required', 'in:1.1_teaching,1.2_academic_research_community,1.3_internal_admin,1.4_other'],
            'purpose_other_detail' => ['required_if:purpose_type,1.4_other', 'nullable', 'string'],
            'project_start_date' => ['required', 'date'],
            'project_end_date' => ['required', 'date', 'after_or_equal:project_start_date'],

            'developers' => ['required', 'array', 'min:1'],
            'developers.*.full_name' => ['required', 'string', 'max:150'],
            'developers.*.role_desc' => ['nullable', 'string', 'max:150'],
            'developers.*.phone' => ['nullable', 'string', 'max:20'],
            'developers.*.email' => ['nullable', 'email', 'max:150'],

            // ส่วนที่ 2 ทรัพยากรและบริการ
            'service_type' => ['required', 'in:virtual_server,web_hosting'],
            'plan_id' => ['nullable', 'exists:resource_plans,plan_id'],
            'custom_cpu_vcpu' => ['nullable', 'integer', 'min:1'],
            'custom_ram_gb' => ['nullable', 'integer', 'min:1'],
            'custom_storage_gb' => ['nullable', 'integer', 'min:1'],
            'custom_fee' => ['nullable', 'numeric', 'min:0'],

            'enabled_services' => ['required', 'array', 'min:1'],
            'enabled_services.*' => ['in:ssh,http_https,database_access,other'],
            'enabled_services_other_detail' => ['nullable', 'string', 'max:255'],

            'language_framework' => ['nullable', 'string', 'max:100'],
            'database_used' => ['nullable', 'string', 'max:100'],
            'port_service_needed' => ['nullable', 'string', 'max:255'],
            'needs_external_connection' => ['nullable', 'boolean'],

            // ส่วนที่ 3 โดเมน
            'domain_name' => ['required', 'string', 'max:255'],
            'domain_format' => ['nullable', 'string', 'max:255'],
            'department_code' => ['nullable', 'exists:department_codes,code'],
            'department_other' => ['nullable', 'string', 'max:150'],

            // ส่วนที่ 4 เอกสารแนบและการรับรอง
            'system_detail_doc' => ['required', 'file', 'mimes:pdf,doc,docx,zip', 'max:10240'],
            'screenshot_evidence' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'agree_to_pay' => ['required_without:request_fee_waiver', 'boolean'],
            'request_fee_waiver' => ['required_without:agree_to_pay', 'boolean'],
            'waiver_reason' => ['required_if:request_fee_waiver,1', 'nullable', 'string'],

            // ส่วนที่ 5 การยอมรับข้อกำหนด
            'accepted' => ['required', 'accepted'],
            'signature_image' => ['required', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'developers.required' => 'กรุณาระบุผู้รับผิดชอบพัฒนาระบบอย่างน้อย 1 คน',
            'project_end_date.after_or_equal' => 'วันสิ้นสุดโครงการต้องไม่ก่อนวันเริ่มต้น',
            'system_detail_doc.required' => 'กรุณาแนบเอกสารรายละเอียดระบบ/โครงสร้างระบบ',
            'accepted.accepted' => 'กรุณายอมรับข้อกำหนดและนโยบายก่อนส่งแบบฟอร์ม',
            'signature_image.required' => 'กรุณาแนบรูปลายเซ็นผู้ขอใช้บริการ',
        ];
    }
}