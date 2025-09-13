<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportInquiry;
use App\Models\Faq;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Api\InquiryController;

class SupportController extends Controller
{
    /**
     * Show the admin support dashboard
     */
    public function index(Request $request)
    {
        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = $request->query('use_api', false);

            if ($useApi) {
                // External API consumption (simulate another module)
                $statsResponse = Http::timeout(10)
                    ->get('http://127.0.0.1:8001/api/v1/inquiries/stats');
                
                if ($statsResponse->failed()) {
                    throw new \Exception('Failed to fetch statistics from API');
                }
                $stats = $statsResponse->json()['data'];

                $inquiriesResponse = Http::timeout(10)
                    ->get('http://127.0.0.1:8001/api/v1/inquiries', [
                        'per_page' => 10
                    ]);
                
                if ($inquiriesResponse->failed()) {
                    throw new \Exception('Failed to fetch inquiries from API');
                }
                $recentInquiries = collect($inquiriesResponse->json()['data'])->map(function($item) {
                    $item = (object) $item;
                    if (isset($item->user) && is_array($item->user)) {
                        $item->user = (object) $item->user;
                    }
                    // Convert date strings to DateTime objects
                    if (isset($item->created_at) && is_string($item->created_at)) {
                        $item->created_at = \Carbon\Carbon::parse($item->created_at);
                    }
                    if (isset($item->updated_at) && is_string($item->updated_at)) {
                        $item->updated_at = \Carbon\Carbon::parse($item->updated_at);
                    }
                    if (isset($item->resolved_at) && is_string($item->resolved_at)) {
                        $item->resolved_at = \Carbon\Carbon::parse($item->resolved_at);
                    }
                    return $item;
                });
            } else {
                // Internal service consumption (default)
                $apiController = new InquiryController();
                $statsResponse = $apiController->stats();
                $stats = $statsResponse->getData(true)['data'];

                $request = new Request(['per_page' => 10]);
                $inquiriesResponse = $apiController->index($request);
                $recentInquiries = collect($inquiriesResponse->getData(true)['data'])->map(function($item) {
                    $item = (object) $item;
                    if (isset($item->user) && is_array($item->user)) {
                        $item->user = (object) $item->user;
                    }
                    // Convert date strings to DateTime objects
                    if (isset($item->created_at) && is_string($item->created_at)) {
                        $item->created_at = \Carbon\Carbon::parse($item->created_at);
                    }
                    if (isset($item->updated_at) && is_string($item->updated_at)) {
                        $item->updated_at = \Carbon\Carbon::parse($item->updated_at);
                    }
                    if (isset($item->resolved_at) && is_string($item->resolved_at)) {
                        $item->resolved_at = \Carbon\Carbon::parse($item->resolved_at);
                    }
                    return $item;
                });
            }
        } catch (\Exception $e) {
            // Fallback to internal service if external API fails
            $apiController = new InquiryController();
            $statsResponse = $apiController->stats();
            $stats = $statsResponse->getData(true)['data'];

            $request = new Request(['per_page' => 10]);
            $inquiriesResponse = $apiController->index($request);
            $recentInquiries = collect($inquiriesResponse->getData(true)['data'])->map(function($item) {
                $item = (object) $item;
                if (isset($item->user) && is_array($item->user)) {
                    $item->user = (object) $item->user;
                }
                // Convert date strings to DateTime objects
                if (isset($item->created_at) && is_string($item->created_at)) {
                    $item->created_at = \Carbon\Carbon::parse($item->created_at);
                }
                if (isset($item->updated_at) && is_string($item->updated_at)) {
                    $item->updated_at = \Carbon\Carbon::parse($item->updated_at);
                }
                if (isset($item->resolved_at) && is_string($item->resolved_at)) {
                    $item->resolved_at = \Carbon\Carbon::parse($item->resolved_at);
                }
                return $item;
            });
        }


        // Get FAQs count
        $faqStats = [
            'total_faqs' => Faq::count(),
            'active_faqs' => Faq::where('is_active', true)->count(),
            'faqs_by_category' => Faq::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->get()
                ->pluck('count', 'category')
        ];

