<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Tests\DatabaseTestCase;

class LoginTest extends DatabaseTestCase
{
    public function test_login_page_has_no_password_recovery_link(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('css/login.css')
            ->assertSee('images/logo.png')
            ->assertSee('autocomplete="current-password"', false)
            ->assertDontSee('forgot-password')
            ->assertDontSee('ลืมรหัสผ่าน');
    }

    public function test_active_staff_can_login_and_logout(): void
    {
        $user = User::factory()->create(['is_active' => true, 'password' => 'test-password']);
        $this->post(route('login'), ['email' => $user->email, 'password' => 'test-password', 'remember' => '1'])
            ->assertRedirect('/admin/requests');
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->remember_token);
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_failed_login_keeps_email_and_displays_error_without_password(): void
    {
        $user = User::factory()->create(['is_active' => true, 'password' => 'test-password']);
        $this->from(route('login'))->post(route('login'), ['email' => $user->email, 'password' => 'wrong-secret'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
        $this->get(route('login'))
            ->assertOk()
            ->assertSee($user->email)
            ->assertSee('อีเมลหรือรหัสผ่านไม่ถูกต้อง')
            ->assertSee('role="alert"', false)
            ->assertDontSee('wrong-secret');
        $this->assertGuest();
    }

    public function test_inactive_staff_cannot_login(): void
    {
        $user = User::factory()->create(['is_active' => false, 'password' => 'test-password']);
        $this->post(route('login'), ['email' => $user->email, 'password' => 'test-password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_password_recovery_endpoints_are_disabled(): void
    {
        Notification::fake();
        $user = User::factory()->create(['is_active' => true]);
        $this->get('/forgot-password')->assertNotFound();
        $this->post('/forgot-password', ['email' => $user->email])->assertNotFound();
        $this->get('/reset-password/old-token')->assertNotFound();
        $this->post('/reset-password', [
            'token' => 'old-token', 'email' => $user->email,
            'password' => 'new-password', 'password_confirmation' => 'new-password',
        ])->assertNotFound();
        $this->assertSame($user->password, $user->fresh()->password);
        Notification::assertNothingSent();
    }
}
