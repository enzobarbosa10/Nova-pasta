<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Expedition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MediaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Media::query();

        // Filter by type
        if ($request->has('type')) {
            $query->byType($request->type);
        }

        // Filter by expedition
        if ($request->has('expedition_id')) {
            $query->byExpedition($request->expedition_id);
        }

        // Search by tags
        if ($request->has('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }

        $media = $query->orderBy('created_at', 'desc')->get();

        return response()->json($media);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => 'required|string',
            'type' => 'required|in:PHOTO,VIDEO,DRONE,REEL,STORY,REVIEW',
            'expedition_id' => 'nullable|uuid|exists:expeditions,id',
            'tags' => 'nullable|array',
        ]);

        $media = Media::create($validated);

        return response()->json($media, 201);
    }

    public function show(Media $media): JsonResponse
    {
        return response()->json($media);
    }

    public function update(Request $request, Media $media): JsonResponse
    {
        $validated = $request->validate([
            'url' => 'sometimes|string',
            'type' => 'sometimes|in:PHOTO,VIDEO,DRONE,REEL,STORY,REVIEW',
            'expedition_id' => 'nullable|uuid|exists:expeditions,id',
            'tags' => 'nullable|array',
        ]);

        $media->update($validated);

        return response()->json($media);
    }

    public function destroy(Media $media): JsonResponse
    {
        $media->delete();

        return response()->json(['message' => 'Media deleted successfully']);
    }

    public function getByExpedition(Expedition $expedition): JsonResponse
    {
        $media = $expedition->media()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($media);
    }

    public function bulkUpload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'media' => 'required|array',
            'media.*.url' => 'required|string',
            'media.*.type' => 'required|in:PHOTO,VIDEO,DRONE,REEL,STORY,REVIEW',
            'media.*.expedition_id' => 'nullable|uuid|exists:expeditions,id',
            'media.*.tags' => 'nullable|array',
        ]);

        $uploadedMedia = [];

        foreach ($validated['media'] as $mediaData) {
            $uploadedMedia[] = Media::create($mediaData);
        }

        return response()->json($uploadedMedia, 201);
    }
}
