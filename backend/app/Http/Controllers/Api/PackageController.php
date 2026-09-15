<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function index()
    {
        return Package::where('is_active', true)->orderBy('price')->get();
    }

    public function adminIndex()
    {
        return Package::orderBy('price')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sessions_count' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'validity_days' => ['nullable', 'integer', 'min:1'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['slug'] = Str::slug($data['name']).'-'.Str::random(4);

        return response()->json(Package::create($data), 201);
    }

    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sessions_count' => ['sometimes', 'integer', 'min:1'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'validity_days' => ['nullable', 'integer', 'min:1'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $package->update($data);

        return $package;
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return response()->json(status: 204);
    }
}
