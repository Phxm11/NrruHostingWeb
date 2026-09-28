<?php

namespace Tests\Feature;

use App\Models\ServiceAccount;
use App\Models\User;
use Tests\DatabaseTestCase;

class ServiceAccountStatusTest extends DatabaseTestCase
{
    public function test_account_filters_counts_and_badges_match_the_report_at_expiry_boundaries(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 28)->startOfDay());
        $this->actingAs(User::factory()->create(['is_active' => true]));
        $expected = ['active' => [], 'disabled' => [], 'expired' => []];
        foreach ([
            ['active', '2026-09-27', 'expired'],
            ['active', '2026-09-28', 'active'],
            ['active', '2026-09-29', 'active'],
            ['active', null, 'active'],
            ['expired', null, 'expired'],
            ['expired', '2026-09-29', 'expired'],
            ['disabled', '2026-09-27', 'disabled'],
            ['disabled', null, 'disabled'],
        ] as [$status, $expiry, $effectiveStatus]) {
            $request = $this->makeServiceRequest(['status' => 'approved']);
            $account = ServiceAccount::create([
                'request_id' => $request->request_id,
                'applicant_id' => $request->applicant_id,
                'username' => "expiry-test-{$request->request_id}",
                'password' => 'test-password',
                'status' => $status,
                'expire_date' => $expiry,
            ]);
            $expected[$effectiveStatus][] = $account->account_id;
        }

        $counts = ['all' => 8, 'active' => 3, 'disabled' => 2, 'expired' => 3];
        foreach ($expected as $status => $ids) {
            foreach (['name', 'expire_soon'] as $sort) {
                $response = $this->get(route('admin.accounts.index', ['status' => $status, 'sort' => $sort]))
                    ->assertOk()
                    ->assertViewHas('statusCounts', $counts);
                $this->assertEqualsCanonicalizing($ids, $response->viewData('accounts')->pluck('account_id')->all());
                $this->assertSame(count($ids), substr_count($response->getContent(), "class=\"status-label status-{$status}\""));
            }
        }

        $summary = $this->get(route('admin.reports.index'))->assertOk()->viewData('accountSummary');
        foreach ($expected as $status => $ids) {
            $this->assertSame(count($ids), (int) $summary->{$status});
        }

        $this->get(route('admin.accounts.index', ['q' => 'expiry-test-1', 'status' => 'expired']))
            ->assertViewHas('statusCounts', ['all' => 1, 'active' => 0, 'disabled' => 0, 'expired' => 1]);
        $this->assertDatabaseHas('service_accounts', ['username' => 'expiry-test-1', 'status' => 'active']);
    }
}
