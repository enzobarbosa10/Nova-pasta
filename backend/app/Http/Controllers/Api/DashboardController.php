<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Expedition;
use App\Models\ChecklistItem;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Aggregated KPIs for the dashboard card grid.
     *
     * Route aliases (both point here):
     *   GET /api/v1/dashboard/stats   — primary
     *   GET /api/v1/analytics         — legacy alias (kept for backward compatibility)
     */
    public function stats(): JsonResponse
    {
        $totalLeads   = Lead::count();
        $paidLeads    = Lead::where('status', 'PAID')->count();
        $totalRevenue = Lead::where('status', 'PAID')->sum('estimated_ticket');

        $newLeadsThisMonth = Lead::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();

        $weekStart = now()->startOfWeek();
        $weekEnd   = now()->endOfWeek();

        $leadsThisWeek       = Lead::whereBetween('created_at', [$weekStart, $weekEnd])->count();
        $conversionsThisWeek = Lead::whereIn('status', ['PAID', 'RESERVED'])
            ->whereBetween('updated_at', [$weekStart, $weekEnd])->count();
        $revenueThisWeek     = Lead::where('status', 'PAID')
            ->whereBetween('updated_at', [$weekStart, $weekEnd])->sum('estimated_ticket');

        $conversionRate = $totalLeads > 0 ? round($paidLeads / $totalLeads * 100, 1) : 0;

        return response()->json([
            'total_leads'           => $totalLeads,
            'new_leads_month'       => $newLeadsThisMonth,
            'total_expeditions'     => Expedition::count(),
            'active_expeditions'    => Expedition::whereIn('status', ['OPEN', 'GUARANTEED', 'IN_PROGRESS'])->count(),
            'pending_tasks'         => ChecklistItem::where('status', 'PENDING')->count(),
            'total_revenue'         => (float) $totalRevenue,
            'conversion_rate'       => $conversionRate,
            'leads_this_week'       => $leadsThisWeek,
            'conversions_this_week' => $conversionsThisWeek,
            'revenue_this_week'     => (float) $revenueThisWeek,
            'recent_leads'          => Lead::latest()
                ->take(5)
                ->get(['id', 'name', 'status', 'created_at', 'destination', 'source']),
            'upcoming_expeditions'  => Expedition::whereIn('status', ['OPEN', 'GUARANTEED', 'IN_PROGRESS'])
                ->orderBy('created_at')
                ->take(5)
                ->get(['id', 'name', 'destination', 'dates', 'start_date', 'end_date', 'status', 'capacity', 'remaining_spots']),
        ]);
    }

    /**
     * Deep metrics: monthly revenue, conversion funnel, lead sources, top destinations.
     *
     * Route: GET /api/v1/dashboard/analytics
     */
    public function analytics(): JsonResponse
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date    = now()->subMonths($i);
            $label   = mb_strtoupper($date->locale('pt_BR')->isoFormat('MMM'), 'UTF-8');
            $revenue = Lead::where('status', 'PAID')
                ->whereYear('updated_at', $date->year)
                ->whereMonth('updated_at', $date->month)
                ->sum('estimated_ticket');
            $months->push(['month' => $label, 'revenue' => (float) $revenue]);
        }

        $stages = ['NEW', 'CONTACTED', 'QUALIFIED', 'PROPOSAL', 'RESERVED', 'PAID'];
        $funnel = collect($stages)->map(fn ($s) => [
            'stage' => $s,
            'count' => Lead::where('status', $s)->count(),
            'value' => (float) Lead::where('status', $s)->sum('estimated_ticket'),
        ]);

        $sources = Lead::selectRaw('source, COUNT(*) as count')
            ->groupBy('source')
            ->orderByDesc('count')
            ->get();

        $destinations = Lead::selectRaw('destination, COUNT(*) as count')
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
    }
}
