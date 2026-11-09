<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        if (! $request->user()->isAdmin()) {
            abort(403, 'Halaman ini hanya dapat diakses admin.');
        }

        return $next($request);
    }
}
