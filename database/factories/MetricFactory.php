<?php

namespace Database\Factories;

use App\Models\Metric;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Metric>
 */
class MetricFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'key' => fake()->unique()->slug(2),
            'name' => fake()->words(2, true),
            'category' => 'operations',
            'value' => fake()->numberBetween(100, 100000),
            'unit' => null,
            'change_percent' => fake()->randomFloat(2, -15, 25),
            'series' => collect(range(1, 12))->map(fn () => fake()->numberBetween(20, 100))->all(),
        ];
    }
}
