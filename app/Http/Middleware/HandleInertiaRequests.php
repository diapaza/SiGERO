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
                'user' => fn () => $request->user() ? [
                    'id' => $request->user()->id,
                    'username' => $request->user()->username,
                    'name' => $request->user()->name,
                    'roles' => $request->user()->getRoleNames()->toArray(),
                    'permissions' => $request->user()->getAllPermissions()->pluck('name')->toArray(),
                ] : null,
            ],
            'notifications' => fn () => $request->user()
                ? $request->user()
                    ->notifications()
                    ->take(10)
                    ->get()
                    ->map(fn ($notification) => [
                        'id' => $notification->id,
                        'type' => $notification->data['type'] ?? 'general',
                        'title' => $notification->data['title'] ?? 'Notificación',
                        'message' => $notification->data['message'] ?? '',
                        'created_at' => $notification->created_at,
                        'read' => $notification->read_at !== null,
                    ])
                : [],
            'unreadNotifications' => fn () => $request->user()?->unreadNotifications()->count() ?? 0,
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
