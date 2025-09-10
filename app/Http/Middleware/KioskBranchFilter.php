<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KioskBranchFilter
{
    /**
     * Handle an incoming request.
     * This middleware ensures all data is filtered by the kiosk's branch
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $kiosk = $request->get('kiosk');
        
        if (!$kiosk) {
            return response()->json([
                'success' => false,
                'message' => 'Kiosk information not found'
            ], 400);
        }

        // Add branch_id to request for use in controllers
        $request->merge(['branch_id' => $kiosk->branch_id]);

        return $next($request);
    }
}
