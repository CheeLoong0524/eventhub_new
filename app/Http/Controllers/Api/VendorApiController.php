<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Event;
use App\Models\VendorEventApplication;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VendorApiController extends Controller
{
    /**
     * Get vendor information by ID
     * IFA: Vendor Information Service
     */
    public function getVendorInfo(Request $request, $id): JsonResponse
    {
        $validator = Validator::make(['vendor_id' => $id], [
            'vendor_id' => 'required|integer|exists:vendors,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid vendor ID',
                'errors' => $validator->errors()
            ], 400);
        }

        $vendor = Vendor::with(['user', 'eventApplications.event'])
            ->where('status', 'approved')
            ->find($id);

        if (!$vendor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vendor not found or not approved'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'vendor_id' => $vendor->id,
                'business_name' => $vendor->business_name,
                'business_type' => $vendor->business_type,
                'business_description' => $vendor->business_description,
                'contact_person' => $vendor->contact_person,
                'contact_email' => $vendor->contact_email,
                'contact_phone' => $vendor->contact_phone,
                'business_address' => $vendor->business_address,
                'service_type' => $vendor->service_type,
                'service_categories' => $vendor->service_categories,
                'website' => $vendor->website,
                'rating' => $vendor->rating,
                'total_events' => $vendor->total_events,
                'is_verified' => $vendor->is_verified,
                'approved_at' => $vendor->approved_at,
                'user_details' => [
                    'user_id' => $vendor->user->id,
                    'name' => $vendor->user->name,
                    'email' => $vendor->user->email
                ]
            ]
        ]);
    }

    /**
     * Get vendor status
     * IFA: Vendor Status Service
     */
    public function getVendorStatus(Request $request, $id): JsonResponse
    {
        $validator = Validator::make(['vendor_id' => $id], [
            'vendor_id' => 'required|integer|exists:vendors,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid vendor ID',
                'errors' => $validator->errors()
            ], 400);
        }

        $vendor = Vendor::find($id);

        return response()->json([
            'status' => 'success',
            'data' => [
                'vendor_id' => $vendor->id,
                'business_name' => $vendor->business_name,
                'status' => $vendor->status,
                'is_verified' => $vendor->is_verified,
                'approved_at' => $vendor->approved_at,
                'rejection_reason' => $vendor->rejection_reason
            ]
        ]);
    }

    /**
     * Search vendors
     * IFA: Vendor Search Service
     */
    public function searchVendors(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'query' => 'nullable|string|max:255',
            'service_type' => 'nullable|string|in:food,equipment,decoration,entertainment,logistics,other',
            'status' => 'nullable|string|in:pending,approved,rejected,suspended',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid search parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        $query = Vendor::with('user')
            ->where('status', 'approved');

        if ($request->filled('query')) {
            $query->where(function($q) use ($request) {
                $q->where('business_name', 'like', '%' . $request->query . '%')
                  ->orWhere('business_description', 'like', '%' . $request->query . '%');
            });
        }

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $vendors = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'vendors' => $vendors->items(),
                'pagination' => [
                    'current_page' => $vendors->currentPage(),
                    'last_page' => $vendors->lastPage(),
                    'per_page' => $vendors->perPage(),
                    'total' => $vendors->total()
                ]
            ]
        ]);
    }

    /**
     * Get event applications for a specific event
     * IFA: Event Applications Service
     */
    public function getEventApplications(Request $request, $eventId): JsonResponse
    {
        $validator = Validator::make(['event_id' => $eventId], [
            'event_id' => 'required|integer|exists:events,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid event ID',
                'errors' => $validator->errors()
            ], 400);
        }

        $applications = VendorEventApplication::with(['vendor.user', 'event'])
            ->where('event_id', $eventId)
            ->where('status', '!=', 'cancelled')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'event_id' => $eventId,
                'applications' => $applications->map(function($app) {
                    return [
                        'application_id' => $app->id,
                        'vendor_id' => $app->vendor_id,
                        'vendor_name' => $app->vendor->business_name,
                        'booth_size' => $app->booth_size,
                        'booth_quantity' => $app->booth_quantity,
                        'service_type' => $app->service_type,
                        'service_description' => $app->service_description,
                        'requested_price' => $app->requested_price,
                        'status' => $app->status,
                        'applied_at' => $app->created_at
                    ];
                })
            ]
        ]);
    }

    /**
     * Submit event application
     * IFA: Event Application Submission Service
     */
    public function submitEventApplication(Request $request, $eventId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|integer|exists:vendors,id',
            'booth_size' => 'required|string|in:10x10,20x20,30x30',
            'booth_quantity' => 'required|integer|min:1|max:10',
            'service_type' => 'required|string|in:food,equipment,decoration,entertainment,logistics,other',
            'service_description' => 'required|string|max:1000',
            'requested_price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid application data',
                'errors' => $validator->errors()
            ], 400);
        }

        $event = Event::find($eventId);
        if (!$event || !$event->isAcceptingApplications()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event is not accepting applications'
            ], 400);
        }

        $vendor = Vendor::find($request->vendor_id);
        if (!$vendor || !$vendor->isApproved()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vendor not approved'
            ], 400);
        }

        try {
            $application = VendorEventApplication::create([
                'vendor_id' => $request->vendor_id,
                'event_id' => $eventId,
                'booth_size' => $request->booth_size,
                'booth_quantity' => $request->booth_quantity,
                'service_type' => $request->service_type,
                'service_description' => $request->service_description,
                'requested_price' => $request->requested_price,
                'status' => 'pending'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Application submitted successfully',
                'data' => [
                    'application_id' => $application->id,
                    'vendor_id' => $application->vendor_id,
                    'event_id' => $application->event_id,
                    'status' => $application->status,
                    'submitted_at' => $application->created_at
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to submit application',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vendor profile (authenticated)
     */
    public function getProfile(Request $request): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vendor profile not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'vendor_id' => $vendor->id,
                'business_name' => $vendor->business_name,
                'business_type' => $vendor->business_type,
                'business_description' => $vendor->business_description,
                'contact_person' => $vendor->contact_person,
                'contact_email' => $vendor->contact_email,
                'contact_phone' => $vendor->contact_phone,
                'business_address' => $vendor->business_address,
                'service_type' => $vendor->service_type,
                'service_categories' => $vendor->service_categories,
                'website' => $vendor->website,
                'rating' => $vendor->rating,
                'total_events' => $vendor->total_events,
                'is_verified' => $vendor->is_verified,
                'status' => $vendor->status
            ]
        ]);
    }

    /**
     * Update vendor profile (authenticated)
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vendor profile not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:100',
            'business_description' => 'required|string|max:500',
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'business_address' => 'required|string|max:500',
            'website' => 'nullable|url|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid profile data',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $vendor->update($request->only([
                'business_name', 'business_type', 'business_description',
                'contact_person', 'contact_email', 'contact_phone',
                'business_address', 'website'
            ]));

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'data' => [
                    'vendor_id' => $vendor->id,
                    'business_name' => $vendor->business_name,
                    'updated_at' => $vendor->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vendor's applications (authenticated)
     */
    public function getMyApplications(Request $request): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vendor profile not found'
            ], 404);
        }

        $applications = VendorEventApplication::with(['event'])
            ->where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'vendor_id' => $vendor->id,
                'applications' => $applications->map(function($app) {
                    return [
                        'application_id' => $app->id,
                        'event_id' => $app->event_id,
                        'event_name' => $app->event->name,
                        'booth_size' => $app->booth_size,
                        'booth_quantity' => $app->booth_quantity,
                        'service_type' => $app->service_type,
                        'status' => $app->status,
                        'applied_at' => $app->created_at
                    ];
                })
            ]
        ]);
    }

    /**
     * Get vendor's bookings (authenticated)
     */
    public function getMyBookings(Request $request): JsonResponse
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vendor profile not found'
            ], 404);
        }

        $bookings = VendorEventApplication::with(['event'])
            ->where('vendor_id', $vendor->id)
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'vendor_id' => $vendor->id,
                'bookings' => $bookings->map(function($booking) {
                    return [
                        'booking_id' => $booking->id,
                        'event_id' => $booking->event_id,
                        'event_name' => $booking->event->name,
                        'booth_size' => $booking->booth_size,
                        'booth_quantity' => $booking->booth_quantity,
                        'final_amount' => $booking->final_amount,
                        'paid_at' => $booking->paid_at
                    ];
                })
            ]
        ]);
    }
}
