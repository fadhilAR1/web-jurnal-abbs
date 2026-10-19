<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JurnalKBM;
use App\Models\JurnalEkskul;
use App\Models\JurnalPiket;
use App\Models\JurnalKegiatan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard');
        }
        
        // Admin dashboard dengan data GLOBAL (semua guru)
        $today = now()->format('Y-m-d');
        
        $jurnalKBMCount = JurnalKBM::whereDate('tanggal', $today)->count();
        $jurnalEkskulCount = JurnalEkskul::whereDate('tanggal', $today)->count();
        $jurnalPiketCount = JurnalPiket::whereDate('tanggal', $today)->count();
        $jurnalKegiatanCount = JurnalKegiatan::whereDate('tanggal', $today)->count();
        
        $stats = [
            'total_guru' => User::where('role', 'guru')->count(),
            // Count jurnal hari ini dari SEMUA guru (global)
            'active_jurnals' => $jurnalKBMCount + $jurnalEkskulCount + $jurnalPiketCount + $jurnalKegiatanCount,
            'jurnalKBMCount' => $jurnalKBMCount,
            'jurnalEkskulCount' => $jurnalEkskulCount,
            'jurnalPiketCount' => $jurnalPiketCount,
            'jurnalKegiatanCount' => $jurnalKegiatanCount,
        ];
        
        // Recent activities dari 1 minggu terakhir (global)
        $week = now()->subWeek();
        
        $kbmActivities = JurnalKBM::where(function($q) use ($week) {
            $q->where('created_at', '>=', $week)->orWhere('updated_at', '>=', $week);
        })->orderBy('updated_at', 'desc')->get();

        $ekskulActivities = JurnalEkskul::where(function($q) use ($week) {
            $q->where('created_at', '>=', $week)->orWhere('updated_at', '>=', $week);
        })->orderBy('updated_at', 'desc')->get();

        $piketActivities = JurnalPiket::where(function($q) use ($week) {
            $q->where('created_at', '>=', $week)->orWhere('updated_at', '>=', $week);
        })->orderBy('updated_at', 'desc')->get();

        $kegiatanActivities = JurnalKegiatan::where(function($q) use ($week) {
            $q->where('created_at', '>=', $week)->orWhere('updated_at', '>=', $week);
        })->orderBy('updated_at', 'desc')->get();
        
        $recentActivities = $kbmActivities
            ->merge($ekskulActivities)
            ->merge($piketActivities)
            ->merge($kegiatanActivities)
            ->sortByDesc(function($item) {
                return $item->updated_at ?? $item->created_at;
            })
            ->take(10);
        
        return view('admin.dashboard', compact('stats', 'recentActivities'));
    }
    
    public function users()
    {
        $users = User::where('role', '!=', 'admin')->latest()->paginate(10);
            
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function showUser(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Auto-set role as 'guru' - password stays plaintext for guru
        $validated['role'] = 'guru';

        User::create($validated);

        return redirect()->route('admin.users')
            ->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (!empty($validated['password'])) {
            // Keep password as plaintext for guru
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users')
            ->with('success', 'Pengguna berhasil diperbarui');
    }
    


    public function monitoringKBM()
    {
        // Redirect to the Guru KBM page so admin sees the exact same UI and interactions
        return redirect()->route('jurnal.kbm', request()->query());
    }

    public function monitoringEkskul()
    {
        // Reuse Guru Ekskul page
        return redirect()->route('jurnal.ekskul', request()->query());
    }

    public function monitoringPiket()
    {
        // Reuse Guru Piket page
        return redirect()->route('jurnal.piket', request()->query());
    }

    public function monitoringKegiatan()
    {
        // Reuse Guru Kegiatan page
        return redirect()->route('jurnal.kegiatan', request()->query());
    }

    public function editJurnalKBM($id)
    {
        // Redirect to the Guru KBM edit page so Admin uses the same edit UI and validation
        return redirect()->route('jurnal.kbm.edit', $id);
    }

    public function updateJurnalKBM(Request $request, $id)
    {
        $jurnal = \App\Models\JurnalKBM::findOrFail($id);
        
        $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'kelas_id' => 'required|exists:master_kelas,id',
            'mapel_id' => 'required|exists:master_mapels,id',
            'materi' => 'required|string',
            'catatan' => 'nullable|string',
            'status' => 'required|in:draft,selesai,dibatalkan',
        ]);
        
        $jurnal->update($request->all());
        
        return redirect()->route('admin.monitoring.kbm')
            ->with('success', 'Jurnal KBM berhasil diperbarui');
    }

    public function destroyUser(User $user)
    {
        // Disable foreign key constraints temporarily
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Delete related records
        $user->biodata()->delete();
        \App\Models\JurnalKBM::where('guru_id', $user->id)->delete();
        \App\Models\JurnalEkskul::where('guru_id', $user->id)->delete();
        \App\Models\JurnalPiket::where('guru_id', $user->id)->delete();
        \App\Models\JurnalKegiatan::where('guru_id', $user->id)->delete();
        
        // Delete the user
        $user->delete();
        
        // Re-enable foreign key constraints
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        return redirect()->back()
            ->with('success', 'Pengguna berhasil dihapus');
    }

    public function exportUsers()
    {
        $users = User::where('role', '!=', 'admin')->get();
        
        $filename = 'users_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        return response()->stream(function () use ($users) {
            $handle = fopen('php://output', 'w');
            
            // CSV header
            fputcsv($handle, ['ID', 'Nama', 'Email', 'Role', 'Tanggal Dibuat']);
            
            // CSV data
            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->nama ?? '-',
                    $user->email,
                    $user->role,
                    $user->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}