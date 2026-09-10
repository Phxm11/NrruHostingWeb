<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\ServiceAccount;
use App\Models\ServiceRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('DemoSeeder is limited to local/testing environments.');
        }
        DB::transaction(function () {
            foreach (['submitted', 'approved'] as $index => $status) {
                $form = 'DEMO-'.($index + 1);
                if (ServiceRequest::where('form_no', $form)->exists()) {
                    continue;
                }
                $applicant = Applicant::create([
                    'full_name' => 'ผู้ขอใช้บริการตัวอย่าง '.($index + 1), 'staff_or_student_id' => $form,
                    'unit_name' => 'หน่วยงานตัวอย่าง', 'affiliation' => 'ข้อมูลสมมติสำหรับสาธิต',
                    'email' => 'demo'.($index + 1).'@example.test',
                ]);
                $request = ServiceRequest::create([
                    'form_no' => $form, 'request_date' => today(), 'applicant_id' => $applicant->applicant_id,
                    'purpose_type' => '1.3_internal_admin', 'project_start_date' => today(),
                    'project_end_date' => today()->addDays(7), 'service_type' => 'web_hosting',
                    'enabled_services' => ['http_https'], 'status' => $status,
                ]);
                $request->domains()->create(['domain_name' => 'demo'.($index + 1).'.example.test']);
                if ($status === 'approved') {
                    $request->approvals()->create([
                        'approver_level' => 'staff', 'approver_name' => 'เจ้าหน้าที่ตัวอย่าง',
                        'decision' => 'certify_info_only', 'decision_date' => today(),
                    ]);
                    ServiceAccount::create([
                        'request_id' => $request->request_id, 'applicant_id' => $applicant->applicant_id,
                        'username' => 'demo-service-2', 'password' => Str::random(32), 'status' => 'active',
                        'account_type' => 'control_panel', 'created_by' => 'DemoSeeder',
                        'expire_date' => today()->addDays(7)->toDateString(),
                    ]);
                }
            }
        });
    }
}
