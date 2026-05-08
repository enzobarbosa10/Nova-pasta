<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expedition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExpeditionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Expedition::query()->with(['checklistItems', 'media']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter active expeditions
        if ($request->has('active')) {
            $query->active();
        }

        $expeditions = $query->orderBy('created_at', 'desc')->get();

        return response()->json($expeditions);
    }

    public function publicList(Request $request): JsonResponse
    {
        $expeditions = Expedition::query()
            ->whereIn('status', ['OPEN', 'GUARANTEED'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($expeditions);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cover_image' => 'nullable|string',
            'destination' => 'required|string|max:255',
            'dates' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'capacity' => 'required|integer|min:1',
            'remaining_spots' => 'required|integer|min:0',
            'guide_id' => 'nullable|uuid',
            'accommodation' => 'required|string|max:255',
            'transport' => 'required|string|max:255',
            'trail_level' => 'required|in:EASY,MODERATE,HARD,CHALLENGING',
            'status' => 'nullable|in:PLANNING,OPEN,GUARANTEED,IN_PROGRESS,COMPLETED,CANCELLED',
            'costs' => 'required|numeric|min:0',
            'margin_predicted' => 'required|numeric|min:0',
            'margin_real' => 'nullable|numeric',
            'participants' => 'nullable|array',
        ]);

        $expedition = Expedition::create($validated);

        return response()->json($expedition, 201);
    }

    public function show(Expedition $expedition): JsonResponse
    {
        $expedition->load(['checklistItems', 'media']);
        return response()->json($expedition);
    }

    public function update(Request $request, Expedition $expedition): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'cover_image' => 'nullable|string',
            'destination' => 'sometimes|string|max:255',
            'dates' => 'sometimes|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'capacity' => 'sometimes|integer|min:1',
            'remaining_spots' => 'sometimes|integer|min:0',
            'guide_id' => 'nullable|uuid',
            'accommodation' => 'sometimes|string|max:255',
            'transport' => 'sometimes|string|max:255',
            'trail_level' => 'sometimes|in:EASY,MODERATE,HARD,CHALLENGING',
            'status' => 'sometimes|in:PLANNING,OPEN,GUARANTEED,IN_PROGRESS,COMPLETED,CANCELLED',
            'costs' => 'sometimes|numeric|min:0',
            'margin_predicted' => 'sometimes|numeric|min:0',
            'margin_real' => 'nullable|numeric',
            'participants' => 'nullable|array',
        ]);

        $expedition->update($validated);

        return response()->json($expedition);
    }

    public function destroy(Expedition $expedition): JsonResponse
    {
        $expedition->delete();

        return response()->json(['message' => 'Expedition deleted successfully']);
    }

    public function updateStatus(Request $request, Expedition $expedition): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:PLANNING,OPEN,GUARANTEED,IN_PROGRESS,COMPLETED,CANCELLED',
        ]);

        $expedition->update($validated);

        return response()->json($expedition);
    }

    public function addParticipant(Request $request, Expedition $expedition): JsonResponse
    {
        $validated = $request->validate([
            'traveler_id' => 'required|uuid',
        ]);

        $expedition->addParticipant($validated['traveler_id']);

        return response()->json($expedition);
    }

    public function removeParticipant(Expedition $expedition, string $participantId): JsonResponse
    {
        $expedition->removeParticipant($participantId);

        return response()->json($expedition);
    }
}
