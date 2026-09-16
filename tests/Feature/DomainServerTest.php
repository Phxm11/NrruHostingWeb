<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\ServiceAccount;
use App\Models\User;
use Tests\DatabaseTestCase;

class DomainServerTest extends DatabaseTestCase
{
    private function domain(string $name = 'portal.example.test'): Domain
    {
        $request = $this->makeServiceRequest();

        return Domain::create(['request_id' => $request->request_id, 'domain_name' => $name]);
    }

    public function test_staff_can_save_and_clear_server_name_without_changing_other_domains(): void
    {
        $this->actingAs(User::factory()->create(['is_active' => true]));
        $domain = $this->domain();
        $other = $this->domain('other.example.test');
        $this->put(route('admin.domains.update', $domain), [
            'domain_name' => $domain->domain_name, 'server_name' => ' hosting-01 ',
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.domains.index'));
        $this->assertSame('hosting-01', $domain->fresh()->server_name);
        $this->assertNull($other->fresh()->server_name);

        $this->put(route('admin.domains.update', $domain), [
            'domain_name' => $domain->domain_name, 'server_name' => '',
        ])->assertSessionHasNoErrors();
        $this->assertNull($domain->fresh()->server_name);
    }

    public function test_invalid_server_input_is_rejected_and_form_can_redisplay_it(): void
    {
        $this->actingAs(User::factory()->create(['is_active' => true]));
        $domain = $this->domain();
        foreach ([
            ['server_name' => ['hosting']],
            ['server_name' => str_repeat('a', 151)],
        ] as $input) {
            $this->from(route('admin.domains.edit', $domain))
                ->put(route('admin.domains.update', $domain), array_merge(['domain_name' => $domain->domain_name], $input))
                ->assertSessionHasErrors(array_keys($input));
            $this->get(route('admin.domains.edit', $domain))->assertOk();
            $this->assertNull($domain->fresh()->server_name);
        }
    }

    public function test_admin_can_search_and_view_server_details(): void
    {
        $this->actingAs(User::factory()->create(['is_active' => true]));
        $domain = $this->domain();
        $domain->update(['server_name' => 'hosting-01']);
        $this->domain('other.example.test');
        foreach (['hosting-01', 'hosting'] as $query) {
            $this->get(route('admin.domains.index', ['q' => $query]))
                ->assertOk()->assertSee('portal.example.test')->assertDontSee('other.example.test')
                ->assertSee('hosting-01')->assertDontSee('IP Address');
        }
        foreach (['admin.domains.show', 'admin.domains.edit'] as $route) {
            $this->get(route($route, $domain))->assertOk()->assertSee('hosting-01')->assertDontSee('IP Address')->assertDontSee('name="server_ip"', false);
        }
    }

    public function test_public_directory_does_not_expose_server_details(): void
    {
        $domain = $this->domain();
        $domain->update(['server_name' => 'private-host-01']);
        ServiceAccount::create([
            'request_id' => $domain->request_id, 'applicant_id' => $domain->serviceRequest->applicant_id,
            'username' => 'test-account', 'password' => 'test-password', 'status' => 'active',
        ]);
        $this->get(route('domains.index'))->assertOk()->assertSee('portal.example.test')
            ->assertDontSee('private-host-01');
        $this->get(route('domains.index', ['q' => 'private-host-01']))->assertViewHas('totalDomains', 0);
    }

    public function test_guests_and_inactive_staff_cannot_access_or_edit_server_details(): void
    {
        $domain = $this->domain();
        foreach ([null, User::factory()->create(['is_active' => false])] as $user) {
            if ($user) {
                $this->actingAs($user);
            }
            foreach (['admin.domains.index', 'admin.domains.show', 'admin.domains.edit'] as $route) {
                $this->get(route($route, $route === 'admin.domains.index' ? [] : [$domain]))->assertRedirect(route('login'));
            }
            $this->put(route('admin.domains.update', $domain), [
                'domain_name' => $domain->domain_name, 'server_name' => 'unauthorized',
            ])->assertRedirect(route('login'));
        }
        $this->assertNull($domain->fresh()->server_name);
    }

    public function test_existing_domains_allow_missing_server_details_and_escape_server_names(): void
    {
        $this->actingAs(User::factory()->create(['is_active' => true]));
        $domain = $this->domain();
        $this->get(route('admin.domains.index'))->assertOk()->assertSee('—');
        $this->put(route('admin.domains.update', $domain), [
            'domain_name' => $domain->domain_name, 'server_name' => '<script>host</script>',
        ])->assertSessionHasNoErrors();
        $this->put(route('admin.domains.update', $domain), ['domain_name' => $domain->domain_name])->assertSessionHasNoErrors();
        $this->assertSame('<script>host</script>', $domain->fresh()->server_name);
        $this->get(route('admin.domains.index'))->assertSee('&lt;script&gt;host&lt;/script&gt;', false)->assertDontSee('<script>host</script>', false);
    }
}
