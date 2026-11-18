<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\DataRecord;
use App\Models\Dataset;
use App\Models\Metric;
use App\Models\PipelineRun;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InsightController extends Controller
{
    public function index(): Response
    {
        $runs = PipelineRun::whereNotNull('started_at')
            ->where('started_at', '>=', now()->subDays(13))
            ->get(['started_at', 'status']);

        $runVolume = collect(range(0, 13))
            ->map(fn (int $offset): string => now()->subDays(13 - $offset)->toDateString())
            ->map(fn (string $day): array => [
                'label' => Carbon::parse($day)->format('M d'),
                'success' => $runs->filter(fn (PipelineRun $run): bool => $run->started_at?->toDateString() === $day && $run->status === 'success')->count(),
                'failed' => $runs->filter(fn (PipelineRun $run): bool => $run->started_at?->toDateString() === $day && $run->status === 'failed')->count(),
            ]);

        $statusMix = DataRecord::selectRaw("json_extract(data, '$.status') as status, count(*) as total")
            ->groupBy('status')
            ->pluck('total', 'status');

        return Inertia::render('Insights/Index', [
            'metrics' => Metric::orderBy('category')->get(),
            'alerts' => Alert::latest()->paginate(15)->withQueryString(),
            'runVolume' => $runVolume,
            'statusMix' => $statusMix,
            'datasets' => Dataset::orderByDesc('record_count')->limit(6)->get(['id', 'name', 'record_count', 'freshness_at']),
        ]);
    }

    public function acknowledge(Alert $alert): RedirectResponse
    {
        $alert->forceFill([
            'status' => 'acknowledged',
            'acknowledged_at' => now(),
        ])->save();

        AuditService::record('updated', $alert, "Acknowledged alert {$alert->title}.");

        return back()->with('success', 'Alert acknowledged.');
    }
}
