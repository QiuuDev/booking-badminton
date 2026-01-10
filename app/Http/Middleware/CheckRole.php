<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
{
    // strtolower akan mengubah 'Admin' menjadi 'admin' sebelum dibandingkan
    if (auth()->check() && strtolower(auth()->user()->role) == strtolower($role)) {
        return $next($request);
    }

    return response()->json(['message' => 'Akses ditolak: Role tidak sesuai'], 403);
    }
}
