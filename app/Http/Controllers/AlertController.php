<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    public function index($id)
    {
        if ((int) $id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $alerts = Auth::user()->notifications()->latest()->paginate(20);

        return view('alerts.index', [
            'user' => Auth::user(),
            'alerts' => $alerts,
        ]);
    }

    public function markRead($id, string $notificationId)
    {
        if ((int) $id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $notification = Auth::user()->notifications()->where('id', $notificationId)->firstOrFail();
        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;

        return $url ? redirect($url) : back();
    }

    public function markAllRead($id)
    {
        if ((int) $id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('message', 'All alerts marked as read.');
    }
}
