<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\DepartmentCode;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    /**
     * รายการโดเมนทั้งหมดที่ถูกขอเปิดใช้งาน พร้อมบัญชี (username) ที่ผูกกับ
     * คำขอเดียวกัน — เพื่อให้เจ้าหน้าที่ดูได้ว่าโดเมนไหนออกให้ผู้ใช้คนไหนไปแล้ว
     */
    public function index(Request $request)
    {
        $query = Domain::with([
                'departmentCode',
                'serviceRequest.applicant',
                'serviceRequest.serviceAccounts',
            ])
            ->latest('domain_id');

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('domain_name', 'like', "%{$search}%")
                  ->orWhereHas('serviceRequest.applicant', function ($q2) use ($search) {
                      $q2->where('full_name', 'like', "%{$search}%")
                         ->orWhere('staff_or_student_id', 'like', "%{$search}%");
                  })
                  ->orWhereHas('serviceRequest.serviceAccounts', function ($q3) use ($search) {
                      $q3->where('username', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('has_account')) {
            if ($request->input('has_account') === 'yes') {
                $query->whereHas('serviceRequest.serviceAccounts');
            } else {
                $query->whereDoesntHave('serviceRequest.serviceAccounts');
            }
        }

        // เรียงลำดับ: ค่าเริ่มต้นคือโดเมนล่าสุดก่อน กดหัวตารางเพื่อสลับ asc/desc ได้
        $sort = $request->input('sort', 'newest');
        if ($sort === 'name_asc') {
            $query->reorder('domain_name', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->reorder('domain_name', 'desc');
        } else {
            $query->reorder('domain_id', 'desc');
        }

        $domains = $query->paginate(50)->appends($request->query());

        return view('admin.domains.index', compact('domains'));
    }

    /**
     * รายละเอียดโดเมน 1 รายการ — ข้อมูลผู้ขอใช้บริการ, คำขอต้นทาง,
     * และบัญชี (username/สถานะ) ทั้งหมดที่ออกให้สำหรับคำขอเดียวกัน
     */
    public function show(Domain $domain)
    {
        $domain->load([
            'departmentCode',
            'serviceRequest.applicant',
            'serviceRequest.serviceAccounts',
        ]);

        return view('admin.domains.show', compact('domain'));
    }

    /**
     * ฟอร์มแก้ไขข้อมูลโดเมน
     */
    public function edit(Domain $domain)
    {
        $domain->load(['departmentCode', 'serviceRequest.applicant']);
        $departmentCodes = DepartmentCode::orderBy('department_name')->get();

        return view('admin.domains.edit', compact('domain', 'departmentCodes'));
    }

    /**
     * บันทึกการแก้ไขโดเมน
     */
    public function update(Request $request, Domain $domain)
    {
        $data = $request->validate([
            'domain_name'      => ['required', 'string', 'max:255'],
            'domain_format'    => ['nullable', 'string', 'max:255'],
            'department_code'  => ['nullable', 'string', 'exists:department_codes,code'],
            'department_other' => ['nullable', 'string', 'max:150'],
        ]);

        // ถ้าเลือกหน่วยงานจากรายการแล้ว ไม่ต้องเก็บชื่อหน่วยงานอิสระซ้ำ
        if (! empty($data['department_code'])) {
            $data['department_other'] = null;
        }

        $domain->update($data);

        return redirect()
            ->route('admin.domains.index')
            ->with('success', "แก้ไขโดเมน {$domain->domain_name} เรียบร้อยแล้ว");
    }

    /**
     * ลบโดเมน (ไม่กระทบคำขอ/บัญชีต้นทาง)
     */
    public function destroy(Domain $domain)
    {
        $domainName = $domain->domain_name;
        $domain->delete();

        return redirect()
            ->route('admin.domains.index')
            ->with('success', "ลบโดเมน {$domainName} เรียบร้อยแล้ว");
    }
}