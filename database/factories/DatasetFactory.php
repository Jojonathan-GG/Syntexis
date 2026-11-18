<?php

namespace Database\Factories;

use App\Models\Connection;
use App\Models\Dataset;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dataset>
 */
class DatasetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'connection_id' => Connection::factory(),
            'name' => fake()->unique()->words(2, true).' Dataset',
            'description' => fake()->sentence(),
            'schema_json' => [
                ['name' => 'id', 'type' => 'string'],
                ['name' => 'amount', 'type' => 'number'],
                ['name' => 'status', 'type' => 'string'],
            ],
            'record_count' => fake()->numberBetween(100, 50000),
            'freshness_at' => now()->subMinutes(fake()->numberBetween(5, 600)),
        ];
    }
}
