<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user()
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'notifications' => $request->user()
                ? $request->user()
                        ->notifications()
                        ->latest()
                        ->take(20)
                        ->get()
                        ->map(fn($n) => [
                            'id'      => $n->id,
                            'title'   => $n->data['title'] ?? 'Notifikasi',
                            'message' => $n->data['message'] ?? '',
                            'url'     => $n->data['url'] ?? null,
                            'type'    => $n->data['type'] ?? null,
                            'time'    => $n->created_at->diffForHumans(),
                            'read_at' => $n->read_at,
                        ])
                : [],
            'unreadCount' => $request->user()
                ? $request->user()->unreadNotifications()->count()
                : 0,
        ];
    }
}
