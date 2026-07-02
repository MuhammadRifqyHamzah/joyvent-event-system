<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Sync and generate user-specific notifications first
        UserNotification::generateForUser($userId);

        $notifications = UserNotification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * Get the unread notification count.
     */
    public function unreadCount(Request $request)
    {
        $userId = $request->user()->id;

        // Sync and generate user-specific notifications first
        UserNotification::generateForUser($userId);

        $count = UserNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Get a specific notification.
     */
    public function show(Request $request, $id)
    {
        $userId = $request->user()->id;

        $notification = UserNotification::where('user_id', $userId)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $notification
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $userId = $request->user()->id;

        $notification = UserNotification::where('user_id', $userId)
            ->findOrFail($id);

        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
            'data' => $notification
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $userId = $request->user()->id;

        UserNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.'
        ]);
    }

    /**
     * Clear (soft delete) all notifications for the user.
     */
    public function clearAll(Request $request)
    {
        $userId = $request->user()->id;

        UserNotification::where('user_id', $userId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'All notifications cleared.'
        ]);
    }
}
