<?php

namespace Tests\Feature;

use App\Models\ServiceAccount;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Tests\DatabaseTestCase;

class ServiceWorkflowTest extends DatabaseTestCase
{
    public function test_approval_returns_to_request_details_after_an_attachment_loads_without_a_referrer(): void
    {
        Storage::fake('private');
        $staff = User::factory()->create(['is_active' => true]);
        $this->actingAs($staff);
        $path = 'attachments/screenshots/evidence.png';
        Storage::disk('private')->put($path, 'test evidence');
        $serviceRequest = $this->makeServiceRequest(['screenshot_evidence_path' => $path]);
        $detailUrl = route('admin.requests.show', $serviceRequest);
        $fileUrl = route('admin.requests.files.show', [$serviceRequest, 'screenshot_evidence']);

        $this->get($detailUrl)->assertOk()->assertHeader('Referrer-Policy', 'no-referrer');
        foreach (range(1, 2) as $attempt) {
            $this->get($fileUrl)->assertOk();
            $this->assertSame($fileUrl, session()->previousUrl());

            $this->patch(route('admin.requests.approve', $serviceRequest))
                ->assertRedirect($detailUrl)
                ->assertSessionHasNoErrors()
                ->assertSessionHas('success', "อนุมัติคำขอ {$serviceRequest->form_no} เรียบร้อยแล้ว");
            $this->assertDatabaseHas('service_requests', ['request_id' => $serviceRequest->request_id, 'status' => 'approved']);
            $this->assertDatabaseHas('approvals', ['request_id' => $serviceRequest->request_id, 'approver_name' => $staff->name]);
            $this->assertDatabaseCount('approvals', 1);
            $this->get($detailUrl)->assertOk()->assertSee('สร้างบัญชีให้ผู้ขอใช้บริการ');
        }
    }

    public function test_invalid_approval_returns_errors_to_request_details_instead_of_the_attachment(): void
    {
        Storage::fake('private');
        $this->actingAs(User::factory()->create(['is_active' => true]));
        $path = 'attachments/screenshots/rejected.png';
        Storage::disk('private')->put($path, 'test evidence');
        $serviceRequest = $this->makeServiceRequest(['status' => 'rejected', 'screenshot_evidence_path' => $path]);

        $this->get(route('admin.requests.files.show', [$serviceRequest, 'screenshot_evidence']))->assertOk();
        $this->patch(route('admin.requests.approve', $serviceRequest))
            ->assertRedirect(route('admin.requests.show', $serviceRequest))
            ->assertSessionHasErrors('status');
        $this->assertDatabaseHas('service_requests', ['request_id' => $serviceRequest->request_id, 'status' => 'rejected']);
        $this->assertDatabaseCount('approvals', 0);
    }

    public function test_new_account_password_is_encrypted_in_flash_storage(): void
    {
        $this->actingAs(User::factory()->create(['is_active' => true]));
        $serviceRequest = $this->makeServiceRequest(['status' => 'approved']);
        $password = 'temporary-secret-123';

        $this->post(route('admin.accounts.store', $serviceRequest), [
            'username' => 'new-service-account',
            'password' => $password,
            'account_type' => 'ssh',
        ])->assertRedirect(route('admin.accounts.index'))
            ->assertSessionMissing('new_password')
            ->assertSessionHas('encrypted_new_password');

        $encrypted = session('encrypted_new_password');
        $this->assertNotSame($password, $encrypted);
        $this->assertSame($password, Crypt::decryptString($encrypted));
        $this->get(route('admin.accounts.index'))
            ->assertOk()
            ->assertSee($password);
    }

    private function requestData(): array
    {
        return [
            'full_name' => 'Test Applicant',
            'staff_or_student_id' => '001234',
            'unit_name' => 'Test Unit',
            'affiliation' => 'Test University',
            'email' => 'applicant@example.test',
            'purpose_type' => '1.3_internal_admin',
            'project_start_date' => '2026-09-10',
            'project_end_date' => '2027-09-10',
            'service_type' => 'web_hosting',
            'enabled_services' => ['http_https'],
        ];
    }

    private function account(ServiceRequest $request): ServiceAccount
    {
        return ServiceAccount::create([
            'request_id' => $request->request_id,
            'applicant_id' => $request->applicant_id,
            'username' => 'account-'.$request->request_id,
            'password' => 'test-password',
            'status' => 'active',
        ]);
    }

    public function test_staff_can_edit_personnel_id_and_preserve_existing_login(): void
    {
        $this->actingAs(User::factory()->create(['is_active' => true]));
        $request = $this->makeServiceRequest();
        $account = $this->account($request);
        $this->get(route('admin.requests.edit', $request))
            ->assertOk()
            ->assertSee('name="staff_or_student_id"', false)
            ->assertDontSee('แก้ไขรหัสประจำตัวไม่ได้');

        $this->put(route('admin.requests.update', $request), $this->requestData())
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.requests.show', $request));

        $this->assertSame('001234', $request->fresh()->applicant->staff_or_student_id);
        $this->assertSame('001234', $account->fresh()->applicant->staff_or_student_id);
        $this->assertSame($account->username, $account->fresh()->username);
        $this->assertSame($account->password_hash, $account->fresh()->password_hash);
    }

    public function test_editing_shared_legacy_applicant_only_changes_the_selected_request(): void
    {
        $this->actingAs(User::factory()->create(['is_active' => true]));
        $request = $this->makeServiceRequest();
        $other = $this->makeServiceRequest(['applicant_id' => $request->applicant_id]);
        $account = $this->account($request);
        $otherAccount = $this->account($other);

        $this->put(route('admin.requests.update', $request), $this->requestData())
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.requests.show', $request));

        $this->assertNotEquals($other->applicant_id, $request->fresh()->applicant_id);
        $this->assertSame('001234', $account->fresh()->applicant->staff_or_student_id);
        $this->assertSame('TEST-001', $other->fresh()->applicant->staff_or_student_id);
        $this->assertSame('TEST-001', $otherAccount->fresh()->applicant->staff_or_student_id);
    }

    public function test_invalid_personnel_ids_are_rejected_without_changing_data(): void
    {
        $this->actingAs(User::factory()->create(['is_active' => true]));
        $request = $this->makeServiceRequest();
        foreach (['', str_repeat('1', 31), ['invalid']] as $id) {
            $this->put(route('admin.requests.update', $request), array_merge($this->requestData(), [
                'staff_or_student_id' => $id,
            ]))->assertSessionHasErrors('staff_or_student_id');
            $this->assertSame('TEST-001', $request->fresh()->applicant->staff_or_student_id);
            $this->get(route('admin.requests.edit', $request))->assertOk();
        }
    }

    public function test_guests_and_inactive_staff_cannot_change_personnel_ids(): void
    {
        $request = $this->makeServiceRequest();
        $this->put(route('admin.requests.update', $request), $this->requestData())
            ->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['is_active' => false]));
        $this->put(route('admin.requests.update', $request), $this->requestData())
            ->assertRedirect(route('login'));
        $this->assertSame('TEST-001', $request->fresh()->applicant->staff_or_student_id);
    }
}
