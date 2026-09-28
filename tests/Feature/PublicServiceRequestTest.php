<?php

namespace Tests\Feature;

use App\Models\ResourcePlan;
use App\Models\ServiceRequest;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\DatabaseTestCase;

class PublicServiceRequestTest extends DatabaseTestCase
{
    private function submission(): array
    {
        return [
            'full_name' => 'Test Applicant',
            'staff_or_student_id' => 'PUBLIC-001',
            'unit_name' => 'Test Unit',
            'affiliation' => 'Test University',
            'purpose_type' => '1.3_internal_admin',
            'project_start_date' => '2026-09-28',
            'project_end_date' => '2027-09-28',
            'developers' => [
                0 => ['full_name' => 'Developer One', 'role_desc' => 'Frontend', 'phone' => '0123456789', 'email' => 'one@example.test'],
                4 => ['full_name' => 'Developer Two', 'role_desc' => 'Backend', 'phone' => '0987654321', 'email' => 'two@example.test'],
            ],
            'service_type' => 'virtual_server',
            'plan_id' => '',
            'custom_cpu_vcpu' => 7,
            'custom_ram_gb' => 17,
            'custom_storage_gb' => 123,
            'custom_fee' => '4321.50',
            'enabled_services' => ['http_https'],
            'domain_name' => 'test.example.test',
            'agree_to_pay' => '1',
            'accepted' => '1',
            'system_detail_doc' => UploadedFile::fake()->createWithContent('system.pdf', "%PDF-1.4\nTest document"),
            'signature_image' => UploadedFile::fake()->createWithContent('signature.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Zl1sAAAAASUVORK5CYII=')),
        ];
    }

    public function test_failed_submission_restores_all_developers_and_custom_resources(): void
    {
        $data = $this->submission();
        unset($data['signature_image']);
        $this->from(route('service-requests.create'))
            ->post(route('service-requests.store'), $data)
            ->assertSessionHasErrors('signature_image');

        $response = $this->get(route('service-requests.create'))->assertOk();
        $document = new DOMDocument;
        @$document->loadHTML($response->getContent());
        $xpath = new DOMXPath($document);
        $this->assertSame(2, $xpath->query('//*[@id="developersWrapper"]/*')->length);
        foreach (array_values($data['developers']) as $index => $developer) {
            foreach ($developer as $field => $value) {
                $this->assertSame($value, $document->getElementById("developer-{$index}-{$field}")->getAttribute('value'));
            }
        }
        foreach (['custom_cpu_vcpu', 'custom_ram_gb', 'custom_storage_gb', 'custom_fee'] as $field) {
            $this->assertSame((string) $data[$field], $document->getElementById($field)->getAttribute('value'));
        }
        $this->assertTrue($document->getElementById('customPlanRadio')->hasAttribute('checked'));
        $this->assertDatabaseCount('service_requests', 0);
    }

    public function test_custom_request_saves_resources_and_every_developer(): void
    {
        Storage::fake('private');
        $this->post(route('service-requests.store'), $this->submission())
            ->assertRedirect(route('service-requests.create'))
            ->assertSessionHasNoErrors();

        $request = ServiceRequest::firstOrFail();
        $this->assertNull($request->plan_id);
        $this->assertSame(7, $request->custom_cpu_vcpu);
        $this->assertSame(17, $request->custom_ram_gb);
        $this->assertSame(123, $request->custom_storage_gb);
        $this->assertEquals(4321.50, $request->custom_fee);
        $this->assertSame(['Developer One', 'Developer Two'], $request->developers()->orderBy('developer_id')->pluck('full_name')->all());
        Storage::disk('private')->assertExists([$request->system_detail_doc_path, $request->signature_image_path]);
    }

    public function test_standard_plan_clears_custom_resources_and_wrong_service_plan_is_rejected(): void
    {
        Storage::fake('private');
        $plan = ResourcePlan::create(['service_type' => 'virtual_server', 'size_label' => 'Small', 'fee_per_year' => 1000]);
        $data = array_merge($this->submission(), ['plan_id' => $plan->plan_id]);
        $this->post(route('service-requests.store'), $data)->assertSessionHasNoErrors();
        $request = ServiceRequest::firstOrFail();
        $this->assertSame($plan->plan_id, $request->plan_id);
        $this->assertNull($request->custom_ram_gb);

        $data['service_type'] = 'web_hosting';
        $this->post(route('service-requests.store'), $data)->assertSessionHasErrors('plan_id');
        $this->assertDatabaseCount('service_requests', 1);
    }

    #[DataProvider('projectDates')]
    public function test_project_duration_is_limited_to_one_calendar_year(string $start, string $end, ?string $error): void
    {
        Storage::fake('private');
        $data = array_merge($this->submission(), ['project_start_date' => $start, 'project_end_date' => $end]);
        $response = $this->post(route('service-requests.store'), $data);
        if ($error) {
            $response->assertSessionHasErrors($error);
            $this->assertDatabaseCount('service_requests', 0);
            Storage::disk('private')->assertDirectoryEmpty('');

            return;
        }

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('service_requests', 1);
    }

    public static function projectDates(): array
    {
        return [
            'one year' => ['2026-09-28', '2027-09-28', null],
            'one day too long' => ['2026-09-28', '2027-09-29', 'project_end_date'],
            'ten years' => ['2026-09-28', '2036-09-28', 'project_end_date'],
            'leap year anniversary' => ['2024-02-29', '2025-02-28', null],
            'leap year overflow' => ['2024-02-29', '2025-03-01', 'project_end_date'],
            'end before start' => ['2026-09-28', '2026-09-27', 'project_end_date'],
            'invalid start' => ['invalid', '2027-09-28', 'project_start_date'],
            'invalid end' => ['2026-09-28', 'invalid', 'project_end_date'],
        ];
    }
}
