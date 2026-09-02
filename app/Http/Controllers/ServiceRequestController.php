<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequestRequest;
use App\Models\Applicant;
use App\Models\DepartmentCode;
use App\Models\ResourcePlan;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\DB;

class ServiceRequestController extends Controller
{
    public function create()
    {
        $resourcePlans = ResourcePlan::orderBy('service_type')->orderBy('fee_per_year')->get();
        $departmentCodes = DepartmentCode::orderBy('department_name')->get();

        return view('service_requests.create', compact('resourcePlans', 'departmentCodes'));
    }

    public function store(StoreServiceRequestRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request) {
            $systemDetailPath = $request->file('system_detail_doc')->store('attachments/system_detail', 'public');
            $screenshotPath = $request->hasFile('screenshot_evidence')
                ? $request->file('screenshot_evidence')->store('attachments/screenshots', 'public')
                : null;
            $signaturePath = $request->file('signature_image')->store('signatures', 'public');

            // แก้ไข: เดิมใช้ firstOrCreate ซึ่งเช็คแค่ staff_or_student_id — ถ้ารหัสนี้เคยมีในระบบแล้ว
            // จะดึงข้อมูลเก่ามาใช้ทันทีโดยไม่อัปเดตชื่อ/หน่วยงานที่เพิ่งกรอกใหม่เลย ทำให้ผู้ใช้กรอกชื่อหนึ่ง
            // แต่ระบบไปแสดงอีกชื่อหนึ่ง (ชื่อเก่าที่เคยผูกกับรหัสนี้) เปลี่ยนเป็น updateOrCreate เพื่อให้
            // ข้อมูล applicant อัปเดตเป็นค่าล่าสุดที่กรอกมาทุกครั้ง
            $applicant = Applicant::updateOrCreate(
                ['staff_or_student_id' => $data['staff_or_student_id']],
                [
                    'full_name' => $data['full_name'],
                    'customer_name' => $data['customer_name'] ?? null,
                    'unit_name' => $data['unit_name'],
                    'affiliation' => $data['affiliation'],
                    'position_title' => $data['position_title'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'email' => $data['email'] ?? null,
                ]
            );

            // A request has one resource set, technical detail, certification,
            // and signature; storing them here avoids six unnecessary child tables.
            $serviceRequest = ServiceRequest::create([
                'form_no' => $this->generateFormNo(),
                'request_date' => now()->toDateString(),
                'applicant_id' => $applicant->applicant_id,
                'purpose_type' => $data['purpose_type'],
                'purpose_other_detail' => $data['purpose_other_detail'] ?? null,
                'project_start_date' => $data['project_start_date'],
                'project_end_date' => $data['project_end_date'],
                'status' => 'submitted',
                'service_type' => $data['service_type'],
                'plan_id' => $data['plan_id'] ?? null,
                'custom_cpu_vcpu' => $data['custom_cpu_vcpu'] ?? null,
                'custom_ram_gb' => $data['custom_ram_gb'] ?? null,
                'custom_storage_gb' => $data['custom_storage_gb'] ?? null,
                'custom_fee' => $data['custom_fee'] ?? null,
                'enabled_services' => $data['enabled_services'],
                'enabled_services_other_detail' => $data['enabled_services_other_detail'] ?? null,
                'language_framework' => $data['language_framework'] ?? null,
                'database_used' => $data['database_used'] ?? null,
                'port_service_needed' => $data['port_service_needed'] ?? null,
                'needs_external_connection' => $request->boolean('needs_external_connection'),
                'system_detail_doc_path' => $systemDetailPath,
                'screenshot_evidence_path' => $screenshotPath,
                'agree_to_pay' => $request->boolean('agree_to_pay'),
                'request_fee_waiver' => $request->boolean('request_fee_waiver'),
                'waiver_reason' => $data['waiver_reason'] ?? null,
                'accepted' => true,
                'signature_image_path' => $signaturePath,
                'accepted_date' => now()->toDateString(),
            ]);

            // These two relationships remain separate because they can have many rows.
            foreach ($data['developers'] as $developer) {
                $serviceRequest->developers()->create($developer);
            }

            $serviceRequest->domains()->create([
                'domain_name' => $data['domain_name'],
                'domain_format' => $data['domain_format'] ?? null,
                'department_code' => $data['department_code'] ?? null,
                'department_other' => $data['department_other'] ?? null,
            ]);
        });

        return redirect()
            ->route('service-requests.create')
            ->with('success', 'ส่งแบบฟอร์มขอใช้บริการเรียบร้อยแล้ว รอการพิจารณาจากสำนักคอมพิวเตอร์');
    }

    private function generateFormNo(): string
    {
        $year = now()->year + 543;
        $runningNo = ServiceRequest::whereYear('request_date', now()->year)->count() + 1;

        return sprintf('%03d/%d', $runningNo, $year);
    }
}