<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FirebaseAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\EventBookingPaymentControllerYf;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\PaymentGatewayController;
use Illuminate\Support\Facades\Auth;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Firebase Authentication routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/firebase', function () {
        return view('auth.firebase-auth');
    })->middleware('guest')->name('firebase');
    Route::post('/firebase/callback', [FirebaseAuthController::class, 'callback'])->name('firebase.callback');
    Route::post('/logout', [FirebaseAuthController::class, 'logout'])->name('logout');
    Route::get('/user', [FirebaseAuthController::class, 'user'])->name('user');
    
    // Check if user exists by email (no auth required)
    Route::post('/check-user', [FirebaseAuthController::class, 'checkUserExists'])->name('check-user');
});

// Admin Authentication routes (public)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// Test search route (temporary)
Route::get('/test-search-route', function() {
    return response()->json(['message' => 'Test search route works!']);
});


// Test events route (temporary)
Route::get('/test-events', function() {
    $events = \App\Models\Event::with(['ticketTypes', 'creator'])
        ->where('status', 'published')
        ->upcoming()
        ->orderBy('date', 'asc')
        ->orderBy('time', 'asc')
        ->paginate(12);
    
    return response()->json([
        'total' => $events->total(),
        'count' => $events->count(),
        'events' => $events->items()
    ]);
});

// Test payment route (temporary)
Route::get('/test-event-booking-payment', function() {
    $user = \App\Models\User::first();
    if (!$user) {
        return response()->json(['error' => 'No user found']);
    }
    
    $cart = \App\Models\Cart::where('user_id', $user->id)->first();
    if (!$cart) {
        return response()->json(['error' => 'No cart found for user']);
    }
    
    $cartItems = $cart->items()->with(['ticketType.event'])->get();
    return response()->json([
        'user_id' => $user->id,
        'cart_id' => $cart->id,
        'cart_items_count' => $cartItems->count(),
        'cart_items' => $cartItems->toArray()
    ]);
});

// Public event routes
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/{event}', [EventController::class, 'show'])->name('show');
    
    // API routes for AJAX
    Route::get('/api/list', [EventController::class, 'apiIndex'])->name('api.index');
    Route::get('/api/{event}', [EventController::class, 'apiShow'])->name('api.show');
    Route::get('/api/featured', [EventController::class, 'featured'])->name('api.featured');
    Route::get('/api/upcoming', [EventController::class, 'upcoming'])->name('api.upcoming');
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
        
        // Debug route for testing
        Route::get('/debug/users', function() {
            $users = \App\Models\User::paginate(5);
            return response()->json($users);
        })->name('debug.users');
        
        // Test search route
        Route::get('/test-search', function() {
            return 'Search route is working!';
        })->name('test.search');
    });
    
    // Vendor routes
    Route::middleware('role:vendor')->prefix('vendor')->name('vendor.')->group(function () {
        Route::get('/dashboard', function () {
            $user = Auth::user();
            return view('dashboard.vendor', compact('user'));
        })->name('dashboard');
    });
    
    // Customer routes
    Route::middleware('role:customer')->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', function () {
            $user = Auth::user();
            
            // Get user's booking history
            $recentBookings = \App\Models\EventOrderYf::where('user_id', $user->id)
                ->with(['event', 'payment'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            
            // Get total bookings count
            $totalBookings = \App\Models\EventOrderYf::where('user_id', $user->id)->count();
            
            // Get upcoming events count
            $upcomingBookings = \App\Models\EventOrderYf::where('user_id', $user->id)
                ->whereHas('event', function($query) {
                    $query->where('date', '>=', now()->toDateString());
                })
                ->count();
            
            // Get attended events count (events that have passed)
            $attendedEvents = \App\Models\EventOrderYf::where('user_id', $user->id)
                ->whereHas('event', function($query) {
                    $query->where('date', '<', now()->toDateString());
                })
                ->count();
            
            // Get total amount spent
            $totalSpent = \App\Models\EventOrderYf::where('user_id', $user->id)
                ->where('status', 'paid')
                ->sum('total_amount');
            
            return view('dashboard.customer', compact('user', 'recentBookings', 'totalBookings', 'upcomingBookings', 'attendedEvents', 'totalSpent'));
        })->name('dashboard');
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
        
        // Download PDF receipt
        Route::get('/receipt/{orderId}/download', [ReceiptController::class, 'downloadReceipt'])->name('receipt.download');
        
        // Get receipt as HTML
        Route::get('/receipt/{orderId}/html', [ReceiptController::class, 'getReceiptHtml'])->name('receipt.html');
    });
});
