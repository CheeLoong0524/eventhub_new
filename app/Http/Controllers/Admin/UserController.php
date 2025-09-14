<?php
/** Author: Tan Chim Yang 
 * RSW2S3G4
 * 23WMR14610 
 * **/
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        try {
            // Auto-detect: if request has 'use_api' query param, consume externally
            $useApi = $request->query('use_api', false);

            if ($useApi) {
                // External API consumption (simulate another module)
                $response = Http::timeout(10)
                    ->get(url('/api/v1/users-xml'));

                if ($response->failed()) {
                    throw new \Exception('Failed to fetch users from API');
                }

                $xml = simplexml_load_string($response->body());
                $users = $this->parseUsersFromXml($xml);
            } else {
                // Internal service consumption
                $xml = $this->userService->generateUsersXml();
                $users = $this->parseUsersFromXml($xml);
            }

            return view('admin.users.index', compact('users'));

        } catch (\Exception $e) {
            return view('admin.users.index', [
                'users' => collect([]),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Parse users from XML response
     */
    private function parseUsersFromXml($xml)
    {
        $users = collect();
        
        foreach ($xml->user as $userXml) {
            $users->push((object) [
                'id' => (string) $userXml->user_id,
                'name' => (string) $userXml->name,
                'email' => (string) $userXml->email,
                'role' => (string) $userXml->role,
                'auth_method' => (string) $userXml->auth_method,
                'is_active' => (string) $userXml->is_active === '1',
                'phone' => (string) $userXml->phone,
                'address' => (string) $userXml->address,
                'created_at' => (string) $userXml->created_at,
                'last_login_at' => (string) $userXml->last_login_at,
            ]);
        }
        
        return $users;
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin', // Only allow admin role
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        try {
            $userData = $request->only(['name', 'email', 'password', 'phone', 'address']);
            
            // Use the new factory method for admin users
            $user = \App\Factories\UserFactory::createAdminUser($userData);

            return redirect()->route('admin.users.index')
                ->with('success', 'Admin user created successfully!');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withErrors(['general' => $e->getMessage()])
                ->withInput($request->except(['password', 'password_confirmation']));
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,vendor,customer',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->update($request->only(['name', 'email', 'role', 'phone', 'address', 'is_active']));

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        try {
            // Prevent admin from deactivating themselves
            if ($user->id === auth()->id()) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'You cannot deactivate your own account.');
            }

            $user->update(['is_active' => !$user->is_active]);

            $status = $user->is_active ? 'activated' : 'deactivated';
            
            return redirect()->route('admin.users.index')
                ->with('success', "User {$status} successfully!");
                
        } catch (\Exception $e) {
            \Log::error('Toggle status error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'admin_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.users.index')
                ->with('error', 'Failed to update user status. Please try again.');
        }
    }

    /**
     * Search users
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('query');
            $role = $request->get('role');

            $users = User::query();

            if ($query) {
                $users->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%");
                });
            }

            if ($role) {
                $users->where('role', $role);
            }

            $users = $users->orderBy('created_at', 'desc')->paginate(15);

            return view('admin.users.index', compact('users', 'query', 'role'));
        } catch (\Exception $e) {
            \Log::error('User search error: ' . $e->getMessage());
            return redirect()->route('admin.users.index')
                ->with('error', 'Search failed. Please try again.');
        }
    }
} 