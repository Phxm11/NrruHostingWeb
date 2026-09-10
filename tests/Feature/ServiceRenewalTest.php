<?php

namespace Tests\Feature;

use App\Models\ServiceAccount;
use App\Models\User;
use Tests\DatabaseTestCase;

class ServiceRenewalTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        User::factory()->create(['id' => 1, 'name' => 'Test Officer', 'is_active' => true]);
        $this->travelTo(now()->setDate(2026, 9, 10)->startOfDay());
    }

    private function account(?string $date = '2026-10-01', string $status = 'active'): ServiceAccount
    {
        $request = $this->makeServiceRequest(['status' => 'approved']);

        return ServiceAccount::create([
            'request_id' => $request->request_id, 'applicant_id' => $request->applicant_id,
            'username' => 'renew-test-'.$request->request_id, 'password' => 'test-password',
            'status' => $status, 'expire_date' => $date,
        ]);
    }

    private function login(): void
    {
        $this->actingAs((new User(['name' => 'Test Officer', 'is_active' => true]))->forceFill(['id' => 1]));
    }

    public function test_guests_cannot_view_or_renew_accounts(): void
    {
        $account = $this->account();
        $url = route('admin.accounts.renew', $account);
        $this->get($url)->assertRedirect(route('login'));
        $this->post($url, [])->assertRedirect(route('login'));
        $this->assertDatabaseCount('service_renewals', 0);
    }

    public function test_renewal_records_history_and_rejects_repeated_submission(): void
    {
        $this->login();
        $account = $this->account();
        $url = route('admin.accounts.renew', $account);
        $this->get($url)->assertOk()->assertSee('2027-10-01');
        $data = ['previous_expire_date' => '2026-10-01', 'expire_date' => '2027-10-01', 'note' => 'REF-123'];
        $this->post($url, $data)->assertRedirect($url)->assertSessionHasNoErrors();
        $this->assertSame('2027-10-01', $account->fresh()->expire_date);
        $this->assertDatabaseHas('service_renewals', ['account_id' => $account->account_id, 'renewed_by' => 1, 'note' => 'REF-123', 'previous_expire_date' => '2026-10-01']);
        $this->post($url, $data)->assertSessionHasErrors('expire_date');
        $this->assertDatabaseCount('service_renewals', 1);
        $this->get($url)->assertOk()->assertSee('REF-123');
    }

    public function test_invalid_dates_do_not_change_the_account(): void
    {
        $this->login();
        $account = $this->account();
        foreach (['2026-09-01', '2026-10-01', 'invalid', '2027-02-30'] as $date) {
            $this->post(route('admin.accounts.renew.store', $account), [
                'previous_expire_date' => '2026-10-01', 'expire_date' => $date,
            ])->assertSessionHasErrors('expire_date');
        }
        $this->assertSame('2026-10-01', $account->fresh()->expire_date);
        $this->assertDatabaseCount('service_renewals', 0);
    }

    public function test_expired_accounts_reactivate_but_disabled_accounts_remain_disabled(): void
    {
        $this->login();
        foreach (['expired' => 'active', 'disabled' => 'disabled'] as $before => $after) {
            $account = $this->account(null, $before);
            $this->get(route('admin.accounts.renew', $account))->assertOk()->assertSee('2027-09-10');
            $this->post(route('admin.accounts.renew.store', $account), [
                'previous_expire_date' => '', 'expire_date' => '2027-09-10',
            ])->assertSessionHasNoErrors();
            $this->assertSame($after, $account->fresh()->status);
        }
        $this->assertDatabaseCount('service_renewals', 2);
    }
}
