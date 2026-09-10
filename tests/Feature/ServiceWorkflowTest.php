<?php

namespace Tests\Feature;

use App\Models\ServiceAccount;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\DatabaseTestCase;

class ServiceWorkflowTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
        Storage::fake('public');
        $this->travelTo(now()->setDate(2026, 9, 10)->startOfDay());
    }

    private function submission(array $overrides = []): array
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aX1sAAAAASUVORK5CYII=');

        return array_merge([
            'full_name' => 'New Applicant', 'staff_or_student_id' => 'TEST-001',
            'unit_name' => 'New Unit', 'affiliation' => 'Test University', 'email' => 'applicant@example.test',
            'purpose_type' => '1.3_internal_admin', 'project_start_date' => '2026-09-10',
            'project_end_date' => '2027-09-10', 'service_type' => 'web_hosting',
            'developers' => [['full_name' => 'Test Developer']], 'enabled_services' => ['http_https'],
            'domain_name' => 'test.example.test', 'agree_to_pay' => 1, 'accepted' => 1,
            'system_detail_doc' => UploadedFile::fake()->createWithContent('details.pdf', '%PDF-1.4 test document'),
            'signature_image' => UploadedFile::fake()->createWithContent('signature.png', $png),
        ], $overrides);
    }

    private function accountData(array $overrides = []): array
    {
        return array_merge([
            'username' => 'test-account', 'password' => 'Example-Password-123',
            'account_type' => 'control_panel', 'expire_date' => '2027-09-10',
            'created_by' => 'Forged name',
        ], $overrides);
    }

    public function test_public_submission_preserves_existing_applicant_and_returns_reference(): void
    {
        $old = $this->makeServiceRequest();
        $this->get(route('service-requests.create'))->assertOk();
        $this->post(route('service-requests.store'), $this->submission())
            ->assertRedirect(route('service-requests.create'))->assertSessionHasNoErrors();
        $new = ServiceRequest::latest('request_id')->firstOrFail();
        $this->assertNotSame($old->applicant_id, $new->applicant_id);
        $this->assertSame('Test Applicant', $old->fresh()->applicant->full_name);
        $this->assertSame('New Applicant', $new->applicant->full_name);
        $this->assertStringStartsWith('REQ-', $new->form_no);
        $this->assertStringContainsString($new->form_no, session('success'));
        $this->assertSame('submitted', $new->status);
        Storage::disk('private')->assertExists($new->system_detail_doc_path);
        Storage::disk('private')->assertExists($new->signature_image_path);
        Storage::disk('public')->assertMissing($new->signature_image_path);
        $this->assertCount(1, $new->developers);
        $this->assertCount(1, $new->domains);
    }

    public function test_invalid_submission_does_not_write_records_or_uploads(): void
    {
        $this->post(route('service-requests.store'), $this->submission([
            'project_end_date' => '2026-01-01', 'accepted' => 0, 'agree_to_pay' => 0, 'request_fee_waiver' => 0,
        ]))->assertSessionHasErrors(['project_end_date', 'accepted', 'agree_to_pay']);
        $this->assertDatabaseCount('service_requests', 0);
        $this->assertDatabaseCount('applicants', 0);
        $this->assertSame([], Storage::disk('private')->allFiles());
    }

    public function test_submit_approve_create_edit_renew_and_suspend_workflow(): void
    {
        $this->post(route('service-requests.store'), $this->submission())->assertSessionHasNoErrors();
        $request = ServiceRequest::firstOrFail();
        $staff = User::factory()->create(['name' => 'Test Officer', 'is_active' => true]);
        $this->actingAs($staff);
        $this->get(route('admin.requests.show', $request))->assertOk();
        $this->get(route('admin.accounts.create', $request))->assertRedirect(route('admin.requests.show', $request));
        $this->post(route('admin.accounts.store', $request), $this->accountData())->assertSessionHasErrors('status');
        $this->assertDatabaseCount('service_accounts', 0);

        $this->patch(route('admin.requests.approve', $request))->assertSessionHasNoErrors();
        $this->patch(route('admin.requests.approve', $request))->assertSessionHasNoErrors();
        $this->assertSame('approved', $request->fresh()->status);
        $this->assertNull($request->fresh()->receipt_no);
        $this->assertNull($request->fresh()->receipt_date);
        $this->assertDatabaseCount('approvals', 1);
        $this->get(route('admin.accounts.create', $request))->assertOk()->assertSee('Test Officer');
        $this->post(route('admin.accounts.store', $request), $this->accountData())
            ->assertRedirect(route('admin.accounts.index'))->assertSessionHasNoErrors();
        $account = ServiceAccount::firstOrFail();
        $this->assertSame('Test Officer', $account->created_by);
        $this->assertTrue(Hash::check('Example-Password-123', $account->password_hash));
        $this->post(route('admin.accounts.store', $request), $this->accountData(['username' => 'another-name']))
            ->assertSessionHasErrors('username');
        $this->assertDatabaseCount('service_accounts', 1);

        $this->get(route('admin.accounts.edit', $account))->assertOk();
        $this->put(route('admin.accounts.update', $account), $this->accountData([
            'username' => 'updated-account', 'status' => 'active', 'password' => '',
        ]))->assertSessionHasNoErrors();
        $this->assertSame('updated-account', $account->fresh()->username);
        $this->assertTrue(Hash::check('Example-Password-123', $account->fresh()->password_hash));
        $this->post(route('admin.accounts.renew.store', $account), [
            'previous_expire_date' => '2027-09-10', 'expire_date' => '2028-09-10', 'note' => 'Test renewal',
        ])->assertSessionHasNoErrors();
        $this->patch(route('admin.accounts.toggle-status', $account))->assertSessionHasNoErrors();
        $this->assertSame('disabled', $account->fresh()->status);
        $this->assertSame('2028-09-10', $account->fresh()->expire_date);

        foreach (['admin.requests.index', 'admin.accounts.index', 'admin.domains.index', 'admin.users.index'] as $route) {
            $this->get(route($route))->assertOk();
        }
        $this->get(route('admin.domains.show', $request->domains->first()))->assertOk();
        $this->get(route('admin.domains.edit', $request->domains->first()))->assertOk();
        $this->get(route('admin.requests.edit', $request))->assertOk();
        $this->get(route('admin.accounts.renew', $account))->assertOk()->assertSee('Test renewal');
    }

    public function test_legacy_shared_applicant_is_detached_when_one_request_is_edited(): void
    {
        $first = $this->makeServiceRequest();
        $second = $this->makeServiceRequest(['applicant_id' => $first->applicant_id]);
        $this->actingAs(User::factory()->create(['is_active' => true]));
        $this->put(route('admin.requests.update', $second), collect($this->submission())->except([
            'system_detail_doc', 'signature_image',
        ])->all())->assertSessionHasNoErrors();
        $this->assertSame('Test Applicant', $first->fresh()->applicant->full_name);
        $this->assertSame('New Applicant', $second->fresh()->applicant->full_name);
        $this->assertNotSame($first->fresh()->applicant_id, $second->fresh()->applicant_id);
    }

    public function test_rejected_requests_cannot_be_approved_or_provisioned(): void
    {
        $request = $this->makeServiceRequest(['status' => 'rejected']);
        $this->actingAs(User::factory()->create(['is_active' => true]));
        $this->patch(route('admin.requests.approve', $request))->assertSessionHasErrors('status');
        $this->post(route('admin.accounts.store', $request), $this->accountData())->assertSessionHasErrors('status');
        $this->assertDatabaseCount('approvals', 0);
        $this->assertDatabaseCount('service_accounts', 0);
    }
}
