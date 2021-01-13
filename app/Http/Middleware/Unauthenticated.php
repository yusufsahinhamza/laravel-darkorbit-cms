<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Unauthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (\Auth::check()) {
            return response(['status' => false, 'message' => 'Authenticated.']);
        }

        return $next($request);
    }
}
