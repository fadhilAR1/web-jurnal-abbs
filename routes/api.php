<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JurnalApiController;

Route::middleware('auth:sanctum')->group(function () {
    // Jurnal APIs
    Route::get('/jurnal/kbm', [JurnalApiController::class, 'getKBM']);
    Route::post('/jurnal/kbm', [JurnalApiController::class, 'storeKBM']);
    Route::get('/jurnal/ekskul', [JurnalApiController::class, 'getEkskul']);
    Route::post('/jurnal/ekskul', [JurnalApiController::class, 'storeEkskul']);
});

// Public APIs (read-only)
Route::get('/kalender-kegiatan', function () {
    return \App\Models\JurnalKegiatan::where('status', 'published')
        ->select('id', 'judul', 'tanggal', 'lokasi')
        ->get();
});