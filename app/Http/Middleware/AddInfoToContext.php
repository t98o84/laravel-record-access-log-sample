<?php

namespace App\Http\Middleware;

use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Log\Context\Repository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AddInfoToContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = CarbonImmutable::createFromTimestamp(defined('LARAVEL_START')
            ? LARAVEL_START
            : $request->server('REQUEST_TIME_FLOAT')
        );

        Context::add([
            'request' => [
                ...Context::get('request', []),
                'url' => $request->url(),
                'method' => $request->method(),
                'origin' => $request->header('Origin'),
                'ip' => $request->ip(),
                'forwarded_for' => $request->header('X-Forwarded-For'),
                'user_agent' => $request->userAgent(),
                'query' => $request->query(),
                'referer' => $request->header('Referer'),
            ],
            'performance' => [
                ...Context::get('performance', []),
                'start_time' => $startTime->toIso8601ZuluString('microsecond'),
            ],
        ]);

        Context::when(
            $request->hasSession(),
            fn (Repository $context) => $context->add('session', [
                ...Context::get('session', []),
                'id' => Hash::make($request->session()->getId()),
            ]),
        );

        $user = Auth::user();
        Context::when(
            !is_null($user),
            fn (Repository $context) => $context->add('auth', [
                ...Context::get('auth', []),
                'id' => $user->getAuthIdentifier(),
            ]),
        );

        $response = $next($request);

        $endTime = CarbonImmutable::now();
        Context::add([
            'performance' => [
                ...Context::get('performance', []),
                'end_time' => $endTime->toIso8601ZuluString('microsecond'),
                'duration_μs' => $startTime->diffInMicroseconds($endTime),
                'memory_peak_usage' => memory_get_peak_usage(),
                'memory_real_peak_usage' => memory_get_peak_usage(true),
            ],
            'response' => [
                ...Context::get('response', []),
                'status_code' => $response->getStatusCode(),
            ]
        ]);

        return $response;
    }
}
