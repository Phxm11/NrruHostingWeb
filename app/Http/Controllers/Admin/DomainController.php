<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
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

        $domains = $query->paginate(15)->appends($request->query());

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
}