<?php

namespace App\Http\Middleware;

use App\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectStaffLogout
{
    public function handle(Request $request, Closure $next): Response
    {
        $isStaffLogout = $request->routeIs('logout')
            && $request->user() instanceof Staff
            && ! $request->wantsJson();

        $response = $next($request);

        return $isStaffLogout
            ? redirect()->route('login')
            : $response;
    }
}
