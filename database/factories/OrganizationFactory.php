<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => str($name)->slug()->append('-'.fake()->unique()->numberBetween(100, 999))->value(),
            'plan' => fake()->randomElement(['starter', 'growth', 'enterprise']),
            'brand_color' => fake()->randomElement(['#6366f1', '#0ea5e9', '#14b8a6', '#f59e0b']),
        ];
    }
}
