<?php

// Author  : Choong Yoong Sheng (Vendor module)


namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Event;
use App\Models\VendorEventApplication;
use App\Payment\PaymentBuilder;
use App\Services\ExternalApiService;
use App\Services\VendorService;
use App\Factories\VendorFactoryManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    protected $vendorService;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    /**
     * Show vendor dashboard
     */
    public function dashboard()
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Please complete your vendor application first.');
        }

        // Get recommended events (active events that vendor hasn't applied to)
        $recommendedEvents = \App\Models\Event::where('status', 'active')
            ->whereDoesntHave('vendorApplications', function($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id)
                      ->where('status', '!=', 'cancelled');
            })
            ->where(function($query) {
                $query->where('start_time', '>', now()->endOfDay())  // 只顯示未來的事件
                      ->orWhereNull('start_time');
            })
            ->with(['venue'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Get recent applications
        $recentApplications = $vendor->eventApplications()
            ->with(['event'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Optional: Get user info using internal service (fast, no HTTP calls)
        // Uncomment the following lines if you need user information in dashboard
        /*
        $userInfo = $this->vendorService->getVendorInfo($vendor->id);
        if ($userInfo['success']) {
            Log::info('Internal service consumption successful', [
                'service' => 'getVendorInfo',
                'vendor_id' => $vendor->id,
                'data' => $userInfo['data']
            ]);
        }
        */

        return view('vendor.dashboard', compact('vendor', 'recommendedEvents', 'recentApplications'));
    }

    /**
     * Show vendor application form
     */
    public function showApplicationForm()
    {
        $vendor = Auth::user()->vendor;
        
        // Allow access to the form if vendor is missing, pending, or rejected
        if ($vendor && !in_array($vendor->status, ['pending', 'rejected'], true)) {
            // Approved or other terminal state -> no reapply
            return redirect()->route('vendor.dashboard')->with('info', 'Your vendor account is already active.');
        }

        return view('vendor.application-form', compact('vendor'));
    }

    /**
     * Submit vendor application
     */
    public function submitApplication(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:100',
            'business_description' => 'required|string|max:500',
            'business_phone' => 'required|string|max:20',
            'business_email' => 'required|email|max:255',
            'years_in_business' => 'required|string|max:20',
            'business_size' => 'required|string|max:20',
            'annual_revenue' => 'required|string|max:20',
            'event_experience' => 'required|string|max:20',
            'product_category' => 'required|string|max:100',
            'target_audience' => 'required|string|max:100',
            'marketing_strategy' => 'required|string|max:300',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $existingVendor = Auth::user()->vendor;

            // If previously rejected or still pending, allow updating the record and keep/reset status to pending
            if ($existingVendor && in_array($existingVendor->status, ['rejected', 'pending'], true)) {
                $existingVendor->update([
                    'business_name' => $request->business_name,
                    'business_type' => $request->business_type,
                    'business_description' => $request->business_description,
                    'business_phone' => $request->business_phone,
                    'business_email' => $request->business_email,
                    'years_in_business' => $request->years_in_business,
                    'business_size' => $request->business_size,
                    'annual_revenue' => $request->annual_revenue,
                    'event_experience' => $request->event_experience,
                    'product_category' => $request->product_category,
                    'target_audience' => $request->target_audience,
                    'marketing_strategy' => $request->marketing_strategy,
                    'status' => 'pending',
                    'rejection_reason' => null,
                    'approved_at' => null,
                    'approved_by' => null,
                ]);

                return redirect()->route('vendor.application.status', $existingVendor->id)
                    ->with('success', 'Your vendor application has been submitted.');
            }

            // If vendor already exists and is not rejected, block duplicate submission
            if ($existingVendor) {
                return redirect()->route('vendor.dashboard')->with('info', 'You have already submitted a vendor application.');
            }

            // First-time submission using VendorFactoryManager with decorators
            $vendor = VendorFactoryManager::createVendor([
                'user_id' => Auth::id(),
                'business_name' => $request->business_name,
                'business_type' => $request->business_type,
                'business_description' => $request->business_description,
                'business_phone' => $request->business_phone,
                'business_email' => $request->business_email,
                'years_in_business' => $request->years_in_business,
                'business_size' => $request->business_size,
                'annual_revenue' => $request->annual_revenue,
                'event_experience' => $request->event_experience,
                'product_category' => $request->product_category,
                'target_audience' => $request->target_audience,
                'marketing_strategy' => $request->marketing_strategy,
                'status' => 'pending',
            ]);

            return redirect()->route('vendor.application.status', $vendor->id)
                ->with('success', 'Your vendor application has been submitted.');

        } catch (\Exception $e) {
            Log::error('Vendor application submission failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to submit application. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show application status
     */
    public function showApplicationStatus($id)
    {
        $vendor = Vendor::where('id', $id)
                       ->where('user_id', Auth::id())
                       ->first();
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Application not found.');
        }

        return view('vendor.application-status', compact('vendor'));
    }

    /**
     * Show vendor profile
     */
    public function showProfile()
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Please complete your vendor application first.');
        }

        if (!$vendor->isApproved()) {
            return redirect()->route('vendor.application.status', $vendor->id)
                ->with('info', 'Your vendor application is pending approval. Profile access will be available after approval.');
        }

        return view('vendor.profile', compact('vendor'));
    }

    /**
     * Update vendor profile
     */
    public function updateProfile(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Please complete your vendor application first.');
        }

        if (!$vendor->isApproved()) {
            return redirect()->route('vendor.application.status', $vendor->id)
                ->with('error', 'Your vendor application is pending approval. You cannot update profile until approved.');
        }

        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:100',
            'business_description' => 'required|string|max:500',
            'business_phone' => 'required|string|max:20',
            'business_email' => 'required|email|max:255',
            'years_in_business' => 'required|string|max:20',
            'business_size' => 'required|string|max:20',
            'annual_revenue' => 'required|string|max:20',
            'event_experience' => 'required|string|max:20',
            'product_category' => 'required|string|max:100',
            'target_audience' => 'required|string|max:100',
            'marketing_strategy' => 'required|string|max:300',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $vendor->update([
                'business_name' => $request->business_name,
                'business_type' => $request->business_type,
                'business_description' => $request->business_description,
                'business_phone' => $request->business_phone,
                'business_email' => $request->business_email,
                'years_in_business' => $request->years_in_business,
                'business_size' => $request->business_size,
                'annual_revenue' => $request->annual_revenue,
                'event_experience' => $request->event_experience,
                'product_category' => $request->product_category,
                'target_audience' => $request->target_audience,
                'marketing_strategy' => $request->marketing_strategy,
            ]);

            return redirect()->route('vendor.profile')
                ->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            Log::error('Vendor profile update failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update profile. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show available events (simplified)
     * @param bool $useApi Set true to consume API via HTTP, false for internal service
     */
    public function showAvailableEvents(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Please complete your vendor application first.');
        }

        if (!$vendor->isApproved()) {
            return redirect()->route('vendor.application.status', $vendor->id)
                ->with('error', 'Your vendor registration is pending approval. You cannot browse/apply to events yet.');
        }

        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = $request->query('use_api', false);

            if ($useApi) {
                // External API consumption (simulate another module)
                $response = Http::timeout(10)
                    ->get(url('/api/v1/vendor/events/accepting-applications'), [
                        'per_page' => 12,
                        'page' => $request->get('page', 1),
                    ]);

                if ($response->failed()) {
                    throw new \Exception('Failed to fetch events from API');
                }

                $payload = $response->json();
                $eventArray = $payload['data'] ?? [];

                if (!empty($eventArray)) {
                    // Convert API data to Event model format
                    $events = collect($eventArray)->map(function($eventData) {
                        $event = new Event($eventData);
                        $event->exists = true;
                        $event->id = $eventData['id'];
                        
                        if (isset($eventData['venue'])) {
                            $venue = new \App\Models\Venue($eventData['venue']);
                            $venue->exists = true;
                            $venue->id = $eventData['venue']['id'];
                            $event->setRelation('venue', $venue);
                        }
                        
                        return $event;
                    });

                    // Create paginator
                    $events = new \Illuminate\Pagination\LengthAwarePaginator(
                        items: $events,
                        total: $payload['pagination']['total'] ?? $events->count(),
                        perPage: $payload['pagination']['per_page'] ?? 12,
                        currentPage: $payload['pagination']['current_page'] ?? 1,
                        options: ['path' => request()->url(), 'query' => request()->query()]
                    );
                } else {
                    throw new \Exception('No events returned from API');
                }
            } else {
                // Internal service consumption
                $result = $this->vendorService->getEventsAcceptingApplications(12, $request->get('page', 1));
                
                if (!$result['success']) {
                    throw new \Exception($result['error']);
                }

                $eventArray = $result['data'];
                $pagination = $result['pagination'];

                // Service already returns Event models, no need to convert
                $events = collect($eventArray);

                // Create paginator
                $events = new \Illuminate\Pagination\LengthAwarePaginator(
                    items: $events,
                    total: $pagination['total'],
                    perPage: $pagination['per_page'],
                    currentPage: $pagination['current_page'],
                    options: ['path' => request()->url(), 'query' => request()->query()]
                );
            }
        } catch (\Exception $e) {
            Log::warning('Vendor events service failed, using fallback', [
                'error' => $e->getMessage(),
                'use_api' => $useApi ?? false
            ]);
            
            // Fallback on exceptions
            $events = Event::where('status', 'active')
                          ->whereNotNull('booth_price')
                          ->where('booth_quantity', '>', 0)
                          ->where(function($query) {
                              $query->where('start_time', '>', now()->endOfDay())
                                    ->orWhereNull('start_time');
                          })
                          ->orderBy('start_time')
                          ->paginate(12);
        }

        return view('vendor.available-events', compact('events', 'vendor'));
    }

    /**
     * Show event details (simplified)
     * @param bool $useApi Set true to consume API via HTTP, false for internal service
     */
    public function showEvent(Request $request, $id)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Please complete your vendor application first.');
        }

        if (!$vendor->isApproved()) {
            return redirect()->route('vendor.application.status', $vendor->id)
                ->with('error', 'Your vendor registration is pending approval. You cannot view event application details yet.');
        }

        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = $request->query('use_api', false);

            if ($useApi) {
                // External API consumption (simulate another module)
                $response = Http::timeout(10)
                    ->get(url("/api/v1/events/{$id}"));

                if ($response->failed()) {
                    throw new \Exception('Failed to fetch event from API');
                }

                $payload = $response->json();
                $data = $payload['data'] ?? null;
                
                if (is_array($data) && isset($data['id'])) {
                    $event = Event::with(['venue', 'activities'])->findOrFail($data['id']);
                } else {
                    throw new \Exception('Invalid event data from API');
                }
            } else {
                // Internal service consumption
                $result = $this->vendorService->getEventInfo($id);
                
                if (!$result['success']) {
                    throw new \Exception($result['error']);
                }

                $event = $result['data'];
            }
        } catch (\Exception $e) {
            Log::warning('Vendor event service failed, using fallback', [
                'event_id' => $id,
                'error' => $e->getMessage(),
                'use_api' => $useApi ?? false
            ]);
            
            // Fallback on exceptions
            $event = Event::with(['venue', 'activities'])->findOrFail($id);
        }
        
        // Get all applications for this event (excluding cancelled applications)
        $existingApplications = $vendor->eventApplications()
            ->where('event_id', $event->id)
            ->where('status', '!=', 'cancelled')
            ->get();

        return view('vendor.event-details', compact('event', 'vendor', 'existingApplications'));
    }

    // Notification features removed as per request

    // Analytics feature removed

    /**
     * Show vendor applications
     * @param bool $useApi Set true to consume API via HTTP, false for internal service
     */
    public function showApplications(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Please complete your vendor application first.');
        }

        if (!$vendor->isApproved()) {
            return redirect()->route('vendor.application.status', $vendor->id)
                ->with('error', 'Your vendor registration is pending approval. You cannot view applications yet.');
        }

        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = $request->query('use_api', false);

            if ($useApi) {
                // External API consumption (simulate another module)
                $response = Http::timeout(10)
                    ->get(url('/api/v1/vendor/applications'));

                if ($response->failed()) {
                    throw new \Exception('Failed to fetch applications from API');
                }

                $payload = $response->json();
                $applicationArray = $payload['data']['applications'] ?? [];

                // Convert API data to VendorEventApplication model format
                $applications = collect($applicationArray)->map(function($appData) {
                    $app = new VendorEventApplication($appData);
                    $app->exists = true;
                    $app->id = $appData['application_id'];
                    
                    // Create event relation
                    if (isset($appData['event'])) {
                        $event = new Event($appData['event']);
                        $event->exists = true;
                        $event->id = $appData['event']['id'];
                        $app->setRelation('event', $event);
                    }
                    
                    return $app;
                });

                // Create paginator
                $applications = new \Illuminate\Pagination\LengthAwarePaginator(
                    items: $applications,
                    total: $applications->count(),
                    perPage: 10,
                    currentPage: 1,
                    options: ['path' => request()->url(), 'query' => request()->query()]
                );
            } else {
                // Internal service consumption
                $result = $this->vendorService->getVendorApplications($vendor->id);
                
                if (!$result['success']) {
                    throw new \Exception($result['error']);
                }

                $applications = $result['data'];

                // Create paginator
                $applications = new \Illuminate\Pagination\LengthAwarePaginator(
                    items: $applications,
                    total: $applications->count(),
                    perPage: 10,
                    currentPage: 1,
                    options: ['path' => request()->url(), 'query' => request()->query()]
                );
            }
        } catch (\Exception $e) {
            Log::warning('Vendor applications service failed, using fallback', [
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage(),
                'use_api' => $useApi ?? false
            ]);
            
            // Fallback on exceptions
            $applications = VendorEventApplication::with(['event', 'event.venue'])
                                                ->where('vendor_id', $vendor->id)
                                                ->where('status', '!=', 'paid')
                                                ->orderBy('created_at', 'desc')
                                                ->paginate(10);
        }
        
        return view('vendor.applications', compact('applications', 'vendor'));
    }

    /**
     * Show specific application
     */
    public function showApplication($id)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Please complete your vendor application first.');
        }

        if (!$vendor->isApproved()) {
            return redirect()->route('vendor.application.status', $vendor->id)
                ->with('error', 'Your vendor registration is pending approval.');
        }

        $application = VendorEventApplication::with(['event', 'event.venue', 'reviewer'])
                                           ->where('vendor_id', $vendor->id)
                                           ->findOrFail($id);
        
        return view('vendor.application-details', compact('application', 'vendor'));
    }

    /**
     * Cancel application
     */
    public function cancelApplication($id)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return response()->json(['success' => false, 'message' => 'Vendor not found'], 404);
        }

        if (!$vendor->isApproved()) {
            return response()->json(['success' => false, 'message' => 'Vendor not approved'], 403);
        }

        try {
            $application = VendorEventApplication::where('vendor_id', $vendor->id)
                                               ->where('id', $id)
                                               ->first();

            if (!$application) {
                return response()->json(['success' => false, 'message' => 'Application not found'], 404);
            }

            if (!$application->canBeCancelled()) {
                return response()->json(['success' => false, 'message' => 'Application cannot be cancelled'], 400);
            }

            // Check if this was a paid application before cancelling
            $wasPaid = $application->status === 'paid';
            $boothQuantity = $application->booth_quantity ?? 1;

            $application->update([
                'status' => 'cancelled',
                'rejection_reason' => 'Cancelled by vendor'
            ]);

            // If this was a paid application, reduce booth_sold count
            if ($wasPaid) {
                $event = $application->event;
                $event->decrement('booth_sold', $boothQuantity);
                $event->updateFinancials();
            }

            return response()->json(['success' => true, 'message' => 'Application cancelled successfully']);

        } catch (\Exception $e) {
            Log::error('Failed to cancel application', [
                'application_id' => $id,
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to cancel application'], 500);
        }
    }

    /**
     * Show vendor bookings
     * @param bool $useApi Set true to consume API via HTTP, false for internal service
     */
    public function showBookings(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Please complete your vendor application first.');
        }

        if (!$vendor->isApproved()) {
            return redirect()->route('vendor.application.status', $vendor->id)
                ->with('error', 'Your vendor registration is pending approval. You cannot view bookings yet.');
        }

        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = $request->query('use_api', false);

            if ($useApi) {
                // External API consumption (simulate another module)
                $response = Http::timeout(10)
                    ->get(url('/api/v1/vendor/bookings'));

                if ($response->failed()) {
                    throw new \Exception('Failed to fetch bookings from API');
                }

                $payload = $response->json();
                $bookingArray = $payload['data']['bookings'] ?? [];

                // Convert API data to VendorEventApplication model format
                $bookings = collect($bookingArray)->map(function($bookingData) {
                    $booking = new VendorEventApplication($bookingData);
                    $booking->exists = true;
                    $booking->id = $bookingData['booking_id'];
                    
                    // Create event relation
                    if (isset($bookingData['event'])) {
                        $event = new Event($bookingData['event']);
                        $event->exists = true;
                        $event->id = $bookingData['event']['id'];
                        $booking->setRelation('event', $event);
                    }
                    
                    return $booking;
                });
            } else {
                // Internal service consumption
                $result = $this->vendorService->getVendorBookings($vendor->id);
                
                if (!$result['success']) {
                    throw new \Exception($result['error']);
                }

                $bookings = $result['data'];
            }
        } catch (\Exception $e) {
            Log::warning('Vendor bookings service failed, using fallback', [
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage(),
                'use_api' => $useApi ?? false
            ]);
            
            // Fallback on exceptions
            $bookings = VendorEventApplication::with(['event', 'event.venue'])
                ->where('vendor_id', $vendor->id)
                ->where('status', 'paid')
                ->latest()
                ->get();
        }

        return view('vendor.bookings', compact('bookings', 'vendor'));
    }

    /**
     * Show specific booking
     */
    public function showBooking($id)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Please complete your vendor application first.');
        }

        if (!$vendor->isApproved()) {
            return redirect()->route('vendor.application.status', $vendor->id)
                ->with('error', 'Your vendor registration is pending approval.');
        }

        // For now, return 404 until booth booking system is implemented
        abort(404, 'Booking not found');
    }

    /**
     * Cancel booking
     */
    public function cancelBooking($id)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return response()->json(['success' => false], 404);
        }

        // For now, return error until booth booking system is implemented
        return response()->json(['success' => false, 'message' => 'Feature not yet implemented'], 400);
    }

    /**
     * Show event application form
     */
    public function showEventApplicationForm($id)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Please complete your vendor application first.');
        }

        if (!$vendor->isApproved()) {
            return redirect()->route('vendor.application.status', $vendor->id)
                ->with('error', 'Your vendor registration is pending approval. You cannot apply yet.');
        }

        $event = Event::with('venue')->findOrFail($id);
        
        // Check if event is accepting applications
        if (!$event->isAcceptingApplications()) {
            return redirect()->route('vendor.events')
                            ->with('error', 'This event is not currently accepting applications.');
        }
        
        // Check if event has available booths
        if (!$event->hasAvailableSlots()) {
            return redirect()->route('vendor.events')
                            ->with('error', 'This event is fully booked. No booths are available.');
        }
        
        // Allow multiple applications for the same event
        // Removed duplicate application check to allow vendors to apply multiple times
        
        return view('vendor.event-application-form', compact('event', 'vendor'));
    }

    /**
     * Submit event application
     */
    public function submitEventApplication(Request $request, $id)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Please complete your vendor application first.');
        }

        if (!$vendor->isApproved()) {
            return redirect()->route('vendor.application.status', $vendor->id)
                ->with('error', 'Your vendor registration is pending approval. You cannot apply yet.');
        }

        $event = Event::findOrFail($id);
        
        // Check if event is accepting applications
        if (!$event->isAcceptingApplications()) {
            return redirect()->route('vendor.events')
                            ->with('error', 'This event is not currently accepting applications.');
        }
        
        // Check if event has available booths
        if (!$event->hasAvailableSlots()) {
            return redirect()->route('vendor.events')
                            ->with('error', 'This event is fully booked. No booths are available.');
        }
        
        // Validate the application data
        $validated = $request->validate([
            'booth_size' => 'required|string|in:10x10,20x20,30x30',
            'booth_quantity' => 'required|integer|min:1|max:10',
            'service_type' => 'required|string|in:food,equipment,decoration,entertainment,logistics,other',
            'requested_price' => 'required|numeric|min:0',
            'service_description' => 'required|string|max:1000',
        ]);

        // Check if requested booth quantity is available
        $availableBooths = $event->available_booths;
        if ($validated['booth_quantity'] > $availableBooths) {
            return redirect()->back()
                            ->with('error', "Only {$availableBooths} booths are available. You requested {$validated['booth_quantity']} booths.")
                            ->withInput();
        }

        // Allow multiple applications for the same event
        // Removed duplicate application check to allow vendors to apply multiple times
        
        // Create the application
        $application = VendorEventApplication::create([
            'vendor_id' => $vendor->id,
            'event_id' => $event->id,
            'booth_size' => $validated['booth_size'],
            'booth_quantity' => $validated['booth_quantity'],
            'service_type' => $validated['service_type'],
            'service_description' => $validated['service_description'],
            'requested_price' => $validated['requested_price'],
            'status' => 'pending',
        ]);
        
        return redirect()->route('vendor.events')
                        ->with('success', 'Your event application has been submitted.');
    }

    /**
     * Show payment form for approved application
     */
    public function showPayment($id)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Please complete your vendor application first.');
        }

        if (!$vendor->isApproved()) {
            return redirect()->route('vendor.application.status', $vendor->id)
                ->with('error', 'Your vendor registration is pending approval. You cannot pay yet.');
        }

        $application = VendorEventApplication::with(['event', 'event.venue'])
                                           ->where('vendor_id', $vendor->id)
                                           ->where('status', 'approved')
                                           ->findOrFail($id);

        // Build payable amount using application's requested price or calculate from booth size and quantity
        $baseAmount = (float) ($application->requested_price ?? 0);
        
        // If no requested price, calculate from event's booth price, booth size, and quantity
        if ($baseAmount <= 0) {
            $eventBoothPrice = (float) ($application->event->booth_price ?? 0);
            $boothSize = (float) ($application->booth_size ?? 1);
            $boothQuantity = (int) ($application->booth_quantity ?? 1);
            
            // Calculate base amount: event booth price * booth size multiplier * quantity
            $baseAmount = $eventBoothPrice * $boothSize * $boothQuantity;
        }
        
        $payment = (new PaymentBuilder($baseAmount))
            // Example policy: 6% tax, RM 10 service charge, no discount by default
            ->withTax(0.06)
            ->withServiceCharge(10.00)
            ->build();

        $paymentTotal = round($payment->getAmount(), 2);
        $paymentBreakdown = $payment->getBreakdown();

        return view('vendor.payment', compact('application', 'vendor', 'paymentTotal', 'paymentBreakdown'))->with('finalAmount', $paymentTotal);
    }

    /**
     * Process payment for approved application
     */
    public function processPayment(Request $request, $id)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Please complete your vendor application first.');
        }

        if (!$vendor->isApproved()) {
            return redirect()->route('vendor.application.status', $vendor->id)
                ->with('error', 'Your vendor registration is pending approval. You cannot pay yet.');
        }

        $application = VendorEventApplication::where('vendor_id', $vendor->id)
                                           ->where('status', 'approved')
                                           ->findOrFail($id);

        // Validate payment inputs
        $validated = $request->validate([
            'payment_method' => 'required|in:debit_payment,credit_payment',
            'terms_accepted' => 'accepted',
            // Debit payment fields
            'bank_name' => 'required_if:payment_method,debit_payment|nullable|string|max:255',
            'account_number' => 'required_if:payment_method,debit_payment|nullable|string|max:50',
            'account_holder_name' => 'required_if:payment_method,debit_payment|nullable|string|max:255',
            // Credit payment fields
            'credit_card_number' => 'required_if:payment_method,credit_payment|nullable|regex:/^\d{4}\s?\d{4}\s?\d{4}\s?\d{1,4}$/',
            'credit_expiry_date' => 'required_if:payment_method,credit_payment|nullable|date_format:m/y',
            'credit_cvv' => 'required_if:payment_method,credit_payment|nullable|digits_between:3,4',
            'credit_cardholder_name' => 'required_if:payment_method,credit_payment|nullable|string|max:255',
        ]);

        // Compute payment using application's requested price or calculate from booth size and quantity
        $baseAmount = (float) ($application->requested_price ?? 0);
        
        // If no requested price, calculate from event's booth price, booth size, and quantity
        if ($baseAmount <= 0) {
            $eventBoothPrice = (float) ($application->event->booth_price ?? 0);
            $boothSize = (float) ($application->booth_size ?? 1);
            $boothQuantity = (int) ($application->booth_quantity ?? 1);
            
            // Calculate base amount: event booth price * booth size multiplier * quantity
            $baseAmount = $eventBoothPrice * $boothSize * $boothQuantity;
        }
        
        $payment = (new PaymentBuilder($baseAmount))
            ->withTax(0.06)
            ->withServiceCharge(10.00)
            ->build();
        $finalAmount = round($payment->getAmount(), 2);

        // Store payment breakdown
        $paymentBreakdown = $payment->getBreakdown();
        
        // For now, simulate payment success
        $application->update([
            'status' => 'paid',
            'paid_at' => now(),
            'approved_price' => $finalAmount, // Store final amount in approved_price
        ]);

        // Update event's booth sales via internal service (single source of truth), fallback to direct update on failure
        $event = $application->event;
        $boothQuantity = $application->booth_quantity ?? 1;
        try {
            $result = $this->vendorService->updateBoothQuantity($event->id, 'subtract', $boothQuantity);
            
            if (!$result['success']) {
                // Fallback: direct DB update to avoid losing state
                $event->increment('booth_sold', $boothQuantity);
                $event->decrement('booth_quantity', $boothQuantity);
                $event->updateFinancials();
            }
        } catch (\Throwable $e) {
            // Fallback on exceptions as well
            $event->increment('booth_sold', $boothQuantity);
            $event->decrement('booth_quantity', $boothQuantity);
            $event->updateFinancials();
        }

        return redirect()->route('vendor.bookings')
                        ->with('success', 'Payment processed successfully! Amount paid: RM ' . number_format($finalAmount, 2) . '. Your booth is now confirmed.');
    }

    /**
     * Delete application
     */
    public function deleteApplication($id)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor profile not found'
            ], 404);
        }

        try {
            $application = VendorEventApplication::where('vendor_id', $vendor->id)
                ->findOrFail($id);

            // Check if application is paid - if so, we need to update booth quantities
            if ($application->status === 'paid') {
                $event = $application->event;
                $boothQuantity = $application->booth_quantity ?? 1;
                
                // Add back the booths to available quantity
                $event->increment('booth_quantity', $boothQuantity);
                $event->decrement('booth_sold', $boothQuantity);
                $event->updateFinancials();
            }

            // Delete the application
            $application->delete();

            return response()->json([
                'success' => true,
                'message' => 'Application deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete application', [
                'application_id' => $id,
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete application: ' . $e->getMessage()
            ], 500);
        }
    }

}