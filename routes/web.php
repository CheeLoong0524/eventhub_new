<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FirebaseAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\Admin\VendorManagementController;
use App\Http\Controllers\Admin\EventApplicationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Customer\InquiryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\EventBookingPaymentControllerYf;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\CustomerEventController;
use App\Http\Controllers\CustomerDashboardController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Customer Support routes
Route::prefix('support')->name('support.')->group(function () {
    Route::get('/', [App\Http\Controllers\SupportController::class, 'index'])->name('index');
    Route::post('/contact', [App\Http\Controllers\SupportController::class, 'contact'])->name('contact');
    Route::get('/faq', [App\Http\Controllers\SupportController::class, 'faq'])->name('faq');
    
    // API Consumer routes - Customer consumes AdminInquiryApiController API
    Route::get('/check', [InquiryController::class, 'checkInquiry'])->name('check');
    Route::get('/inquiry/{inquiryId}', [InquiryController::class, 'viewInquiry'])->name('inquiry.show');
    Route::get('/stats', [InquiryController::class, 'getStats'])->name('stats');
});

// Firebase Authentication routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/firebase', function () {
        return view('auth.firebase-auth');
    })->middleware('guest')->name('firebase');
    Route::post('/firebase/callback', [FirebaseAuthController::class, 'callback'])->name('firebase.callback');
    Route::post('/logout', [FirebaseAuthController::class, 'logout'])->name('logout');
    Route::get('/user', [FirebaseAuthController::class, 'user'])->name('user');
    
});

// Main login route (redirects to appropriate login based on user type)
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Main logout route is handled by FirebaseAuthController

// Admin Authentication routes (public)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});


// Test basic route
Route::get('/test-basic', function() {
    return 'Basic route works!';
});

// Test customer route without middleware
Route::get('/test-customer-events', function() {
    $events = \App\Models\Event::where('status', 'active')->get();
    return response()->json(['events' => $events->count()]);
});

// Test customer event details without middleware
Route::get('/test-customer-event-details/{id}', function($id) {
    $event = \App\Models\Event::find($id);
    if (!$event) {
        return response()->json(['error' => 'Event not found'], 404);
    }
    return response()->json(['event' => $event->name, 'status' => 'success']);
});

