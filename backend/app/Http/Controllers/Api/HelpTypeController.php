<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HelpType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HelpTypeController extends Controller
{
    public function index()
    {
        return HelpType::where('is_active', true)->orderBy('name')->get();
    }

    public function adminIndex()
    {
        return HelpType::orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['slug'] = Str::slug($data['name']).'-'.Str::random(4);

        return response()->json(HelpType::create($data), 201);
    }

    public function update(Request $request, HelpType $helpType)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_price' => ['sometimes', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $helpType->update($data);

        return $helpType;
    }

    public function destroy(HelpType $helpType)
    {
        $helpType->delete();

        return response()->json(status: 204);
    }
}
