<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventApiController;
use App\Http\Controllers\Api\TicketApiController;
use App\Http\Controllers\Api\VendorApiController_cl;
use App\Http\Controllers\Api\VendorApiController;
use App\Http\Controllers\Api\VendorManagementApiController;

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
Route::prefix('v1')->group(function () {
    // General event information API
    Route::get('/events', [EventApiController::class, 'index']);
    Route::get('/events/{event}', [EventApiController::class, 'show']);
    Route::get('/venues/{venue}/events', [EventApiController::class, 'getByVenue']);
    
    // Vendor information API (using existing VendorApiController)
    Route::get('/vendors/{id}', [VendorApiController::class, 'getVendorInfo']);
    Route::get('/vendors/{id}/status', [VendorApiController::class, 'getVendorStatus']);
    Route::get('/vendors/search', [VendorApiController::class, 'searchVendors']);
    
    // Event application API (using existing VendorApiController)
    Route::get('/events/{eventId}/applications', [VendorApiController::class, 'getEventApplications']);
    Route::post('/events/{eventId}/apply', [VendorApiController::class, 'submitEventApplication']);
});

// Ticketing Module API routes
Route::prefix('v1/ticketing')->group(function () {
    // Get ticket information
    Route::get('/events/{event}/tickets', [TicketApiController::class, 'getTicketInfo']);
    Route::get('/events', [TicketApiController::class, 'getAllEventsWithTickets']);
    
    // Update ticket quantities (requires authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::patch('/events/{event}/tickets/quantity', [TicketApiController::class, 'updateTicketQuantity']);
    });
});

// Vendor Module API routes
Route::prefix('v1/vendor')->group(function () {
    // Get event and booth information
    Route::get('/events/{event}', [VendorApiController_cl::class, 'getEventInfo']);
    Route::get('/events/{event}/booths', [VendorApiController_cl::class, 'getBoothInfo']);
    Route::get('/events', [VendorApiController_cl::class, 'getAllEventsWithBooths']);
    Route::get('/events/accepting-applications', [VendorApiController_cl::class, 'getEventsAcceptingApplications']);
    
    // Update booth quantities (requires authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::patch('/events/{event}/booths/quantity', [VendorApiController_cl::class, 'updateBoothQuantity']);
    });
});

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
    
    // Vendor-specific API (using existing VendorApiController)
    Route::prefix('vendor')->group(function () {
        Route::get('/profile', [VendorApiController::class, 'getProfile']);
        Route::put('/profile', [VendorApiController::class, 'updateProfile']);
        Route::get('/applications', [VendorApiController::class, 'getMyApplications']);
        Route::get('/bookings', [VendorApiController::class, 'getMyBookings']);
    });
});
