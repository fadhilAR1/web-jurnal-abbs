<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\MasterKelasController;
use App\Http\Controllers\Admin\MasterMapelController;
use App\Http\Controllers\Admin\MasterEkskulController;
use App\Http\Controllers\Admin\MasterSiswaController;
use App\Http\Controllers\Admin\MasterTahunAjaranController;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

// Serve storage files through Laravel (avoid symlink issues on Windows)
Route::get('/file/{path}', function ($path) {
    // Prevent directory traversal attacks
    $path = str_replace('..', '', $path);
    
    if (!Storage::disk('public')->exists($path)) {
        abort(404, 'File not found');
    }
    
    $file = Storage::disk('public')->get($path);
    $mimeType = Storage::disk('public')->mimeType($path) ?? 'application/octet-stream';
    
    return response($file, 200)
        ->header('Content-Type', $mimeType)
        ->header('Content-Length', strlen($file))
        ->header('Cache-Control', 'public, max-age=3600')
        ->header('Content-Disposition', 'inline; filename=' . basename($path));
})->where('path', '.*');

// Public routes (untuk user biasa/murid/wali murid)
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/public/dashboard', [PublicController::class, 'dashboard'])->name('public.dashboard');
Route::get('/public/jurnal/kbm', [PublicController::class, 'kbm'])->name('public.jurnal.kbm');
Route::get('/public/jurnal/ekskul', [PublicController::class, 'ekskul'])->name('public.jurnal.ekskul');
Route::get('/public/jurnal/piket', [PublicController::class, 'piket'])->name('public.jurnal.piket');
Route::get('/public/jurnal/kegiatan', [PublicController::class, 'kegiatan'])->name('public.jurnal.kegiatan');

// Auth routes
// Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

