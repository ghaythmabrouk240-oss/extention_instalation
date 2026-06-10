<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoRoleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = $request->query('role');

        if (is_string($role) && in_array($role, User::roles(), true)) {
            $request->session()->put('demo_role', $role);
        }

        return $next($request);
    }
}
