<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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
    // Vendor information API
    Route::get('/vendors/{id}', [VendorApiController::class, 'getVendorInfo']);
    Route::get('/vendors/{id}/status', [VendorApiController::class, 'getVendorStatus']);
    Route::get('/vendors/search', [VendorApiController::class, 'searchVendors']);
    
    // Event application API
    Route::get('/events/{eventId}/applications', [VendorApiController::class, 'getEventApplications']);
    Route::post('/events/{eventId}/apply', [VendorApiController::class, 'submitEventApplication']);
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
    
    // Vendor-specific API
    Route::prefix('vendor')->group(function () {
        Route::get('/profile', [VendorApiController::class, 'getProfile']);
        Route::put('/profile', [VendorApiController::class, 'updateProfile']);
        Route::get('/applications', [VendorApiController::class, 'getMyApplications']);
        Route::get('/bookings', [VendorApiController::class, 'getMyBookings']);
    });
});
