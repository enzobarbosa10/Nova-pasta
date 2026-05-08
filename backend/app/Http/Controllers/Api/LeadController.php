<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LeadController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Lead::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by upcoming follow-ups
        if ($request->has('upcoming')) {
            $query->upcoming();
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('whatsapp', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        $leads = $query->orderBy('created_at', 'desc')->get();

        return response()->json($leads);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'source' => 'required|string|max:255',
            'interest' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'date_desired' => 'required|date',
            'people_count' => 'required|integer|min:1',
            'estimated_ticket' => 'required|numeric|min:0',
            'status' => 'nullable|in:NEW,CONTACTED,QUALIFIED,PROPOSAL,RESERVED,PAID,POST_TRIP,REFERRAL',
            'notes' => 'nullable|string',
            'last_contact' => 'required|date',
            'next_follow_up' => 'required|date',
            'tags' => 'nullable|array',
        ]);

        $lead = Lead::create($validated);

        return response()->json($lead, 201);
    }

    public function show(Lead $lead): JsonResponse
    {
        return response()->json($lead);
    }

    public function update(Request $request, Lead $lead): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'whatsapp' => 'sometimes|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'source' => 'sometimes|string|max:255',
            'interest' => 'sometimes|string|max:255',
            'destination' => 'sometimes|string|max:255',
            'date_desired' => 'sometimes|date',
            'people_count' => 'sometimes|integer|min:1',
            'estimated_ticket' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:NEW,CONTACTED,QUALIFIED,PROPOSAL,RESERVED,PAID,POST_TRIP,REFERRAL',
            'notes' => 'nullable|string',
            'last_contact' => 'sometimes|date',
            'next_follow_up' => 'sometimes|date',
            'tags' => 'nullable|array',
        ]);

        $lead->update($validated);

        return response()->json($lead);
    }

    public function destroy(Lead $lead): JsonResponse
    {
        $lead->delete();

        return response()->json(['message' => 'Lead deleted successfully']);
    }

    public function updateStatus(Request $request, Lead $lead): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:NEW,CONTACTED,QUALIFIED,PROPOSAL,RESERVED,PAID,POST_TRIP,REFERRAL',
        ]);

        $lead->update($validated);

        return response()->json($lead);
    }

    public function addNote(Request $request, Lead $lead): JsonResponse
    {
        $validated = $request->validate([
            'note' => 'required|string',
        ]);

        $currentNotes = $lead->notes ?? '';
        $newNote = date('Y-m-d H:i:s') . ': ' . $validated['note'];
        $lead->notes = $currentNotes ? $currentNotes . "\n\n" . $newNote : $newNote;
        $lead->save();

        return response()->json($lead);
    }
}
