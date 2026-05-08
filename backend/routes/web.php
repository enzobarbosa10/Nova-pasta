<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

// API Info Route
Route::get('/', function () {
    // Check if request expects JSON (API call)
    if (request()->expectsJson() || request()->is('api/*')) {
        return response()->json([
            'message' => 'Expedition Management API',
            'version' => '1.0.0',
            'documentation' => '/api/documentation'
        ]);
    }
    
    // Redirect to dashboard for web requests
    return redirect('/dashboard.php');
});

// Welcome page route
Route::get('/welcome', function () {
    return response()->file(public_path('../../welcome.html'));
});
