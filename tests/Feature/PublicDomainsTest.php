<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\ServiceAccount;
use Tests\DatabaseTestCase;

class PublicDomainsTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 9, 16)->setTime(12, 0));
    }

    private function domain(string $name, string $status = 'active', ?string $expiry = null): ServiceAccount
    {
        $request = $this->makeServiceRequest(['status' => 'approved']);
        Domain::create(['request_id' => $request->request_id, 'domain_name' => $name]);

        return ServiceAccount::create([
            'request_id' => $request->request_id,
            'applicant_id' => $request->applicant_id,
            'username' => 'private-user-'.$request->request_id,
            'password' => 'private-password',
            'status' => $status,
            'expire_date' => $expiry,
        ]);
    }

    public function test_guests_see_only_domains_with_an_active_unexpired_account(): void
    {
        $this->domain('future.example.test', 'active', '2027-01-01');
        $this->domain('today.example.test', 'active', '2026-09-16');
        $this->domain('no-expiry.example.test');
        $this->domain('disabled.example.test', 'disabled', '2027-01-01');
        $this->domain('expired-status.example.test', 'expired', '2027-01-01');
        $this->domain('past-date.example.test', 'active', '2026-09-15');
        $request = $this->makeServiceRequest();
        Domain::create(['request_id' => $request->request_id, 'domain_name' => 'pending.example.test']);

        $this->get(route('domains.index'))
            ->assertOk()
            ->assertSee('future.example.test')
            ->assertSee('today.example.test')
            ->assertSee('no-expiry.example.test')
            ->assertDontSee('disabled.example.test')
            ->assertDontSee('expired-status.example.test')
            ->assertDontSee('past-date.example.test')
            ->assertDontSee('pending.example.test')
            ->assertViewHas('totalDomains', 3)
            ->assertViewHas('groups', fn ($groups) => $groups->total() === 1 && $groups->first()->domain_count === 3)
            ->assertHeader('Cache-Control', 'no-store, private');
        $this->assertGuest();
    }

    public function test_duplicate_domains_and_multiple_accounts_do_not_duplicate_results(): void
    {
        $account = $this->domain('  Portal.Example.Test ');
        $this->domain('portal.example.test');
        $this->domain('portal.example.test', 'disabled');
        ServiceAccount::create([
            'request_id' => $account->request_id, 'applicant_id' => $account->applicant_id,
            'username' => 'second-account', 'password' => 'secret-test', 'status' => 'active',
        ]);
        $this->get(route('domains.index'))
            ->assertOk()
            ->assertViewHas('totalDomains', 1)
            ->assertViewHas('groups', fn ($groups) => $groups->total() === 1
                && $groups->first()->domain_count === 1
                && $groups->first()->domains->all() === ['portal.example.test']);
    }

    public function test_search_accepts_partial_names_and_website_urls_and_escapes_wildcards(): void
    {
        $this->domain('portal.example.test');
        $this->domain('other.example.test');
        foreach (['PORTAL', ' portal.example.test. ', 'https://PORTAL.example.test/home?ref=123'] as $query) {
            $this->get(route('domains.index', ['q' => $query]))
                ->assertOk()
                ->assertViewHas('totalDomains', 1)
                ->assertViewHas('groups', fn ($groups) => $groups->total() === 1
                    && $groups->first()->domain_count === 2
                    && $groups->first()->matching_count === 1
                    && $groups->first()->domains->all() === ['portal.example.test']);
        }
        foreach (['%', '_', 'missing', '0'] as $query) {
            $this->get(route('domains.index', ['q' => $query]))
                ->assertOk()
                ->assertSee('ไม่พบโดเมนที่มีบัญชีเปิดใช้งานตรงกับคำค้น')
                ->assertViewHas('totalDomains', 0)
                ->assertViewHas('groups', fn ($groups) => $groups->total() === 0);
        }
    }

    public function test_search_by_owner_returns_all_their_active_domains_and_correct_counts(): void
    {
        foreach (['สมชาย ใจดี', 'Alice Smith'] as $ownerName) {
            $first = $this->domain('first.example.test');
            $first->applicant->update(['full_name' => $ownerName]);
            $second = $this->domain('second.example.test');
            $second->applicant->update(['full_name' => $ownerName]);
            $duplicate = $this->domain('first.example.test');
            $duplicate->applicant->update(['full_name' => $ownerName]);
            $disabled = $this->domain('disabled.example.test', 'disabled');
            $disabled->applicant->update(['full_name' => $ownerName]);
            $expired = $this->domain('expired.example.test', 'active', '2026-09-15');
            $expired->applicant->update(['full_name' => $ownerName]);
        }
        $this->domain('unrelated.example.test');

        foreach (['สมชาย ใจดี', 'สมชาย', 'ใจดี', ' ALICE SMITH ', 'smith'] as $query) {
            $this->get(route('domains.index', ['q' => $query]))
                ->assertOk()
                ->assertViewHas('totalDomains', 2)
                ->assertViewHas('groups', fn ($groups) => $groups->total() === 1
                    && $groups->first()->domain_count === 2
                    && $groups->first()->matching_count === 2
                    && $groups->first()->domains->all() === ['first.example.test', 'second.example.test'])
                ->assertDontSee('disabled.example.test')
                ->assertDontSee('expired.example.test')
                ->assertDontSee('unrelated.example.test');
        }
    }

    public function test_owner_search_treats_wildcards_as_literal_characters(): void
    {
        $account = $this->domain('literal.example.test');
        $account->applicant->update(['full_name' => 'Owner 100%_!']);
        $this->domain('unrelated.example.test');

        foreach (['%', '_', '!', '100%_!'] as $query) {
            $this->get(route('domains.index', ['q' => $query]))
                ->assertOk()
                ->assertViewHas('totalDomains', 1)
                ->assertViewHas('groups', fn ($groups) => $groups->total() === 1
                    && $groups->first()->matching_count === 1
                    && $groups->first()->domains->all() === ['literal.example.test']);
        }
    }

    public function test_results_show_owner_names_without_exposing_accounts_or_private_fields(): void
    {
        $account = $this->domain('public.example.test');
        $this->get(route('domains.index'))
            ->assertOk()
            ->assertSee('Test Applicant')
            ->assertDontSee('TEST-001')
            ->assertDontSee('applicant@example.test')
            ->assertDontSee($account->username)
            ->assertDontSee($account->password_hash)
            ->assertDontSee('private-password')
            ->assertViewHas('groups', fn ($groups) => array_keys((array) $groups->first()) === ['owner_name', 'domain_count', 'matching_count', 'domains']);
    }

    public function test_pagination_keeps_owners_together_and_preserves_search(): void
    {
        for ($index = 11; $index >= 1; $index--) {
            $account = $this->domain(sprintf('portal-%02d.example.test', $index));
            $account->applicant->update([
                'full_name' => sprintf('Owner %02d', $index),
                'staff_or_student_id' => "OWNER-{$index}",
            ]);
            Domain::create(['request_id' => $account->request_id, 'domain_name' => sprintf('portal-%02d-extra.example.test', $index)]);
        }
        $this->domain('different.example.test');
        $this->get(route('domains.index', ['q' => 'portal']))
            ->assertOk()
            ->assertViewHas('totalDomains', 22)
            ->assertViewHas('groups', fn ($groups) => $groups->total() === 11
                && $groups->count() === 10
                && $groups->first()->owner_name === 'Owner 01'
                && $groups->first()->domains->all() === ['portal-01-extra.example.test', 'portal-01.example.test']
                && str_contains($groups->nextPageUrl(), 'q=portal'));
        $this->get(route('domains.index', ['q' => 'portal', 'page' => 2]))
            ->assertOk()
            ->assertViewHas('groups', fn ($groups) => $groups->count() === 1
                && $groups->first()->owner_name === 'Owner 11'
                && $groups->first()->domains->count() === 2);
    }

    public function test_same_names_merge_even_with_different_or_missing_identity_fields(): void
    {
        $this->domain('one.example.test');
        $other = $this->domain('two.example.test');
        $other->applicant->update(['staff_or_student_id' => 'OTHER-002']);
        $third = $this->domain('three.example.test');
        $third->applicant->update(['email' => 'other@example.test']);
        foreach (['four', 'five'] as $name) {
            $account = $this->domain("{$name}.example.test");
            $account->applicant->update(['email' => null, 'full_name' => '  test applicant  ']);
        }
        $duplicate = $this->domain('ONE.example.test');
        $duplicate->applicant->update(['staff_or_student_id' => 'DUPLICATE']);

        $this->get(route('domains.index'))
            ->assertOk()
            ->assertViewHas('totalDomains', 5)
            ->assertViewHas('groups', fn ($groups) => $groups->total() === 1
                && $groups->first()->domain_count === 5
                && $groups->first()->domains->all() === ['five.example.test', 'four.example.test', 'one.example.test', 'three.example.test', 'two.example.test']);
    }

    public function test_different_names_stay_separate_even_with_matching_identity_fields(): void
    {
        $this->domain('one.example.test');
        $account = $this->domain('two.example.test');
        $account->applicant->update(['full_name' => 'Another Applicant']);

        $this->get(route('domains.index'))
            ->assertOk()
            ->assertViewHas('groups', fn ($groups) => $groups->total() === 2
                && $groups->every(fn ($group) => $group->domain_count === 1 && $group->domains->count() === 1));
    }

    public function test_shared_domains_are_counted_once_per_owner_and_owner_names_are_escaped(): void
    {
        $this->domain('shared.example.test');
        $account = $this->domain('shared.example.test');
        $account->applicant->update(['staff_or_student_id' => 'OTHER', 'full_name' => '<script>owner</script>']);
        $this->get(route('domains.index'))
            ->assertOk()
            ->assertViewHas('totalDomains', 1)
            ->assertViewHas('groups', fn ($groups) => $groups->total() === 2
                && $groups->every(fn ($group) => $group->domain_count === 1))
            ->assertSee('&lt;script&gt;owner&lt;/script&gt;', false)
            ->assertDontSee('<script>owner</script>', false);
    }

    public function test_invalid_input_returns_to_search_safely_and_search_text_is_escaped(): void
    {
        foreach ([['q' => ['bad']], ['q' => str_repeat('a', 256)], ['page' => 0], ['page' => 'wrong']] as $input) {
            $this->getJson(route('domains.index', $input))->assertUnprocessable();
        }
        $this->get(route('domains.index', ['q' => ['bad']]))
            ->assertRedirect(route('domains.index'))
            ->assertSessionHasErrors('q');
        $this->get(route('domains.index'))->assertOk();
        $this->get(route('domains.index', ['q' => '<script>alert(1)</script>']))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_status_changes_are_reflected_and_home_links_to_the_directory(): void
    {
        $account = $this->domain('changing.example.test');
        $this->get(route('domains.index'))->assertSee('changing.example.test');
        $account->update(['status' => 'disabled']);
        $this->get(route('domains.index'))
            ->assertOk()
            ->assertDontSee('changing.example.test')
            ->assertSee('ยังไม่มีโดเมนที่มีบัญชีเปิดใช้งานในขณะนี้');
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('domains.index'))
            ->assertSee('ตรวจสอบโดเมนของคุณ');
    }
}
