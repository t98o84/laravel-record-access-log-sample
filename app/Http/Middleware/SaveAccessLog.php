<?php

namespace App\Http\Middleware;

use App\Models\AccessLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;

class SaveAccessLog
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        try {
            if(empty($log = Context::all())) {
                // For redirects
                return;
            }
            AccessLog::query()->create(['log' => $log]);
        } catch (\Throwable) {
        }
    }
}
