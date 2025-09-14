<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class VendorManagementController extends Controller
{
    /**
     * Display vendor applications
     */
    public function applications(Request $request)
    {
        $query = Vendor::with('user')
            ->whereIn('status', ['pending', 'rejected']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('business_type')) {
            $query->where('business_type', $request->business_type);
        }


        $applications = $query->latest()->paginate(15);

        return view('admin.vendor.applications', compact('applications'));
    }

    /**
     * Show application details
     */
    public function showApplication($id)
    {
        $application = Vendor::with('user')->findOrFail($id);
        
        return view('admin.vendor.application-details', compact('application'));
    }


    /**
     * Approve application
     */
    public function approveApplication(Request $request, $id)
    {
        $application = Vendor::findOrFail($id);
        
        if ($application->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending applications can be approved.');
        }

        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            // Update user role to vendor if not already
            $user = $application->user;
            if ($user && $user->role !== 'vendor') {
                $user->update(['role' => 'vendor']);
            }

            // Update vendor application status
            $application->update([
                'status' => 'approved',
                'admin_notes' => $request->admin_notes,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'approved_at' => now(),
                'approved_by' => Auth::id()
            ]);

            // Vendor notifications removed

            return redirect()->route('admin.vendor.applications')
                ->with('success', 'Application approved successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to approve application', [
                'application_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to approve application: ' . $e->getMessage());
        }
    }

    /**
     * Reject application
     */
    public function rejectApplication(Request $request, $id)
    {
        $application = Vendor::findOrFail($id);
        
        if ($application->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending applications can be rejected.');
        }

        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            $application->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now()
            ]);

            // Vendor notifications removed

            return redirect()->route('admin.vendor.applications')
                ->with('success', 'Application rejected successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to reject application', [
                'application_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to reject application.');
        }
    }

    /**
     * Display approved vendors
     */
    public function vendors(Request $request)
    {
        $query = Vendor::with('user')
            ->where('status', 'approved');

        if ($request->filled('business_type')) {
            $query->where('business_type', $request->business_type);
        }

        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->is_verified === '1');
        }


        $vendors = $query->latest()->paginate(15);

        return view('admin.vendor.vendors', compact('vendors'));
    }

    /**
     * Show vendor details
     */
    public function showVendor($id)
    {
        $vendor = Vendor::with(['user', 'eventApplications.event'])->findOrFail($id);
        
        return view('admin.vendor.vendor-details', compact('vendor'));
    }



    /**
     * Send notification to vendor
     */
    // sendVendorNotification removed
}