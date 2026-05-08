<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ExpeditionController;
use App\Http\Controllers\Api\ChecklistItemController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\TravelerPortalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    
    // Health check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'message' => 'API is running',
            'timestamp' => now()->toIso8601String(),
            'version' => '1.0.0'
        ]);
    });
    
    // -----------------------------------------------
    // Public read endpoints (internal admin tool)
    // -----------------------------------------------
    Route::get('/expeditions/public', [ExpeditionController::class, 'publicList']);
    Route::get('/expeditions', [ExpeditionController::class, 'index']);
    Route::get('/expeditions/{expedition}', [ExpeditionController::class, 'show']);
    Route::get('/leads', [LeadController::class, 'index']);
    Route::get('/leads/{lead}', [LeadController::class, 'show']);

    // Dashboard stats (aggregated, no PII)
    Route::get('/dashboard/stats', function () {
        $totalLeads    = \App\Models\Lead::count();
        $paidLeads     = \App\Models\Lead::where('status', 'PAID')->count();
        $totalRevenue  = \App\Models\Lead::where('status', 'PAID')->sum('estimated_ticket');

        $newLeadsThisMonth = \App\Models\Lead::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();

        $weekStart = now()->startOfWeek();
        $weekEnd   = now()->endOfWeek();

        $leadsThisWeek       = \App\Models\Lead::whereBetween('created_at', [$weekStart, $weekEnd])->count();
        $conversionsThisWeek = \App\Models\Lead::whereIn('status', ['PAID', 'RESERVED'])
            ->whereBetween('updated_at', [$weekStart, $weekEnd])->count();
        $revenueThisWeek     = \App\Models\Lead::where('status', 'PAID')
            ->whereBetween('updated_at', [$weekStart, $weekEnd])->sum('estimated_ticket');

        $conversionRate = $totalLeads > 0 ? round($paidLeads / $totalLeads * 100, 1) : 0;

        return response()->json([
            'total_leads'            => $totalLeads,
            'new_leads_month'        => $newLeadsThisMonth,
            'total_expeditions'      => \App\Models\Expedition::count(),
            'active_expeditions'     => \App\Models\Expedition::whereIn('status', ['OPEN', 'GUARANTEED', 'IN_PROGRESS'])->count(),
            'pending_tasks'          => \App\Models\ChecklistItem::where('status', 'PENDING')->count(),
            'total_revenue'          => (float) $totalRevenue,
            'conversion_rate'        => $conversionRate,
            'leads_this_week'        => $leadsThisWeek,
            'conversions_this_week'  => $conversionsThisWeek,
            'revenue_this_week'      => (float) $revenueThisWeek,
            'recent_leads'           => \App\Models\Lead::latest()->take(5)->get(['id','name','status','created_at','destination','source']),
            'upcoming_expeditions'   => \App\Models\Expedition::whereIn('status', ['OPEN', 'GUARANTEED', 'IN_PROGRESS'])
                ->orderBy('created_at')
                ->take(5)
                ->get(['id','name','destination','dates','start_date','end_date','status','capacity','remaining_spots']),
        ]);
    });

    // Analytics — deep metrics
    Route::get('/dashboard/analytics', function () {
        // Monthly revenue for the last 6 months (PAID leads)
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $label = mb_strtoupper($date->locale('pt_BR')->isoFormat('MMM'), 'UTF-8');
            $revenue = \App\Models\Lead::where('status', 'PAID')
                ->whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->sum('estimated_ticket');
            $months->push(['month' => $label, 'revenue' => (float) $revenue]);
        }

        // Lead funnel per stage
        $stages = ['NEW', 'CONTACTED', 'QUALIFIED', 'PROPOSAL', 'RESERVED', 'PAID'];
        $funnel = collect($stages)->map(fn($s) => [
            'stage' => $s,
            'count' => \App\Models\Lead::where('status', $s)->count(),
            'value' => (float) \App\Models\Lead::where('status', $s)->sum('estimated_ticket'),
        ]);

        // Lead source distribution
        $sources = \App\Models\Lead::selectRaw('source, COUNT(*) as count')
            ->groupBy('source')
            ->orderByDesc('count')
            ->get();

        // Top destinations
        $destinations = \App\Models\Lead::selectRaw('destination, COUNT(*) as count')
            ->whereNotNull('destination')
            ->groupBy('destination')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        return response()->json([
            'monthly_revenue' => $months,
            'funnel'          => $funnel,
            'sources'         => $sources,
            'destinations'    => $destinations,
        ]);
    });

    // -----------------------------------------------
    // Write / mutation routes (require authentication)
    // -----------------------------------------------
    Route::middleware('auth:sanctum')->group(function () {
        
        // Leads — create / update / delete
        Route::post('leads', [LeadController::class, 'store']);
        Route::put('leads/{lead}', [LeadController::class, 'update']);
        Route::patch('leads/{lead}', [LeadController::class, 'update']);
        Route::delete('leads/{lead}', [LeadController::class, 'destroy']);
        Route::patch('leads/{lead}/status', [LeadController::class, 'updateStatus']);
        Route::post('leads/{lead}/notes', [LeadController::class, 'addNote']);
        
        // Expeditions — create / update / delete
        Route::post('expeditions', [ExpeditionController::class, 'store']);
        Route::put('expeditions/{expedition}', [ExpeditionController::class, 'update']);
        Route::patch('expeditions/{expedition}', [ExpeditionController::class, 'update']);
        Route::delete('expeditions/{expedition}', [ExpeditionController::class, 'destroy']);
        Route::patch('expeditions/{expedition}/status', [ExpeditionController::class, 'updateStatus']);
        Route::post('expeditions/{expedition}/participants', [ExpeditionController::class, 'addParticipant']);
        Route::delete('expeditions/{expedition}/participants/{participantId}', [ExpeditionController::class, 'removeParticipant']);
        
        // Checklist Items
        Route::apiResource('checklist-items', ChecklistItemController::class);
        Route::patch('checklist-items/{checklistItem}/toggle', [ChecklistItemController::class, 'toggleStatus']);
        Route::get('expeditions/{expedition}/checklist', [ChecklistItemController::class, 'getByExpedition']);
        
        // Media Bank
        Route::apiResource('media', MediaController::class);
        Route::get('expeditions/{expedition}/media', [MediaController::class, 'getByExpedition']);
        Route::post('media/bulk-upload', [MediaController::class, 'bulkUpload']);
        
        // Traveler Portal
        Route::get('traveler-portal/{travelerId}', [TravelerPortalController::class, 'getData']);
        Route::get('traveler-portal/{travelerId}/itinerary', [TravelerPortalController::class, 'getItinerary']);
        Route::get('traveler-portal/{travelerId}/documents', [TravelerPortalController::class, 'getDocuments']);
    });
});
