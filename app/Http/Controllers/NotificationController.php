<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(): JsonResponse
    {
        $count = $this->notificationService->getUnreadCount(auth()->id());
        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications
     */
    public function getRecent(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $notifications = $this->notificationService->getRecentNotifications(auth()->id(), $limit);
        
        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'created_by' => $notification->createdBy ? $notification->createdBy->name : null,
                ];
            })
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'notification_id' => 'required|exists:notifications,id'
        ]);

        $success = $this->notificationService->markAsRead(
            $request->notification_id,
            auth()->id()
        );

        return response()->json(['success' => $success]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $count = $this->notificationService->markAllAsRead(auth()->id());
        return response()->json(['success' => true, 'count' => $count]);
    }

    /**
     * Get all notifications for admin dashboard
     */
    public function index(Request $request)
    {
        $notifications = Notification::forUser(auth()->id())
            ->with(['createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Choose layout based on role
        $user = auth()->user();
        $role = $user?->role;
        $layout = 'layouts.user';
        if ($role === 'admin' || $role === 'superadmin') {
            $layout = 'layouts.admin';
        } elseif ($role === 'gsu') {
            $layout = 'layouts.gsu';
        }

        return view('notifications.index', [
            'notifications' => $notifications,
            'layout' => $layout,
        ]);
    }
}