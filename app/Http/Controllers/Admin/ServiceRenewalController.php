<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ServiceRenewalController extends Controller
{
    public function create(ServiceAccount $account)
    {
        $account->load('applicant');
        $base = $account->expire_date && $account->expire_date > today()->toDateString()
            ? Carbon::parse($account->expire_date) : today();
        $suggestedDate = $base->copy()->addYearNoOverflow()->toDateString();
        $minimumDate = $base->copy()->addDay()->toDateString();
        $renewals = DB::table('service_renewals')->where('account_id', $account->account_id)
            ->orderByDesc('id')->paginate(10);

        return view('admin.accounts.renew', compact('account', 'suggestedDate', 'minimumDate', 'renewals'));
    }

    public function store(Request $request, ServiceAccount $account)
    {
        $data = $request->validate([
            'expire_date' => ['required', 'date_format:Y-m-d', 'after:today'],
            'previous_expire_date' => ['present', 'nullable', 'date_format:Y-m-d'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($account, $data, $request) {
            $locked = ServiceAccount::whereKey($account->account_id)->lockForUpdate()->firstOrFail();
            if (($locked->expire_date ?? '') !== ($data['previous_expire_date'] ?? '')) {
                throw ValidationException::withMessages(['expire_date' => 'ข้อมูลวันหมดอายุเปลี่ยนแปลงแล้ว กรุณาโหลดหน้านี้ใหม่ก่อนต่ออายุ']);
            }
            if ($locked->expire_date && $data['expire_date'] <= $locked->expire_date) {
                throw ValidationException::withMessages(['expire_date' => 'วันหมดอายุใหม่ต้องมากกว่าวันหมดอายุเดิม']);
            }
            DB::table('service_renewals')->insert([
                'account_id' => $locked->account_id,
                'previous_expire_date' => $locked->expire_date,
                'expire_date' => $data['expire_date'],
                'previous_status' => $locked->status,
                'renewed_by' => $request->user()->id,
                'renewed_by_name' => $request->user()->name,
                'note' => $data['note'] ?? null,
                'created_at' => now(),
            ]);
            $locked->expire_date = $data['expire_date'];
            if ($locked->status === 'expired') {
                $locked->status = 'active';
            }
            $locked->save();
        });

        return redirect()->route('admin.accounts.renew', $account->account_id)
            ->with('success', 'ต่ออายุบริการเรียบร้อยแล้ว');
    }
}
