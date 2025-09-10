<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Event;
use App\Models\VendorEventApplication;
use App\Payment\PaymentBuilder;
use App\Services\ExternalApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
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

        // Example of consuming external web service
        $externalApiService = new ExternalApiService();
        $userInfo = $externalApiService->getUserInfo(Auth::id(), 1);
        
        // Log the external service consumption for demonstration
        if ($userInfo['success']) {
            Log::info('External API consumption successful', [
                'service' => 'getUserInfo',
                'user_id' => Auth::id(),
                'data' => $userInfo['data']
            ]);
        }

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

            // First-time submission
            $vendor = Vendor::create([
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
     */
    public function showAvailableEvents()
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Please complete your vendor application first.');
        }

        if (!$vendor->isApproved()) {
            return redirect()->route('vendor.application.status', $vendor->id)
                ->with('error', 'Your vendor registration is pending approval. You cannot browse/apply to events yet.');
        }

        $events = Event::where('status', 'active')
                      ->where('start_time', '>=', now())
                      ->orderBy('start_time')
                      ->paginate(12);

        return view('vendor.available-events', compact('events', 'vendor'));
    }

    /**
     * Show event details (simplified)
     */
    public function showEvent($id)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Please complete your vendor application first.');
        }

        if (!$vendor->isApproved()) {
            return redirect()->route('vendor.application.status', $vendor->id)
                ->with('error', 'Your vendor registration is pending approval. You cannot view event application details yet.');
        }

        $event = Event::findOrFail($id);
        
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
     */
    public function showApplications()
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Please complete your vendor application first.');
        }

        if (!$vendor->isApproved()) {
            return redirect()->route('vendor.application.status', $vendor->id)
                ->with('error', 'Your vendor registration is pending approval. You cannot view applications yet.');
        }

        $applications = VendorEventApplication::with(['event', 'event.venue'])
                                            ->where('vendor_id', $vendor->id)
                                            ->where('status', '!=', 'paid')
                                            ->orderBy('created_at', 'desc')
                                            ->paginate(10);
        
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
     */
    public function showBookings()
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.apply')->with('error', 'Please complete your vendor application first.');
        }

        if (!$vendor->isApproved()) {
            return redirect()->route('vendor.application.status', $vendor->id)
                ->with('error', 'Your vendor registration is pending approval. You cannot view bookings yet.');
        }

        // Show paid applications as confirmed bookings
        $bookings = VendorEventApplication::with(['event', 'event.venue'])
            ->where('vendor_id', $vendor->id)
            ->where('status', 'paid')
            ->latest()
            ->get();

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

        // Build payable amount using event's booth price
        $baseAmount = (float) ($application->event->booth_price ?? 0);
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

        // Compute payment using event's booth price (same policy as in showPayment)
        $baseAmount = (float) ($application->event->booth_price ?? 0);
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
            'base_amount' => $baseAmount,
            'tax_amount' => $paymentBreakdown['tax'] ?? 0,
            'service_charge_amount' => $paymentBreakdown['service_charge'] ?? 0,
            'final_amount' => $finalAmount,
        ]);

        // Update event's booth sales and revenue
        $event = $application->event;
        $boothQuantity = $application->booth_quantity ?? 1;
        $event->increment('booth_sold', $boothQuantity);
        $event->updateFinancials();

        return redirect()->route('vendor.bookings')
                        ->with('success', 'Payment processed successfully! Amount paid: RM ' . number_format($finalAmount, 2) . '. Your booth is now confirmed.');
    }

}