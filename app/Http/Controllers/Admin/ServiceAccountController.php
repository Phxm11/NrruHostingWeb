<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\ResourcePlan;
use App\Models\ServiceAccount;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceAccountController extends Controller
{
    /**
     * รายการคำขอใช้บริการที่ "ยังไม่ได้สร้างบัญชี" เท่านั้น — คำขอที่สร้างบัญชีให้แล้ว
     * จะถูกตัดออกจากรายการนี้ (ดูรายละเอียด/บัญชีของคำขอเหล่านั้นได้จากหน้า "บัญชีผู้ใช้บริการ" แทน)
     */
    public function requestsIndex(Request $request)
    {
        $query = ServiceRequest::with(['applicant', 'domains', 'serviceAccounts'])
            ->withCount('serviceAccounts')
            ->whereDoesntHave('serviceAccounts')
            ->latest('request_id');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('applicant', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('staff_or_student_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $serviceRequests = $query->paginate(50);
        $serviceRequests->appends($request->query());

        return view('admin.requests.index', compact('serviceRequests'));
    }

    /**
     * ดูรายละเอียดคำขอ 1 รายการ — ข้อมูลทั้งหมดที่ผู้ใช้กรอกมาจากฟอร์ม
     * (ผู้ขอใช้บริการ, ผู้พัฒนาระบบ, ทรัพยากรที่ขอ, บริการที่เปิดใช้, รายละเอียดทางเทคนิค,
     *  โดเมน, เอกสารแนบ, การรับรองค่าใช้จ่าย, การยอมรับข้อกำหนด/ลายเซ็น, ประวัติการอนุมัติ, บัญชีที่สร้างให้แล้ว)
     */
    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load([
            'applicant',
            'developers',
            'plan',
            'domains.departmentCode',
            'approvals',
            'serviceAccounts',
        ]);

        return view('admin.requests.show', compact('serviceRequest'));
    }

    /**
     * ฟอร์มแก้ไขรายละเอียดคำขอ — แก้ได้ทั้งข้อมูลผู้ขอใช้บริการ, วัตถุประสงค์/ระยะเวลา,
     * ทรัพยากรที่ขอ, บริการที่เปิดใช้ และรายละเอียดทางเทคนิค
     * (ไม่รวมโดเมน — มีหน้าแก้ไขโดเมนแยกต่างหากที่ /admin/domains/{domain}/edit อยู่แล้ว,
     *  ไม่รวมสถานะ/การอนุมัติ — จัดการผ่านปุ่ม "อนุมัติคำขอ" เพื่อไม่ให้ตัดขั้นตอนออกเลขที่ใบเสร็จ)
     */
    public function editRequest(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load(['applicant', 'plan']);
        $plans = ResourcePlan::orderBy('service_type')->orderBy('fee_per_year')->get();

        return view('admin.requests.edit', compact('serviceRequest', 'plans'));
    }

    /**
     * บันทึกการแก้ไขคำขอ — อัปเดตทั้งตาราง service_requests และ applicants ที่ผูกกัน
     * หมายเหตุ: applicant_id เดียวกันอาจถูกใช้ร่วมกันหลายคำขอ การแก้ข้อมูลผู้ขอตรงนี้
     * จะมีผลกับคำขออื่นของผู้ขอคนเดียวกันด้วย (ต้องการให้ข้อมูลล่าสุดตรงกันทุกคำขอ)
     */
    public function updateRequest(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            // ผู้ขอใช้บริการ
            'full_name' => ['required', 'string', 'max:150'],
            'customer_name' => ['nullable', 'string', 'max:150'],
            'unit_name' => ['required', 'string', 'max:150'],
            'affiliation' => ['required', 'string', 'max:150'],
            'position_title' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],

            // วัตถุประสงค์และระยะเวลา
            'purpose_type' => ['required', 'in:1.1_teaching,1.2_academic_research_community,1.3_internal_admin,1.4_other'],
            'purpose_other_detail' => ['required_if:purpose_type,1.4_other', 'nullable', 'string'],
            'project_start_date' => ['required', 'date'],
            'project_end_date' => ['required', 'date', 'after_or_equal:project_start_date'],

            // ทรัพยากรและบริการ
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
        ]);

        // ถ้าเลือกแพ็กเกจสำเร็จรูป ล้างค่าสเปกที่กำหนดเองทิ้ง กันข้อมูลค้างขัดแย้งกัน
        if (! empty($data['plan_id'])) {
            $data['custom_cpu_vcpu'] = null;
            $data['custom_ram_gb'] = null;
            $data['custom_storage_gb'] = null;
            $data['custom_fee'] = null;
        }

        $serviceRequest->applicant->update([
            'full_name' => $data['full_name'],
            'customer_name' => $data['customer_name'] ?? null,
            'unit_name' => $data['unit_name'],
            'affiliation' => $data['affiliation'],
            'position_title' => $data['position_title'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
        ]);

        $serviceRequest->update([
            'purpose_type' => $data['purpose_type'],
            'purpose_other_detail' => $data['purpose_other_detail'] ?? null,
            'project_start_date' => $data['project_start_date'],
            'project_end_date' => $data['project_end_date'],
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
        ]);

        return redirect()
            ->route('admin.requests.show', $serviceRequest->request_id)
            ->with('success', "แก้ไขคำขอ {$serviceRequest->form_no} เรียบร้อยแล้ว");
    }

    /**
     * ลบคำขอใช้บริการ (ลบทิ้งทั้งคำขอ รวมถึงบัญชี/โดเมน/เอกสารที่ผูกไว้ เนื่องจากมี cascadeOnDelete)
     */
    public function destroyRequest(ServiceRequest $serviceRequest)
    {
        $formNo = $serviceRequest->form_no;

        // ลบไฟล์แนบและลายเซ็นออกจาก disk ป้องกันไฟล์ค้าง
        foreach (['system_detail_doc_path', 'screenshot_evidence_path', 'signature_image_path'] as $column) {
            if ($serviceRequest->{$column}) {
                Storage::disk('public')->delete($serviceRequest->{$column});
            }
        }

        $serviceRequest->delete();

        return redirect()
            ->route('admin.requests.index')
            ->with('success', "ลบคำขอ {$formNo} เรียบร้อยแล้ว");
    }

    /**
     * อนุมัติคำขอใช้บริการ (เปลี่ยนสถานะเป็น approved)
     * แก้ไข: เดิมแค่เปลี่ยน status ตรงๆ ไม่เคยบันทึกลง approvals เลย ทำให้ประวัติการอนุมัติว่างเปล่าตลอด
     * และไม่เคยออกเลขที่ใบเสร็จ (receipt_no/date/time) เลยสักครั้ง
     */
    public function approveRequest(ServiceRequest $serviceRequest)
    {
        $serviceRequest->status = 'approved';
        $serviceRequest->receipt_no = $serviceRequest->receipt_no ?? $this->generateReceiptNo();
        $serviceRequest->receipt_date = $serviceRequest->receipt_date ?? now()->toDateString();
        $serviceRequest->receipt_time = $serviceRequest->receipt_time ?? now()->format('H:i:s');
        $serviceRequest->save();

        // บันทึกประวัติการอนุมัติจริง แทนที่จะแค่เปลี่ยนสถานะเฉยๆ
        Approval::create([
            'request_id' => $serviceRequest->request_id,
            'approver_level' => 'computer_center_director',
            'approver_name' => auth()->user()->name,
            'decision' => 'certify_info_only',
            'decision_date' => now()->toDateString(),
        ]);

        return back()->with('success', "อนุมัติคำขอ {$serviceRequest->form_no} เรียบร้อยแล้ว");
    }

    private function generateReceiptNo(): string
    {
        $year = now()->year + 543; // พ.ศ.
        $runningNo = ServiceRequest::whereYear('receipt_date', now()->year)->count() + 1;

        return sprintf('RC-%03d/%d', $runningNo, $year);
    }

    /**
     * ฟอร์มสร้างบัญชี Username/Password ให้ผู้ขอใช้บริการของคำขอนี้
     * กันไว้ไม่ให้เข้าฟอร์มนี้ซ้ำถ้าคำขอนี้มีบัญชีอยู่แล้ว (เช่น กดปุ่มย้อนกลับ/เปิดลิงก์เก่าซ้ำ)
     */
    public function createAccount(ServiceRequest $serviceRequest)
    {
        if ($serviceRequest->serviceAccounts()->exists()) {
            return redirect()
                ->route('admin.requests.show', $serviceRequest->request_id)
                ->with('success', 'คำขอนี้มีบัญชีที่สร้างไว้แล้ว');
        }

        $serviceRequest->load(['applicant', 'domains', 'serviceAccounts']);

        $suggestedUsername = Str::slug($serviceRequest->applicant->staff_or_student_id, '');
        $suggestedPassword = Str::password(12, symbols: false);

        return view('admin.accounts.create', compact('serviceRequest', 'suggestedUsername', 'suggestedPassword'));
    }

    /**
     * บันทึกบัญชีใหม่ลงตาราง service_accounts
     */
    public function storeAccount(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('service_accounts', 'username')],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'account_type' => ['required', 'in:ssh,database,control_panel,ftp'],
            'created_by' => ['required', 'string', 'max:150'],
            'expire_date' => ['nullable', 'date'],
        ]);

        $account = new ServiceAccount();
        $account->request_id = $serviceRequest->request_id;
        $account->applicant_id = $serviceRequest->applicant_id;
        $account->username = $data['username'];
        $account->password = $data['password']; // ผ่าน mutator -> hash อัตโนมัติ
        $account->account_type = $data['account_type'];
        $account->status = 'active';
        $account->created_by = $data['created_by'];
        $account->expire_date = $data['expire_date'] ?? $serviceRequest->project_end_date;
        $account->save();

        return redirect()
            ->route('admin.accounts.index')
            ->with('success', "สร้างบัญชีให้ {$serviceRequest->applicant->full_name} เรียบร้อยแล้ว")
            ->with('new_username', $account->username)
            ->with('new_password', $data['password']); // แสดงให้เห็นครั้งเดียวตอนสร้างเสร็จ
    }

    /**
     * รายการบัญชีทั้งหมดที่สร้างไปแล้ว
     */
    public function accountsIndex(Request $request)
    {
        $baseQuery = ServiceAccount::query();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $baseQuery->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhereHas('applicant', function ($q2) use ($search) {
                      $q2->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        // นับจำนวนต่อสถานะ (สำหรับปุ่มตัวกรองด้านบนตาราง) — ใช้ตัวกรองค้นหาเดียวกัน
        // แต่ไม่ผูกกับสถานะที่เลือกอยู่ เพื่อให้เห็นภาพรวมทุกสถานะพร้อมกัน
        $statusCounts = [
            'all' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('status', 'active')->count(),
            'disabled' => (clone $baseQuery)->where('status', 'disabled')->count(),
            'expired' => (clone $baseQuery)->where('status', 'expired')->count(),
        ];

        $query = (clone $baseQuery)->with(['applicant', 'serviceRequest.domains']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        switch ($request->input('sort')) {
            case 'expire_soon':
                $query->orderByRaw('expire_date IS NULL')->orderBy('expire_date', 'asc');
                break;
            case 'name':
                $query->join('applicants', 'applicants.applicant_id', '=', 'service_accounts.applicant_id')
                    ->orderBy('applicants.full_name')
                    ->select('service_accounts.*');
                break;
            default:
                $query->latest('account_id');
        }

        $accounts = $query->paginate(20);
        $accounts->appends($request->query());

        return view('admin.accounts.index', compact('accounts', 'statusCounts'));
    }

    /**
     * เปิด/ปิดใช้งานบัญชี
     */
    public function toggleStatus(ServiceAccount $account)
    {
        $account->status = $account->status === 'active' ? 'disabled' : 'active';
        $account->save();

        return back()->with('success', 'อัปเดตสถานะบัญชีเรียบร้อยแล้ว');
    }

    /**
     * ฟอร์มแก้ไขข้อมูลบัญชี
     */
    public function editAccount(ServiceAccount $account)
    {
        $account->load(['applicant', 'serviceRequest.domains']);

        return view('admin.accounts.edit', compact('account'));
    }

    /**
     * บันทึกการแก้ไขบัญชี
     */
    public function updateAccount(Request $request, ServiceAccount $account)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('service_accounts', 'username')->ignore($account->account_id, 'account_id')],
            'password' => ['nullable', 'string', 'min:8', 'max:100'],
            'account_type' => ['required', 'in:ssh,database,control_panel,ftp'],
            'status' => ['required', 'in:active,disabled,expired'],
            'expire_date' => ['nullable', 'date'],
        ]);

        $account->username = $data['username'];
        if (!empty($data['password'])) {
            $account->password = $data['password']; // ผ่าน mutator -> hash อัตโนมัติ
        }
        $account->account_type = $data['account_type'];
        $account->status = $data['status'];
        $account->expire_date = $data['expire_date'] ?? null;
        $account->save();

        return redirect()
            ->route('admin.accounts.index')
            ->with('success', "แก้ไขบัญชี {$account->username} เรียบร้อยแล้ว");
    }

    /**
     * ลบบัญชี
     * แก้ไข: ลบบัญชีแล้วให้ลบโดเมนของคำขอ (request) ที่ผูกกับบัญชีนี้ไปด้วย
     * หมายเหตุ: ถ้าคำขอเดียวกันยังมีบัญชีอื่นเหลืออยู่ (เช่น ssh + database ของ request เดียวกัน)
     * จะไม่ลบโดเมนทิ้ง เพื่อไม่ให้กระทบบัญชีอื่นที่ยังใช้โดเมนเดิมอยู่
     */
    public function destroyAccount(ServiceAccount $account)
    {
        $username = $account->username;
        $requestId = $account->request_id;

        $account->delete();

        // ลบโดเมนของคำขอนี้ เฉพาะกรณีที่ไม่มีบัญชีอื่นเหลืออยู่ในคำขอเดียวกันแล้ว
        $remainingAccounts = ServiceAccount::where('request_id', $requestId)->exists();

        if (! $remainingAccounts) {
            \App\Models\Domain::where('request_id', $requestId)->delete();
        }

        return redirect()
            ->route('admin.accounts.index')
            ->with('success', "ลบบัญชี {$username} และโดเมนที่เกี่ยวข้องเรียบร้อยแล้ว");
    }
}