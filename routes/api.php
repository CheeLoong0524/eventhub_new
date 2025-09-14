<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventApiController;
use App\Http\Controllers\Api\TicketApiController;
use App\Http\Controllers\Api\VendorApiController;
use App\Http\Controllers\Api\VendorApiController_cl;
use App\Http\Controllers\Api\VendorManagementApiController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\AdminInquiryApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Public API routes (no authentication required)
Route::prefix('v1')->middleware('throttle:100,1')->group(function () {
    // Authentication APIs
    Route::get('/auth/user', [App\Http\Controllers\FirebaseAuthController::class, 'user']);
    Route::post('/auth/check-user', [App\Http\Controllers\FirebaseAuthController::class, 'checkUserExists']);
    
    // User Management APIs (External Consumption)
    Route::get('/users-xml', [App\Http\Controllers\Api\UserAuthApiController::class, 'getUsersXml']);
    Route::get('/users-xml/{id}', [App\Http\Controllers\Api\UserAuthApiController::class, 'getUserXml']);
    Route::get('/users/{id}/auth-status', [App\Http\Controllers\Api\UserAuthApiController::class, 'getUserAuthStatus']);
    Route::post('/users', [App\Http\Controllers\Api\UserAuthApiController::class, 'createUser']);
    Route::put('/users/{id}', [App\Http\Controllers\Api\UserAuthApiController::class, 'updateUser']);
    
    // (CL) General event information API 
    Route::get('/events', [EventApiController::class, 'index']);
    Route::get('/events/{event}', [EventApiController::class, 'show']);
    
    // Inquiry API - Public access to inquiry data
    Route::get('/inquiries', [InquiryController::class, 'index']);
    Route::get('/inquiries/stats', [InquiryController::class, 'stats']);
    Route::get('/inquiries/status/{status}', [InquiryController::class, 'getByStatus']);
    Route::get('/inquiries/{inquiryId}', [InquiryController::class, 'show']);
    
    // Admin Inquiry API - Complete inquiry data with admin replies
    Route::get('/admin/inquiries', [AdminInquiryApiController::class, 'index']);
    Route::get('/admin/inquiries/stats', [AdminInquiryApiController::class, 'stats']);
    Route::get('/admin/inquiries/status/{status}', [AdminInquiryApiController::class, 'getByStatus']);
    Route::get('/admin/inquiries/{inquiryId}', [AdminInquiryApiController::class, 'show']);
        
    // Vendor information API
    Route::get('/vendors/{id}', [VendorApiController::class, 'getVendorInfo']);
    Route::get('/vendors/{id}/status', [VendorApiController::class, 'getVendorStatus']);
    
    // Event application API
    Route::get('/events/{eventId}/applications', [VendorApiController::class, 'getEventApplications']);
    Route::post('/events/{eventId}/apply', [VendorApiController::class, 'submitEventApplication']);
    
    // Receipt API endpoints (public)
    Route::prefix('receipts')->group(function () {
        Route::get('/order/{orderId}', [App\Http\Controllers\ReceiptController::class, 'getReceiptByOrderId']);
        Route::get('/order/{orderId}/data', [App\Http\Controllers\ReceiptController::class, 'getReceiptData']);
    });
});


// (CL) APIs for ticketing module to get ticket information
// Ticketing Module API routes
Route::prefix('v1/ticketing')->group(function () {
    // Get ticket information
    Route::get('/events/{event}/tickets', [TicketApiController::class, 'getTicketInfo']);
    
    // Update ticket quantities (requires authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::patch('/events/{event}/tickets/quantity', [TicketApiController::class, 'updateTicketQuantity']);
    });
    
});


// (CL) APIs for vendor page to get event information 
// Vendor Module API routes
Route::prefix('v1/vendor')->group(function () {
    Route::get('/events/{event}/booths', [VendorApiController_cl::class, 'getBoothInfo']); //good,but no use
    Route::get('/events', [VendorApiController_cl::class, 'getAllEventsWithBooths']); // excluded activities information
    Route::get('/events/accepting-applications', [VendorApiController_cl::class, 'getEventsAcceptingApplications']); //got use
    // Update booth quantities (requires authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::patch('/events/{event}/booths/quantity', [VendorApiController_cl::class, 'updateBoothQuantity']);//good 
    });
});


//Vendor Management (module) API routes
// Protected API routes (require authentication)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Vendor management API
    Route::prefix('admin')->group(function () {
        Route::get('/vendors', [VendorManagementApiController::class, 'getAllVendors']);
        Route::get('/vendors/{id}', [VendorManagementApiController::class, 'getVendorDetails']);
        Route::post('/vendors/{id}/approve', [VendorManagementApiController::class, 'approveVendor']);
        Route::post('/vendors/{id}/reject', [VendorManagementApiController::class, 'rejectVendor']);
        Route::get('/applications', [VendorManagementApiController::class, 'getApplications']);
        Route::get('/applications/{id}', [VendorManagementApiController::class, 'getApplicationDetails']);
    });
    
    // Vendor-specific API
    Route::prefix('vendor')->group(function () {
        Route::get('/profile', [VendorApiController::class, 'getProfile']);
        Route::put('/profile', [VendorApiController::class, 'updateProfile']);
        Route::get('/business-info/{id}', [VendorApiController::class, 'getBusinessInfo']);
        Route::get('/applications', [VendorApiController::class, 'getMyApplications']);
        Route::get('/bookings', [VendorApiController::class, 'getMyBookings']);
    });
    
    // Receipt API endpoints (authenticated)
    Route::prefix('receipts')->group(function () {
        Route::post('/', [App\Http\Controllers\ReceiptController::class, 'generateReceipt']);
        Route::get('/customer/{customerId}', [App\Http\Controllers\ReceiptController::class, 'getCustomerReceipts']);
        Route::get('/event/{eventId}', [App\Http\Controllers\ReceiptController::class, 'getEventReceipts']);
    });
    
});
