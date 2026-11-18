<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\AuditLog;
use App\Models\Connection;
use App\Models\DataRecord;
use App\Models\Dataset;
use App\Models\Metric;
use App\Models\Pipeline;
use App\Models\PipelineRun;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $runs = PipelineRun::whereNotNull('started_at')
            ->where('started_at', '>=', now()->subDays(13))
            ->get(['started_at', 'status']);

        $runVolume = collect(range(0, 13))
            ->map(fn (int $offset): Carbon => now()->subDays(13 - $offset)->startOfDay())
            ->map(fn (Carbon $day): array => [
                'date' => $day->toDateString(),
                'label' => $day->format('M d'),
                'count' => $runs->filter(
                    fn (PipelineRun $run): bool => $run->started_at?->toDateString() === $day->toDateString()
                )->count(),
                'success' => $runs->filter(
                    fn (PipelineRun $run): bool => $run->started_at?->toDateString() === $day->toDateString()
                        && $run->status === 'success'
                )->count(),
                'failed' => $runs->filter(
                    fn (PipelineRun $run): bool => $run->started_at?->toDateString() === $day->toDateString()
                        && $run->status === 'failed'
                )->count(),
            ]);

        $recentRuns = PipelineRun::with('pipeline')
            ->latest('started_at')
            ->limit(6)
            ->get();

        $monthlyRuns = PipelineRun::where('created_at', '>=', now()->subDays(30))->get();
        $successRate = $monthlyRuns->isNotEmpty()
            ? round($monthlyRuns->where('status', 'success')->count() / $monthlyRuns->count() * 100, 1)
            : 100.0;

        return Inertia::render('Dashboard', [
            'stats' => [
                'connections' => Connection::count(),
                'activePipelines' => Pipeline::where('status', 'active')->count(),
                'records' => (int) Dataset::sum('record_count'),
                'liveRecords' => DataRecord::count(),
                'openAlerts' => Alert::where('status', 'open')->count(),
                'successRate' => $successRate,
                'runsToday' => PipelineRun::whereDate('created_at', today())->count(),
            ],
            'metrics' => Metric::orderBy('category')->orderBy('name')->get(),
            'runVolume' => $runVolume,
            'recentRuns' => $recentRuns,
            'recentActivity' => AuditLog::with('user')->latest('created_at')->limit(8)->get(),
            'alerts' => Alert::latest()->limit(5)->get(),
            'topDatasets' => Dataset::orderByDesc('record_count')->limit(5)->get(),
        ]);
    }
}
