<?php

namespace App\Observers;

use App\Models\SupportInquiry;
use App\Services\NotificationService;

class CustomerInquiryObserver
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the SupportInquiry "updated" event.
     * Triggered when inquiry status changes to "closed" or "resolved"
     */
    public function updated(SupportInquiry $supportInquiry)
    {
        // Check if status changed to "closed" or "resolved"
        if ($supportInquiry->isDirty('status') && 
            in_array($supportInquiry->status, ['closed', 'resolved'])) {
            $this->notificationService->notifyCustomerInquiryResolved($supportInquiry);
        }
    }
}
