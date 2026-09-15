<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\ServiceAccount;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\DatabaseTestCase;

class ExecutiveReportTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 9, 15)->setTime(10, 30));
    }

    private function login(): void
    {
        $this->actingAs(User::factory()->create(['is_active' => true]));
    }

    private function account(ServiceRequest $request, ?string $expiry, string $status = 'active'): ServiceAccount
    {
        return ServiceAccount::create([
            'request_id' => $request->request_id,
            'applicant_id' => $request->applicant_id,
            'username' => 'report-'.bin2hex(random_bytes(6)),
            'password' => 'report-test-password',
            'expire_date' => $expiry,
            'status' => $status,
        ]);
    }

    public function test_guests_and_inactive_staff_cannot_access_reports_or_print(): void
    {
        foreach (['admin.reports.index', 'admin.reports.print'] as $route) {
            $this->get(route($route))->assertRedirect(route('login'));
            $this->actingAs(User::factory()->create(['is_active' => false]));
            $this->get(route($route))->assertRedirect(route('login'));
            $this->assertGuest();
        }
    }

    public function test_report_counts_each_request_once_and_includes_created_accounts_and_unknown_types(): void
    {
        $this->login();
        $approved = $this->makeServiceRequest(['request_date' => '2026-09-01', 'status' => 'approved']);
        $this->account($approved, '2027-01-01');
        $this->account($approved, '2027-01-01');
        foreach (['one.example.test', 'two.example.test'] as $domain) {
            Domain::create(['request_id' => $approved->request_id, 'domain_name' => $domain]);
        }
        $this->makeServiceRequest(['request_date' => '2026-09-30', 'service_type' => 'virtual_server']);
        $this->makeServiceRequest(['service_type' => null, 'status' => 'draft']);
        $this->makeServiceRequest(['request_date' => '2026-08-31']);
        $this->makeServiceRequest(['request_date' => '2026-10-01']);

        $this->get(route('admin.reports.index', ['start_date' => '2026-09-01', 'end_date' => '2026-09-30']))
            ->assertOk()
            ->assertSee('รายงานผู้บริหาร')
            ->assertViewHas('totalRequests', 3)
            ->assertViewHas('domainCount', 2)
            ->assertViewHas('statusCounts', fn ($counts) => $counts['approved'] === 1 && $counts['submitted'] === 1 && $counts['draft'] === 1)
            ->assertViewHas('serviceCounts', fn ($counts) => $counts['web_hosting'] === 1 && $counts['virtual_server'] === 1 && $counts[''] === 1)
            ->assertViewHas('departments', fn ($rows) => $rows->sum('total') === 3)
            ->assertViewHas('serviceRequests', fn ($rows) => $rows->total() === 3)
            ->assertDontSee('report-test-password');
    }

    public function test_months_include_zeroes_and_same_named_units_in_different_affiliations_stay_separate(): void
    {
        $this->login();
        $this->makeServiceRequest(['request_date' => '2025-12-31']);
        $other = $this->makeServiceRequest(['request_date' => '2026-02-01']);
        $other->applicant->update(['affiliation' => 'Another University']);
        $this->makeServiceRequest(['request_date' => '2026-02-02', 'service_type' => 'virtual_server']);
        $this->makeServiceRequest(['request_date' => '2025-12-30']);

        $this->get(route('admin.reports.index', [
            'start_date' => '2025-12-31', 'end_date' => '2026-02-01', 'service_type' => 'web_hosting',
        ]))
            ->assertOk()
            ->assertViewHas('totalRequests', 2)
            ->assertViewHas('months', fn ($months) => $months->pluck('total')->all() === [1, 0, 1] && $months->first()['label'] === '12/2568')
            ->assertViewHas('departments', fn ($rows) => $rows->count() === 2);
    }

    public function test_current_accounts_use_expiry_boundaries_and_are_independent_of_request_period(): void
    {
        $this->login();
        $old = $this->makeServiceRequest(['request_date' => '2020-01-01']);
        $this->account($old, '2026-09-14');
        $this->account($old, '2026-09-15');
        $this->account($old, '2026-10-15');
        $this->account($old, '2026-10-16');
        $this->account($old, null);
        $this->account($old, null, 'expired');
        $this->account($old, '2026-01-01', 'disabled');
        $this->account($old, '2026-10-01', 'disabled');
        $virtual = $this->makeServiceRequest(['service_type' => 'virtual_server']);
        $this->account($virtual, null);

        $this->get(route('admin.reports.index', ['service_type' => 'web_hosting']))
            ->assertOk()
            ->assertViewHas('totalRequests', 0)
            ->assertViewHas('accountSummary', fn ($summary) => (int) $summary->total === 8
                && (int) $summary->active === 4
                && (int) $summary->expired === 2
                && (int) $summary->disabled === 2
                && (int) $summary->expiring === 2
                && (int) $summary->no_expiry === 1);
        $this->assertSame('active', $old->serviceAccounts()->first()->status);
    }

    public function test_renewal_events_use_their_own_dates_and_count_unique_accounts(): void
    {
        $this->login();
        $old = $this->makeServiceRequest(['request_date' => '2020-01-01']);
        $account = $this->account($old, '2027-01-01');
        foreach (['2026-08-31 23:59:59', '2026-09-01 00:00:00', '2026-09-30 23:59:59', '2026-10-01 00:00:00'] as $date) {
            DB::table('service_renewals')->insert([
                'account_id' => $account->account_id, 'expire_date' => '2027-01-01',
                'previous_status' => 'active', 'renewed_by_name' => 'Officer', 'created_at' => $date,
            ]);
        }
        $filters = ['start_date' => '2026-09-01', 'end_date' => '2026-09-30', 'service_type' => 'web_hosting'];
        foreach (['admin.reports.index', 'admin.reports.print'] as $route) {
            $this->get(route($route, $filters))
                ->assertOk()
                ->assertViewHas('totalRequests', 0)
                ->assertViewHas('renewalCount', 2)
                ->assertViewHas('renewedAccountCount', 1);
            $this->get(route($route, array_merge($filters, ['service_type' => 'virtual_server'])))
                ->assertOk()
                ->assertViewHas('renewalCount', 0);
        }
    }

    public function test_invalid_filters_are_rejected_on_both_routes(): void
    {
        $this->login();
        foreach (['admin.reports.index', 'admin.reports.print'] as $route) {
            foreach ([
                ['start_date' => 'invalid'],
                ['start_date' => '2026-02-30'],
                ['start_date' => ''],
                ['start_date' => ['2026-01-01']],
                ['start_date' => '1800-01-01'],
                ['end_date' => '2200-01-01'],
                ['start_date' => '2026-09-30', 'end_date' => '2026-09-01'],
                ['service_type' => 'invalid'],
            ] as $filters) {
                $this->getJson(route($route, $filters))->assertUnprocessable();
            }
        }
    }

    public function test_empty_report_defaults_to_current_year_and_print_is_ready_for_pdf(): void
    {
        $this->login();
        $this->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSee('ไม่พบคำขอในช่วงวันที่')
            ->assertSee('ทุกประเภทบริการ')
            ->assertViewHas('filters', ['start_date' => '2026-01-01', 'end_date' => '2026-09-15'])
            ->assertViewHas('totalRequests', 0);
        $this->get(route('admin.reports.print'))
            ->assertOk()
            ->assertSee('window.print()', false)
            ->assertSee('@page { size: A4 portrait;', false)
            ->assertDontSee('id="sidebar"', false)
            ->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_pagination_preserves_filters_while_print_includes_every_department(): void
    {
        $this->login();
        for ($index = 1; $index <= 21; $index++) {
            $request = $this->makeServiceRequest();
            $request->applicant->update(['unit_name' => sprintf('Unit %02d', $index)]);
        }
        $filters = ['start_date' => '2026-09-01', 'end_date' => '2026-09-30', 'service_type' => 'web_hosting'];
        $this->get(route('admin.reports.index', $filters))
            ->assertOk()
            ->assertViewHas('totalRequests', 21)
            ->assertViewHas('serviceRequests', fn ($rows) => $rows->count() === 20 && str_contains($rows->nextPageUrl(), 'service_type=web_hosting'));
        $this->get(route('admin.reports.print', $filters + ['page' => 2]))
            ->assertOk()
            ->assertViewHas('departments', fn ($rows) => $rows->count() === 21)
            ->assertSee('Unit 01')
            ->assertSee('Unit 21');
    }
}
