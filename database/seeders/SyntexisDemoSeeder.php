<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\AuditLog;
use App\Models\Connection;
use App\Models\DataRecord;
use App\Models\Dataset;
use App\Models\Metric;
use App\Models\Organization;
use App\Models\Pipeline;
use App\Models\PipelineRun;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SyntexisDemoSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::firstOrCreate(
            ['slug' => 'demo-workspace'],
            [
                'name' => 'Northwind Industries',
                'plan' => 'enterprise',
                'brand_color' => '#6366f1',
                'settings' => ['timezone' => 'UTC', 'currency' => 'USD'],
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@syntexis.test'],
            [
                'name' => 'Avery Morgan',
                'password' => Hash::make('password'),
                'organization_id' => $organization->id,
                'role' => 'admin',
                'avatar_color' => '#6366f1',
            ]
        );

        User::firstOrCreate(
            ['email' => 'analyst@syntexis.test'],
            [
                'name' => 'Jordan Lee',
                'password' => Hash::make('password'),
                'organization_id' => $organization->id,
                'role' => 'analyst',
                'avatar_color' => '#14b8a6',
            ]
        );

        $connectionDefinitions = [
            ['ERP Core (PostgreSQL)', 'postgresql', ['host' => 'erp.internal', 'database' => 'production']],
            ['Stripe Payments', 'stripe', ['api_key' => 'sk_live_••••••••']],
            ['Salesforce CRM', 'salesforce', ['instance' => 'https://northwind.my.salesforce.com']],
            ['Partner Orders (SFTP)', 'sftp', ['host' => 'sftp.partner.com', 'path' => '/incoming/']],
            ['Webhook Events', 'webhook', ['secret' => 'whsec_••••••••']],
            ['Legacy CSV Drop', 'csv_upload', ['path' => 'uploads/legacy.csv']],
        ];

        $connections = collect($connectionDefinitions)->map(function (array $definition) use ($organization, $admin): Connection {
            return Connection::create([
                'organization_id' => $organization->id,
                'created_by' => $admin->id,
                'name' => $definition[0],
                'type' => $definition[1],
                'config' => $definition[2],
                'status' => 'active',
                'last_tested_at' => now()->subMinutes(random_int(3, 240)),
            ]);
        });

        $datasetDefinitions = [
            ['Unified Orders', 'Canonical order records from ERP and partner feeds.', 128040, 12],
            ['Payment Transactions', 'Normalized charges, refunds and disputes.', 84219, 34],
            ['Customer 360', 'Merged account and contact records.', 15230, 95],
            ['Fulfilment Events', 'Shipment and delivery milestones.', 43110, 8],
        ];

        $datasets = collect($datasetDefinitions)->map(function (array $definition, int $index) use ($organization, $connections): Dataset {
            return Dataset::create([
                'organization_id' => $organization->id,
                'connection_id' => $connections[$index % $connections->count()]->id,
                'name' => $definition[0],
                'description' => $definition[1],
                'schema_json' => [
                    ['name' => 'external_id', 'type' => 'string'],
                    ['name' => 'customer', 'type' => 'string'],
                    ['name' => 'region', 'type' => 'string'],
                    ['name' => 'amount', 'type' => 'number'],
                    ['name' => 'status', 'type' => 'string'],
                ],
                'record_count' => $definition[2],
                'freshness_at' => now()->subMinutes($definition[3]),
            ]);
        });

        $pipelineDefinitions = [
            ['ERP Orders Sync', 0, 0, 'active', 'hourly', 320],
            ['Stripe Payments Sync', 1, 1, 'active', 'hourly', 280],
            ['Salesforce Account Sync', 2, 2, 'active', 'daily', 180],
            ['Partner File Import', 3, 3, 'paused', 'manual', 95],
            ['Webhook Event Stream', 4, 0, 'active', 'manual', 240],
        ];

        $pipelines = collect($pipelineDefinitions)->map(function (array $definition) use ($organization, $connections, $datasets): Pipeline {
            return Pipeline::create([
                'organization_id' => $organization->id,
                'connection_id' => $connections[$definition[1]]->id,
                'dataset_id' => $datasets[$definition[2]]->id,
                'name' => $definition[0],
                'status' => $definition[3],
                'schedule' => $definition[4],
                'config' => ['rows' => $definition[5], 'mapping_mode' => 'auto'],
                'last_run_at' => now()->subMinutes(random_int(10, 400)),
            ]);
        });

        foreach ($pipelines as $pipeline) {
            for ($day = 13; $day >= 0; $day--) {
                if (random_int(1, 3) === 1) {
                    continue;
                }

                $startedAt = now()->subDays($day)->setTime(random_int(1, 20), random_int(0, 59));
                $status = random_int(1, 12) === 1 ? 'failed' : 'success';

                PipelineRun::create([
                    'organization_id' => $organization->id,
                    'pipeline_id' => $pipeline->id,
                    'status' => $status,
                    'rows_processed' => random_int(100, 9000),
                    'rows_failed' => $status === 'failed' ? random_int(2, 35) : 0,
                    'duration_ms' => random_int(350, 7800),
                    'started_at' => $startedAt,
                    'finished_at' => (clone $startedAt)->addSeconds(random_int(1, 8)),
                    'error' => $status === 'failed' ? 'Upstream endpoint returned 503' : null,
                ]);
            }
        }

        $regions = ['EMEA', 'AMER', 'APAC', 'LATAM'];
        $statuses = ['paid', 'paid', 'paid', 'pending', 'failed', 'refunded'];
        $customers = ['Northwind Traders', 'Acme Industries', 'Globex Corp', 'Initech', 'Umbrella Group', 'Stark Ltd'];

        foreach ($datasets as $dataset) {
            for ($index = 0; $index < 30; $index++) {
                DataRecord::create([
                    'organization_id' => $organization->id,
                    'dataset_id' => $dataset->id,
                    'external_id' => 'TXN-'.str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT),
                    'data' => [
                        'customer' => $customers[array_rand($customers)],
                        'region' => $regions[array_rand($regions)],
                        'amount' => random_int(120, 18000),
                        'status' => $statuses[array_rand($statuses)],
                        'source' => 'demo-seed',
                    ],
                ]);
            }
        }

        $series = collect(range(0, 11))->map(fn (): int => random_int(3000, 12000))->all();

        $metrics = [
            ['records_ingested', 'Records Ingested', 'operations', 128040, 'records', 12.4, $series],
            ['pipeline_success', 'Pipeline Success Rate', 'reliability', 98.6, '%', 1.2, [94, 95, 96, 95, 97, 98, 97, 99, 98, 98, 99, 98.6]],
            ['avg_latency', 'Avg. Sync Latency', 'performance', 428, 'ms', -8.3, [610, 590, 580, 540, 520, 510, 480, 470, 460, 450, 440, 428]],
            ['active_sources', 'Active Sources', 'operations', 6, 'connections', 0, [4, 4, 5, 5, 5, 6, 6, 6, 6, 6, 6, 6]],
            ['data_freshness', 'Data Freshness', 'quality', 12, 'min', -25.0, [45, 40, 38, 30, 28, 25, 20, 18, 16, 15, 14, 12]],
            ['insights_generated', 'Insights Generated', 'insights', 342, 'insights', 18.6, [120, 140, 165, 180, 195, 220, 240, 265, 280, 300, 320, 342]],
        ];

        foreach ($metrics as $metric) {
            Metric::create([
                'organization_id' => $organization->id,
                'key' => $metric[0],
                'name' => $metric[1],
                'category' => $metric[2],
                'value' => $metric[3],
                'unit' => $metric[4],
                'change_percent' => $metric[5],
                'series' => $metric[6],
            ]);
        }

        $alertDefinitions = [
            ['Schema drift detected in ERP Orders', 'The `unit_price` column changed from numeric to text in the source system.', 'warning', 'pipeline', 'open'],
            ['Partner SFTP import paused', 'No files received from the partner drop in the last 24 hours.', 'critical', 'connection', 'open'],
            ['Anomaly in Payment Transactions', 'Failed payments are 3.2x above the 30-day average.', 'warning', 'metric', 'open'],
            ['Salesforce token expires soon', 'The stored OAuth token expires in 6 days.', 'info', 'connection', 'acknowledged'],
            ['Sync latency recovered', 'Average pipeline latency returned to normal range.', 'info', 'pipeline', 'acknowledged'],
        ];

        foreach ($alertDefinitions as $alert) {
            Alert::create([
                'organization_id' => $organization->id,
                'title' => $alert[0],
                'message' => $alert[1],
                'severity' => $alert[2],
                'source' => $alert[3],
                'status' => $alert[4],
                'acknowledged_at' => $alert[4] === 'acknowledged' ? now()->subHours(4) : null,
            ]);
        }

        $auditDefinitions = [
            ['created', 'Connection', 'Connected Stripe Payments (stripe).', 2],
            ['ran', 'Pipeline', 'Queued a run for ERP Orders Sync.', 1],
            ['updated', 'Pipeline', 'Partner File Import is now paused.', 3],
            ['created', 'Dataset', 'Created canonical dataset Customer 360.', 1],
            ['tested', 'Connection', 'Health check passed for ERP Core.', 2],
            ['acknowledged', 'Alert', 'Acknowledged alert Salesforce token expires soon.', 1],
        ];

        foreach ($auditDefinitions as $index => $audit) {
            AuditLog::create([
                'organization_id' => $organization->id,
                'user_id' => $admin->id,
                'action' => $audit[0],
                'subject_type' => $audit[1],
                'subject_id' => $index + 1,
                'description' => $audit[2],
                'ip_address' => '192.168.1.'.random_int(10, 250),
                'created_at' => now()->subMinutes($audit[3] * 60 + random_int(0, 59)),
            ]);
        }
    }
}
