<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruApprovedMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Middleware ini sudah tidak diperlukan karena is_approved dan profile_completed
        // sudah dihapus dari tabel users
        return $next($request);
    }
}