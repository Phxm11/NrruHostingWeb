<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\DatabaseTestCase;

class PrivateFilesTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
        Storage::fake('public');
    }

    public function test_only_active_staff_can_open_request_files(): void
    {
        $request = $this->makeServiceRequest(['system_detail_doc_path' => 'attachments/details.pdf']);
        Storage::disk('private')->put('attachments/details.pdf', '%PDF-1.4 Test');
        $url = route('admin.requests.files.show', [$request, 'system_detail_doc']);
        $this->get($url)->assertRedirect(route('login'));
        $staff = User::factory()->create(['is_active' => true]);
        $this->actingAs($staff)->get($url)->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $staff->update(['is_active' => false]);
        $this->get($url)->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_unknown_missing_and_unsafe_file_paths_return_not_found(): void
    {
        $request = $this->makeServiceRequest(['system_detail_doc_path' => '../.env']);
        $this->actingAs(User::factory()->create(['is_active' => true]));
        foreach (['system_detail_doc', 'signature_image', 'unknown'] as $file) {
            $this->get(route('admin.requests.files.show', [$request, $file]))->assertNotFound();
        }
    }

    public function test_legacy_files_are_verified_and_moved_and_command_can_run_again(): void
    {
        $request = $this->makeServiceRequest(['system_detail_doc_path' => 'attachments/details.pdf']);
        Storage::disk('public')->put('attachments/details.pdf', '%PDF-1.4 Legacy');
        $this->artisan('files:privatize', ['--dry-run' => true])->assertSuccessful();
        Storage::disk('public')->assertExists('attachments/details.pdf');
        Storage::disk('private')->assertMissing('attachments/details.pdf');
        $this->artisan('files:privatize')->assertSuccessful();
        Storage::disk('public')->assertMissing('attachments/details.pdf');
        $this->assertSame('%PDF-1.4 Legacy', Storage::disk('private')->get('attachments/details.pdf'));
        $this->artisan('files:privatize')->assertSuccessful();
        $this->actingAs(User::factory()->create(['is_active' => true]));
        $this->get(route('admin.requests.files.show', [$request, 'system_detail_doc']))->assertOk();
        $this->delete(route('admin.requests.destroy', $request))->assertRedirect();
        Storage::disk('private')->assertMissing('attachments/details.pdf');
    }

    public function test_different_existing_private_file_never_overwrites_or_deletes_public_file(): void
    {
        Storage::disk('public')->put('attachments/details.pdf', 'original');
        Storage::disk('private')->put('attachments/details.pdf', 'different');
        $this->artisan('files:privatize')->assertFailed();
        $this->assertSame('original', Storage::disk('public')->get('attachments/details.pdf'));
        $this->assertSame('different', Storage::disk('private')->get('attachments/details.pdf'));
    }
}
