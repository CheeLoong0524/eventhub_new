<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\SupportInquiry;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Notify all admin users about a new inquiry
     */
    public function notifyAdminsNewInquiry(SupportInquiry $inquiry)
    {
        try {
            // Get all admin users
            $adminUsers = User::where('role', 'admin')->get();

            if ($adminUsers->isEmpty()) {
                Log::warning('No admin users found to notify about new inquiry', [
                    'inquiry_id' => $inquiry->inquiry_id
                ]);
                return;
            }

            // Create notification for each admin
            foreach ($adminUsers as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'inquiry_id' => $inquiry->inquiry_id,
                    'message' => 'New inquiry received!',
                    'status' => 'unread'
                ]);
            }

            Log::info('Admin notifications created for new inquiry', [
                'inquiry_id' => $inquiry->inquiry_id,
                'admin_count' => $adminUsers->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to notify admins about new inquiry', [
                'inquiry_id' => $inquiry->inquiry_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notify customer about inquiry resolution
     */
    public function notifyCustomerInquiryResolved(SupportInquiry $inquiry)
    {
        try {
            // Find customer by inquiry email
            $customer = User::where('email', $inquiry->email)->first();

            if (!$customer) {
                Log::warning('Customer not found for inquiry resolution notification', [
                    'inquiry_id' => $inquiry->inquiry_id,
                    'email' => $inquiry->email
                ]);
                return;
            }

            // Create notification for customer
            Notification::create([
                'user_id' => $customer->id,
                'inquiry_id' => $inquiry->inquiry_id,
                'message' => 'Your inquiry has been resolved!',
                'status' => 'unread'
            ]);

            Log::info('Customer notification created for inquiry resolution', [
                'inquiry_id' => $inquiry->inquiry_id,
                'customer_id' => $customer->id,
                'customer_email' => $customer->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to notify customer about inquiry resolution', [
                'inquiry_id' => $inquiry->inquiry_id,
                'email' => $inquiry->email,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get notifications for a specific user
     */
    public function getUserNotifications($userId, $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->with('inquiry')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread notification count for a user
     */
    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->count();
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->update(['status' => 'read']);
    }

    /**
     * Mark specific notification as read
     */
    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        return false;
    }
}
