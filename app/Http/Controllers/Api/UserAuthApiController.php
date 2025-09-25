<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Factories\UserFactory;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserAuthApiController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get all users (JSON format)
     * IFA: User Information Service
     * URL: /api/v1/users
     */
    public function getUsers(Request $request): JsonResponse
    {
        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = $request->query('use_api', false);

            if ($useApi) {
                // External API consumption (simulate another module)
                $response = Http::timeout(10)
                    ->get(url('/api/v1/users'));

                if ($response->failed()) {
                    throw new \Exception('Failed to fetch users from API');
                }

                return response()->json($response->json());
            } else {
                // Internal service consumption
                $users = User::with(['vendor'])->get();
                
                $usersData = $users->map(function ($user) {
                    $userData = [
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'auth_method' => $user->auth_method,
                        'is_active' => $user->is_active,
                        'phone' => $user->phone,
                        'address' => $user->address,
                        'created_at' => $user->created_at->toISOString(),
                        'last_login_at' => $user->last_login_at ? $user->last_login_at->toISOString() : null,
                    ];
                    
                    // Add vendor info if exists
                    if ($user->vendor) {
                        $userData['vendor'] = [
                            'vendor_id' => $user->vendor->id,
                            'business_name' => $user->vendor->business_name,
                            'status' => $user->vendor->status,
                        ];
                    }
                    
                    return $userData;
                });

                return response()->json([
                    'status' => 'success',
                    'message' => 'Users retrieved successfully',
                    'data' => $usersData,
                    'total' => $users->count()
                ]);
            }

        } catch (\Exception $e) {
            Log::error('User retrieval failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user by ID (JSON format)
     * IFA: User Detail Service
     * URL: /api/v1/users/{id}
     */
    public function getUser(Request $request, $id): JsonResponse
    {
        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = $request->query('use_api', false);

            if ($useApi) {
                // External API consumption (simulate another module)
                $response = Http::timeout(10)
                    ->get(url("/api/v1/users/{$id}"));

                if ($response->failed()) {
                    throw new \Exception('Failed to fetch user from API');
                }

                return response()->json($response->json());
            } else {
                // Internal service consumption
                $user = User::with(['vendor'])->find($id);
                
                if (!$user) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'User not found'
                    ], 404);
                }

                $userData = [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'auth_method' => $user->auth_method,
                    'is_active' => $user->is_active,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'created_at' => $user->created_at->toISOString(),
                    'last_login_at' => $user->last_login_at ? $user->last_login_at->toISOString() : null,
                    'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toISOString() : null,
                ];
                
                // Add vendor info if exists
                if ($user->vendor) {
                    $userData['vendor'] = [
                        'vendor_id' => $user->vendor->id,
                        'business_name' => $user->vendor->business_name,
                        'status' => $user->vendor->status,
                    ];
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'User retrieved successfully',
                    'data' => $userData
                ]);
            }

        } catch (\Exception $e) {
            Log::error('User retrieval failed', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user authentication status (JSON format)
     * IFA: User Authentication Status Service
     * URL: /api/v1/users/{id}/auth-status
     */
    public function getUserAuthStatus(Request $request, $id): JsonResponse
    {
        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = $request->query('use_api', false);

            if ($useApi) {
                // External API consumption (simulate another module)
                $response = Http::timeout(10)
                    ->get(url("/api/v1/users/{$id}/auth-status"));

                if ($response->failed()) {
                    throw new \Exception('Failed to fetch user auth status from API');
                }

                return response()->json($response->json());
            } else {
                // Internal service consumption
                $user = User::find($id);
                
                if (!$user) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'User not found'
                    ], 404);
                }

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name,
                        'role' => $user->role,
                        'auth_method' => $user->auth_method,
                        'is_active' => $user->is_active,
                        'is_firebase_managed' => $user->isFirebaseManaged(),
                        'can_change_password' => $user->canChangePassword(),
                        'last_login_at' => $user->last_login_at,
                        'email_verified_at' => $user->email_verified_at
                    ]
                ]);
            }

        } catch (\Exception $e) {
            Log::error('User auth status check failed', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve user authentication status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create user (JSON format)
     * IFA: User Creation Service
     * URL: /api/v1/users
     */
    public function createUser(Request $request): JsonResponse
    {
        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = $request->query('use_api', false);

            if ($useApi) {
                // External API consumption (simulate another module)
                $response = Http::timeout(10)
                    ->post(url('/api/v1/users'), $request->all());

                if ($response->failed()) {
                    throw new \Exception('Failed to create user via API');
                }

                return response()->json($response->json());
            } else {
                // Internal service consumption
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email',
                    'role' => 'required|in:admin,vendor,customer',
                    'auth_method' => 'required|in:laravel,firebase_email,oauth',
                    'password' => 'required_if:auth_method,laravel|string|min:8',
                    'firebase_uid' => 'required_if:auth_method,firebase_email|string|unique:users,firebase_uid',
                    'phone' => 'nullable|string|max:20',
                    'address' => 'nullable|string|max:500'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 400);
                }

                $userData = $request->only([
                    'name', 'email', 'role', 'auth_method', 'password', 
                    'firebase_uid', 'phone', 'address'
                ]);

                // Use appropriate factory method based on auth method
                if ($request->auth_method === 'laravel') {
                    $user = UserFactory::createAdminUser($userData);
                } else {
                    $user = UserFactory::createFirebaseUser([
                        'uid' => $userData['firebase_uid'],
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'role' => $userData['role'],
                        'auth_type' => $userData['auth_method'],
                        'phone' => $userData['phone'] ?? null,
                        'address' => $userData['address'] ?? null
                    ]);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'User created successfully',
                    'data' => [
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'auth_method' => $user->auth_method,
                        'created_at' => $user->created_at
                    ]
                ], 201);
            }

        } catch (\Exception $e) {
            Log::error('User creation failed', [
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user (JSON format)
     * IFA: User Update Service
     * URL: /api/v1/users/{id}
     */
    public function updateUser(Request $request, $id): JsonResponse
    {
        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = $request->query('use_api', false);

            if ($useApi) {
                // External API consumption (simulate another module)
                $response = Http::timeout(10)
                    ->put(url("/api/v1/users/{$id}"), $request->all());

                if ($response->failed()) {
                    throw new \Exception('Failed to update user via API');
                }

                return response()->json($response->json());
            } else {
                // Internal service consumption
                $validator = Validator::make($request->all(), [
                    'name' => 'sometimes|string|max:255',
                    'phone' => 'nullable|string|max:20',
                    'address' => 'nullable|string|max:500'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 400);
                }

                $user = User::find($id);

                if (!$user) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'User not found'
                    ], 404);
                }

                // Use factory for profile updates
                if ($user->isFirebaseManaged()) {
                    UserFactory::updateFirebaseUser($user, $request->only(['name', 'phone', 'address']));
                } else {
                    $user->update($request->only(['name', 'phone', 'address']));
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'User updated successfully',
                    'data' => [
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'address' => $user->address,
                        'updated_at' => $user->updated_at
                    ]
                ]);
            }

        } catch (\Exception $e) {
            Log::error('User update failed', [
                'user_id' => $id,
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
