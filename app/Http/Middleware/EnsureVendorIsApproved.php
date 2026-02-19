<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class EnsureVendorIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // log the action
        Log::info(
            "[app\Http\Middleware\EnsureVendorIsApproved@handle] Vendor Status Check initiated!",
        );

        // check the status
        Log::info($request->user()->vendorProfile?->status . " is the status");

        // redirect back to 'dashboard route' for vendor
        if ($request->user()->vendorProfile?->status !== "approved") {
            // redirect to profile route
            return response()
                ->redirectToRoute("vendor.profile.edit")
                ->with(
                    "warning",
                    "OOPS! looks like you aren't approved. Try updating profile to increase chances of approval",
                );
        }

        // go for next middleware / controller function
        return $next($request);
    }
}
