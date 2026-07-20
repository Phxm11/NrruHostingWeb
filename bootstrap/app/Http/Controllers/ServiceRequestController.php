<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequestRequest;
use App\Models\Applicant;
use App\Models\Attachment;
use App\Models\DepartmentCode;
use App\Models\Domain;
use App\Models\FeeCertification;
use App\Models\PolicyAcceptance;
use App\Models\RequestEnabledService;
use App\Models\RequestResource;
use App\Models\ResourcePlan;
use App\Models\ServiceRequest;
use App\Models\TechDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            // 1) ผู้ขอใช้บริการ - หาเดิมจากรหัสบุคลากรก่อน ไม่พบจึงสร้างใหม่
            $applicant = Applicant::firstOrCreate(
                ['staff_or_student_id' => $data['staff_or_student_id']],
                [
                    'full_name' => $data['full_name'],
                    'unit_name' => $data['unit_name'],
                    'affiliation' => $data['affiliation'],
                    'position_title' => $data['position_title'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'email' => $data['email'] ?? null,
                ]
            );

            // 2) คำขอหลัก
            $serviceRequest = ServiceRequest::create([
                'form_no' => $this->generateFormNo(),
                'request_date' => now()->toDateString(),
                'applicant_id' => $applicant->applicant_id,
                'purpose_type' => $data['purpose_type'],
                'purpose_other_detail' => $data['purpose_other_detail'] ?? null,
                'project_start_date' => $data['project_start_date'],
                'project_end_date' => $data['project_end_date'],
                'status' => 'submitted',
            ]);

            // 3) ผู้พัฒนาระบบ (หลายคน)
            foreach ($data['developers'] as $dev) {
                $serviceRequest->developers()->create($dev);
            }

            // 4) ทรัพยากรที่เลือก
            $serviceRequest->requestResources()->create([
                'plan_id' => $data['plan_id'] ?? null,
                'custom_cpu_vcpu' => $data['custom_cpu_vcpu'] ?? null,
                'custom_ram_gb' => $data['custom_ram_gb'] ?? null,
                'custom_storage_gb' => $data['custom_storage_gb'] ?? null,
                'custom_fee' => $data['custom_fee'] ?? null,
            ]);

            // 5) บริการที่ต้องการเปิดใช้งาน
            foreach ($data['enabled_services'] as $service) {
                $serviceRequest->enabledServices()->create([
                    'service_name' => $service,
                    'other_detail' => $service === 'other'
                        ? ($data['enabled_services_other_detail'] ?? null)
                        : null,
                ]);
            }

            // 6) รายละเอียดทางเทคนิค
            TechDetail::create([
                'request_id' => $serviceRequest->request_id,
                'language_framework' => $data['language_framework'] ?? null,
                'database_used' => $data['database_used'] ?? null,
                'port_service_needed' => $data['port_service_needed'] ?? null,
                'needs_external_connection' => $request->boolean('needs_external_connection'),
            ]);

            // 7) โดเมน
            $serviceRequest->domains()->create([
                'domain_name' => $data['domain_name'],
                'domain_format' => $data['domain_format'] ?? null,
                'department_code' => $data['department_code'] ?? null,
                'department_other' => $data['department_other'] ?? null,
            ]);

            // 8) ไฟล์แนบ
            if ($request->hasFile('system_detail_doc')) {
                $path = $request->file('system_detail_doc')->store('attachments/system_detail', 'public');
                $serviceRequest->attachments()->create([
                    'file_type' => 'system_detail_doc',
                    'file_path' => $path,
                ]);
            }
            if ($request->hasFile('screenshot_evidence')) {
                $path = $request->file('screenshot_evidence')->store('attachments/screenshots', 'public');
                $serviceRequest->attachments()->create([
                    'file_type' => 'screenshot_evidence',
                    'file_path' => $path,
                ]);
            }

            // 9) การรับรองค่าใช้จ่าย
            FeeCertification::create([
                'request_id' => $serviceRequest->request_id,
                'agree_to_pay' => $request->boolean('agree_to_pay'),
                'request_fee_waiver' => $request->boolean('request_fee_waiver'),
                'waiver_reason' => $data['waiver_reason'] ?? null,
            ]);

            // 10) การยอมรับข้อกำหนด + ลายเซ็น
            $signaturePath = $request->file('signature_image')->store('signatures', 'public');
            PolicyAcceptance::create([
                'request_id' => $serviceRequest->request_id,
                'accepted' => true,
                'signature_image_path' => $signaturePath,
                'accepted_date' => now()->toDateString(),
            ]);
        });

        return redirect()
            ->route('service-requests.create')
            ->with('success', 'ส่งแบบฟอร์มขอใช้บริการเรียบร้อยแล้ว รอการพิจารณาจากสำนักคอมพิวเตอร์');
    }

    private function generateFormNo(): string
    {
        $year = now()->year + 543; // พ.ศ.
        $runningNo = ServiceRequest::whereYear('request_date', now()->year)->count() + 1;

        return sprintf('%03d/%d', $runningNo, $year);
    }
}
