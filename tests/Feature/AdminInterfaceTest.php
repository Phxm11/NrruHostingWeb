<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\ServiceAccount;
use App\Models\User;
use Tests\DatabaseTestCase;

class AdminInterfaceTest extends DatabaseTestCase
{
    public function test_all_admin_workflows_render_with_the_shared_interface(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $this->actingAs($user);
        $request = $this->makeServiceRequest(['status' => 'approved']);
        $unassigned = $this->makeServiceRequest(['status' => 'approved']);
        $account = ServiceAccount::create([
            'request_id' => $request->request_id,
            'applicant_id' => $request->applicant_id,
            'username' => 'interface-test',
            'password' => 'test-password',
            'expire_date' => '2027-09-15',
        ]);
        $domain = Domain::create(['request_id' => $request->request_id, 'domain_name' => 'interface.example.test']);

        foreach ([
            'admin.requests.index' => [],
            'admin.requests.show' => [$request],
            'admin.requests.edit' => [$request],
            'admin.accounts.index' => [],
            'admin.accounts.create' => [$unassigned],
            'admin.accounts.edit' => [$account],
            'admin.accounts.renew' => [$account],
            'admin.domains.index' => [],
            'admin.domains.show' => [$domain],
            'admin.domains.edit' => [$domain],
            'admin.users.index' => [],
            'admin.users.create' => [],
            'admin.users.edit' => [$user],
            'admin.reports.index' => [],
        ] as $route => $parameters) {
            $this->get(route($route, $parameters))
                ->assertOk()
                ->assertSee('css/admin/admin.css')
                ->assertSee('js/admin.js')
                ->assertSee('รายงานผู้บริหาร');
        }
    }
}
