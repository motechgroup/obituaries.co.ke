<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $admin = Auth::guard('admin')->user();
        
        // Count verified obituaries by this admin
        $verifiedCount = \App\Models\Obituary::where('verified_by', $admin->id)->count();

        return view('admin.profile.edit', compact('admin', 'verifiedCount'));
    }

    /**
     * Update the admin profile details and/or password.
     */
    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('admins')->ignore($admin->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'current_password' => ['nullable', 'required_with:password'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        // Verify current password if changing password
        if (!empty($validated['password'])) {
            if (!Hash::check($validated['current_password'], $admin->password)) {
                return back()->withErrors(['current_password' => 'The current password provided is incorrect.'])->withInput();
            }
            $admin->password = Hash::make($validated['password']);
        }

        // Handle Avatar File Upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if present
            if ($admin->avatar && Storage::disk('public')->exists($admin->avatar)) {
                Storage::disk('public')->delete($admin->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $admin->avatar = $path;
        }

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        $admin->phone = $validated['phone'] ?? null;
        $admin->save();

        return back()->with('success', '👤 Profile details and account settings updated successfully!');
    }
}
