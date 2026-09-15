<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function show(string $slug)
    {
        return Page::where('slug', $slug)->where('is_published', true)->firstOrFail();
    }

    public function adminIndex()
    {
        return Page::orderBy('title')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'is_published' => ['sometimes', 'boolean'],
        ]);

        $data['slug'] = Str::slug($data['title']).'-'.Str::random(4);

        return response()->json(Page::create($data), 201);
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'is_published' => ['sometimes', 'boolean'],
        ]);

        $page->update($data);

        return $page;
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return response()->json(status: 204);
    }
}
