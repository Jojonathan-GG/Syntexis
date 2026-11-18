<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'action' => fake()->randomElement(['created', 'updated', 'deleted', 'ran']),
            'subject_type' => fake()->randomElement(['Connection', 'Pipeline', 'Dataset']),
            'subject_id' => fake()->numberBetween(1, 100),
            'description' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
            'created_at' => now()->subMinutes(fake()->numberBetween(1, 5000)),
        ];
    }
}
