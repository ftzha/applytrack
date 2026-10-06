<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationStatusHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_change_creates_history_record(): void
    {
        $user = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $user->id,
            'status' => 'applied',
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson("/api/applications/{$application->id}", [
                'status' => 'interview',
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('application_status_histories', [
            'application_id' => $application->id,
            'from_status' => 'applied',
            'to_status' => 'interview',
        ]);

        $this->assertDatabaseCount(
            'application_status_histories',
            1,
        );
    }

    public function test_unchanged_status_does_not_create_history_record(): void
    {
        $user = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $user->id,
            'status' => 'applied',
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson("/api/applications/{$application->id}", [
                'status' => 'applied',
            ]);

        $response->assertOk();

        $this->assertDatabaseCount(
            'application_status_histories',
            0,
        );
    }

    public function test_multiple_status_changes_create_history_in_sequence(): void
    {
        $user = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $user->id,
            'status' => 'applied',
        ]);

        $this
            ->actingAs($user)
            ->patchJson("/api/applications/{$application->id}", [
                'status' => 'interview',
            ])
            ->assertOk();

        $this
            ->actingAs($user)
            ->patchJson("/api/applications/{$application->id}", [
                'status' => 'offer',
            ])
            ->assertOk();

        $this->assertDatabaseHas('application_status_histories', [
            'application_id' => $application->id,
            'from_status' => 'applied',
            'to_status' => 'interview',
        ]);

        $this->assertDatabaseHas('application_status_histories', [
            'application_id' => $application->id,
            'from_status' => 'interview',
            'to_status' => 'offer',
        ]);

        $this->assertDatabaseCount(
            'application_status_histories',
            2,
        );
    }

    public function test_updating_unrelated_field_does_not_create_status_history(): void
    {
        $user = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $user->id,
            'company_name' => 'Original Company',
            'status' => 'applied',
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson("/api/applications/{$application->id}", [
                'company_name' => 'Updated Company',
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'company_name' => 'Updated Company',
            'status' => 'applied',
        ]);

        $this->assertDatabaseCount(
            'application_status_histories',
            0,
        );
    }
}
