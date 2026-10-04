<?php

namespace Database\Factories;

use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Enums\ApplicationStatus;
use App\Models\User;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),

            'company_name' => fake()->company(),
            'position' => fake()->randomElement([
                'Software Engineer',
                'Backend Developer',
                'Full Stack Developer',
                'PHP Developer',
                'Web Developer',
            ]),

            'location' => fake()->randomElement([
                'Kuala Lumpur',
                'Selangor',
                'Cyberjaya',
                'Petaling Jaya',
                'Remote',
            ]),

            'employment_type' => fake()->randomElement([
                'full-time',
                'contract',
                'part-time',
            ]),

            'work_mode' => fake()->randomElement([
                'on-site',
                'hybrid',
                'remote',
            ]),

            'salary_min' => fake()->numberBetween(3500, 6000),
            'salary_max' => fake()->numberBetween(6500, 10000),

            'currency' => 'MYR',

            'source' => fake()->randomElement([
                'JobStreet',
                'LinkedIn',
                'Company Website',
                'Referral',
            ]),

            'job_url' => fake()->url(),

            'status' => fake()->randomElement(ApplicationStatus::cases()),

            'applied_at' => fake()->optional()->dateTimeBetween('-3 months', 'now'),

            'notes' => fake()->optional()->sentence(),

        ];
    }
}
