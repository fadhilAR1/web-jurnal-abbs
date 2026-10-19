<?php

namespace App\Http\Controllers;

use App\Models\JurnalKBM;
use App\Models\JurnalEkskul;
use App\Models\JurnalPiket;
use App\Models\JurnalKegiatan;
use App\Models\MasterKelas;
use App\Models\MasterMapel;
use App\Models\MasterEkskul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\SiswaEkstrakurikuler;
use Illuminate\Support\Facades\Storage;

class JurnalController extends Controller
{
    // ==================== JURNAL KBM ====================
    public function getSiswa($kelasId)
    {
        // Be defensive: accept numeric id or a kelas identifier/name string.
        $kelasIdTrim = is_string($kelasId) ? trim($kelasId) : $kelasId;

        // Try to resolve a MasterKelas model first (by id or by name)
        $kelas = null;
        if (is_numeric($kelasIdTrim)) {
            $kelas = MasterKelas::find((int) $kelasIdTrim);
        } else {
            $kelas = MasterKelas::where('nama_kelas', $kelasIdTrim)
                ->orWhere('nama_kelas', 'like', "%{$kelasIdTrim}%")
                ->first();
        }

        if ($kelas) {
            $siswa = Siswa::where('kelas_id', $kelas->id)->get(['id', 'nama_siswa']);
        } else {
            // Fallback: query directly by provided value (in case callers send raw id-like values)
            $siswa = Siswa::where('kelas_id', $kelasIdTrim)->get(['id', 'nama_siswa']);
        }

        \Log::debug('jurnal.getSiswa', ['input' => $kelasId, 'resolved_kelas_id' => $kelas?->id, 'count' => $siswa->count()]);

        return response()->json($siswa);
    }

    public function getMapelForKelas($kelasId)
    {
        $kelas = MasterKelas::find($kelasId);
        if (!$kelas) {
            return response()->json([], 404);
        }

        $program = $kelas->program;
        $tingkat = $kelas->tingkat;

        $mapelQuery = MasterMapel::query();
        $mapelQuery->where(function($q) use ($program, $tingkat) {
            // program filter: allow null / "Semua" or matching program
            $q->whereNull('program')
              ->orWhere('program', 'Semua');
            if ($program) {
                $q->orWhere('program', $program);
            }

            // jenjang/tingkat filter: allow null / "Semua" or matching tingkat
            $q->where(function($qq) use ($tingkat) {
                $qq->whereNull('jenjang')
                   ->orWhere('jenjang', 'Semua');
                if ($tingkat) {
                    $qq->orWhere('jenjang', $tingkat);
                }
            });
        });

        $mapels = $mapelQuery->orderBy('nama_mapel')->get(['id', 'nama_mapel']);

        return response()->json($mapels);
    }
    
    public function kbm()
    {
        $user = Auth::user();
        $query = JurnalKBM::with(['kelas', 'mapel', 'guru']);
        
        // Show all jurnals for all users
        
        // Filter: single date and kelas
        if (request('tanggal')) {
            $query->whereDate('tanggal', request('tanggal'));
        }

        if (request('kelas_id')) {
            $query->where('kelas_id', request('kelas_id'));
        }

        // Filter by program (kelas.program)
        if (request('program')) {
            $prog = request('program');
            $query->whereHas('kelas', function($q) use ($prog) {
                $q->where('program', $prog);
            });
        }
        
        // Export CSV if requested
        if (request('export') === 'csv') {
            $items = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->get();
            $filename = 'jurnal_kbm_' . now()->format('Ymd_His') . '.csv';

            $callback = function() use ($items) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Tanggal', 'Guru', 'Kelas', 'Mapel', 'Jam Mulai', 'Jam Selesai', 'Materi', 'Catatan', 'Absensi']);
                foreach ($items as $i) {
                    $absensi = is_array($i->absensi) ? json_encode($i->absensi) : ($i->absensi ?? '');
                    fputcsv($out, [
                        $i->tanggal?->format('Y-m-d') ?? $i->tanggal,
                        $i->guru?->name ?? '-',
                        $i->kelas?->nama_kelas ?? '-',
                        $i->mapel?->nama_mapel ?? '-',
                        $i->jam_mulai ?? $i->waktu_mulai ?? '-',
                        $i->jam_selesai ?? $i->waktu_selesai ?? '-',
                        $i->materi ?? '-',
                        $i->catatan ?? '-',
                        $absensi,
                    ]);
                }
                fclose($out);
            };

            return response()->streamDownload($callback, $filename, [
                'Content-Type' => 'text/csv',
            ]);
        }

        $jurnals = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->paginate(10);
        $programList = ['Cambridge', 'ICT', 'Tahfidz'];
        $kelas = MasterKelas::when(request('program'), function($q) { return $q->where('program', request('program')); })->get();
        $mapel = MasterMapel::all();
        
