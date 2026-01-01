<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return Inertia::render('Notifications', [
            'notifications'    => $notifications,
        ]);
    }

    public function show(DatabaseNotification $notification)
    {
        $user = auth()->user();

        // Make sure the notification belongs to this user
        if ($notification->notifiable_id !== $user->id) {
            abort(403);
        }

        // Mark as read
        $notification->markAsRead();

        // Redirect to the URL stored in notification data
        return redirect($notification->data['url'] ?? '/');
    }

    public function notifications(Request $request)
    {
        return $request->user()
            ->notifications()
            ->latest()
            ->get()
            ->map(fn($n) => [
                'id' => $n->id,
                'title' => $n->data['title'] ?? null,
                'message' => $n->data['message'] ?? null,
                'url' => $n->data['url'] ?? null,
                'read_at' => $n->read_at,
                'created_at' => $n->created_at,
            ]);
    }

    public function destroy(DatabaseNotification $notification)
    {
        $user = auth()->user();

        // Make sure the notification belongs to this user
        if ($notification->notifiable_id !== $user->id) {
            abort(403);
        }

        $notification->delete();

        return redirect()->back()->with('success', 'Notification deleted successfully.');
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function deleteAllNotifications(Request $request)
    {
        $request->user()->notifications()->delete();

        return redirect()->back()->with('success', 'All notifications deleted successfully.');
    }
}
