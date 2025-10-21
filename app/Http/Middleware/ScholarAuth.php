<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ScholarAuth
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
        if (!$request->session()->has('scholar')) {
            return redirect()->route('scholar.login')->withErrors(['error' => 'You must be logged in to access this page.']);
        }

        return $next($request);
    }
}
