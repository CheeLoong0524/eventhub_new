<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get notifications for the authenticated user
     */
    public function index()
    {
        $userId = Auth::id();
        $notifications = $this->notificationService->getUserNotifications($userId, 10);
        $unreadCount = $this->notificationService->getUnreadCount($userId);

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark all notifications as read for the authenticated user
     */
    public function markAllAsRead()
    {
        $userId = Auth::id();
        $this->notificationService->markAllAsRead($userId);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Mark specific notification as read
     */
    public function markAsRead($id)
    {
        $success = $this->notificationService->markAsRead($id);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Notification marked as read' : 'Notification not found'
        ]);
    }

    /**
     * Get unread notification count for the authenticated user
     */
    public function getUnreadCount()
    {
        $userId = Auth::id();
        $unreadCount = $this->notificationService->getUnreadCount($userId);

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
    }
}
