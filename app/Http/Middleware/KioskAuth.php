<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\KioskUser;

class KioskAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication token required'
            ], 401);
        }

        $kioskUser = KioskUser::where('api_token', $token)
            ->where('is_active', 1)
            ->with('kiosk')
            ->first();

        if (!$kioskUser) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token'
            ], 401);
        }

        // Add kiosk user to request for use in controllers
        $request->merge(['kiosk_user' => $kioskUser]);
        $request->merge(['kiosk' => $kioskUser->kiosk]);

        return $next($request);
    }
}
