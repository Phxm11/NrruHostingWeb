<?php

namespace Tests\Feature;

use App\Models\ResourcePlan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;
use Tests\DatabaseTestCase;

class HandoverInstallationTest extends DatabaseTestCase
{
    public function test_master_seed_is_repeatable_and_does_not_create_a_default_admin(): void
    {
        $this->seed(DatabaseSeeder::class);
        ResourcePlan::first()->update(['fee_per_year' => 999]);
        $this->seed(DatabaseSeeder::class);
        $this->assertDatabaseCount('resource_plans', 5);
        $this->assertDatabaseCount('department_codes', 7);
        $this->assertDatabaseCount('users', 0);
        $this->assertEquals(999, ResourcePlan::first()->fee_per_year);
    }

    public function test_staff_command_creates_an_account_with_a_chosen_password(): void
    {
        $this->artisan('staff:create')->expectsQuestion('ชื่อเจ้าหน้าที่', 'Test Officer')
            ->expectsQuestion('อีเมล', 'officer@example.test')
            ->expectsQuestion('รหัสผ่าน (อย่างน้อย 12 ตัวอักษร)', 'Chosen-Password-123')
            ->expectsQuestion('ยืนยันรหัสผ่าน', 'Chosen-Password-123')->assertSuccessful();
        $user = User::firstOrFail();
        $this->assertTrue($user->isActive());
        $this->assertTrue(Hash::check('Chosen-Password-123', $user->password));
        $this->post(route('login'), ['email' => $user->email, 'password' => 'Chosen-Password-123'])->assertRedirect();
        $this->assertAuthenticatedAs($user);
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_staff_cannot_disable_their_own_account_through_edit_form(): void
    {
        $staff = User::factory()->create(['is_active' => true]);
        $this->actingAs($staff)->put(route('admin.users.update', $staff), [
            'name' => $staff->name, 'email' => $staff->email, 'is_active' => 0,
        ])->assertSessionHasErrors('is_active');
        $this->assertTrue($staff->fresh()->isActive());
    }
}