// Protected routes - For Guru
Route::middleware(['auth'])->group(function () {
    // Guru Profile
    Route::middleware(['guru'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Jurnal routes
    Route::prefix('jurnal')->group(function () {
        // KBM
        Route::get('/kbm', [JurnalController::class, 'kbm'])->name('jurnal.kbm');
        Route::post('/kbm', [JurnalController::class, 'storeKBM'])->name('jurnal.kbm.store');
        Route::get('/kbm/{jurnal}', [JurnalController::class, 'showKBM'])->name('jurnal.kbm.show');
        Route::delete('/kbm/{jurnal}', [JurnalController::class, 'destroyKBM'])->name('jurnal.kbm.destroy');
        Route::get('/kbm/{jurnal}/edit', [JurnalController::class, 'editKBM'])->name('jurnal.kbm.edit');
        Route::put('/kbm/{jurnal}', [JurnalController::class, 'updateKBM'])->name('jurnal.kbm.update');
        Route::get('/get-siswa/{kelasId}', [JurnalController::class, 'getSiswa']);
        Route::get('/get-mapel/{kelasId}', [JurnalController::class, 'getMapelForKelas']);
        Route::get('/get-kelas-by-program/{program}', [JurnalController::class, 'getKelasByProgram']);
        Route::get('/get-siswa-ekskul/{ekskulId}', [JurnalController::class, 'getSiswaEkskul']);
        
        // Ekskul
        Route::get('/ekskul', [JurnalController::class, 'ekskul'])->name('jurnal.ekskul');
        Route::post('/ekskul', [JurnalController::class, 'storeEkskul'])->name('jurnal.ekskul.store');
        Route::get('/ekskul/{jurnal}', [JurnalController::class, 'showEkskul'])->name('jurnal.ekskul.show');
        Route::get('/ekskul/{jurnal}/edit', [JurnalController::class, 'editEkskul'])->name('jurnal.ekskul.edit');
        Route::put('/ekskul/{jurnal}', [JurnalController::class, 'updateEkskul'])->name('jurnal.ekskul.update');
        Route::delete('/ekskul/{jurnal}', [JurnalController::class, 'destroyEkskul'])->name('jurnal.ekskul.destroy');
        
        // Piket
        Route::get('/piket', [JurnalController::class, 'piket'])->name('jurnal.piket');
        Route::post('/piket', [JurnalController::class, 'storePiket'])->name('jurnal.piket.store');
        Route::get('/piket/{jurnal}', [JurnalController::class, 'showPiket'])->name('jurnal.piket.show');
        Route::get('/piket/{jurnal}/edit', [JurnalController::class, 'editPiket'])->name('jurnal.piket.edit');
        Route::put('/piket/{jurnal}', [JurnalController::class, 'updatePiket'])->name('jurnal.piket.update');
        Route::delete('/piket/{jurnal}', [JurnalController::class, 'destroyPiket'])->name('jurnal.piket.destroy');
        
        // Kegiatan
        Route::get('/kegiatan', [JurnalController::class, 'kegiatan'])->name('jurnal.kegiatan');
        Route::post('/kegiatan', [JurnalController::class, 'storeKegiatan'])->name('jurnal.kegiatan.store');
        Route::get('/kegiatan/{jurnal}', [JurnalController::class, 'showKegiatan'])->name('jurnal.kegiatan.show');
        Route::get('/kegiatan/{jurnal}/download/{index}', [JurnalController::class, 'downloadDokumentasi'])->name('jurnal.kegiatan.download');
        Route::get('/kegiatan/{jurnal}/edit', [JurnalController::class, 'editKegiatan'])->name('jurnal.kegiatan.edit');
        Route::put('/kegiatan/{jurnal}', [JurnalController::class, 'updateKegiatan'])->name('jurnal.kegiatan.update');
        Route::delete('/kegiatan/{jurnal}', [JurnalController::class, 'destroyKegiatan'])->name('jurnal.kegiatan.destroy');
    });
});

// Admin routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard Admin
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::get('/users-export', [AdminController::class, 'exportUsers'])->name('admin.users.export');
    // Siswa Management
    Route::resource('/master-siswa', MasterSiswaController::class)->names('admin.master-siswa');
    Route::get('/master-siswa-export', [MasterSiswaController::class, 'export'])->name('admin.master-siswa.export');
    
    // Master Data Management
    Route::resource('/master-kelas', MasterKelasController::class)->names('admin.master-kelas');
    // Routes to manage siswa per kelas
    Route::get('/master-kelas/{id}/siswa', [MasterKelasController::class, 'students'])->name('admin.master-kelas.students');
    Route::post('/master-kelas/{id}/siswa', [MasterKelasController::class, 'storeStudents'])->name('admin.master-kelas.students.store');
    Route::delete('/master-kelas/{id}/siswa/{siswa}', [MasterKelasController::class, 'destroyStudent'])->name('admin.master-kelas.students.destroy');
    Route::get('/master-kelas-export', [MasterKelasController::class, 'export'])->name('admin.master-kelas.export');

    Route::resource('/master-mapel', MasterMapelController::class)->names('admin.master-mapel');
    Route::get('/master-mapel-export', [MasterMapelController::class, 'export'])->name('admin.master-mapel.export');
    Route::resource('/master-ekskul', MasterEkskulController::class)->names('admin.master-ekskul');
    // Additional routes to manage peserta (siswa) for each ekstrakurikuler
    Route::get('/master-ekskul/{id}/peserta', [MasterEkskulController::class, 'participants'])->name('admin.master-ekskul.participants');
    Route::post('/master-ekskul/{id}/peserta', [MasterEkskulController::class, 'storeParticipants'])->name('admin.master-ekskul.participants.store');
    Route::delete('/master-ekskul/{id}/peserta/{siswa}', [MasterEkskulController::class, 'destroyParticipant'])->name('admin.master-ekskul.participants.destroy');
    Route::get('/master-ekskul-export', [MasterEkskulController::class, 'export'])->name('admin.master-ekskul.export');

    Route::resource('/master-tahun-ajaran', MasterTahunAjaranController::class)->names('admin.master-tahun-ajaran');
    Route::post('/master-tahun-ajaran/{master_tahun_ajaran}/set-active', 
        [MasterTahunAjaranController::class, 'setActive'])->name('admin.master-tahun-ajaran.set-active');
    
    // Monitoring Jurnal
    Route::get('/monitoring/kbm', [AdminController::class, 'monitoringKBM'])->name('admin.monitoring.kbm');
    Route::get('/monitoring/ekskul', [AdminController::class, 'monitoringEkskul'])->name('admin.monitoring.ekskul');
    Route::get('/monitoring/piket', [AdminController::class, 'monitoringPiket'])->name('admin.monitoring.piket');
    Route::get('/monitoring/kegiatan', [AdminController::class, 'monitoringKegiatan'])->name('admin.monitoring.kegiatan');
    
    // Edit Jurnal dari Admin - reuse Guru edit/update routes
    Route::get('/monitoring/kbm/{id}/edit', [AdminController::class, 'editJurnalKBM'])->name('admin.monitoring.edit-kbm');
    // Forward admin update to the same controller as Guru to keep validation & behavior identical
    Route::put('/monitoring/kbm/{jurnal}', [\App\Http\Controllers\JurnalController::class, 'updateKBM'])->name('admin.monitoring.update-kbm');
});