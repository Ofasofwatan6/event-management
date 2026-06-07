<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OrganizationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Silakan login sebagai organisasi terlebih dahulu.');
        }

        if (auth()->user()->role !== 'organization') {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk organisasi.');
        }

        if (! auth()->user()->organization) {
            return redirect()->route('home')
                ->with('error', 'Akun organisasi belum lengkap.');
        }

        return $next($request);
    }
}