// Protected routes
Route::middleware('auth')->group(function () {
    // Main dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
    });
    
    // Admin routes
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            $user = Auth::user();
            $totalUsers = \App\Models\User::count();
            $totalCustomers = \App\Models\User::where('role', 'customer')->count();
            $totalVendors = \App\Models\User::where('role', 'vendor')->count();
            $recentUsers = \App\Models\User::latest()->take(5)->get();
            return view('dashboard.admin', compact('user', 'totalUsers', 'totalCustomers', 'totalVendors', 'recentUsers'));
        })->name('dashboard');
        
        // User management - specific routes first to avoid conflicts
        Route::get('/users-search', [UserController::class, 'search'])->name('users.search');
        Route::post('/toggle-user-status/{user}', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::resource('users', UserController::class);
        
        // Vendor management routes
        Route::prefix('vendor')->name('vendor.')->group(function () {
            Route::get('/applications', [VendorManagementController::class, 'applications'])->name('applications');
            Route::get('/applications/{id}', [VendorManagementController::class, 'showApplication'])->name('applications.show');
            Route::post('/applications/{id}/review', [VendorManagementController::class, 'reviewApplication'])->name('applications.review');
            Route::post('/applications/{id}/approve', [VendorManagementController::class, 'approveApplication'])->name('applications.approve');
            Route::post('/applications/{id}/reject', [VendorManagementController::class, 'rejectApplication'])->name('applications.reject');
            
            Route::get('/vendors', [VendorManagementController::class, 'vendors'])->name('vendors');
            Route::get('/vendors/{id}', [VendorManagementController::class, 'showVendor'])->name('vendors.show');
            
        });

        // Event application management routes
        Route::get('/event-applications', [EventApplicationController::class, 'index'])->name('event-applications.index');
        Route::get('/event-applications/{id}', [EventApplicationController::class, 'show'])->name('event-applications.show');
        Route::post('/event-applications/{id}/approve', [EventApplicationController::class, 'approve'])->name('event-applications.approve');
        Route::post('/event-applications/{id}/reject', [EventApplicationController::class, 'reject'])->name('event-applications.reject');
        Route::get('/event-applications/stats', [EventApplicationController::class, 'getStats'])->name('event-applications.stats');
        
        // Financial reports routes
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/event/{id}', [ReportController::class, 'event'])->name('reports.event');

        // Admin Support routes
        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\SupportController::class, 'index'])->name('index');
            Route::get('/inquiries', [App\Http\Controllers\Admin\SupportController::class, 'inquiries'])->name('inquiries');
            Route::get('/inquiry/{inquiryId}', [App\Http\Controllers\Admin\SupportController::class, 'showInquiry'])->name('inquiry.show');
            Route::post('/inquiry/{inquiryId}/update', [App\Http\Controllers\Admin\SupportController::class, 'updateInquiry'])->name('inquiry.update');
            Route::get('/faqs', [App\Http\Controllers\Admin\SupportController::class, 'faqs'])->name('faqs');
            Route::post('/faqs', [App\Http\Controllers\Admin\SupportController::class, 'createFaq'])->name('faqs.create');
            Route::put('/faqs/{id}/update', [App\Http\Controllers\Admin\SupportController::class, 'updateFaq'])->name('faqs.update');
            Route::delete('/faqs/{id}', [App\Http\Controllers\Admin\SupportController::class, 'deleteFaq'])->name('faqs.delete');
        });
        
        // Debug route for testing
        Route::get('/debug/users', function() {
            $users = \App\Models\User::paginate(5);
            return response()->json($users);
        })->name('debug.users');
        
    });
    
    // Vendor routes
    Route::middleware('role:vendor')->prefix('vendor')->name('vendor.')->group(function () {
        Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('dashboard');
        
        // Application routes
        Route::get('/apply', [VendorController::class, 'showApplicationForm'])->name('apply');
        Route::post('/apply', [VendorController::class, 'submitApplication'])->name('apply.submit');
        Route::get('/application/{id}/status', [VendorController::class, 'showApplicationStatus'])->name('application.status');
        
        // Profile routes
        Route::get('/profile', [VendorController::class, 'showProfile'])->name('profile');
        Route::put('/profile', [VendorController::class, 'updateProfile'])->name('profile.update');
        
        // Event and application routes
        Route::get('/events', [VendorController::class, 'showAvailableEvents'])->name('events');
        Route::get('/events/{id}', [VendorController::class, 'showEvent'])->name('events.show');
        Route::get('/events/{id}/apply', [VendorController::class, 'showEventApplicationForm'])->name('events.apply');
        Route::post('/events/{id}/apply', [VendorController::class, 'submitEventApplication'])->name('events.apply.submit');
        
        // Application management routes
        Route::get('/applications', [VendorController::class, 'showApplications'])->name('applications');
        Route::get('/applications/{id}', [VendorController::class, 'showApplication'])->name('applications.show');
        Route::delete('/applications/{id}/cancel', [VendorController::class, 'cancelApplication'])->name('applications.cancel');
        
        // Payment routes
        Route::get('/applications/{id}/payment', [VendorController::class, 'showPayment'])->name('payment');
        Route::post('/applications/{id}/payment', [VendorController::class, 'processPayment'])->name('payment.process');
        
        // Booking management
        Route::get('/bookings', [VendorController::class, 'showBookings'])->name('bookings');
        Route::get('/bookings/{id}', [VendorController::class, 'showBooking'])->name('bookings.show');
        Route::post('/bookings/{id}/cancel', [VendorController::class, 'cancelBooking'])->name('bookings.cancel');
        
        // Notification routes removed
        
        // Analytics routes removed
    });
    
    // Customer routes
    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        
        // Event viewing (customer-specific)
        Route::get('/events', [CustomerEventController::class, 'index'])->name('events.index');
        Route::get('/events/{id}', [CustomerEventController::class, 'show'])->name('events.show');
        
        // API routes for AJAX
        Route::get('/events/api/list', [CustomerEventController::class, 'apiIndex'])->name('events.api.index');
        Route::get('/events/api/{event}', [CustomerEventController::class, 'apiShow'])->name('events.api.show');
        Route::get('/events/api/featured', [CustomerEventController::class, 'featured'])->name('events.api.featured');
        Route::get('/events/api/upcoming', [CustomerEventController::class, 'upcoming'])->name('events.api.upcoming');
    });
    
    // Cart routes (authenticated users only)
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'addItem'])->name('add');
        Route::put('/items/{cartItem}', [CartController::class, 'updateItem'])->name('update');
        Route::delete('/items/{cartItem}', [CartController::class, 'removeItem'])->name('remove');
        Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
        Route::get('/summary', [CartController::class, 'summary'])->name('summary');
    });
    
    // Event Booking Payment routes (authenticated users only)
    Route::prefix('event-booking')->name('event-booking.')->group(function () {
        Route::get('/payment-form-yf', [EventBookingPaymentControllerYf::class, 'show'])->name('payment-form-yf');
        Route::post('/payment-process-yf', [EventBookingPaymentControllerYf::class, 'process'])->name('payment-process-yf');
        Route::get('/payment-success-yf', [EventBookingPaymentControllerYf::class, 'success'])->name('payment-success-yf');
        Route::get('/bank-transfer-instructions-yf', [EventBookingPaymentControllerYf::class, 'bankTransfer'])->name('bank-transfer-instructions-yf');
        Route::get('/payment-receipt-yf', [EventBookingPaymentControllerYf::class, 'receipt'])->name('payment-receipt-yf');
    });

    // Payment Gateway routes (authenticated users only)
    Route::prefix('payment-gateway')->name('payment-gateway.')->group(function () {
        Route::get('/gateway', [PaymentGatewayController::class, 'showGateway'])->name('show');
        Route::post('/process', [PaymentGatewayController::class, 'processGateway'])->name('process');
    });
});

// API Routes for Receipt Service
Route::prefix('api')->name('api.')->group(function () {
    // Receipt API routes (authenticated users only)
    Route::middleware('auth')->group(function () {
        // Generate receipt after payment
        Route::post('/receipt', [ReceiptController::class, 'generateReceipt'])->name('receipt.generate');
        
        // Get user's receipts
        Route::get('/receipts', [ReceiptController::class, 'getUserReceipts'])->name('receipts.user');
        
        // Get receipt statistics (Admin only)
        Route::get('/receipts/stats', [ReceiptController::class, 'getReceiptStats'])->name('receipts.stats');
        
        // Get specific receipt
        Route::get('/receipt/{orderId}', [ReceiptController::class, 'getReceipt'])->name('receipt.get');
        
        // Get receipt data (replaces PDF download)
        Route::get('/receipt/{orderId}/data', [ReceiptController::class, 'getReceiptData'])->name('receipt.data');
        
        // Get receipt as HTML
        Route::get('/receipt/{orderId}/html', [ReceiptController::class, 'getReceiptHtml'])->name('receipt.html');
    });
});

Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
Route::resource('events', EventController::class);
Route::patch('events/{event}/status', [EventController::class, 'updateStatus'])->name('events.status.update');

// Notification routes
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::get('/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
});