<?php

namespace App\Http\Middleware;

use App\Models\Alert;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'organization' => Organization::current(),
            ],
            'navigation' => $this->navigation(),
            'openAlertCount' => fn () => $request->user()
                ? Alert::where('status', 'open')->count()
                : 0,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function navigation(): array
    {
        return [
            ['label' => 'Overview', 'route' => 'dashboard', 'icon' => 'grid'],
            ['label' => 'Connections', 'route' => 'connections.index', 'icon' => 'plug'],
            ['label' => 'Pipelines', 'route' => 'pipelines.index', 'icon' => 'workflow'],
            ['label' => 'Datasets', 'route' => 'datasets.index', 'icon' => 'database'],
            ['label' => 'Insights', 'route' => 'insights.index', 'icon' => 'chart'],
            ['label' => 'Admin', 'route' => 'admin.index', 'icon' => 'shield'],
        ];
    }
}
