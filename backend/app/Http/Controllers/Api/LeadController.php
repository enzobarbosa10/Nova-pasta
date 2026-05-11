<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadNote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class LeadController extends Controller
{
    /** Maximum allowed per_page to prevent memory exhaustion. */
    private const MAX_PER_PAGE = 100;
    private const DEFAULT_PER_PAGE = 25;

    // ---------------------------------------------------------------------------
    // [CRÍTICO 2] index — paginated with configurable per_page (max 100)
    // [MÉDIO 7]   Uses Lead::scopeSearch() (driver-aware fulltext / trigram / LIKE)
    // ---------------------------------------------------------------------------

    public function index(Request $request): JsonResponse
    {
        $perPage = min(
            (int) $request->input('per_page', self::DEFAULT_PER_PAGE),
            self::MAX_PER_PAGE
        );

        $query = Lead::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('upcoming')) {
            $query->upcoming();
        }

        // [MÉDIO 7] Driver-aware full-text search via scopeSearch()
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $paginator = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($this->paginationEnvelope($paginator));
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

    // ---------------------------------------------------------------------------
    // [ALTO 3] Notes CRUD — replaces legacy text-concatenation approach
    // ---------------------------------------------------------------------------

    /**
     * POST /leads/{lead}/notes
     * Creates a new note row in lead_notes (author = authenticated user).
     */
    public function addNote(Request $request, Lead $lead): JsonResponse
    {
        $validated = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $note = $lead->leadNotes()->create([
            'user_id' => $request->user()->id,
            'body'    => $validated['body'],
        ]);

        return response()->json($note->load('author:id,name'), 201);
    }

    /**
     * PUT /leads/{lead}/notes/{note}
     * Edits an existing note. Users can only edit their own notes;
     * ADMIN / MASTER_ADMIN may edit any note.
     */
    public function editNote(Request $request, Lead $lead, LeadNote $note): JsonResponse
    {
        // Authorisation: own note OR admin-level role
        $user = $request->user();
        if ($note->user_id !== $user->id && ! in_array($user->role, ['ADMIN', 'MASTER_ADMIN'], true)) {
            return response()->json(['message' => 'Você não tem permissão para editar esta nota.'], 403);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $note->update(['body' => $validated['body']]);

        return response()->json($note->fresh()->load('author:id,name'));
    }

    /**
     * DELETE /leads/{lead}/notes/{note}
     * Deletes a note. Same ownership rule as editNote().
     */
    public function deleteNote(Request $request, Lead $lead, LeadNote $note): JsonResponse
    {
        $user = $request->user();
        if ($note->user_id !== $user->id && ! in_array($user->role, ['ADMIN', 'MASTER_ADMIN'], true)) {
            return response()->json(['message' => 'Você não tem permissão para excluir esta nota.'], 403);
        }

        $note->delete();

        return response()->json(['message' => 'Nota excluída com sucesso.']);
    }

    // ---------------------------------------------------------------------------
    // Private helpers
    // ---------------------------------------------------------------------------

    /**
     * [CRÍTICO 2] Standardised pagination envelope:
     *   { data: [...], meta: { current_page, per_page, total, last_page }, links: {...} }
     */
    private function paginationEnvelope(LengthAwarePaginator $paginator): array
    {
        return [
            'data'  => $paginator->items(),
            'meta'  => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last'  => $paginator->url($paginator->lastPage()),
                'prev'  => $paginator->previousPageUrl(),
                'next'  => $paginator->nextPageUrl(),
            ],
        ];
    }
}
