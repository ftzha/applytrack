<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_view_another_users_application(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $owner->id,
        ]);

        $response = $this
            ->actingAs($otherUser)
            ->getJson("/api/applications/{$application->id}");

        // $response->assertNotFound(); // 404
        $response->assertForbidden(); // 403
    }

    public function test_user_cannot_update_another_users_application(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $owner->id,
        ]);

        $response = $this
            ->actingAs($otherUser)
            ->patchJson("/api/applications/{$application->id}", [
                'company_name' => 'Unauthorized Change',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('applications', [
            'id' => $application->id,
            'company_name' => 'Unauthorized Change',
        ]);
    }

    public function test_user_cannot_delete_another_users_application(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $owner->id,
        ]);

        $response = $this
            ->actingAs($otherUser)
            ->deleteJson("/api/applications/{$application->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'user_id' => $owner->id,
        ]);
    }

    public function test_user_can_view_their_own_application(): void
    {
        $user = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson("/api/applications/{$application->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $application->id);
    }

    public function test_user_can_update_their_own_application(): void
    {
        $user = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson("/api/applications/{$application->id}", [
                'company_name' => 'Updated Company',
                'position' => 'Senior Software Engineer',
                'status' => $application->status,
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'user_id' => $user->id,
            'company_name' => 'Updated Company',
            'position' => 'Senior Software Engineer',
        ]);
    }

    public function test_user_can_delete_their_own_application(): void
    {
        $user = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->deleteJson("/api/applications/{$application->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('applications', [
            'id' => $application->id,
        ]);
    }
}
