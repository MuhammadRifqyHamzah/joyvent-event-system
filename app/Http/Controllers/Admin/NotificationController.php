<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display the list of notifications.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        Notification::checkTableAndSync();

        $notifications = Notification::whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Mark a specific notification as read and redirect to the corresponding event.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleClick(Notification $notification)
    {
        $notification->update(['is_read' => true]);

        if ($notification->event_id) {
            $url = "/admin/events/{$notification->event_id}";
            if ($notification->target_tab) {
                $url .= "?tab={$notification->target_tab}";
            }
            return redirect($url);
        }

        // Warning Log
        \Illuminate\Support\Facades\Log::warning("Notification click fallback triggered: Notification ID {$notification->id} of type '{$notification->type}' has no event_id (source_key: '{$notification->source_key}').");

        return redirect()
            ->route('admin.notifications')
            ->with('warning', 'Detail event untuk notifikasi ini tidak ditemukan.');
    }

    /**
     * Mark a specific notification as read.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead(Notification $notification)
    {
        $notification->update(['is_read' => true]);

        return back()->with('success', 'Notifikasi berhasil ditandai sebagai dibaca.');
    }

    /**
     * Mark all unread notifications as read.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead()
    {
        Notification::where('is_read', false)->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi berhasil ditandai sebagai dibaca.');
    }

    /**
     * Remove the specified notification (Soft Delete).
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }
}
