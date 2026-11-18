<?php

namespace Database\Factories;

use App\Models\Alert;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alert>
 */
class AlertFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'title' => fake()->sentence(4),
            'message' => fake()->sentence(),
            'severity' => fake()->randomElement(['critical', 'warning', 'info']),
            'status' => 'open',
            'source' => fake()->randomElement(['pipeline', 'connection', 'metric']),
        ];
    }
}
