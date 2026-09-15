<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return $request->user();
    }

    /**
     * Update profile data. Frozen accounts are read-only (spec §5.3).
     */
    public function update(Request $request)
    {
        $user = $request->user();

        if ($user->isFrozen()) {
            abort(403, 'Профилът е замразен — само за преглед.');
        }

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['sometimes', 'confirmed', Password::defaults()],
            'profile_photo' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $data['profile_photo_path'] = $path;
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        unset($data['profile_photo']);

        $user->update($data);

        return $user->fresh();
    }
}
