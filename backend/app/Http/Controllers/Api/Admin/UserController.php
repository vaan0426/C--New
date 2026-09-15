<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Admin: list all users (spec §6 admin panel — "Потребители").
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        return $query->orderBy('name')->get();
    }

    public function show(User $user)
    {
        return $user;
    }

    /**
     * Admin manages roles/status and the internal note (spec §2.2, §5.3).
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['sometimes', 'in:admin,editor,user'],
            'status' => ['sometimes', 'in:active,frozen'],
            'internal_note' => ['nullable', 'string', 'max:4000'],
        ]);

        $user->update($data);

        return $user;
    }
}
