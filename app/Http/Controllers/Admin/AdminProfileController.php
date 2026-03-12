<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminProfileController extends Controller {
    public function edit(Request $request): View {
        return view('admin.profile.edit', [
            'admin' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse {
        $admin = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'mobile' => 'nullable|string|max:15',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $admin->name = $validated['name'];
        $admin->mobile = $validated['mobile'] ?? null;

        if ($request->hasFile('profile_image')) {
            if ($admin->profile_image) {
                Storage::disk('public')->delete($admin->profile_image);
            }

            $admin->profile_image = $request->file('profile_image')->store('profiles', 'public');
        }

        if (! empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        return redirect()->route('admin.profile.edit')->with('success', 'Profile updated successfully.');
    }
}
