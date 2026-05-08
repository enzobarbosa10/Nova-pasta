<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChecklistItem;
use App\Models\Expedition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChecklistItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ChecklistItem::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by expedition
        if ($request->has('expedition_id')) {
            $query->where('expedition_id', $request->expedition_id);
        }

        $items = $query->orderBy('created_at', 'desc')->get();

        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'task' => 'required|string|max:500',
            'category' => 'required|in:PRE,DURING,POST',
            'status' => 'nullable|in:PENDING,DONE',
            'expedition_id' => 'nullable|uuid|exists:expeditions,id',
            'assigned_to' => 'nullable|uuid',
        ]);

        $item = ChecklistItem::create($validated);

        return response()->json($item, 201);
    }

    public function show(ChecklistItem $checklistItem): JsonResponse
    {
        return response()->json($checklistItem);
    }

    public function update(Request $request, ChecklistItem $checklistItem): JsonResponse
    {
        $validated = $request->validate([
            'task' => 'sometimes|string|max:500',
            'category' => 'sometimes|in:PRE,DURING,POST',
            'status' => 'sometimes|in:PENDING,DONE',
            'expedition_id' => 'nullable|uuid|exists:expeditions,id',
            'assigned_to' => 'nullable|uuid',
        ]);

        $checklistItem->update($validated);

        return response()->json($checklistItem);
    }

    public function destroy(ChecklistItem $checklistItem): JsonResponse
    {
        $checklistItem->delete();

        return response()->json(['message' => 'Checklist item deleted successfully']);
    }

    public function toggleStatus(ChecklistItem $checklistItem): JsonResponse
    {
        $checklistItem->toggleStatus();

        return response()->json($checklistItem);
    }

    public function getByExpedition(Expedition $expedition): JsonResponse
    {
        $items = $expedition->checklistItems()
            ->orderBy('category')
            ->orderBy('created_at')
            ->get();

        return response()->json($items);
    }
}
