<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_list_applications(): void
    {
        $response = $this->getJson('/api/applications');

        $response->assertUnauthorized();
    }

    public function test_guest_cannot_view_application(): void
    {
        $user = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->getJson(
            "/api/applications/{$application->id}"
        );

        $response->assertUnauthorized();
    }

    public function test_guest_cannot_create_application(): void
    {
        $response = $this->postJson('/api/applications', [
            'company_name' => 'Test Company',
            'position' => 'Software Engineer',
        ]);

        $response->assertUnauthorized();
    }

    public function test_guest_cannot_update_application(): void
    {
        $user = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->patchJson(
            "/api/applications/{$application->id}",
            [
                'company_name' => 'Unauthorized Change',
            ],
        );

        $response->assertUnauthorized();
    }

    public function test_guest_cannot_delete_application(): void
    {
        $user = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson(
            "/api/applications/{$application->id}"
        );

        $response->assertUnauthorized();

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
        ]);
    }
}
