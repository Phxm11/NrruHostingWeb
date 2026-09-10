<?php

namespace Tests;

use App\Models\Applicant;
use App\Models\ServiceRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class DatabaseTestCase extends TestCase
{
    use RefreshDatabase;

    protected function makeServiceRequest(array $attributes = []): ServiceRequest
    {
        $applicant = Applicant::create([
            'full_name' => 'Test Applicant', 'staff_or_student_id' => 'TEST-001',
            'unit_name' => 'Test Unit', 'affiliation' => 'Test University', 'email' => 'applicant@example.test',
        ]);

        return ServiceRequest::create(array_merge([
            'form_no' => 'TEST-'.bin2hex(random_bytes(4)), 'request_date' => '2026-09-10',
            'applicant_id' => $applicant->applicant_id, 'purpose_type' => '1.3_internal_admin',
            'project_start_date' => '2026-09-10', 'project_end_date' => '2027-09-10',
            'status' => 'submitted', 'service_type' => 'web_hosting',
        ], $attributes));
    }
}
