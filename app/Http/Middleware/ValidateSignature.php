<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ValidateSignature as Middleware;
use Symfony\Component\HttpFoundation\Response;

class ValidateSignature extends Middleware
{
    /**
     * The URIs that should be excluded from signature verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // 'api/payment/webhook',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle($request, $next, ...$args)
    {
        return parent::handle($request, $next, ...$args);
    }
}
