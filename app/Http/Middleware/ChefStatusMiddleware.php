<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ChefStatusMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $chef = auth('chef')->user();
        
        if ($chef && $chef->is_active == 0) {
            auth('chef')->logout();
            return redirect()->route('chef.auth.login')->withErrors('Your account has been deactivated.');
        }

        return $next($request);
    }
}
