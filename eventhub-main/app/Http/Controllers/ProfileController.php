<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Show user profile
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Use factory for profile updates to ensure proper validation
        try {
            if ($user->isFirebaseManaged()) {
                \App\Factories\UserFactory::updateFirebaseUser($user, $request->only(['name', 'phone', 'address']));
            } else {
                // For admin users, direct update is fine
                $user->update($request->only(['name', 'phone', 'address']));
            }

            return redirect()->route('profile.show')
                ->with('success', 'Profile updated successfully! Note: Email cannot be changed to maintain authentication security.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withErrors(['general' => $e->getMessage()])
                ->withInput();
        }
    }
} 