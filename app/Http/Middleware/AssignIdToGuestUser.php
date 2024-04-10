<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AssignIdToGuestUser
{
    const KEY = 'guest_id';

    const HEADER_KEY = 'X-Guest-Id';

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return Auth::check()
            ? $this->handleAuthed($request, $next)
            : $this->handleGuest($request, $next);
    }

    private function handleAuthed(Request $request, Closure $next): Response
    {
        ['id' => $guestId, 'is_new' => $isNew] = $this->getGuestId($request);

        if (!$isNew) {
            $this->setToContext($guestId);
            $this->forget($request);
        }

        return $next($request);
    }

    private function handleGuest(Request $request, Closure $next): Response
    {
        ['id' => $guestId] = $this->getGuestId($request);

        $this->setToRequest($request, $guestId);
        $this->setToContext($guestId);

        $response = $next($request);

        $this->setToResponse($response, $guestId);

        return $response;
    }

    /**
     * @return array{id: string, is_new: bool}
     */
    private function getGuestId(Request $request): array
    {
        return [
            'id' => $request->session()->get(self::KEY, $this->generateId()),
            'is_new' => $request->session()->missing(self::KEY),
        ];
    }

    private function setToResponse(Response $response, string $guestId): void
    {
        $response->headers->set(self::HEADER_KEY, $guestId);
        Cookie::make(name: self::KEY, value: $guestId, httpOnly: false);
    }

    private function setToRequest(Request $request, string $guestId): void
    {
        $request->session()->put(self::KEY, $guestId);
        $request->header(self::HEADER_KEY, $guestId);
    }

    private function setToContext(string $guestId): void
    {
        Context::add([
            'guest' => [
                ...Context::get('guest', []),
                'id' => $guestId
            ]
        ]);
    }

    private function forget(Request $request): void
    {
        $request->session()->forget(self::KEY);
        Cookie::forget(self::KEY);
    }

    private function generateId(): string
    {
        return Str::orderedUuid()->toString();
    }
}
