<?php

namespace Database\Factories;

use App\Models\Connection;
use App\Models\Dataset;
use App\Models\Organization;
use App\Models\Pipeline;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pipeline>
 */
class PipelineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'connection_id' => Connection::factory(),
            'dataset_id' => Dataset::factory(),
            'name' => fake()->unique()->words(2, true).' Sync',
            'status' => fake()->randomElement(['active', 'active', 'paused']),
            'schedule' => fake()->randomElement(['manual', 'hourly', 'daily']),
            'config' => ['mapping' => ['auto' => true]],
            'last_run_at' => now()->subHours(fake()->numberBetween(1, 48)),
        ];
    }
}
