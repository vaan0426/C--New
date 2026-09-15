<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        return Event::where('is_active', true)
            ->where('starts_at', '>=', now())
            ->with('editor:id,name')
            ->orderBy('starts_at')
            ->get();
    }

    public function show(Event $event)
    {
        return $event->load('editor:id,name');
    }

    public function adminIndex()
    {
        return Event::with('editor:id,name')->orderBy('starts_at')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'editor_id' => ['required', 'exists:users,id'],
            'help_type_id' => ['nullable', 'exists:help_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['required', 'date', 'after:now'],
            'price' => ['required', 'numeric', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1'],
            'images' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['slug'] = Str::slug($data['title']).'-'.Str::random(4);

        return response()->json(Event::create($data), 201);
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'editor_id' => ['sometimes', 'exists:users,id'],
            'help_type_id' => ['nullable', 'exists:help_types,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['sometimes', 'date'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'capacity' => ['sometimes', 'integer', 'min:1'],
            'images' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $event->update($data);

        return $event;
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return response()->json(status: 204);
    }
}
