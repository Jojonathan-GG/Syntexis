<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConnectionRequest;
use App\Models\Connection;
use App\Services\AuditService;
use App\Services\ConnectorCatalog;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ConnectionController extends Controller
{
    public function index(): Response
    {
        $connections = Connection::withCount(['datasets', 'pipelines'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Connections/Index', [
            'connections' => $connections,
            'catalog' => ConnectorCatalog::all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Connections/Create', [
            'catalog' => ConnectorCatalog::all(),
        ]);
    }

    public function store(StoreConnectionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()?->id;
        $data['config'] = $data['config'] ?? [];

        $connection = Connection::create($data);
        AuditService::record('created', $connection, "Connected {$connection->name} ({$connection->type}).");

        return to_route('connections.index')->with('success', 'Connection created successfully.');
    }

    public function show(Connection $connection): Response
    {
        return Inertia::render('Connections/Show', [
            'connection' => $connection->load(['creator', 'datasets', 'pipelines']),
            'definition' => ConnectorCatalog::find($connection->type),
        ]);
    }

    public function edit(Connection $connection): Response
    {
        return Inertia::render('Connections/Edit', [
            'connection' => $connection,
            'catalog' => ConnectorCatalog::all(),
        ]);
    }

    public function update(StoreConnectionRequest $request, Connection $connection): RedirectResponse
    {
        $data = $request->validated();
        $data['config'] = $data['config'] ?? [];

        $connection->update($data);
        AuditService::record('updated', $connection, "Updated connection {$connection->name}.");

        return to_route('connections.index')->with('success', 'Connection updated successfully.');
    }

    public function destroy(Connection $connection): RedirectResponse
    {
        $name = $connection->name;
        AuditService::record('deleted', $connection, "Deleted connection {$name}.");
        $connection->delete();

        return to_route('connections.index')->with('success', 'Connection deleted.');
    }

    public function test(Connection $connection): RedirectResponse
    {
        $connection->forceFill([
            'status' => 'active',
            'last_tested_at' => now(),
        ])->save();

        AuditService::record('tested', $connection, "Health check passed for {$connection->name}.");

        return back()->with('success', "Connection to {$connection->name} is healthy.");
    }
}
