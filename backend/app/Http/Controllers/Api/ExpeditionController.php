<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expedition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ExpeditionController extends Controller
{
    /** Maximum allowed per_page to prevent memory exhaustion. */
    private const MAX_PER_PAGE = 100;
    private const DEFAULT_PER_PAGE = 25;

    // ---------------------------------------------------------------------------
    // [CRÍTICO 2] index — paginated
    // ---------------------------------------------------------------------------

    public function index(Request $request): JsonResponse
    {
        $perPage = min(
            (int) $request->input('per_page', self::DEFAULT_PER_PAGE),
            self::MAX_PER_PAGE
        );

        $query = Expedition::query()->with(['checklistItems', 'media']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('active')) {
            $query->active();
        }

        $paginator = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($this->paginationEnvelope($paginator));
    }

    public function publicList(Request $request): JsonResponse
    {
        $perPage = min(
            (int) $request->input('per_page', self::DEFAULT_PER_PAGE),
            self::MAX_PER_PAGE
        );

        $paginator = Expedition::query()
            ->whereIn('status', ['OPEN', 'GUARANTEED'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($this->paginationEnvelope($paginator));
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

    // ---------------------------------------------------------------------------
    // [ALTO 4] destroy — verifica dependências antes de deletar (soft delete)
    // ---------------------------------------------------------------------------

    /**
     * DELETE /expeditions/{expedition}
     *
     * Returns HTTP 409 with the list of blocking dependencies if:
     *   - The expedition has enrolled participants, or
     *   - There are unfinished checklist items.
     *
     * If no dependencies exist, performs a SOFT DELETE (sets deleted_at).
     * Hard purge requires Expedition::withTrashed()->find($id)->forceDelete()
     * and is intentionally not exposed via API.
     */
    public function destroy(Expedition $expedition): JsonResponse
    {
        $blockers = $expedition->getActiveDependencies();

        if (! empty($blockers)) {
            return response()->json([
                'message'  => 'Não é possível excluir a expedição: existem dependências ativas.',
                'blockers' => $blockers,
            ], 409);
        }

        $expedition->delete(); // soft delete — sets deleted_at via SoftDeletes trait

        return response()->json(['message' => 'Expedition deleted successfully.']);
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

    // ---------------------------------------------------------------------------
    // Private helpers
    // ---------------------------------------------------------------------------

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
