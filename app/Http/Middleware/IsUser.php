<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role == 'USER') {
            return $next($request);
        }

        if ($request->expectsJson()) {
            // Untuk AJAX request atau fetch()
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        return redirect('/')->with('error', 'Akses tidak diizinkan.');
    }
}
