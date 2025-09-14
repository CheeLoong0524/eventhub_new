<?php
/** Author: Tan Chim Yang 
 * RSW2S3G4
 * 23WMR14610 
 * **/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the main dashboard based on user role
     */
    public function index()
    {
        $user = Auth::user();

        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('auth.firebase')
                ->with('error', 'Your account has been deactivated. Please contact the administrator for assistance.');
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isVendor()) {
            return redirect()->route('vendor.dashboard');
        } else {
            return redirect()->route('customer.dashboard');
        }
    }
} 