<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Index', [
            'organization' => Organization::current(),
            'users' => User::where('organization_id', Organization::currentId())
                ->latest()
                ->get(['id', 'name', 'email', 'role', 'created_at']),
            'auditLogs' => AuditLog::with('user')->latest('created_at')->limit(30)->get(),
        ]);
    }
}
