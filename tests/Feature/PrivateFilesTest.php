<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\DatabaseTestCase;

class PrivateFilesTest extends DatabaseTestCase
{
    public function test_attachment_is_served_only_from_private_storage_to_active_staff(): void
    {
        Storage::fake('private');
        Storage::fake('public');

        $path = 'attachments/system_detail/secret.pdf';
        $serviceRequest = $this->makeServiceRequest(['system_detail_doc_path' => $path]);
        $url = route('admin.requests.files.show', [$serviceRequest, 'system_detail_doc']);
        Storage::disk('public')->put($path, 'public copy');

        $this->get($url)->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create(['is_active' => true]));
        $this->get($url)->assertNotFound();

        Storage::disk('private')->put($path, 'private copy');
        $this->get($url)
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Content-Security-Policy', "default-src 'none'; sandbox");
    }

    public function test_inactive_staff_cannot_read_private_attachment(): void
    {
        Storage::fake('private');

        $path = 'signatures/secret.png';
        $serviceRequest = $this->makeServiceRequest(['signature_image_path' => $path]);
        Storage::disk('private')->put($path, 'private signature');

        $this->actingAs(User::factory()->create(['is_active' => false]));
        $this->get(route('admin.requests.files.show', [$serviceRequest, 'signature_image']))
            ->assertRedirect(route('login'));
    }

    public function test_dynamic_pages_prevent_caching_and_referrer_leaks(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertHeader('Referrer-Policy', 'no-referrer')
            ->assertHeader('X-Frame-Options', 'DENY');

        $this->actingAs(User::factory()->create(['is_active' => true]));
        $this->get(route('admin.requests.index'))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertHeader('Referrer-Policy', 'no-referrer')
            ->assertHeader('X-Frame-Options', 'DENY');
    }
}
