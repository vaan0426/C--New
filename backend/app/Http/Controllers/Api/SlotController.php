<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slot;
use Illuminate\Http\Request;

class SlotController extends Controller
{
    /**
     * Public: list free upcoming slots, optionally filtered by editor.
     */
    public function index(Request $request)
    {
        $query = Slot::available()->with('editor:id,name');

        if ($request->filled('editor_id')) {
            $query->where('editor_id', $request->integer('editor_id'));
        }

        return $query->orderBy('date')->orderBy('start_time')->get();
    }

    /**
     * Admin/editor: full schedule including booked slots.
     */
    public function adminIndex(Request $request)
    {
        $query = Slot::with(['editor:id,name', 'booking']);

        if ($request->filled('editor_id')) {
            $query->where('editor_id', $request->integer('editor_id'));
        }

        return $query->orderBy('date')->orderBy('start_time')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'editor_id' => ['required', 'exists:users,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        return response()->json(Slot::create($data), 201);
    }

    public function destroy(Slot $slot)
    {
        if ($slot->is_booked) {
            return response()->json(['message' => 'Не може да изтриете зает час.'], 422);
        }

        $slot->delete();

        return response()->json(status: 204);
    }
}
