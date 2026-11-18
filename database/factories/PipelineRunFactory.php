<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Pipeline;
use App\Models\PipelineRun;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PipelineRun>
 */
class PipelineRunFactory extends Factory
{
    public function definition(): array
    {
        $startedAt = now()->subMinutes(fake()->numberBetween(5, 2000));

        return [
            'organization_id' => Organization::factory(),
            'pipeline_id' => Pipeline::factory(),
            'status' => fake()->randomElement(['success', 'success', 'success', 'failed']),
            'rows_processed' => fake()->numberBetween(50, 20000),
            'rows_failed' => fake()->numberBetween(0, 40),
            'duration_ms' => fake()->numberBetween(200, 9000),
            'started_at' => $startedAt,
            'finished_at' => (clone $startedAt)->addSeconds(fake()->numberBetween(1, 9)),
        ];
    }
}
