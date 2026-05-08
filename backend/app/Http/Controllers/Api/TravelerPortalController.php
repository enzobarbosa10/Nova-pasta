<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Expedition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TravelerPortalController extends Controller
{
    public function getData(string $travelerId): JsonResponse
    {
        $traveler = User::findOrFail($travelerId);

        if (!$traveler->isTraveler()) {
            return response()->json(['error' => 'User is not a traveler'], 403);
        }

        // Get expeditions where traveler is a participant
        $expeditions = Expedition::whereJsonContains('participants', $travelerId)
            ->with(['checklistItems', 'media'])
            ->get();

        $data = [
            'traveler' => [
                'id' => $traveler->id,
                'name' => $traveler->name,
                'email' => $traveler->email,
            ],
            'expeditions' => $expeditions,
            'itinerary' => $this->getItineraryData($expeditions),
            'documents' => $this->getDocumentsData($travelerId),
            'personal_checklist' => $this->getPersonalChecklist($travelerId),
        ];

        return response()->json($data);
    }

    public function getItinerary(string $travelerId): JsonResponse
    {
        $expeditions = Expedition::whereJsonContains('participants', $travelerId)->get();
        $itinerary = $this->getItineraryData($expeditions);

        return response()->json($itinerary);
    }

    public function getDocuments(string $travelerId): JsonResponse
    {
        $documents = $this->getDocumentsData($travelerId);

        return response()->json($documents);
    }

    private function getItineraryData($expeditions)
    {
        return $expeditions->map(function ($expedition) {
            return [
                'expedition_id' => $expedition->id,
                'name' => $expedition->name,
                'destination' => $expedition->destination,
                'dates' => $expedition->dates,
                'accommodation' => $expedition->accommodation,
                'transport' => $expedition->transport,
                'trail_level' => $expedition->trail_level,
            ];
        });
    }

    private function getDocumentsData($travelerId)
    {
        // This would typically fetch from a documents table
        // For now, returning a placeholder structure
        return [
            [
                'id' => '1',
                'name' => 'Contrato de Serviço',
                'url' => '#',
                'type' => 'PDF',
            ],
            [
                'id' => '2',
                'name' => 'Termo de Responsabilidade',
                'url' => '#',
                'type' => 'PDF',
            ],
        ];
    }

    private function getPersonalChecklist($travelerId)
    {
        // This would typically fetch traveler-specific checklist items
        // For now, returning a placeholder structure
        return [
            'Documentos pessoais',
            'Seguro viagem',
            'Vacinação em dia',
            'Equipamentos de trekking',
            'Roupas adequadas',
        ];
    }
}
