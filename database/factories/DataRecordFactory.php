<?php

namespace Database\Factories;

use App\Models\DataRecord;
use App\Models\Dataset;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DataRecord>
 */
class DataRecordFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'dataset_id' => Dataset::factory(),
            'external_id' => fake()->uuid(),
            'data' => [
                'customer' => fake()->company(),
                'region' => fake()->randomElement(['EMEA', 'AMER', 'APAC']),
                'amount' => fake()->numberBetween(100, 10000),
                'status' => fake()->randomElement(['paid', 'pending', 'failed']),
            ],
        ];
    }
}
