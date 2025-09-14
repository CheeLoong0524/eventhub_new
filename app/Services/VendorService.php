<?php

namespace App\Services;

use App\Models\Vendor;
use App\Models\Event;
use App\Models\VendorEventApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VendorService
{
    /**
     * Get vendor information by ID (internal service)
     * IFA: Vendor Information Service - Internal Consumption
     */
    public function getVendorInfo($id)
    {
        try {
            $vendor = Vendor::with(['user', 'eventApplications.event'])
                ->where('status', 'approved')
                ->find($id);

            if (!$vendor) {
                return [
                    'success' => false,
                    'error' => 'Vendor not found or not approved'
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'vendor_id' => $vendor->id,
                    'user_id' => $vendor->user_id,
                    'business_name' => $vendor->business_name,
                    'business_type' => $vendor->business_type,
                    'business_description' => $vendor->business_description,
                    'business_phone' => $vendor->business_phone,
                    'business_email' => $vendor->business_email,
                    'years_in_business' => $vendor->years_in_business,
                    'business_size' => $vendor->business_size,
                    'annual_revenue' => $vendor->annual_revenue,
                    'event_experience' => $vendor->event_experience,
                    'product_category' => $vendor->product_category,
                    'target_audience' => $vendor->target_audience,
                    'marketing_strategy' => $vendor->marketing_strategy,
                    'status' => $vendor->status,
                    'is_verified' => $vendor->is_verified,
                    'approved_at' => $vendor->approved_at,
                    'user_details' => [
                        'name' => $vendor->user->name,
                        'email' => $vendor->user->email,
                        'phone' => $vendor->user->phone,
                        'address' => $vendor->user->address
                    ],
                    'event_applications' => $vendor->eventApplications->map(function($app) {
                        return [
                            'application_id' => $app->id,
                            'event_id' => $app->event_id,
                            'event_name' => $app->event->name,
                            'status' => $app->status,
                            'applied_at' => $app->created_at
                        ];
                    })
                ]
            ];
        } catch (\Exception $e) {
            Log::error('VendorService - getVendorInfo failed', [
                'vendor_id' => $id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to retrieve vendor information',
                'exception' => $e->getMessage()
            ];
        }
    }

    /**
     * Get vendor status (internal service)
     * IFA: Vendor Status Service - Internal Consumption
     */
    public function getVendorStatus($id)
    {
        try {
            $vendor = Vendor::find($id);

            if (!$vendor) {
                return [
                    'success' => false,
                    'error' => 'Vendor not found'
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'vendor_id' => $vendor->id,
                    'business_name' => $vendor->business_name,
                    'status' => $vendor->status,
                    'is_verified' => $vendor->is_verified,
                    'approved_at' => $vendor->approved_at,
                    'rejection_reason' => $vendor->rejection_reason
                ]
            ];
        } catch (\Exception $e) {
            Log::error('VendorService - getVendorStatus failed', [
                'vendor_id' => $id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to retrieve vendor status',
                'exception' => $e->getMessage()
            ];
        }
    }

    /**
     * Get events accepting applications (internal service)
     * IFA: Event Applications Service - Internal Consumption
     */
    public function getEventsAcceptingApplications($perPage = 12, $page = 1)
    {
        try {
            $events = Event::where('status', 'active')
                ->whereNotNull('booth_price')
                ->where('booth_quantity', '>', 0)
                ->whereRaw('booth_quantity > booth_sold')
                ->where(function($query) {
                    $query->where('start_time', '>', now())
                          ->orWhereNull('start_time');
                })
                ->with(['venue'])
                ->orderBy('start_time')
                ->paginate($perPage, ['*'], 'page', $page);

            return [
                'success' => true,
                'data' => $events->items(),
                'pagination' => [
                    'current_page' => $events->currentPage(),
                    'last_page' => $events->lastPage(),
                    'per_page' => $events->perPage(),
                    'total' => $events->total()
                ]
            ];
        } catch (\Exception $e) {
            Log::error('VendorService - getEventsAcceptingApplications failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to retrieve events',
                'exception' => $e->getMessage()
            ];
        }
    }

    /**
     * Get specific event information (internal service)
     * IFA: Event Information Service - Internal Consumption
     */
    public function getEventInfo($eventId)
    {
        try {
            $event = Event::with(['venue', 'activities'])
                ->find($eventId);

            if (!$event) {
                return [
                    'success' => false,
                    'error' => 'Event not found'
                ];
            }

            return [
                'success' => true,
                'data' => $event
            ];
        } catch (\Exception $e) {
            Log::error('VendorService - getEventInfo failed', [
                'event_id' => $eventId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to retrieve event information',
                'exception' => $e->getMessage()
            ];
        }
    }

    /**
     * Get vendor's applications (internal service)
     * IFA: Vendor Applications Service - Internal Consumption
     */
    public function getVendorApplications($vendorId)
    {
        try {
            $applications = VendorEventApplication::with(['event', 'event.venue'])
                ->where('vendor_id', $vendorId)
                ->where('status', '!=', 'paid')
                ->orderBy('created_at', 'desc')
                ->get();

            return [
                'success' => true,
                'data' => $applications
            ];
        } catch (\Exception $e) {
            Log::error('VendorService - getVendorApplications failed', [
                'vendor_id' => $vendorId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to retrieve vendor applications',
                'exception' => $e->getMessage()
            ];
        }
    }

    /**
     * Get vendor's bookings (internal service)
     * IFA: Vendor Bookings Service - Internal Consumption
     */
    public function getVendorBookings($vendorId)
    {
        try {
            $bookings = VendorEventApplication::with(['event', 'event.venue'])
                ->where('vendor_id', $vendorId)
                ->where('status', 'paid')
                ->orderBy('created_at', 'desc')
                ->get();

            return [
                'success' => true,
                'data' => $bookings
            ];
        } catch (\Exception $e) {
            Log::error('VendorService - getVendorBookings failed', [
                'vendor_id' => $vendorId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to retrieve vendor bookings',
                'exception' => $e->getMessage()
            ];
        }
    }

    /**
     * Update booth quantity (internal service)
     * IFA: Booth Management Service - Internal Consumption
     */
    public function updateBoothQuantity($eventId, $operation, $quantity)
    {
        try {
            return DB::transaction(function () use ($eventId, $operation, $quantity) {
                $event = Event::where('id', $eventId)->lockForUpdate()->first();
                
                if (!$event) {
                    throw new \Exception('Event not found');
                }

                if ($operation === 'subtract') {
                    if ($event->booth_sold + $quantity > $event->booth_quantity) {
                        throw new \Exception('Insufficient booths available');
                    }
                    $event->increment('booth_sold', $quantity);
                } elseif ($operation === 'add') {
                    if ($event->booth_sold < $quantity) {
                        throw new \Exception('Cannot add more booths than sold');
                    }
                    $event->decrement('booth_sold', $quantity);
                }

                $event->updateFinancials();

                return [
                    'success' => true,
                    'message' => 'Booth quantity updated successfully',
                    'data' => [
                        'event_id' => $event->id,
                        'booth_quantity' => $event->booth_quantity,
                        'booth_sold' => $event->booth_sold,
                        'available_booths' => $event->available_booths
                    ]
                ];
            });
        } catch (\Exception $e) {
            Log::error('VendorService - updateBoothQuantity failed', [
                'event_id' => $eventId,
                'operation' => $operation,
                'quantity' => $quantity,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Failed to update booth quantity',
                'exception' => $e->getMessage()
            ];
        }
    }
}
