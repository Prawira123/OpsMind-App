<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(){
        
        return Inertia::render('Notifications/index', [
            'notifications' => auth()->user()
                                ->notifications()
                                ->latest()
                                ->take(50)
                                ->get()
                                ->map(fn($n) => [
                                    'id'      => $n->id,
                                    'title'   => $n->data['title'] ?? 'Notification',
                                    'message' => $n->data['message'] ?? '',
                                    'url'     => $n->data['url'] ?? null,
                                    'type'    => $n->data['type'] ?? 'system',
                                    'time'    => $n->created_at->diffForHumans(),
                                    'read_at' => $n->read_at,
                                ])
        ]);
    }

    public function markAsRead($id){
        auth()->user()->notifications()->findOrFail($id)->markAsRead();

        return back();
    }

    public function markAllAsRead(){
        Auth::user()->unreadNotifications->markAsRead(); 

        return back();
    }

    public function destroy($id)
    {
        Auth::user()->notifications()->findOrFail($id)->delete();
        return back();
    }

    public function show($id){
        $notification = Auth::user()->notifications()->findOrFail($id);


        return Inertia::render('Notifications/detail', [
            'notification' => [
                'id' => $notification->id,
                'data' => $notification->data,
                'read_at' => $notification->read_at,

                'time'    => $notification->created_at->diffForHumans(),            ]
        ]);
    }
}
