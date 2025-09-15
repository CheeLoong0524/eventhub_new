<?php
/** Author: Yap Jia Wei **/

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InquiryController extends Controller
{
    private $apiBaseUrl = 'http://127.0.0.1:8000/api/v1/admin';

    /**
     * Check inquiry status - Customer can check their inquiries
     */
    public function checkInquiry(Request $request)
    {
        try {
            // Get email from authenticated user or query parameter
            $email = $request->query('email');
            $inquiryId = $request->query('inquiry_id');

            // If no email in query, try to get from authenticated user
            if (!$email && auth()->check()) {
                $email = auth()->user()->email;
            }

            // If still no email, show search form for guest users
            if (!$email) {
                return view('support.check', [
                    'inquiries' => collect([]),
                    'showSearchForm' => true,
                    'isGuest' => true
                ]);
            }

            $useApi = $request->query('use_api', false);
            $inquiries = collect([]);

            if ($useApi) {
                // External API consumption (simulate another module)
                try {
                    if ($inquiryId) {
                        // Get specific inquiry
                        $response = Http::timeout(10)
                            ->get("{$this->apiBaseUrl}/inquiries/{$inquiryId}");
                        
                        if ($response->failed()) {
                            throw new \Exception('Failed to fetch inquiry from external API');
                        }
                        
                        $apiData = $response->json();
                        if (!$apiData['success']) {
                            throw new \Exception($apiData['message'] ?? 'API returned unsuccessful response');
                        }
                        
                        $inquiry = $apiData['data'];
                        
                        // Verify email matches (security check)
                        if ($inquiry['email'] !== $email) {
                            return view('support.check', [
                                'inquiries' => collect([]),
                                'error' => 'Email address does not match this inquiry. Please verify your email.',
                                'showSearchForm' => !auth()->check(),
                            'isAuthenticated' => auth()->check()
                            ]);
                        }
                        
                        $inquiries = collect([$inquiry]);
                    } else {
                        // Get all inquiries and filter by email
                        $response = Http::timeout(10)
                            ->get("{$this->apiBaseUrl}/inquiries");
                        
                        if ($response->failed()) {
                            throw new \Exception('Failed to fetch inquiries from external API');
                        }
                        
                        $apiData = $response->json();
                        if (!$apiData['success']) {
                            throw new \Exception($apiData['message'] ?? 'API returned unsuccessful response');
                        }
                        
                        // Filter inquiries by email
                        $allInquiries = collect($apiData['data']);
                        $inquiries = $allInquiries->filter(function($inquiry) use ($email) {
                            return $inquiry['email'] === $email;
                        });
                    }
                } catch (\Exception $e) {
                    Log::warning('External API failed, falling back to internal service', [
                        'error' => $e->getMessage(),
                        'email' => $email,
                        'inquiry_id' => $inquiryId
                    ]);
                    throw $e; // Re-throw to trigger fallback
                }
            } else {
                // Internal service consumption (default)
                $apiController = new \App\Http\Controllers\Api\AdminInquiryApiController();
                
                if ($inquiryId) {
                    // Get specific inquiry
                    $response = $apiController->show($inquiryId);
                    $apiData = $response->getData(true);
                    
                    if (!$apiData['success']) {
                        throw new \Exception($apiData['message'] ?? 'Internal service returned unsuccessful response');
                    }
                    
                    $inquiry = $apiData['data'];
                    
                    // Verify email matches (security check)
                    if ($inquiry['email'] !== $email) {
                        return view('support.check', [
                            'inquiries' => collect([]),
                            'error' => 'Email address does not match this inquiry. Please verify your email.',
                            'showSearchForm' => !auth()->check(),
                            'isAuthenticated' => auth()->check()
                        ]);
                    }
                    
                    $inquiries = collect([$inquiry]);
                } else {
                    // Get all inquiries and filter by email
                    $response = $apiController->index(new Request());
                    $apiData = $response->getData(true);
                    
                    if (!$apiData['success']) {
                        throw new \Exception($apiData['message'] ?? 'Internal service returned unsuccessful response');
                    }
                    
                    // Filter inquiries by email
                    $allInquiries = collect($apiData['data']);
                    $inquiries = $allInquiries->filter(function($inquiry) use ($email) {
                        return $inquiry['email'] === $email;
                    });
                }
            }

        } catch (\Exception $e) {
            // Fallback to internal service if external API fails
            try {
                Log::info('Using fallback internal service for inquiry check', [
                    'email' => $email,
                    'inquiry_id' => $inquiryId,
                    'original_error' => $e->getMessage()
                ]);
                
                $apiController = new \App\Http\Controllers\Api\AdminInquiryApiController();
                
                if ($inquiryId) {
                    // Get specific inquiry
                    $response = $apiController->show($inquiryId);
                    $apiData = $response->getData(true);
                    
                    if (!$apiData['success']) {
                        if (strpos($apiData['message'] ?? '', 'not found') !== false) {
                            return view('support.check', [
                                'inquiries' => collect([]),
                                'error' => 'Inquiry not found. Please check your Inquiry ID.',
                                'showSearchForm' => !auth()->check(),
                            'isAuthenticated' => auth()->check()
                            ]);
                        }
                        throw new \Exception($apiData['message'] ?? 'Failed to retrieve inquiry information.');
                    }
                    
                    $inquiry = $apiData['data'];
                    
                    // Verify email matches (security check)
                    if ($inquiry['email'] !== $email) {
                        return view('support.check', [
                            'inquiries' => collect([]),
                            'error' => 'Email address does not match this inquiry. Please verify your email.',
                            'showSearchForm' => !auth()->check(),
                            'isAuthenticated' => auth()->check()
                        ]);
                    }
                    
                    $inquiries = collect([$inquiry]);
                } else {
                    // Get all inquiries and filter by email
                    $response = $apiController->index(new Request());
                    $apiData = $response->getData(true);
                    
                    if (!$apiData['success']) {
                        throw new \Exception($apiData['message'] ?? 'Failed to retrieve inquiry information.');
                    }
                    
                    // Filter inquiries by email
                    $allInquiries = collect($apiData['data']);
                    $inquiries = $allInquiries->filter(function($inquiry) use ($email) {
                        return $inquiry['email'] === $email;
                    });
                }
            } catch (\Exception $fallbackError) {
                Log::error('Both external API and internal service failed', [
                    'email' => $email,
                    'inquiry_id' => $inquiryId,
                    'external_error' => $e->getMessage(),
                    'internal_error' => $fallbackError->getMessage()
                ]);
                
                return view('support.check', [
                    'inquiries' => collect([]),
                    'error' => 'Unable to retrieve inquiry information. Please try again later.',
                    'showSearchForm' => true
                ]);
            }
        }

        // Transform API data for view compatibility
        $inquiries = $inquiries->map(function($inquiry) {
            return $this->transformInquiryData($inquiry);
        });

        return view('support.check', [
            'inquiries' => $inquiries,
            'email' => $email,
            'showSearchForm' => !auth()->check(), // Only show search form for guest users
            'isAuthenticated' => auth()->check()
        ]);
    }

    /**
     * View specific inquiry details with admin reply
     */
    public function viewInquiry(Request $request, $inquiryId)
    {
        try {
            $email = $request->query('email');

            if (!$email) {
                return redirect()->route('support.check')
                    ->with('error', 'Email address is required to view inquiry details.');
            }

            $useApi = $request->query('use_api', false);
            $inquiry = null;

            if ($useApi) {
                // External API consumption (simulate another module)
                try {
                    $response = Http::timeout(10)
                        ->get("{$this->apiBaseUrl}/inquiries/{$inquiryId}");
                    
                    if ($response->failed()) {
                        throw new \Exception('Failed to fetch inquiry details from external API');
                    }
                    
                    $apiData = $response->json();
                    if (!$apiData['success']) {
                        throw new \Exception($apiData['message'] ?? 'API returned unsuccessful response');
                    }
                    
                    $inquiry = $apiData['data'];
                } catch (\Exception $e) {
                    Log::warning('External API failed for inquiry details, falling back to internal service', [
                        'error' => $e->getMessage(),
                        'inquiry_id' => $inquiryId
                    ]);
                    throw $e; // Re-throw to trigger fallback
                }
            } else {
                // Internal service consumption (default)
                $apiController = new \App\Http\Controllers\Api\AdminInquiryApiController();
                $response = $apiController->show($inquiryId);
                $apiData = $response->getData(true);
                
                if (!$apiData['success']) {
                    throw new \Exception($apiData['message'] ?? 'Internal service returned unsuccessful response');
                }
                
                $inquiry = $apiData['data'];
            }

        } catch (\Exception $e) {
            // Fallback to internal service if external API fails
            try {
                Log::info('Using fallback internal service for inquiry details', [
                    'inquiry_id' => $inquiryId,
                    'original_error' => $e->getMessage()
                ]);
                
                $apiController = new \App\Http\Controllers\Api\AdminInquiryApiController();
                $response = $apiController->show($inquiryId);
                $apiData = $response->getData(true);
                
                if (!$apiData['success']) {
                    if (strpos($apiData['message'] ?? '', 'not found') !== false) {
                        return redirect()->route('support.check')
                            ->with('error', 'Inquiry not found.');
                    }
                    throw new \Exception($apiData['message'] ?? 'Failed to retrieve inquiry details.');
                }
                
                $inquiry = $apiData['data'];
            } catch (\Exception $fallbackError) {
                Log::error('Both external API and internal service failed for inquiry details', [
                    'inquiry_id' => $inquiryId,
                    'external_error' => $e->getMessage(),
                    'internal_error' => $fallbackError->getMessage()
                ]);
                
                return redirect()->route('support.check')
                    ->with('error', 'Unable to retrieve inquiry details. Please try again later.');
            }
        }

        // Verify email matches (security check)
        if ($inquiry['email'] !== $email) {
            return redirect()->route('support.check')
                ->with('error', 'Email address does not match this inquiry.');
        }

        // Transform API data for view compatibility
        $inquiry = $this->transformInquiryData($inquiry);

        return view('support.inquiry-details', [
            'inquiry' => $inquiry
        ]);
    }

    /**
     * Get inquiry statistics (if needed for customer dashboard)
     */
    public function getStats(Request $request)
    {
        try {
            $useApi = $request->query('use_api', false);
            $apiData = null;

            if ($useApi) {
                // External API consumption (simulate another module)
                try {
                    $response = Http::timeout(10)
                        ->get("{$this->apiBaseUrl}/inquiries/stats");
                    
                    if ($response->failed()) {
                        throw new \Exception('Failed to fetch statistics from external API');
                    }
                    
                    $apiData = $response->json();
                    if (!$apiData['success']) {
                        throw new \Exception($apiData['message'] ?? 'API returned unsuccessful response');
                    }
                } catch (\Exception $e) {
                    Log::warning('External API failed for statistics, falling back to internal service', [
                        'error' => $e->getMessage()
                    ]);
                    throw $e; // Re-throw to trigger fallback
                }
            } else {
                // Internal service consumption (default)
                $apiController = new \App\Http\Controllers\Api\AdminInquiryApiController();
                $response = $apiController->stats();
                $apiData = $response->getData(true);
                
                if (!$apiData['success']) {
                    throw new \Exception($apiData['message'] ?? 'Internal service returned unsuccessful response');
                }
            }

        } catch (\Exception $e) {
            // Fallback to internal service if external API fails
            try {
                Log::info('Using fallback internal service for statistics', [
                    'original_error' => $e->getMessage()
                ]);
                
                $apiController = new \App\Http\Controllers\Api\AdminInquiryApiController();
                $response = $apiController->stats();
                $apiData = $response->getData(true);
                
                if (!$apiData['success']) {
                    throw new \Exception($apiData['message'] ?? 'Failed to retrieve statistics.');
                }
            } catch (\Exception $fallbackError) {
                Log::error('Both external API and internal service failed for statistics', [
                    'external_error' => $e->getMessage(),
                    'internal_error' => $fallbackError->getMessage()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to retrieve statistics.'
                ], 500);
            }
        }

        return response()->json($apiData);
    }

    /**
     * Transform API data for view compatibility
     */
    private function transformInquiryData($inquiry)
    {
        // Convert to object for view compatibility
        $inquiry = (object) $inquiry;

        // Convert nested arrays to objects
        if (isset($inquiry->user) && is_array($inquiry->user)) {
            $inquiry->user = (object) $inquiry->user;
        }
        if (isset($inquiry->resolver) && is_array($inquiry->resolver)) {
            $inquiry->resolver = (object) $inquiry->resolver;
        }

        // Convert date strings to Carbon objects
        if (isset($inquiry->created_at) && is_string($inquiry->created_at)) {
            $inquiry->created_at = \Carbon\Carbon::parse($inquiry->created_at);
        }
        if (isset($inquiry->updated_at) && is_string($inquiry->updated_at)) {
            $inquiry->updated_at = \Carbon\Carbon::parse($inquiry->updated_at);
        }
        if (isset($inquiry->resolved_at) && is_string($inquiry->resolved_at)) {
            $inquiry->resolved_at = \Carbon\Carbon::parse($inquiry->resolved_at);
        }

        return $inquiry;
    }
}
