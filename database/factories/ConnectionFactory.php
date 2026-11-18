<?php

namespace Database\Factories;

use App\Models\Connection;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Connection>
 */
class ConnectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->unique()->words(2, true).' Source',
            'type' => fake()->randomElement(['rest_api', 'postgresql', 'csv_upload', 'sftp', 'webhook']),
            'config' => ['endpoint' => fake()->url()],
            'status' => 'active',
            'last_tested_at' => now()->subMinutes(fake()->numberBetween(1, 240)),
        ];
    }
}
