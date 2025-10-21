<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->session()->has('lydopers')) {
            return redirect()->route('login')->withErrors(['error' => 'You must be logged in to access this page.']);
        }

        $user = $request->session()->get('lydopers');

        if ($user->lydopers_role !== $role) {
            return redirect('/')->withErrors(['error' => 'You do not have permission to access this page.']);
        }

        return $next($request);
    }
    
}
