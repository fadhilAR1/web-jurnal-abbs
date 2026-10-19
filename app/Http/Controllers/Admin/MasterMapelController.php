<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterMapelController extends Controller
{
    /**
     * Map program abbreviation to full name
     */
    private function getProgramName($abbrev)
    {
        $programs = [
            'CAM' => 'Cambridge',
            'ICT' => 'ICT',
            'TAH' => 'Tahfidz',
        ];
        return $programs[$abbrev] ?? null;
    }

    /**
     * Parse kode_mapel format: PROGRAM-MAPEL-JENJANG
     * Returns array with: program, jenjang
     */
    private function parseKodeMapel($kodeMapel)
    {
        $parts = explode('-', $kodeMapel);
        
        if (count($parts) !== 3) {
            throw new \Exception('Format kode mapel harus: PROGRAM-MAPEL-JENJANG (contoh: CAM-MAT-X)');
        }

        $programAbbrev = strtoupper($parts[0]);
        $jenjang = strtoupper($parts[2]);

        $program = $this->getProgramName($programAbbrev);
        if (!$program) {
            throw new \Exception('Program tidak valid. Gunakan: CAM (Cambridge), ICT, atau TAH (Tahfidz)');
        }

        if (!in_array($jenjang, ['X', 'XI', 'XII'])) {
            throw new \Exception('Jenjang harus: X, XI, atau XII');
        }

        return ['program' => $program, 'jenjang' => $jenjang];
    }

    /**
     * Validate that kode_mapel format matches selected program and jenjang
     */
    private function validateKodeMapelFormat($kodeMapel, $programName, $jenjang)
    {
        $parts = explode('-', $kodeMapel);
        
        if (count($parts) !== 3) {
            throw new \Exception('Format kode mapel harus: SINGKAT_PROGRAM-SINGKAT_MAPEL-JENJANG (contoh: CAM-MAT-X)');
        }

        $programAbbrev = strtoupper($parts[0]);
        $kodedJenjang = strtoupper($parts[2]);

        // Get program abbreviation from full name
        $abbrevMap = [
            'Cambridge' => 'CAM',
            'ICT' => 'ICT',
            'Tahfidz' => 'TAH',
        ];

        $expectedAbbrev = $abbrevMap[$programName] ?? null;
        
        if (!$expectedAbbrev || $programAbbrev !== $expectedAbbrev) {
            throw new \Exception('Singkatan program di kode mapel tidak sesuai. Pilihan program "' . $programName . '" harus menggunakan singkatan "' . $expectedAbbrev . '". Contoh: ' . $expectedAbbrev . '-MAT-X');
        }

        if ($kodedJenjang !== $jenjang) {
            throw new \Exception('Jenjang di kode mapel tidak sesuai dengan pilihan jenjang. Format: PROGRAM-MAPEL-' . $jenjang);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mapels = \App\Models\MasterMapel::all();
        $programList = ['Cambridge', 'ICT', 'Tahfidz'];
        $jenjangList = ['X', 'XI', 'XII'];
        return view('admin.master.mapel.index', compact('mapels', 'programList', 'jenjangList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.master.mapel.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'program' => 'required|in:Cambridge,ICT,Tahfidz',
            'jenjang' => 'required|in:X,XI,XII',
            'kode_mapel' => 'required|string|max:20|unique:master_mapels',
        ]);

        // Check for duplicate combination of nama_mapel, program, and jenjang
        $exists = \App\Models\MasterMapel::where('nama_mapel', $request->nama_mapel)
            ->where('program', $request->program)
            ->where('jenjang', $request->jenjang)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Kombinasi Mata Pelajaran, Program, dan Jenjang ini sudah ada. Setiap mata pelajaran hanya bisa digunakan sekali per jenjang dalam program yang sama.');
        }

        try {
            // Validate kode_mapel format matches selected program and jenjang
            $this->validateKodeMapelFormat($request->kode_mapel, $request->program, $request->jenjang);
            
            \App\Models\MasterMapel::create([
                'nama_mapel' => $request->nama_mapel,
                'kode_mapel' => strtoupper($request->kode_mapel),
                'jenjang' => $request->jenjang,
                'program' => $request->program,
            ]);

            return redirect()->route('admin.master-mapel.index')
                ->with('success', 'Mata pelajaran berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mapel = \App\Models\MasterMapel::findOrFail($id);
        return view('admin.master.mapel.show', compact('mapel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $mapel = \App\Models\MasterMapel::findOrFail($id);
        return view('admin.master.mapel.edit', compact('mapel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'program' => 'required|in:Cambridge,ICT,Tahfidz',
            'jenjang' => 'required|in:X,XI,XII',
            'kode_mapel' => 'required|string|max:20|unique:master_mapels,kode_mapel,' . $id,
        ]);

        // Check for duplicate combination of nama_mapel, program, and jenjang
        $exists = \App\Models\MasterMapel::where('nama_mapel', $request->nama_mapel)
            ->where('program', $request->program)
            ->where('jenjang', $request->jenjang)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Kombinasi Mata Pelajaran, Program, dan Jenjang ini sudah ada. Setiap mata pelajaran hanya bisa digunakan sekali per jenjang dalam program yang sama.');
        }

        try {
            // Validate kode_mapel format matches selected program and jenjang
            $this->validateKodeMapelFormat($request->kode_mapel, $request->program, $request->jenjang);
            
            $mapel = \App\Models\MasterMapel::findOrFail($id);
            $mapel->update([
                'nama_mapel' => $request->nama_mapel,
                'kode_mapel' => strtoupper($request->kode_mapel),
                'jenjang' => $request->jenjang,
                'program' => $request->program,
            ]);

            return redirect()->route('admin.master-mapel.index')
                ->with('success', 'Mata pelajaran berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $mapel = \App\Models\MasterMapel::findOrFail($id);
            $namaMapel = $mapel->nama_mapel;
            $mapel->delete();

            return redirect()->route('admin.master-mapel.index')
                ->with('success', 'Mata pelajaran ' . $namaMapel . ' berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                // Foreign key constraint error
                return redirect()->route('admin.master-mapel.index')
                    ->with('error', 'Tidak bisa menghapus mata pelajaran ini karena masih digunakan di jurnal. Hapus semua jurnal yang menggunakan mata pelajaran ini terlebih dahulu.');
            }
            throw $e;
        }
    }

    public function export()
    {
        $mapels = \App\Models\MasterMapel::all();
        
        $filename = 'master_mapel_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        return response()->stream(function () use ($mapels) {
            $handle = fopen('php://output', 'w');
            
            // CSV header
            fputcsv($handle, ['ID', 'Kode Mapel', 'Nama Mapel', 'Program', 'Jenjang']);
            
            // CSV data
            foreach ($mapels as $mapel) {
                fputcsv($handle, [
                    $mapel->id,
                    $mapel->kode_mapel,
                    $mapel->nama_mapel,
                    $mapel->program,
                    $mapel->jenjang
                ]);
            }
            
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
