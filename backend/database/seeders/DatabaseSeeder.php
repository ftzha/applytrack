<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@applytrack.test',
            'password' => Hash::make('password'),
        ]);

        $applications = Application::factory()
            ->count(15)
            ->for($user)
            ->create();

        foreach ($applications as $application) {
            $application->statusHistories()->create([
                'from_status' => null,
                'to_status' => $application->status,
            ]);
        }
    }
}
