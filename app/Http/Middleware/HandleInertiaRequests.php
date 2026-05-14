<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
                'user' => (function () use ($request) {
                    if (!$request->user()) return null;
                    $user = Cache::remember("user_{$request->user()->id}:profile:getDataProfile", 60*60*24, function() use ($request){
                        return $request->user()->load('roles', 'permissions', 'tenant');
                    });
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'is_active' => $user->is_active,
                        'two_factor_enabled' => $user->two_factor_enabled,
                        'tenant_id' => $user->tenant_id,
                        'tenant' => $user->tenant,
                        'roles' => $user->getRoleNames(),
                        'permissions' => $user->getAllPermissions()->pluck('name'),
                    ];
                }),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'notifications' => (function() use ($request){
                if (!$request->user()) return [];
                return Cache::remember("user_{$request->user()->id}:notification:getDataNotification", 60*60*24, function() use ($request){
                    $request->user()
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
                        ]);
                });
            }),
            'unreadCount' => (function() use ($request){
                if (!$request->user()) return 0;
                return Cache::remember("user_{$request->user()->id}:notification:getCountUnreadNotification", 60*60*24, function() use ($request){
                    return $request->user()->unreadNotifications()->count();
                });
            })(),
        ];
    }
}
