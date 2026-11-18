<?php

namespace Tests\Feature;

use App\Jobs\RunPipeline;
use App\Models\Connection;
use App\Models\Dataset;
use App\Models\Organization;
use App\Models\Pipeline;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class SyntexisMvpTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_dashboard_exposes_workspace_metrics(): void
    {
        $organization = $this->organization();
        $user = $this->user($organization);
        Connection::factory()->count(3)->create(['organization_id' => $organization->id]);

        $this->actingAs($user)->get('/')->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('Dashboard')
                ->where('stats.connections', 3)
                ->has('metrics')
                ->has('runVolume')
        );
    }

    public function test_user_cannot_view_another_organizations_connection(): void
    {
        $organization = $this->organization();
        $otherOrganization = Organization::factory()->create();
        $connection = Connection::factory()->create(['organization_id' => $otherOrganization->id]);

        $this->actingAs($this->user($organization))
            ->get(route('connections.show', $connection->id))
            ->assertNotFound();
    }

    public function test_connection_creation_rejects_unknown_connector_types(): void
    {
        $organization = $this->organization();

        $this->actingAs($this->user($organization))
            ->post(route('connections.store'), [
                'name' => 'Mystery Source',
                'type' => 'telepathy',
                'status' => 'active',
                'config' => [],
            ])
            ->assertSessionHasErrors('type');
    }

    public function test_running_a_pipeline_ingests_records_and_records_the_run(): void
    {
        $organization = $this->organization();
        $connection = Connection::factory()->create(['organization_id' => $organization->id]);
        $dataset = Dataset::factory()->create([
            'organization_id' => $organization->id,
            'connection_id' => $connection->id,
            'record_count' => 0,
        ]);
        $pipeline = Pipeline::factory()->create([
            'organization_id' => $organization->id,
            'connection_id' => $connection->id,
            'dataset_id' => $dataset->id,
            'config' => ['rows' => 12, 'mapping_mode' => 'auto'],
        ]);

        (new RunPipeline($pipeline->id, $organization->id))->handle();

        $this->assertDatabaseCount('data_records', 12);
        $this->assertDatabaseHas('datasets', ['id' => $dataset->id, 'record_count' => 12]);
        $this->assertDatabaseHas('pipeline_runs', [
            'pipeline_id' => $pipeline->id,
            'status' => 'success',
            'rows_processed' => 12,
        ]);
    }

    private function organization(): Organization
    {
        return Organization::factory()->create([
            'name' => 'Test Workspace',
            'slug' => 'test-workspace-'.uniqid(),
        ]);
    }

    private function user(Organization $organization): User
    {
        return User::factory()->create([
            'organization_id' => $organization->id,
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
    }
}
