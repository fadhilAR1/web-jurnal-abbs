<?php

use Illuminate\Support\Facades\Route;

// Test route
Route::get('/test-db', function () {
    try {
        $users = \App\Models\User::count();
        $sessions = DB::table('sessions')->count();
        return response()->json([
            'status' => 'success',
            'users' => $users,
            'sessions' => $sessions,
            'message' => 'Database connection is working!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

Route::get('/test-session', function () {
    try {
        session(['test' => 'value']);
        return response()->json([
            'status' => 'success',
            'session_value' => session('test'),
            'message' => 'Session is working!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});
