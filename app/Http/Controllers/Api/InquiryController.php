<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InquiryResource;
use App\Models\SupportInquiry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InquiryController extends Controller
{
    /**
     * Get all inquiries
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get query parameters
            $perPage = $request->get('per_page', 15);
            $status = $request->get('status');
            $search = $request->get('search');

            // Build query
            $query = SupportInquiry::with('user')->orderBy('created_at', 'desc');

            // Filter by status if provided
            if ($status) {
                $query->where('status', $status);
            }

            // Search functionality
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('subject', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Get paginated results
            $inquiries = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Inquiries retrieved successfully',
                'data' => InquiryResource::collection($inquiries->items()),
                'meta' => [
                    'total' => $inquiries->total(),
                    'per_page' => $inquiries->perPage(),
                    'current_page' => $inquiries->currentPage(),
                    'last_page' => $inquiries->lastPage(),
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
     * Get specific inquiry by ID
     */
    public function show(string $inquiryId): JsonResponse
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
                'data' => new InquiryResource($inquiry)
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
     * Get inquiry statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_inquiries' => SupportInquiry::count(),
                'pending_inquiries' => SupportInquiry::where('status', 'pending')->count(),
                'resolved_inquiries' => SupportInquiry::where('status', 'resolved')->count(),
                'closed_inquiries' => SupportInquiry::where('status', 'closed')->count(),
                'today_inquiries' => SupportInquiry::whereDate('created_at', today())->count(),
                'this_week_inquiries' => SupportInquiry::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'this_month_inquiries' => SupportInquiry::whereMonth('created_at', now()->month)->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Statistics retrieved successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get inquiries by status
     */
    public function getByStatus(string $status, Request $request): JsonResponse
    {
        try {
            $validStatuses = ['pending', 'resolved', 'closed'];
            
            if (!in_array($status, $validStatuses)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status. Valid statuses: ' . implode(', ', $validStatuses)
                ], 400);
            }

            $perPage = $request->get('per_page', 15);
            $inquiries = SupportInquiry::with('user')
                ->where('status', $status)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => "Inquiries with status '{$status}' retrieved successfully",
                'data' => InquiryResource::collection($inquiries->items()),
                'meta' => [
                    'total' => $inquiries->total(),
                    'per_page' => $inquiries->perPage(),
                    'current_page' => $inquiries->currentPage(),
                    'last_page' => $inquiries->lastPage(),
                    'from' => $inquiries->firstItem(),
                    'to' => $inquiries->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve inquiries by status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
