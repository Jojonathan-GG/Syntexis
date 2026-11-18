<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationContext
{
    /**
     * Bind the signed-in user's organization to the session on first request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && $user->organization_id !== null && ! $request->session()->has('current_organization_id')) {
            $request->session()->put('current_organization_id', $user->organization_id);
        }

        return $next($request);
    }
}
