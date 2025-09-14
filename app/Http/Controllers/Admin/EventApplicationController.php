<?php

// Author  : Choong Yoong Sheng (Vendor module)


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorEventApplication;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EventApplicationController extends Controller
{

    public function index(Request $request)
    {
        $query = VendorEventApplication::with(['vendor', 'event', 'reviewer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('vendor', function($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%");
            })->orWhereHas('event', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $applications = $query->latest()->paginate(15);
        $events = Event::where('status', 'active')->get();

        return view('admin.event-applications.index', compact('applications', 'events'));
    }

    public function show($id)
    {
        $application = VendorEventApplication::with(['vendor', 'event', 'reviewer'])->findOrFail($id);
        
        return view('admin.event-applications.show', compact('application'));
    }

    public function approve(Request $request, $id)
    {
        try {
            $application = VendorEventApplication::findOrFail($id);
            
            if (!$application->canBeApproved()) {
                return redirect()->back()->with('error', 'This application cannot be approved.');
            }

            // Update the application
            $updateData = [
                'status' => 'approved',
                'approved_price' => $application->requested_price,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'approved_at' => now()
            ];
            
            Log::info('Updating application', [
                'application_id' => $id,
                'update_data' => $updateData
            ]);
            
            $application->update($updateData);

            Log::info('Event application approved', [
                'application_id' => $id,
                'admin_id' => Auth::id(),
                'vendor_id' => $application->vendor_id,
                'event_id' => $application->event_id
            ]);

            return redirect()->back()->with('success', 'Application approved successfully.');

        } catch (\Exception $e) {
            Log::error('Application approval failed', [
                'application_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to approve application. Error: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:1000',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $application = VendorEventApplication::findOrFail($id);
            
            if (!$application->canBeRejected()) {
                return redirect()->back()->with('error', 'This application cannot be rejected.');
            }

            $application->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'admin_notes' => $request->admin_notes,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'rejected_at' => now()
            ]);


            Log::info('Event application rejected', [
                'application_id' => $id,
                'admin_id' => Auth::id(),
                'vendor_id' => $application->vendor_id,
                'event_id' => $application->event_id,
                'reason' => $request->rejection_reason
            ]);

            return redirect()->back()->with('success', 'Application rejected successfully.');

        } catch (\Exception $e) {
            Log::error('Application rejection failed', [
                'application_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to reject application. Please try again.');
        }
    }


    public function getStats()
    {
        $stats = [
            'total' => VendorEventApplication::count(),
            'pending' => VendorEventApplication::pending()->count(),
            'approved' => VendorEventApplication::approved()->count(),
            'rejected' => VendorEventApplication::rejected()->count(),
        ];

        return response()->json($stats);
    }
}