        return view('admin.support.index', compact('stats', 'recentInquiries', 'faqStats'));
    }

    /**
     * Show all support inquiries
     */
    public function inquiries(Request $request)
    {
        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = $request->query('use_api', false);

            if ($useApi) {
                // External API consumption (simulate another module)
                $response = Http::timeout(10)
                    ->get('http://127.0.0.1:8001/api/v1/inquiries', [
                        'per_page' => 20,
                        'page' => $request->get('page', 1),
                        'status' => $request->get('status'),
                        'search' => $request->get('search')
                    ]);
                
                if ($response->failed()) {
                    throw new \Exception('Failed to fetch inquiries from API');
                }
                $apiData = $response->json();
            } else {
                // Internal service consumption (default)
                $apiController = new InquiryController();
                $response = $apiController->index($request);
                $apiData = $response->getData(true);
            }
        } catch (\Exception $e) {
            // Fallback to internal service if external API fails
            $apiController = new InquiryController();
            $response = $apiController->index($request);
            $apiData = $response->getData(true);
        }

        $inquiries = collect($apiData['data'])->map(function($item) {
            $item = (object) $item;
            if (isset($item->user) && is_array($item->user)) {
                $item->user = (object) $item->user;
            }
            // Convert date strings to DateTime objects
            if (isset($item->created_at) && is_string($item->created_at)) {
                $item->created_at = \Carbon\Carbon::parse($item->created_at);
            }
            if (isset($item->updated_at) && is_string($item->updated_at)) {
                $item->updated_at = \Carbon\Carbon::parse($item->updated_at);
            }
            if (isset($item->resolved_at) && is_string($item->resolved_at)) {
                $item->resolved_at = \Carbon\Carbon::parse($item->resolved_at);
            }
            return $item;
        });
        $meta = $apiData['meta'];
        
        // Create a paginator-like object for the view
        $inquiries = new \Illuminate\Pagination\LengthAwarePaginator(
            $inquiries,
            $meta['total'],
            $meta['per_page'],
            $meta['current_page'],
            ['path' => $request->url(), 'pageName' => 'page']
        );

        return view('admin.support.inquiries', compact('inquiries'));
    }

    /**
     * Show specific inquiry details
     */
    public function showInquiry(Request $request, $inquiryId)
    {
        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = $request->query('use_api', false);

            if ($useApi) {
                // External API consumption (simulate another module)
                $response = Http::timeout(10)
                    ->get('http://127.0.0.1:8001/api/v1/inquiries/' . $inquiryId);
                
                if ($response->failed()) {
                    throw new \Exception('Failed to fetch inquiry from API');
                }
                $apiData = $response->json();
            } else {
                // Internal service consumption (default)
                $apiController = new InquiryController();
                $response = $apiController->show($inquiryId);
                $apiData = $response->getData(true);
            }
        } catch (\Exception $e) {
            // Fallback to internal service if external API fails
            $apiController = new InquiryController();
            $response = $apiController->show($inquiryId);
            $apiData = $response->getData(true);
        }

        $inquiry = (object) $apiData['data']; // Convert array to object for view compatibility
        
        // Convert nested user and resolver objects if they exist
        if (isset($inquiry->user) && is_array($inquiry->user)) {
            $inquiry->user = (object) $inquiry->user;
        }
        if (isset($inquiry->resolver) && is_array($inquiry->resolver)) {
            $inquiry->resolver = (object) $inquiry->resolver;
        }
        
        // Convert date strings to DateTime objects
        if (isset($inquiry->created_at) && is_string($inquiry->created_at)) {
            $inquiry->created_at = \Carbon\Carbon::parse($inquiry->created_at);
        }
        if (isset($inquiry->updated_at) && is_string($inquiry->updated_at)) {
            $inquiry->updated_at = \Carbon\Carbon::parse($inquiry->updated_at);
        }
        if (isset($inquiry->resolved_at) && is_string($inquiry->resolved_at)) {
            $inquiry->resolved_at = \Carbon\Carbon::parse($inquiry->resolved_at);
        }

        return view('admin.support.inquiry-details', compact('inquiry'));
    }

    /**
     * Update inquiry status and add admin reply
     */
    public function updateInquiry(Request $request, $inquiryId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,resolved,closed',
            'admin_reply' => 'nullable|string|max:2000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $inquiry = SupportInquiry::where('inquiry_id', $inquiryId)->firstOrFail();

        $updateData = [
            'status' => $request->status
        ];

        if ($request->admin_reply) {
            $updateData['admin_reply'] = $request->admin_reply;
        }

        if ($request->status === 'resolved' || $request->status === 'closed') {
            $updateData['resolved_at'] = now();
            $updateData['resolved_by'] = auth()->id();
        }

        $inquiry->update($updateData);

        return redirect()->back()->with('success', 'Inquiry updated successfully!');
    }

    /**
     * Show FAQ management page
     */
    public function faqs()
    {
        $faqs = Faq::orderBy('category')
            ->orderBy('created_at')
            ->get()
            ->groupBy('category');

        return view('admin.support.faqs', compact('faqs'));
    }

    /**
     * Create new FAQ
     */
    public function createFaq(Request $request)
    {
        // Debug: Log the request
        \Log::info('FAQ Create Request', [
            'method' => $request->method(),
            'data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'category' => 'required|in:general,technical,billing,event,customer',
            'is_active' => 'nullable|in:on'
        ]);

        if ($validator->fails()) {
            \Log::error('FAQ Create Validation Failed', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $faq = Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'is_active' => $request->has('is_active')
        ]);

        \Log::info('FAQ Created Successfully', [
            'id' => $faq->id,
            'question' => $faq->question,
            'category' => $faq->category
        ]);

        return redirect()->back()->with('success', 'FAQ created successfully!');
    }

    /**
     * Update FAQ
     */
    public function updateFaq(Request $request, $id)
    {
        // Debug: Log the request
        \Log::info('FAQ Update Request', [
            'id' => $id,
            'method' => $request->method(),
            'data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'category' => 'required|in:general,technical,billing,event,customer',
            'is_active' => 'nullable|in:on'
        ]);

        if ($validator->fails()) {
            \Log::error('FAQ Update Validation Failed', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $faq = Faq::findOrFail($id);
        
        \Log::info('FAQ Before Update', [
            'id' => $faq->id,
            'question' => $faq->question,
            'answer' => $faq->answer
        ]);

        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'is_active' => $request->has('is_active')
        ]);

        \Log::info('FAQ After Update', [
            'id' => $faq->id,
            'question' => $faq->question,
            'answer' => $faq->answer
        ]);

        return redirect()->back()->with('success', 'FAQ updated successfully!');
    }

    /**
     * Delete FAQ
     */
    public function deleteFaq($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return redirect()->back()->with('success', 'FAQ deleted successfully!');
    }
}
