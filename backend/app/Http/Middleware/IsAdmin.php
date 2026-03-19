<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is logged in AND has the specific 'admin' role
        if ($request->user() && $request->user()->hasRole('admin')) {
            return $next($request);
        }

        // Kick them out if they aren't an admin
        return response()->json(['message' => 'Unauthorized. Admin access required.'], 403);
    }
}