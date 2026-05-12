<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserManagementController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\ExpeditionController;
use App\Http\Controllers\Api\ChecklistItemController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\TravelerPortalController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
| Authentication is handled exclusively by Supabase Auth.
| The 'supabase.auth' middleware validates the JWT Bearer token.
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Health check (public)
    Route::get('/health', function () {
        return response()->json([
            'status'    => 'ok',
            'message'   => 'API is running',
            'timestamp' => now()->toIso8601String(),
            'version'   => '1.0.0',
        ]);
    });

    // Protected - requires valid Supabase JWT
    Route::middleware(['supabase.auth', 'throttle:60,1'])->group(function () {

        // Profile
        Route::get('/auth/me', [AuthController::class, 'me']);

        // User management - MASTER_ADMIN only
        Route::middleware('role:MASTER_ADMIN')->prefix('users')->group(function () {
            Route::get('/',                       [UserManagementController::class, 'index']);
            Route::post('/',                      [UserManagementController::class, 'store'])->middleware('throttle:20,1');
            Route::put('/{user}',                 [UserManagementController::class, 'update'])->middleware('throttle:20,1');
            Route::delete('/{user}',              [UserManagementController::class, 'destroy'])->middleware('throttle:20,1');
            Route::post('/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->middleware('throttle:10,1');
            Route::patch('/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->middleware('throttle:20,1');
        });

        // Dashboard
        Route::get('/dashboard/stats',     [DashboardController::class, 'stats']);
        Route::get('/dashboard/analytics', [DashboardController::class, 'analytics']);

        // Expeditions & Leads - read
        Route::get('/expeditions/public',       [ExpeditionController::class, 'publicList']);
        Route::get('/expeditions',              [ExpeditionController::class, 'index']);
        Route::get('/expeditions/{expedition}', [ExpeditionController::class, 'show']);
        Route::get('/leads',                    [LeadController::class, 'index']);
        Route::get('/leads/{lead}',             [LeadController::class, 'show']);

        // Write routes - ADMIN or OPERATOR
        Route::middleware(['role:ADMIN,OPERATOR', 'throttle:30,1'])->group(function () {
            Route::post('leads',                                   [LeadController::class, 'store']);
            Route::put('leads/{lead}',                             [LeadController::class, 'update']);
            Route::patch('leads/{lead}',                           [LeadController::class, 'update']);
            Route::delete('leads/{lead}',                          [LeadController::class, 'destroy']);
            Route::patch('leads/{lead}/status',                    [LeadController::class, 'updateStatus']);
            Route::post('leads/{lead}/notes',                      [LeadController::class, 'addNote']);
            Route::put('leads/{lead}/notes/{note}',                [LeadController::class, 'editNote']);
            Route::delete('leads/{lead}/notes/{note}',             [LeadController::class, 'deleteNote']);

            Route::post('expeditions',                                             [ExpeditionController::class, 'store']);
            Route::put('expeditions/{expedition}',                                 [ExpeditionController::class, 'update']);
            Route::patch('expeditions/{expedition}',                               [ExpeditionController::class, 'update']);
            Route::delete('expeditions/{expedition}',                              [ExpeditionController::class, 'destroy']);
            Route::patch('expeditions/{expedition}/status',                        [ExpeditionController::class, 'updateStatus']);
            Route::post('expeditions/{expedition}/participants',                   [ExpeditionController::class, 'addParticipant']);
            Route::delete('expeditions/{expedition}/participants/{participantId}', [ExpeditionController::class, 'removeParticipant']);
        });

        // Checklist Items
        Route::get('checklist-items',                    [ChecklistItemController::class, 'index']);
        Route::get('checklist-items/{checklistItem}',    [ChecklistItemController::class, 'show']);
        Route::get('expeditions/{expedition}/checklist', [ChecklistItemController::class, 'getByExpedition']);

        Route::middleware(['role:ADMIN,OPERATOR', 'throttle:30,1'])->group(function () {
            Route::post('checklist-items',                         [ChecklistItemController::class, 'store']);
            Route::put('checklist-items/{checklistItem}',          [ChecklistItemController::class, 'update']);
            Route::patch('checklist-items/{checklistItem}',        [ChecklistItemController::class, 'update']);
            Route::delete('checklist-items/{checklistItem}',       [ChecklistItemController::class, 'destroy']);
            Route::patch('checklist-items/{checklistItem}/toggle', [ChecklistItemController::class, 'toggleStatus']);
        });

        // Media Bank
        Route::get('media',                          [MediaController::class, 'index']);
        Route::get('media/{media}',                  [MediaController::class, 'show']);
        Route::get('expeditions/{expedition}/media', [MediaController::class, 'getByExpedition']);

        Route::middleware(['role:ADMIN,OPERATOR', 'throttle:30,1'])->group(function () {
            Route::post('media',             [MediaController::class, 'store']);
            Route::put('media/{media}',      [MediaController::class, 'update']);
            Route::patch('media/{media}',    [MediaController::class, 'update']);
            Route::delete('media/{media}',   [MediaController::class, 'destroy']);
            Route::post('media/bulk-upload', [MediaController::class, 'bulkUpload']);
        });

        // Traveler Portal
        Route::get('traveler-portal/{travelerId}',           [TravelerPortalController::class, 'getData']);
        Route::get('traveler-portal/{travelerId}/itinerary', [TravelerPortalController::class, 'getItinerary']);
        Route::get('traveler-portal/{travelerId}/documents', [TravelerPortalController::class, 'getDocuments']);

    }); // end supabase.auth
});
