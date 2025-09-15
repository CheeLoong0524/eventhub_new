<?php
// Author: Lee Chee Loong

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'vendor_id' => $this->id,
            'business_name' => $this->business_name,
            'business_type' => $this->business_type,
            'business_description' => $this->business_description,
            'contact_person' => $this->contact_person,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'business_address' => $this->business_address,
            'service_type' => $this->service_type,
            'service_categories' => $this->service_categories,
            'website' => $this->website,
            'rating' => $this->rating,
            'total_events' => $this->total_events,
            'is_verified' => $this->is_verified,
            'status' => $this->status,
            'approved_at' => $this->approved_at?->format('Y-m-d H:i:s'),
            'rejection_reason' => $this->rejection_reason,
            
            // User relationship
            'user' => $this->whenLoaded('user', function () {
                return [
                    'user_id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            
            // Event applications relationship
            'event_applications' => $this->whenLoaded('eventApplications', function () {
                return $this->eventApplications->map(function ($application) {
                    return [
                        'application_id' => $application->id,
                        'event_id' => $application->event_id,
                        'event_name' => $application->event->name ?? null,
                        'booth_size' => $application->booth_size,
                        'booth_quantity' => $application->booth_quantity,
                        'service_type' => $application->service_type,
                        'service_description' => $application->service_description,
                        'requested_price' => $application->requested_price,
                        'final_amount' => $application->approved_price ?? $application->requested_price,
                        'status' => $application->status,
                        'applied_at' => $application->created_at?->format('Y-m-d H:i:s'),
                        'paid_at' => $application->paid_at?->format('Y-m-d H:i:s'),
                    ];
                });
            }),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
