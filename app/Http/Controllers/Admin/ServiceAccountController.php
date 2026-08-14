<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceAccount;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceAccountController extends Controller
{
    /**
     * รายการคำขอใช้บริการทั้งหมด พร้อมจำนวนบัญชีที่สร้างให้แล้ว
     */
    public function requestsIndex(Request $request)
    {
        $query = ServiceRequest::with(['applicant', 'domains', 'serviceAccounts'])
            ->withCount('serviceAccounts')
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

        $serviceRequests = $query->paginate(15)->withQueryString();

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
            'requestResources.plan',
            'enabledServices',
            'techDetail',
            'domains.departmentCode',
            'attachments',
            'feeCertification',
            'policyAcceptance',
            'approvals',
            'serviceAccounts',
        ]);

        return view('admin.requests.show', compact('serviceRequest'));
    }

    /**
     * ลบคำขอใช้บริการ (ลบทิ้งทั้งคำขอ รวมถึงบัญชี/โดเมน/เอกสารที่ผูกไว้ เนื่องจากมี cascadeOnDelete)
     */
    public function destroyRequest(ServiceRequest $serviceRequest)
    {
        $formNo = $serviceRequest->form_no;

        // ลบไฟล์แนบและลายเซ็นออกจาก disk ป้องกันไฟล์ค้าง
        foreach ($serviceRequest->attachments as $attachment) {
            if ($attachment->file_path) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        }
        if ($serviceRequest->policyAcceptance && $serviceRequest->policyAcceptance->signature_image_path) {
            Storage::disk('public')->delete($serviceRequest->policyAcceptance->signature_image_path);
        }

        $serviceRequest->delete();

        return redirect()
            ->route('admin.requests.index')
            ->with('success', "ลบคำขอ {$formNo} เรียบร้อยแล้ว");
    }

    /**
     * อนุมัติคำขอใช้บริการ (เปลี่ยนสถานะเป็น approved)
     */
    public function approveRequest(ServiceRequest $serviceRequest)
    {
        $serviceRequest->status = 'approved';
        $serviceRequest->save();

        return back()->with('success', "อนุมัติคำขอ {$serviceRequest->form_no} เรียบร้อยแล้ว");
    }

    /**
     * ฟอร์มสร้างบัญชี Username/Password ให้ผู้ขอใช้บริการของคำขอนี้
     */
    public function createAccount(ServiceRequest $serviceRequest)
    {
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
        $query = ServiceAccount::with(['applicant', 'serviceRequest.domains'])
            ->latest('account_id');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $accounts = $query->paginate(15)->withQueryString();

        return view('admin.accounts.index', compact('accounts'));
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
     */
    public function destroyAccount(ServiceAccount $account)
    {
        $username = $account->username;
        $account->delete();

        return redirect()
            ->route('admin.accounts.index')
            ->with('success', "ลบบัญชี {$username} เรียบร้อยแล้ว");
    }
}
