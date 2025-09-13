<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminInquiryResource;
use App\Models\SupportInquiry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminInquiryApiController extends Controller
{
    /**
     * Get all inquiries with admin replies
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page', 15);
            $status = $request->query('status');
            $search = $request->query('search');

            $query = SupportInquiry::with(['user', 'resolver']);

            // Filter by status if provided
            if ($status) {
                $query->where('status', $status);
            }

            // Search functionality
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('inquiry_id', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('subject', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%");
                });
            }

            $inquiries = $query->orderBy('created_at', 'desc')
                              ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Inquiries retrieved successfully',
                'data' => AdminInquiryResource::collection($inquiries->items()),
                'pagination' => [
                    'current_page' => $inquiries->currentPage(),
                    'last_page' => $inquiries->lastPage(),
                    'per_page' => $inquiries->perPage(),
                    'total' => $inquiries->total(),
                    'from' => $inquiries->firstItem(),
                    'to' => $inquiries->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve inquiries',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific inquiry with admin reply
     */
    public function show($inquiryId): JsonResponse
    {
        try {
            $inquiry = SupportInquiry::with(['user', 'resolver'])
                                   ->where('inquiry_id', $inquiryId)
                                   ->first();

            if (!$inquiry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Inquiry not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Inquiry retrieved successfully',
                'data' => new AdminInquiryResource($inquiry)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve inquiry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get inquiries by status
     */
    public function getByStatus($status): JsonResponse
    {
        try {
            $validStatuses = ['pending', 'resolved', 'closed'];
            
            if (!in_array($status, $validStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status. Valid statuses: ' . implode(', ', $validStatuses)
                ], 400);
            }

            $inquiries = SupportInquiry::with(['user', 'resolver'])
                                     ->where('status', $status)
                                     ->orderBy('created_at', 'desc')
                                     ->get();

            return response()->json([
                'success' => true,
                'message' => "Inquiries with status '{$status}' retrieved successfully",
                'data' => AdminInquiryResource::collection($inquiries),
                'count' => $inquiries->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve inquiries by status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get inquiry statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $totalInquiries = SupportInquiry::count();
            $pendingInquiries = SupportInquiry::where('status', 'pending')->count();
            $resolvedInquiries = SupportInquiry::where('status', 'resolved')->count();
            $closedInquiries = SupportInquiry::where('status', 'closed')->count();
            
            $recentInquiries = SupportInquiry::with(['user', 'resolver'])
                                           ->orderBy('created_at', 'desc')
                                           ->limit(5)
                                           ->get();

            $inquiriesWithReplies = SupportInquiry::whereNotNull('admin_reply')
                                                 ->where('admin_reply', '!=', '')
                                                 ->count();

            $avgResolutionTime = SupportInquiry::whereNotNull('resolved_at')
                                             ->whereNotNull('created_at')
                                             ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
                                             ->value('avg_hours');

            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => [
                    'total_inquiries' => $totalInquiries,
                    'status_breakdown' => [
                        'pending' => $pendingInquiries,
                        'resolved' => $resolvedInquiries,
                        'closed' => $closedInquiries,
                    ],
                    'inquiries_with_replies' => $inquiriesWithReplies,
                    'avg_resolution_time_hours' => round($avgResolutionTime ?? 0, 2),
                    'recent_inquiries' => AdminInquiryResource::collection($recentInquiries),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
