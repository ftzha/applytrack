<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_requires_company_name_position_and_status(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/applications', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'company_name',
                'position',
                'status',
            ]);
    }

    public function test_application_rejects_invalid_status(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/applications', [
                'company_name' => 'Test Company',
                'position' => 'Software Engineer',
                'status' => 'whatever_status',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');

        $this->assertDatabaseMissing('applications', [
            'company_name' => 'Test Company',
        ]);
    }

    public function test_salary_max_cannot_be_lower_than_salary_min(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/applications', [
                'company_name' => 'Test Company',
                'position' => 'Software Engineer',
                'status' => 'interested',
                'salary_min' => 7000,
                'salary_max' => 5000,
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('salary_max');

        $this->assertDatabaseMissing('applications', [
            'company_name' => 'Test Company',
        ]);
    }

    public function test_application_rejects_negative_salary(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/applications', [
                'company_name' => 'Test Company',
                'position' => 'Software Engineer',
                'status' => 'interested',
                'salary_min' => -1000,
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('salary_min');
    }

    public function test_application_rejects_invalid_job_url(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/api/applications', [
                'company_name' => 'Test Company',
                'position' => 'Software Engineer',
                'status' => 'interested',
                'job_url' => 'definitely-not-a-url',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('job_url');
    }

    public function test_update_rejects_salary_min_higher_than_existing_salary_max(): void
    {
        $user = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $user->id,
            'salary_min' => 5000,
            'salary_max' => 8000,
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson("/api/applications/{$application->id}", [
                'salary_min' => 9000,
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('salary_min');

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'salary_min' => 5000,
            'salary_max' => 8000,
        ]);
    }

    public function test_update_rejects_salary_max_lower_than_existing_salary_min(): void
    {
        $user = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $user->id,
            'salary_min' => 5000,
            'salary_max' => 8000,
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson("/api/applications/{$application->id}", [
                'salary_max' => 4000,
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('salary_max');

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'salary_min' => 5000,
            'salary_max' => 8000,
        ]);
    }

    public function test_update_allows_valid_salary_min_against_existing_max(): void
    {
        $user = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $user->id,
            'salary_min' => 5000,
            'salary_max' => 8000,
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson("/api/applications/{$application->id}", [
                'salary_min' => 6000,
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'salary_min' => 6000,
            'salary_max' => 8000,
        ]);
    }

    public function test_update_allows_valid_salary_max_against_existing_salary_min(): void
    {
        $user = User::factory()->create();

        $application = Application::factory()->create([
            'user_id' => $user->id,
            'salary_min' => 5000,
            'salary_max' => 8000,
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson("/api/applications/{$application->id}", [
                'salary_max' => 9000,
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'salary_min' => 5000,
            'salary_max' => 9000,
        ]);
    }
}
