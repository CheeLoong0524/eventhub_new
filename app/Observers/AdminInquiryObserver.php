<?php

namespace App\Observers;

use App\Models\SupportInquiry;
use App\Services\NotificationService;

class AdminInquiryObserver
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the SupportInquiry "updated" event.
     * Triggered when inquiry status changes to "pending"
     */
    public function updated(SupportInquiry $supportInquiry)
    {
        // Check if status changed to "pending"
        if ($supportInquiry->isDirty('status') && $supportInquiry->status === 'pending') {
            $this->notificationService->notifyAdminsNewInquiry($supportInquiry);
        }
    }

    /**
     * Handle the SupportInquiry "created" event.
     * Triggered when a new inquiry is created with "pending" status
     */
    public function created(SupportInquiry $supportInquiry)
    {
        // Check if status is "pending" when created
        if ($supportInquiry->status === 'pending') {
            $this->notificationService->notifyAdminsNewInquiry($supportInquiry);
        }
    }
}