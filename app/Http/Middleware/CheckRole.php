<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// path to Log class
use Illuminate\Support\Facades\Log;

// path to Auth class
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // log the action
        Log::info('[CheckRole@handle] Authorization initiated.');

        // check if not authenticated & authorized?
        if (Auth::user()->role !== $role) {

            // Log the un-authorized access
            Log::info('un-authorized access', ['role' => $role]);

            // redirect to 403 page
        }

        // otherwise move to controller or next middleware..
        return $next($request);
    }
}