        return view('jurnal.kbm', compact('jurnals', 'kelas', 'mapel', 'programList'));
    }

    public function getKelasByProgram($program)
    {
        $kelas = MasterKelas::where('program', $program)->orderBy('nama_kelas')->get(['id', 'nama_kelas']);
        return response()->json($kelas);
    }
    
    public function storeKBM(Request $request)
    {
        // Normalize time inputs to H:i (strip seconds if present)
        if ($request->filled('jam_mulai')) {
            $request->merge(['jam_mulai' => substr($request->jam_mulai, 0, 5)]);
        }
        if ($request->filled('jam_selesai')) {
            $request->merge(['jam_selesai' => substr($request->jam_selesai, 0, 5)]);
        }

        $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'kelas_id' => 'required|exists:master_kelas,id',
            'mapel_id' => 'required|exists:master_mapels,id',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'materi' => 'required|string',
            'catatan' => 'nullable|string',
        ]);
        
        $absensi = [];
        if ($request->has('absensi')) {
            foreach ($request->absensi as $absensiData) {
                // Handle both array and object formats
                $nama = $absensiData['nama'] ?? null;
                $status = $absensiData['status'] ?? 'Hadir';
                
                if ($nama) {
                    $absensi[] = [
                        'nama' => $nama,
                        'status' => $status,
                    ];
                }
            }
        }
        
        // Validate mapel-program compatibility
        $kelasModel = MasterKelas::find($request->kelas_id);
        $mapelModel = MasterMapel::find($request->mapel_id);
        if ($mapelModel && $mapelModel->program && $mapelModel->program !== 'Semua') {
            if (!$kelasModel || $kelasModel->program !== $mapelModel->program) {
                return redirect()->back()->withInput()->withErrors(['mapel_id' => 'Mata pelajaran tidak sesuai dengan program kelas yang dipilih.']);
            }
        }
        // Validate jenjang compatibility as well
        if ($mapelModel && $mapelModel->jenjang && $mapelModel->jenjang !== 'Semua') {
            if (!$kelasModel || $kelasModel->tingkat !== $mapelModel->jenjang) {
                return redirect()->back()->withInput()->withErrors(['mapel_id' => 'Mata pelajaran tidak sesuai dengan jenjang/tingkat kelas yang dipilih.']);
            }
        }
        
        JurnalKBM::create([
            'guru_id' => Auth::id(),
            'tanggal' => $request->tanggal,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'materi' => $request->materi,
            'absensi' => json_encode($absensi),
            'catatan' => $request->catatan,
        ]);
        
        return redirect()->route('jurnal.kbm')
            ->with('success', 'Jurnal KBM berhasil ditambahkan');
    }
    
    public function editKBM(JurnalKBM $jurnal)
    {
        $user = Auth::user();
        
        // Cek hak akses
        if (!$user->isAdmin() && $user->id != $jurnal->guru_id) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki izin untuk mengedit jurnal ini');
        }
        
        // If AJAX/JSON requested, return JSON (used by modal edit)
        if (request()->expectsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'id' => $jurnal->id,
                'tanggal' => $jurnal->tanggal->format('Y-m-d'),
                'kelas_id' => $jurnal->kelas_id,
                'mapel_id' => $jurnal->mapel_id,
                // ensure time values are H:i (strip seconds if saved as H:i:s)
                'jam_mulai' => $jurnal->jam_mulai ? substr($jurnal->jam_mulai, 0, 5) : null,
                'jam_selesai' => $jurnal->jam_selesai ? substr($jurnal->jam_selesai, 0, 5) : null,
                'materi' => $jurnal->materi,
                'catatan' => $jurnal->catatan,
                'absensi' => $jurnal->absensi,
                'program' => $jurnal->kelas?->program ?? null,
            ]);
        }
        
        // Otherwise return HTML edit page
        $kelas = MasterKelas::all();
        $mapel = MasterMapel::all();
        
        return view('jurnal.kbm-edit', compact('jurnal', 'kelas', 'mapel'));
    }
    
    public function updateKBM(Request $request, JurnalKBM $jurnal)
    {
        $user = Auth::user();
        
        // Cek hak akses
        if (!$user->isAdmin() && $user->id != $jurnal->guru_id) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki izin untuk mengubah jurnal ini');
        }
        
        // Normalize time inputs to H:i (strip seconds if present)
        if ($request->filled('jam_mulai')) {
            $request->merge(['jam_mulai' => substr($request->jam_mulai, 0, 5)]);
        }
        if ($request->filled('jam_selesai')) {
            $request->merge(['jam_selesai' => substr($request->jam_selesai, 0, 5)]);
        }

        $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'kelas_id' => 'required|exists:master_kelas,id',
            'mapel_id' => 'required|exists:master_mapels,id',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'materi' => 'required|string',
            'catatan' => 'nullable|string',
        ]);
        
        $absensi = [];
        if ($request->has('absensi')) {
            foreach ($request->absensi as $absensiData) {
                // Handle both array and object formats
                $nama = $absensiData['nama'] ?? null;
                $status = $absensiData['status'] ?? 'Hadir';
                
                if ($nama) {
                    $absensi[] = [
                        'nama' => $nama,
                        'status' => $status,
                    ];
                }
            }
        }

        // Validate mapel-program compatibility
        $kelasModel = MasterKelas::find($request->kelas_id);
        $mapelModel = MasterMapel::find($request->mapel_id);
        if ($mapelModel && $mapelModel->program && $mapelModel->program !== 'Semua') {
            if (!$kelasModel || $kelasModel->program !== $mapelModel->program) {
                return redirect()->back()->withInput()->withErrors(['mapel_id' => 'Mata pelajaran tidak sesuai dengan program kelas yang dipilih.']);
            }
        }
        // Validate jenjang compatibility as well
        if ($mapelModel && $mapelModel->jenjang && $mapelModel->jenjang !== 'Semua') {
            if (!$kelasModel || $kelasModel->tingkat !== $mapelModel->jenjang) {
                return redirect()->back()->withInput()->withErrors(['mapel_id' => 'Mata pelajaran tidak sesuai dengan jenjang/tingkat kelas yang dipilih.']);
            }
        }

        $jurnal->update([
            'tanggal' => $request->tanggal,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'materi' => $request->materi,
            'absensi' => json_encode($absensi),
            'catatan' => $request->catatan,
        ]);
        
        return redirect()->route('jurnal.kbm')
            ->with('success', 'Jurnal KBM berhasil diperbarui');
    }

    public function showKBM(JurnalKBM $jurnal)
    {
        return view('jurnal.kbm-show', compact('jurnal'));
    }

    public function destroyKBM(JurnalKBM $jurnal)
    {
        $user = Auth::user();
        
        // Cek hak akses
        if (!$user->isAdmin() && $user->id != $jurnal->guru_id) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki izin untuk menghapus jurnal ini');
        }
        
        $jurnal->delete();
        
        return redirect()->route('jurnal.kbm')
            ->with('success', 'Jurnal KBM berhasil dihapus');
    }
    
    // ==================== JURNAL EKSKUL ====================
    public function getSiswaEkskul($ekskulId)
    {
        try {
            $list = SiswaEkstrakurikuler::where('ekstrakurikuler_id', $ekskulId)
                ->with('siswa')
                ->get()
                ->filter(function($se) { return $se->siswa !== null; })
                ->map(function($se) {
                    return [
                        'id' => $se->siswa->id,
                        'nama_siswa' => $se->siswa->nama_siswa,
                    ];
                })->values();

            return response()->json($list);
        } catch (\Throwable $e) {
            \Log::error('getSiswaEkskul error: ' . $e->getMessage(), ['ekskul_id' => $ekskulId]);
            return response()->json(['error' => 'Gagal memuat daftar peserta'], 500);
        }
    }

    public function ekskul()
    {
        $user = Auth::user();
        $query = JurnalEkskul::with(['ekskul', 'guru']);
        
        // Show all jurnals for all users
        
        // Filter: single tanggal
        if (request('tanggal')) {
            $query->whereDate('tanggal', request('tanggal'));
        }
        
        // Export CSV if requested
        if (request('export') === 'csv') {
            $items = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->get();
            $filename = 'jurnal_ekskul_' . now()->format('Ymd_His') . '.csv';

            $callback = function() use ($items) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Tanggal', 'Guru', 'Ekskul', 'Jam Mulai', 'Jam Selesai', 'Materi', 'Catatan', 'Absensi']);
                foreach ($items as $i) {
                    $absensi = is_array($i->absensi) ? json_encode($i->absensi) : ($i->absensi ?? '');
                    fputcsv($out, [
                        $i->tanggal?->format('Y-m-d') ?? $i->tanggal,
                        $i->guru?->name ?? '-',
                        $i->ekskul?->nama_ekskul ?? '-',
                        $i->waktu_mulai ?? $i->jam_mulai ?? '-',
                        $i->waktu_selesai ?? $i->jam_selesai ?? '-',
                        $i->materi ?? '-',
                        $i->catatan ?? '-',
                        $absensi,
                    ]);
                }
                fclose($out);
            };

            return response()->streamDownload($callback, $filename, [
                'Content-Type' => 'text/csv',
            ]);
        }

        $jurnals = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->paginate(10);
        $ekskulList = MasterEkskul::all();
        
        return view('jurnal.ekskul', compact('jurnals', 'ekskulList'));
    }
    
    public function storeEkskul(Request $request)
    {
        // Normalize time inputs to H:i (strip seconds if present)
        if ($request->filled('jam_mulai')) {
            $request->merge(['jam_mulai' => substr($request->jam_mulai, 0, 5)]);
        }
        if ($request->filled('jam_selesai')) {
            $request->merge(['jam_selesai' => substr($request->jam_selesai, 0, 5)]);
        }

        $messages = [
            'foto_kegiatan.max' => 'Tidak bisa lebih dari 5 foto.'
        ];

        $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'ekskul_id' => 'required|exists:master_ekskul,id',
            'materi' => 'required|string',
            'catatan' => 'nullable|string',
            'foto_kegiatan' => 'nullable|array|max:5',
            'foto_kegiatan.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], $messages);
        
        // Handle file upload - normalize path
        $fotoPaths = [];
        if ($request->hasFile('foto_kegiatan')) {
            foreach ($request->file('foto_kegiatan') as $foto) {
                $path = $foto->store('ekskul-photos', 'public');
                // Normalize path: convert backslashes to forward slashes for consistency
                $path = str_replace('\\', '/', $path);
                $fotoPaths[] = $path;
            }
        }

        $absensi = [];
        if ($request->has('absensi')) {
            foreach ($request->absensi as $absensiData) {
                // Handle both array and object formats and include siswa_id when available
                $nama = $absensiData['nama'] ?? null;
                $status = $absensiData['status'] ?? 'hadir';
                $siswaId = $absensiData['siswa_id'] ?? null;

                if ($nama) {
                    $absensi[] = [
                        'siswa_id' => $siswaId,
                        'nama' => $nama,
                        'status' => $status,
                    ];
                }
            }
        }
        JurnalEkskul::create([
            'guru_id' => Auth::id(),
            'ekskul_id' => $request->ekskul_id,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->jam_mulai,
            'waktu_selesai' => $request->jam_selesai,
            'materi' => $request->materi,
            'absensi' => $absensi,
            'foto_kegiatan' => $fotoPaths,
            'catatan' => $request->catatan,
            'status' => 'draft',
        ]);
        
        return redirect()->route('jurnal.ekskul')
            ->with('success', 'Jurnal Ekstrakurikuler berhasil ditambahkan');
    }
    
    public function destroyEkskul(JurnalEkskul $jurnal)
    {
        $user = Auth::user();
        
        // Cek hak akses
        if (!$user->isAdmin() && $user->id != $jurnal->guru_id) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki izin untuk menghapus jurnal ini');
        }
        
        // Delete associated photos
        if ($jurnal->foto_kegiatan && is_array($jurnal->foto_kegiatan)) {
            foreach ($jurnal->foto_kegiatan as $foto) {
                Storage::delete($foto);
            }
        }
        
        $jurnal->delete();
        
        return redirect()->route('jurnal.ekskul')
            ->with('success', 'Jurnal Ekstrakurikuler berhasil dihapus');
    }

    public function editEkskul(JurnalEkskul $jurnal)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $user->id != $jurnal->guru_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $jurnal = $jurnal->load(['guru', 'ekskul']);

        // Prepare public URLs and original paths for existing photos so the edit modal can show previews and allow per-item deletion
        $foto_items = [];
        $foto_paths = is_array($jurnal->foto_kegiatan) ? $jurnal->foto_kegiatan : (is_string($jurnal->foto_kegiatan) ? json_decode($jurnal->foto_kegiatan, true) : []);
        foreach ($foto_paths as $p) {
            if (!$p) continue;
            // Normalize path: replace escaped backslashes
            $p = str_replace('\\', '/', $p);
            // Use asset() helper to generate correct storage URL
            $url = asset('storage/' . $p);
            $foto_items[] = ['path' => $p, 'url' => $url];
        }

        $data = $jurnal->toArray();
        $data['foto_kegiatan_items'] = $foto_items;
        // Format tanggal as Y-m-d to be compatible with <input type="date">
        $data['tanggal'] = $jurnal->tanggal ? $jurnal->tanggal->format('Y-m-d') : null;
        // Ensure jam attributes are present and normalized to H:i (strip seconds if present)
        $data['jam_mulai'] = $jurnal->jam_mulai ? substr($jurnal->jam_mulai, 0, 5) : null;
        $data['jam_selesai'] = $jurnal->jam_selesai ? substr($jurnal->jam_selesai, 0, 5) : null;
        // Include absensi data so edit modal can pre-fill it
        $data['absensi'] = $jurnal->absensi;

        return response()->json($data);
    }

    public function updateEkskul(Request $request, $id)
    {
        $jurnal = JurnalEkskul::findOrFail($id);
        $user = Auth::user();

        // Log debug info for update attempts
        \Log::info('updateEkskul attempt', ['user_id' => $user->id ?? null, 'jurnal_id' => $jurnal->id, 'input' => $request->only(['tanggal','jam_mulai','jam_selesai','ekskul_id','materi'])]);
        
        // Cek hak akses
        if (!$user->isAdmin() && $user->id != $jurnal->guru_id) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki izin untuk mengubah jurnal ini');
        }

        // Normalize time inputs to H:i (strip seconds if present)
        if ($request->filled('jam_mulai')) {
            $request->merge(['jam_mulai' => substr($request->jam_mulai, 0, 5)]);
        }
        if ($request->filled('jam_selesai')) {
            $request->merge(['jam_selesai' => substr($request->jam_selesai, 0, 5)]);
        }

        $messages = [
            'foto_kegiatan.max' => 'Tidak bisa lebih dari 5 foto.'
        ];

        $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'ekskul_id' => 'required|exists:master_ekskul,id',
            'materi' => 'required|string',
            'catatan' => 'nullable|string',
            'foto_kegiatan' => 'nullable|array|max:5',
            'foto_kegiatan.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], $messages);

        // Handle existing photos and removals (do not delete existing photos unless explicitly requested)
        $fotoPaths = is_array($jurnal->foto_kegiatan) ? $jurnal->foto_kegiatan : (is_string($jurnal->foto_kegiatan) ? json_decode($jurnal->foto_kegiatan, true) : []);
        $fotoPaths = $fotoPaths ?: [];

        // Remove selected photos
        $removePhotos = $request->input('remove_photos', []);
        if (is_array($removePhotos) && count($removePhotos) > 0) {
            foreach ($removePhotos as $rp) {
                if (($key = array_search($rp, $fotoPaths)) !== false) {
                    // delete from storage and remove from list
                    Storage::disk('public')->delete($rp);
                    unset($fotoPaths[$key]);
                }
            }
            $fotoPaths = array_values($fotoPaths);
        }

        // Handle new uploads - append to existing - normalize path
        if ($request->hasFile('foto_kegiatan')) {
            foreach ($request->file('foto_kegiatan') as $foto) {
                $path = $foto->store('ekskul-photos', 'public');
                // Normalize path: convert backslashes to forward slashes for consistency
                $path = str_replace('\\', '/', $path);
                $fotoPaths[] = $path;
            }
        }

        // Ensure total photos do not exceed 10
        if (count($fotoPaths) > 10) {
            return redirect()->back()->withInput()->with('error', 'Tidak bisa lebih dari 10 foto.');
        }

        // Handle absensi data
        $absensi = [];
        if ($request->has('absensi')) {
            foreach ($request->absensi as $absensiData) {
                $absensi[] = [
                    'siswa_id' => $absensiData['siswa_id'],
                    'nama' => $absensiData['nama'],
                    'status' => $absensiData['status'],
                ];
            }
        }
        $jurnal->update([
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->jam_mulai,
            'waktu_selesai' => $request->jam_selesai,
            'ekskul_id' => $request->ekskul_id,
            'materi' => $request->materi,
            'absensi' => $absensi,
            'foto_kegiatan' => $fotoPaths,
            'catatan' => $request->catatan,
        ]);

        \Log::info('updateEkskul saved', ['jurnal_id' => $jurnal->id, 'updated' => $jurnal->only(['tanggal','waktu_mulai','waktu_selesai','materi','foto_kegiatan'])]);

        return redirect()->route('jurnal.ekskul')
            ->with('success', 'Jurnal Ekstrakurikuler berhasil diperbarui');
    }

    public function showEkskul(JurnalEkskul $jurnal)
    {
        return view('jurnal.ekskul-show', compact('jurnal'));
    }

    // ==================== JURNAL PIKET ====================
    public function piket()
    {
        $user = Auth::user();
        $query = JurnalPiket::with(['guru']);
        
        // Show all jurnals for all users
        
        // Filter: single tanggal
        if (request('tanggal')) {
            $query->whereDate('tanggal', request('tanggal'));
        }
        
        // Export CSV if requested
        if (request('export') === 'csv') {
            $items = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->get();
            $filename = 'jurnal_piket_' . now()->format('Ymd_His') . '.csv';

            $callback = function() use ($items) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Tanggal', 'Guru', 'Hari', 'Catatan', 'Guru_Tidak_Hadir', 'Tamu_Sekolah']);
                foreach ($items as $i) {
                    $guruTidakHadir = is_array($i->guru_tidak_hadir) ? json_encode($i->guru_tidak_hadir) : ($i->guru_tidak_hadir ?? '');
                    $tamu = is_array($i->tamu_sekolah) ? json_encode($i->tamu_sekolah) : ($i->tamu_sekolah ?? '');
                    fputcsv($out, [
                        $i->tanggal?->format('Y-m-d') ?? $i->tanggal,
                        $i->guru?->name ?? '-',
                        $i->hari ?? '-',
                        $i->catatan ?? '-',
                        $guruTidakHadir,
                        $tamu,
                    ]);
                }
                fclose($out);
            };

            return response()->streamDownload($callback, $filename, [
                'Content-Type' => 'text/csv',
            ]);
        }

        $jurnals = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->paginate(10);
        
        return view('jurnal.piket', compact('jurnals'));
    }
    
    public function storePiket(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'hari' => 'required|string',
            'catatan' => 'nullable|string',
        ]);
        
        // Guru tidak hadir (store name + catatan)
        $guruTidakHadir = [];
        if ($request->has('guru_tidak_hadir')) {
            foreach ($request->guru_tidak_hadir as $guru) {
                if (!empty($guru['nama'])) {
                    $guruTidakHadir[] = [
                        'nama' => $guru['nama'],
                        'catatan' => $guru['catatan'] ?? ($guru['alasan'] ?? '-'),
                    ];
                }
            }
        }

        // Tamu sekolah
        $tamuSekolah = [];
        if ($request->has('tamu_sekolah')) {
            foreach ($request->tamu_sekolah as $tamu) {
                if (!empty($tamu['nama'])) {
                    $tamuSekolah[] = [
                        'nama' => $tamu['nama'],
                        'tujuan' => $tamu['tujuan'] ?? '-',
                    ];
                }
            }
        }
        
        JurnalPiket::create([
            'guru_id' => Auth::id(),
            'hari' => $request->hari,
            'tanggal' => $request->tanggal,
            'guru_tidak_hadir' => json_encode($guruTidakHadir),
            'tamu_sekolah' => json_encode($tamuSekolah),
            'catatan' => $request->catatan,
            'status' => 'draft',
        ]);
        
        return redirect()->route('jurnal.piket')
            ->with('success', 'Jurnal Piket berhasil ditambahkan');
    }
    
    public function destroyPiket(JurnalPiket $jurnal)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin() && $user->id != $jurnal->guru_id) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki izin untuk menghapus jurnal ini');
        }
        
        $jurnal->delete();
        
        return redirect()->route('jurnal.piket')
            ->with('success', 'Jurnal Piket berhasil dihapus');
    }

    public function editPiket(Request $request, JurnalPiket $jurnal)
    {
        $user = Auth::user();
        
        // Allow Admin or the guru who created the jurnal to edit
        if (!$user->isAdmin() && $user->id != $jurnal->guru_id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengedit jurnal ini');
        }

        // If AJAX/JSON requested, return JSON (used by modal edit)
        if ($request->ajax() || $request->wantsJson()) {
            $jurnal->load(['guru']);
            return response()->json([
                'id' => $jurnal->id,
                'tanggal' => $jurnal->tanggal ? $jurnal->tanggal->format('Y-m-d') : null,
                'hari' => $jurnal->hari,
                'guru_tidak_hadir' => $jurnal->guru_tidak_hadir,
                'tamu_sekolah' => $jurnal->tamu_sekolah,
                'catatan' => $jurnal->catatan,
                'guru' => $jurnal->guru,
            ]);
        }

        // Otherwise return HTML edit page (behaviour like Jurnal KBM)
        $guruTidakHadir = is_array($jurnal->guru_tidak_hadir) ? $jurnal->guru_tidak_hadir : (is_string($jurnal->guru_tidak_hadir) ? json_decode($jurnal->guru_tidak_hadir, true) : []);
        $tamuSekolah = is_array($jurnal->tamu_sekolah) ? $jurnal->tamu_sekolah : (is_string($jurnal->tamu_sekolah) ? json_decode($jurnal->tamu_sekolah, true) : []);

        return view('jurnal.piket-edit', compact('jurnal', 'guruTidakHadir', 'tamuSekolah'));
    }


    public function showPiket(JurnalPiket $jurnal)
    {
        // Detail view for jurnal piket
        return view('jurnal.piket-show', compact('jurnal'));
    }

    public function updatePiket(Request $request, $id)
    {
        $jurnal = JurnalPiket::findOrFail($id);
        $user = Auth::user();
        
        // Cek hak akses
        if (!$user->isAdmin() && $user->id != $jurnal->guru_id) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki izin untuk mengubah jurnal ini');
        }

        $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'hari' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        // Prepare guru tidak hadir
        if ($request->has('guru_tidak_hadir')) {
            $guruTidakHadir = [];
            foreach ($request->guru_tidak_hadir as $guru) {
                if (!empty($guru['nama'])) {
                    $guruTidakHadir[] = [
                        'nama' => $guru['nama'],
                        'catatan' => $guru['catatan'] ?? ($guru['alasan'] ?? '-'),
                    ];
                }
            }
            $guruTidakHadirJson = json_encode($guruTidakHadir);
        } elseif ($request->has('guru_tidak_hadir_cleared')) {
            // explicit clear requested from form
            $guruTidakHadirJson = json_encode([]);
        } else {
            // keep existing
            $guruTidakHadirJson = is_array($jurnal->guru_tidak_hadir) ? json_encode($jurnal->guru_tidak_hadir) : $jurnal->guru_tidak_hadir;
        }

        // Prepare tamu sekolah
        if ($request->has('tamu_sekolah')) {
            $tamuSekolah = [];
            foreach ($request->tamu_sekolah as $tamu) {
                if (!empty($tamu['nama'])) {
                    $tamuSekolah[] = [
                        'nama' => $tamu['nama'],
                        'tujuan' => $tamu['tujuan'] ?? '-',
                    ];
                }
            }
            $tamuSekolahJson = json_encode($tamuSekolah);
        } elseif ($request->has('tamu_sekolah_cleared')) {
            $tamuSekolahJson = json_encode([]);
        } else {
            $tamuSekolahJson = is_array($jurnal->tamu_sekolah) ? json_encode($jurnal->tamu_sekolah) : $jurnal->tamu_sekolah;
        }

        $jurnal->update([
            'tanggal' => $request->tanggal,
            'hari' => $request->hari,
            'guru_tidak_hadir' => $guruTidakHadirJson,
            'tamu_sekolah' => $tamuSekolahJson,
            'catatan' => $request->catatan,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Jurnal Piket berhasil diperbarui']);
        }

        return redirect()->route('jurnal.piket')
            ->with('success', 'Jurnal Piket berhasil diperbarui');
    }

    // ==================== JURNAL KEGIATAN ====================
    public function kegiatan()
    {
        $user = Auth::user();
        $query = JurnalKegiatan::with(['guru']);
        
        // Show all jurnals for all users
        
        // Filter: single tanggal
        if (request('tanggal')) {
            $query->whereDate('tanggal', request('tanggal'));
        }
        
        // Export CSV if requested
        if (request('export') === 'csv') {
            $items = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->get();
            $filename = 'jurnal_kegiatan_' . now()->format('Ymd_His') . '.csv';

            $callback = function() use ($items) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Tanggal', 'Jenis', 'Nama Kegiatan', 'Lokasi', 'Waktu Mulai', 'Waktu Selesai', 'Peserta_Siswa', 'Peserta_Guru', 'Deskripsi', 'Catatan']);
                foreach ($items as $i) {
                    $peserta = is_array($i->peserta) ? $i->peserta : (is_string($i->peserta) ? json_decode($i->peserta, true) : []);
                    $siswaCount = $peserta['siswa'] ?? '-';
                    $guruCount = $peserta['guru'] ?? '-';
                    fputcsv($out, [
                        $i->tanggal?->format('Y-m-d') ?? $i->tanggal,
                        $i->jenis_kegiatan ?? '-',
                        $i->nama_kegiatan ?? '-',
                        $i->lokasi ?? '-',
                        $i->waktu_mulai ?? '-',
                        $i->waktu_selesai ?? '-',
                        $siswaCount,
                        $guruCount,
                        $i->deskripsi ?? '-',
                        $i->catatan ?? '-',
                    ]);
                }
                fclose($out);
            };

            return response()->streamDownload($callback, $filename, [
                'Content-Type' => 'text/csv',
            ]);
        }

        $jurnals = $query->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->paginate(10);
        
        return view('jurnal.kegiatan', compact('jurnals'));
    }
    
    public function storeKegiatan(Request $request)
    {
        $messages = [
            'foto_kegiatan.max' => 'Tidak bisa lebih dari 5 foto.'
        ];

        $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'jenis_kegiatan' => 'required|string',
            'nama_kegiatan' => 'required|string|max:200',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'lokasi' => 'required|string',
            'peserta.siswa' => 'required|integer|min:0',
            'peserta.guru' => 'required|integer|min:0',
            'deskripsi' => 'required|string',
            'foto_kegiatan' => 'nullable|array|max:5',
            'foto_kegiatan.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'dokumentasi.*' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:5120',
        ], $messages);
        
        // Handle foto upload - normalize path to use forward slashes
        $fotoPaths = [];
        if ($request->hasFile('foto_kegiatan')) {
            foreach ($request->file('foto_kegiatan') as $foto) {
                $path = $foto->store('kegiatan-photos', 'public');
                // Normalize path: convert backslashes to forward slashes for consistency
                $path = str_replace('\\', '/', $path);
                $fotoPaths[] = $path;
            }
        }
        
        // Handle dokumentasi upload
        $dokumentasiPaths = [];
        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                $path = $file->store('kegiatan-dokumentasi', 'public');
                $dokumentasiPaths[] = [
                    'nama' => $file->getClientOriginalName(),
                    'path' => $path,
                ];
            }
        }
        
        JurnalKegiatan::create([
            'guru_id' => Auth::id(),
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'nama_kegiatan' => $request->nama_kegiatan,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'lokasi' => $request->lokasi,
            'peserta' => [
                'siswa' => $request->peserta['siswa'],
                'guru' => $request->peserta['guru'],
            ],
            'deskripsi' => $request->deskripsi,
            'foto_kegiatan' => $fotoPaths,
            'dokumentasi' => $dokumentasiPaths,
            'status' => 'draft',
        ]);
        
        return redirect()->route('jurnal.kegiatan')
            ->with('success', 'Jurnal Kegiatan berhasil ditambahkan');
    }
    
    public function showKegiatan(JurnalKegiatan $jurnal)
    {
        return view('jurnal.kegiatan-show', compact('jurnal'));
    }
    
    public function destroyKegiatan(JurnalKegiatan $jurnal)
    {
        $user = Auth::user();
        
        // Cek hak akses
        if (!$user->isAdmin() && $user->id != $jurnal->guru_id) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki izin untuk menghapus jurnal ini');
        }
        
        // Delete associated files (handle both array and JSON-string representations)
        $fotoPaths = is_array($jurnal->foto_kegiatan) ? $jurnal->foto_kegiatan : (is_string($jurnal->foto_kegiatan) ? json_decode($jurnal->foto_kegiatan, true) : []);
        if ($fotoPaths && is_array($fotoPaths)) {
            foreach ($fotoPaths as $foto) {
                Storage::delete($foto);
            }
        }

        $dokumentasiPaths = is_array($jurnal->dokumentasi) ? $jurnal->dokumentasi : (is_string($jurnal->dokumentasi) ? json_decode($jurnal->dokumentasi, true) : []);
        if ($dokumentasiPaths && is_array($dokumentasiPaths)) {
            foreach ($dokumentasiPaths as $doc) {
                Storage::delete($doc['path'] ?? ($doc['path'] ?? null));
            }
        }
        
        $jurnal->delete();
        
        return redirect()->route('jurnal.kegiatan')
            ->with('success', 'Jurnal Kegiatan berhasil dihapus');
    }

    public function editKegiatan(Request $request, JurnalKegiatan $jurnal)
    {
        $user = Auth::user();
        
        // Cek hak akses
        if (!$user->isAdmin() && $user->id != $jurnal->guru_id) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengubah jurnal ini');
        }

        // Jika request AJAX/JSON (dipanggil dari modal), kembalikan JSON dengan semua data yang diperlukan
        if ($request->ajax() || $request->wantsJson()) {
            $fotoKegiatan = is_array($jurnal->foto_kegiatan) ? $jurnal->foto_kegiatan : (is_string($jurnal->foto_kegiatan) ? json_decode($jurnal->foto_kegiatan, true) : []);
            $dokumentasi = is_array($jurnal->dokumentasi) ? $jurnal->dokumentasi : (is_string($jurnal->dokumentasi) ? json_decode($jurnal->dokumentasi, true) : []);
            $peserta = is_array($jurnal->peserta) ? $jurnal->peserta : (is_string($jurnal->peserta) ? json_decode($jurnal->peserta, true) : []);

            // Prepare foto_kegiatan_items with both path and a working URL (/file/ route)
            $foto_items = [];
            $foto_paths = $fotoKegiatan ?: [];
            foreach ($foto_paths as $p) {
                if (!$p) continue;
                $p = str_replace('\\', '/', $p);
                $url = url('/file/' . $p);
                $foto_items[] = ['path' => $p, 'url' => $url];
            }

            return response()->json([
                'id' => $jurnal->id,
                'tanggal' => $jurnal->tanggal ? $jurnal->tanggal->format('Y-m-d') : null,
                'nama_kegiatan' => $jurnal->nama_kegiatan,
                'jenis_kegiatan' => $jurnal->jenis_kegiatan,
                'waktu_mulai' => $jurnal->waktu_mulai ? substr($jurnal->waktu_mulai, 0, 5) : null,
                'waktu_selesai' => $jurnal->waktu_selesai ? substr($jurnal->waktu_selesai, 0, 5) : null,
                'lokasi' => $jurnal->lokasi,
                'peserta' => $peserta,
                'deskripsi' => $jurnal->deskripsi,
                'foto_kegiatan' => $fotoKegiatan,
                'foto_kegiatan_items' => $foto_items,
                'dokumentasi' => $dokumentasi
            ]);
        }

        // Jika request biasa, tampilkan halaman edit
        return view('jurnal.kegiatan-edit', compact('jurnal'));
    }


    public function downloadDokumentasi(JurnalKegiatan $jurnal, $index)
    {
        $dokumentasiPaths = is_array($jurnal->dokumentasi) ? $jurnal->dokumentasi : (is_string($jurnal->dokumentasi) ? json_decode($jurnal->dokumentasi, true) : []);

        if (!isset($dokumentasiPaths[$index])) {
            abort(404);
        }

        $doc = $dokumentasiPaths[$index];
        $path = $doc['path'] ?? ($doc['path'] ?? null);
        $name = $doc['nama'] ?? ($doc['original_name'] ?? basename($path));

        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $fullPath = storage_path('app/public/' . $path);
        return response()->download($fullPath, $name);
    }

    public function updateKegiatan(Request $request, $id)
    {
        $jurnal = JurnalKegiatan::findOrFail($id);
        $user = Auth::user();
        
        // Cek hak akses
        if (!$user->isAdmin() && $user->id != $jurnal->guru_id) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki izin untuk mengubah jurnal ini');
        }

        $messages = [
            'foto_kegiatan.max' => 'Tidak bisa lebih dari 5 foto.'
        ];

        $request->validate([
            'tanggal' => 'required|date|before_or_equal:today',
            'nama_kegiatan' => 'required|string',
            'jenis_kegiatan' => 'required|in:upacara,sosialisasi,pelatihan,perlombaan,lainnya',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'peserta.siswa' => 'required|integer|min:0',
            'peserta.guru' => 'required|integer|min:0',
            'deskripsi' => 'required|string',
            'foto_kegiatan' => 'nullable|array|max:5',
            'foto_kegiatan.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'dokumentasi.*' => 'nullable|mimes:pdf,doc,docx,ppt,pptx|max:5120',
            'catatan' => 'nullable|string',
        ], $messages);

        // Handle existing photos and removals (do not delete existing photos unless explicitly requested)
        $fotoPaths = is_array($jurnal->foto_kegiatan) ? $jurnal->foto_kegiatan : (is_string($jurnal->foto_kegiatan) ? json_decode($jurnal->foto_kegiatan, true) : []);
        $fotoPaths = $fotoPaths ?: [];

        // Remove selected photos
        $removePhotos = $request->input('remove_photos', []);
        if (is_array($removePhotos) && count($removePhotos) > 0) {
            foreach ($removePhotos as $rp) {
                if (($key = array_search($rp, $fotoPaths)) !== false) {
                    // delete from storage and remove from list
                    Storage::disk('public')->delete($rp);
                    unset($fotoPaths[$key]);
                }
            }
            $fotoPaths = array_values($fotoPaths);
        }

        // Handle new uploads - append to existing - normalize path
        if ($request->hasFile('foto_kegiatan')) {
            try {
                foreach ($request->file('foto_kegiatan') as $foto) {
                    $path = $foto->store('kegiatan-photos', 'public');
                    // Normalize path: convert backslashes to forward slashes for consistency
                    $path = str_replace('\\', '/', $path);
                    $fotoPaths[] = $path;
                }
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan foto: ' . $e->getMessage());
            }
        }

        // Ensure total photos do not exceed 10
        if (count($fotoPaths) > 10) {
            return redirect()->back()->withInput()->with('error', 'Tidak bisa lebih dari 10 foto.');
        }

        $dokumentasiPaths = $jurnal->dokumentasi ?? [];

        // Handle removal of individual dokumentasi items (checked in the edit form)
        $removeDocs = $request->input('remove_documents', []);
        if (is_array($removeDocs) && count($removeDocs) > 0 && $dokumentasiPaths && is_array($dokumentasiPaths)) {
            foreach ($removeDocs as $rd) {
                foreach ($dokumentasiPaths as $k => $doc) {
                    $path = $doc['path'] ?? null;
                    if ($path && $path === $rd) {
                        try {
                            Storage::disk('public')->delete($path);
                        } catch (\Exception $e) {
                            \Log::warning('Could not delete dokumentasi (remove_documents): ' . $path . ' - ' . $e->getMessage());
                        }
                        unset($dokumentasiPaths[$k]);
                    }
                }
            }
            $dokumentasiPaths = array_values($dokumentasiPaths);
        }

        if ($request->hasFile('dokumentasi')) {
            // Delete old documents if new ones are uploaded
            if ($jurnal->dokumentasi && is_array($jurnal->dokumentasi)) {
                foreach ($jurnal->dokumentasi as $oldDoc) {
                    try {
                        Storage::delete($oldDoc['path']);
                    } catch (\Exception $e) {
                        \Log::warning('Could not delete old dokumentasi: ' . json_encode($oldDoc) . ' - ' . $e->getMessage());
                    }
                }
            }

            $dokumentasiPaths = [];
            try {
                foreach ($request->file('dokumentasi') as $doc) {
                    $path = $doc->store('kegiatan-docs', 'public');
                    $dokumentasiPaths[] = [
                        'original_name' => $doc->getClientOriginalName(),
                        'path' => $path,
                    ];
                }
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan dokumentasi: ' . $e->getMessage());
            }
        }

        $jurnal->update([
            'tanggal' => $request->tanggal,
            'nama_kegiatan' => $request->nama_kegiatan,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'peserta' => [
                'siswa' => $request->peserta['siswa'],
                'guru' => $request->peserta['guru'],
            ],
            'deskripsi' => $request->deskripsi,
            'foto_kegiatan' => $fotoPaths,
            'dokumentasi' => $dokumentasiPaths,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('jurnal.kegiatan')
            ->with('success', 'Jurnal Kegiatan berhasil diperbarui');
    }
}