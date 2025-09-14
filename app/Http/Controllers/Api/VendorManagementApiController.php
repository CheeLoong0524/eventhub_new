<?php

// Author  : Choong Yoong Sheng (Vendor module)


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorEventApplication;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VendorManagementApiController extends Controller
{
    /**
     * Get all vendors (admin)
     * IFA: Vendor Management Service
     */
    public function getAllVendors(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|string|in:pending,approved,rejected,suspended',
            'service_type' => 'nullable|string|in:food,equipment,decoration,entertainment,logistics,other',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        $query = Vendor::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }


        $perPage = $request->get('per_page', 15);
        $vendors = $query->orderBy('created_at', 'desc')->paginate($perPage);

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
     * Get vendor details (admin)
     * IFA: Vendor Details Service
     */
    public function getVendorDetails(Request $request, $id): JsonResponse
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
            ->find($id);

        if (!$vendor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vendor not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'vendor_id' => $vendor->id,
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
                'contact_person' => $vendor->contact_person,
                'contact_email' => $vendor->contact_email,
                'contact_phone' => $vendor->contact_phone,
                'website' => $vendor->website,
                'status' => $vendor->status,
                'rejection_reason' => $vendor->rejection_reason,
                'approved_at' => $vendor->approved_at,
                'rating' => $vendor->rating,
                'total_events' => $vendor->total_events,
                'is_verified' => $vendor->is_verified,
                'user_details' => [
                    'user_id' => $vendor->user->id,
                    'name' => $vendor->user->name,
                    'email' => $vendor->user->email,
                    'created_at' => $vendor->user->created_at
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
        ]);
    }

    /**
     * Approve vendor (admin)
     * IFA: Vendor Approval Service
     */
    public function approveVendor(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid data',
                'errors' => $validator->errors()
            ], 400);
        }

        $vendor = Vendor::find($id);
        
        if (!$vendor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vendor not found'
            ], 404);
        }

        if ($vendor->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only pending applications can be approved'
            ], 400);
        }

        try {
            // Update user role to vendor if not already
            $user = $vendor->user;
            if ($user && $user->role !== 'vendor') {
                $user->update(['role' => 'vendor']);
            }

            // Update vendor application status
            $vendor->update([
                'status' => 'approved',
                'admin_notes' => $request->admin_notes,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'approved_at' => now(),
                'approved_by' => Auth::id()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Vendor approved successfully',
                'data' => [
                    'vendor_id' => $vendor->id,
                    'business_name' => $vendor->business_name,
                    'status' => $vendor->status,
                    'approved_at' => $vendor->approved_at,
                    'approved_by' => $vendor->approved_by
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to approve vendor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject vendor (admin)
     * IFA: Vendor Rejection Service
     */
    public function rejectVendor(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Rejection reason is required',
                'errors' => $validator->errors()
            ], 400);
        }

        $vendor = Vendor::find($id);
        
        if (!$vendor) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vendor not found'
            ], 404);
        }

        if ($vendor->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only pending applications can be rejected'
            ], 400);
        }

        try {
            $vendor->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Vendor rejected successfully',
                'data' => [
                    'vendor_id' => $vendor->id,
                    'business_name' => $vendor->business_name,
                    'status' => $vendor->status,
                    'rejection_reason' => $vendor->rejection_reason,
                    'reviewed_at' => $vendor->reviewed_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reject vendor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all applications (admin)
     * IFA: Application Management Service
     */
    public function getApplications(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|string|in:pending,approved,rejected,paid,cancelled',
            'event_id' => 'nullable|integer|exists:events,id',
            'vendor_id' => 'nullable|integer|exists:vendors,id',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid parameters',
                'errors' => $validator->errors()
            ], 400);
        }

        $query = VendorEventApplication::with(['vendor.user', 'event']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        $perPage = $request->get('per_page', 15);
        $applications = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'applications' => $applications->items(),
                'pagination' => [
                    'current_page' => $applications->currentPage(),
                    'last_page' => $applications->lastPage(),
                    'per_page' => $applications->perPage(),
                    'total' => $applications->total()
                ]
            ]
        ]);
    }

    /**
     * Get application details (admin)
     * IFA: Application Details Service
     */
    public function getApplicationDetails(Request $request, $id): JsonResponse
    {
        $validator = Validator::make(['application_id' => $id], [
            'application_id' => 'required|integer|exists:vendor_event_applications,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid application ID',
                'errors' => $validator->errors()
            ], 400);
        }

        $application = VendorEventApplication::with(['vendor.user', 'event', 'reviewer'])
            ->find($id);

        if (!$application) {
            return response()->json([
                'status' => 'error',
                'message' => 'Application not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'application_id' => $application->id,
                'vendor_id' => $application->vendor_id,
                'event_id' => $application->event_id,
                'booth_size' => $application->booth_size,
                'booth_quantity' => $application->booth_quantity,
                'service_type' => $application->service_type,
                'service_description' => $application->service_description,
                'requested_price' => $application->requested_price,
                'status' => $application->status,
                'rejection_reason' => $application->rejection_reason,
                'applied_at' => $application->created_at,
                'reviewed_at' => $application->reviewed_at,
                'paid_at' => $application->paid_at,
                'vendor_details' => [
                    'vendor_id' => $application->vendor->id,
                    'business_name' => $application->vendor->business_name,
                    'contact_person' => $application->vendor->contact_person,
                    'contact_email' => $application->vendor->contact_email
                ],
                'event_details' => [
                    'event_id' => $application->event->id,
                    'event_name' => $application->event->name,
                    'start_time' => $application->event->start_time,
                    'end_time' => $application->event->end_time
                ]
            ]
        ]);
    }
}
