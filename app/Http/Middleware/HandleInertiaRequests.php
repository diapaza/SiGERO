<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaRequests
{
    protected $rootView = 'app';

    public function handle(Request $request, Closure $next): Response
    {
        /** @var ViewErrorBag $errors */
        $errors = $request->session()->get('errors');

        Inertia::share([
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'username' => $request->user()->username,
                    'name' => $request->user()->name,
                    'role' => $request->user()->role,
                ] : null,
            ],
            'errors' => fn () => (object) collect($errors?->getBag('default')->toArray())
                ->map(fn (array $messages) => $messages[0] ?? '')
                ->filter()
                ->all(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'appName' => config('app.name', 'SiGERO'),
        ]);

        return $next($request);
    }
}
