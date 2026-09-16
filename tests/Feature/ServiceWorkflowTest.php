<?php

namespace Tests\Feature;

use App\Models\ServiceAccount;
use App\Models\ServiceRequest;
use App\Models\User;
use Tests\DatabaseTestCase;

class ServiceWorkflowTest extends DatabaseTestCase
{
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
