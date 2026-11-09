<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        if (! $request->user()->isCustomer()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Akun admin tidak dapat menggunakan fitur customer.');
        }

        return $next($request);
    }
}
