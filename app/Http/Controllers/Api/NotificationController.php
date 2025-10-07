<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Mock notifications data - replace with actual implementation
        $notifications = [
            [
                'id' => 1,
                'type' => 'info',
                'title' => 'Welcome to the system',
                'message' => 'Your account has been successfully created and verified.',
                'data' => [
                    'title' => 'Welcome to the system',
                    'message' => 'Your account has been successfully created and verified.',
                    'action' => [
                        'text' => 'View Profile',
                        'url' => '/profile'
                    ]
                ],
                'read_at' => null,
                'created_at' => now()->subMinutes(5)->toISOString(),
                'updated_at' => now()->subMinutes(5)->toISOString()
            ],
            [
                'id' => 2,
                'type' => 'success',
                'title' => 'Asset Created',
                'message' => 'New asset "Laptop Dell XPS" has been added to the inventory.',
                'data' => [
                    'title' => 'Asset Created',
                    'message' => 'New asset "Laptop Dell XPS" has been added to the inventory.',
                    'action' => [
                        'text' => 'View Asset',
                        'url' => '/assets/1'
                    ]
                ],
                'read_at' => now()->subHours(2)->toISOString(),
                'created_at' => now()->subHours(3)->toISOString(),
                'updated_at' => now()->subHours(2)->toISOString()
            ],
            [
                'id' => 3,
                'type' => 'warning',
                'title' => 'Maintenance Required',
                'message' => 'Asset "Server HP ProLiant" requires scheduled maintenance.',
                'data' => [
                    'title' => 'Maintenance Required',
                    'message' => 'Asset "Server HP ProLiant" requires scheduled maintenance.',
                    'action' => [
                        'text' => 'Schedule Maintenance',
                        'url' => '/assets/2/maintenance'
                    ]
                ],
                'read_at' => null,
                'created_at' => now()->subDays(1)->toISOString(),
                'updated_at' => now()->subDays(1)->toISOString()
            ]
        ];

        // Apply pagination
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;

        $total = count($notifications);
        $paginatedNotifications = array_slice($notifications, $offset, $perPage);

        return response()->json([
            'success' => true,
            'data' => $paginatedNotifications,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
                'unread_count' => count(array_filter($notifications, fn($n) => is_null($n['read_at'])))
            ]
        ]);
    }

    public function markAsRead(Request $request, $id): JsonResponse
    {
        // Mock implementation - replace with actual database update
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        // Mock implementation - replace with actual database update
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    public function getUnreadCount(Request $request): JsonResponse
    {
        // Mock implementation - replace with actual database query
        $unreadCount = 2; // This should come from your database

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $unreadCount
            ]
        ]);
    }
}