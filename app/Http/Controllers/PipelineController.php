<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePipelineRequest;
use App\Jobs\RunPipeline;
use App\Models\Connection;
use App\Models\Dataset;
use App\Models\Pipeline;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PipelineController extends Controller
{
    public function index(): Response
    {
        $pipelines = Pipeline::with(['connection', 'dataset', 'latestRun'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Pipelines/Index', [
            'pipelines' => $pipelines,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Pipelines/Create', [
            'connections' => Connection::where('status', 'active')->get(['id', 'name', 'type']),
            'datasets' => Dataset::get(['id', 'name']),
        ]);
    }

    public function store(StorePipelineRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $config = [
            'rows' => (int) $data['rows'],
            'mapping_mode' => $data['mapping_mode'],
        ];
        unset($data['rows'], $data['mapping_mode']);

        $data['config'] = $config;

        $pipeline = Pipeline::create($data);
        AuditService::record('created', $pipeline, "Created pipeline {$pipeline->name}.");

        return to_route('pipelines.index')->with('success', 'Pipeline created. Run it to ingest data.');
    }

    public function show(Pipeline $pipeline): Response
    {
        return Inertia::render('Pipelines/Show', [
            'pipeline' => $pipeline->load(['connection', 'dataset']),
            'runs' => $pipeline->runs()->latest('started_at')->limit(20)->get(),
        ]);
    }

    public function edit(Pipeline $pipeline): Response
    {
        return Inertia::render('Pipelines/Edit', [
            'pipeline' => $pipeline,
            'connections' => Connection::where('status', 'active')->get(['id', 'name', 'type']),
            'datasets' => Dataset::get(['id', 'name']),
            'rows' => data_get($pipeline->config, 'rows', 250),
            'mappingMode' => data_get($pipeline->config, 'mapping_mode', 'auto'),
        ]);
    }

    public function update(StorePipelineRequest $request, Pipeline $pipeline): RedirectResponse
    {
        $data = $request->validated();
        $data['config'] = [
            'rows' => (int) $data['rows'],
            'mapping_mode' => $data['mapping_mode'],
        ];
        unset($data['rows'], $data['mapping_mode']);

        $pipeline->update($data);
        AuditService::record('updated', $pipeline, "Updated pipeline {$pipeline->name}.");

        return to_route('pipelines.show', $pipeline)->with('success', 'Pipeline updated.');
    }

    public function destroy(Pipeline $pipeline): RedirectResponse
    {
        $name = $pipeline->name;
        AuditService::record('deleted', $pipeline, "Deleted pipeline {$name}.");
        $pipeline->delete();

        return to_route('pipelines.index')->with('success', 'Pipeline deleted.');
    }

    public function run(Pipeline $pipeline): RedirectResponse
    {
        if ($pipeline->status !== 'active') {
            return back()->with('error', 'Resume the pipeline before running it.');
        }

        RunPipeline::dispatch($pipeline->id, (int) $pipeline->organization_id);
        AuditService::record('ran', $pipeline, "Queued a run for {$pipeline->name}.");

        return back()->with('success', 'Pipeline run queued. Data will appear shortly.');
    }

    public function toggle(Pipeline $pipeline): RedirectResponse
    {
        $newStatus = $pipeline->status === 'active' ? 'paused' : 'active';
        $pipeline->forceFill(['status' => $newStatus])->save();

        AuditService::record('updated', $pipeline, "Pipeline {$pipeline->name} is now {$newStatus}.");

        return back()->with('success', "Pipeline is now {$newStatus}.");
    }
}
