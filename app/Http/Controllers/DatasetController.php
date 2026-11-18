<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Inertia\Inertia;
use Inertia\Response;

class DatasetController extends Controller
{
    public function index(): Response
    {
        $datasets = Dataset::with('connection')
            ->withCount('records')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Datasets/Index', [
            'datasets' => $datasets,
        ]);
    }

    public function show(Dataset $dataset): Response
    {
        return Inertia::render('Datasets/Show', [
            'dataset' => $dataset->load('connection'),
            'records' => $dataset->records()->latest()->limit(25)->get(),
            'pipelines' => $dataset->pipelines()->with('latestRun')->latest()->limit(10)->get(),
        ]);
    }
}
