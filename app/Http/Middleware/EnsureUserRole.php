<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->role === 'organization') {
            return redirect()->route('admin.profile');
        }

        return $next($request);
    }
}
