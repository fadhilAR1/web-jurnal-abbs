<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterKelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kelas = \App\Models\MasterKelas::all();
        $tingkatList = ['X', 'XI', 'XII'];
        $programList = ['Cambridge', 'ICT', 'Tahfidz'];
        return view('admin.master.kelas.index', compact('kelas', 'tingkatList', 'programList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tingkatList = ['X', 'XI', 'XII'];
        $programList = ['Cambridge', 'ICT', 'Tahfidz'];
        return view('admin.master.kelas.create', compact('tingkatList', 'programList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tingkat' => 'required|string|max:255',
            'jurusan' => 'required|string|max:255',
            'program' => 'required|string|in:Cambridge,ICT,Tahfidz',
            'kapasitas' => 'nullable|integer|min:1|max:40',
        ]);

        \App\Models\MasterKelas::create($request->only(['nama_kelas','tingkat','jurusan','program','kapasitas']));

        return redirect()->route('admin.master-kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kelas = \App\Models\MasterKelas::findOrFail($id);
        $students = \App\Models\Siswa::where('kelas_id', $id)->orderBy('nama_siswa')->get();
        return view('admin.master.kelas.show', compact('kelas', 'students'));
    }

    /**
     * Display students management for a specific class.
     */
    public function students(string $id)
    {
        $kelas = \App\Models\MasterKelas::findOrFail($id);

        $students = \App\Models\Siswa::where('kelas_id', $id)->orderBy('nama_siswa')->get();

        // available siswa not yet assigned to any kelas
        $availableSiswa = \App\Models\Siswa::whereNull('kelas_id')->orderBy('nama_siswa')->get();

        return view('admin.master.kelas.students', compact('kelas', 'students', 'availableSiswa'));
    }

    /**
     * Add students to kelas (assign existing students or create new ones)
     */
    public function storeStudents(Request $request, string $id)
    {
        // If siswa_id[] is provided, assign existing siswa to this kelas (similar to ekskul participants)
        if ($request->has('siswa_id')) {
            $request->validate([
                'siswa_id' => 'required|array',
                'siswa_id.*' => 'exists:siswas,id',
            ]);

            $kelas = \App\Models\MasterKelas::findOrFail($id);

            foreach ($request->siswa_id as $siswaId) {
                $s = \App\Models\Siswa::find($siswaId);
                if ($s) {
                    $s->kelas_id = $id;
                    $s->save();
                }
            }

            return redirect()->route('admin.master-kelas.students', $id)->with('success', 'Siswa berhasil ditambahkan ke kelas.');
        }

        // Fallback: create a new siswa and assign to kelas (backwards compatibility)
        $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'nis' => 'nullable|string|max:50|unique:siswas,nis',
        ]);

        $kelas = \App\Models\MasterKelas::findOrFail($id);

        \App\Models\Siswa::create([
            'nama_siswa' => $request->nama_siswa,
            'jenis_kelamin' => $request->jenis_kelamin,
            'nis' => $request->nis,
            'kelas_id' => $id,
        ]);

        return redirect()->route('admin.master-kelas.students', $id)->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }

    /**
     * Remove student from kelas (unassign)
     */
    public function destroyStudent(string $id, string $siswa)
    {
        $s = \App\Models\Siswa::find($siswa);
        if ($s && $s->kelas_id == $id) {
            try {
                // Instead of deleting the siswa record, unassign from kelas
                $s->kelas_id = null;
                $s->save();
            } catch (\Exception $e) {
                Log::error('Failed to unassign siswa from kelas', ['siswa' => $siswa, 'kelas' => $id, 'error' => $e->getMessage()]);
                return redirect()->route('admin.master-kelas.students', $id)
                    ->with('error', 'Gagal menghapus siswa: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.master-kelas.students', $id)->with('success', 'Siswa berhasil dihapus dari kelas.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $kelas = \App\Models\MasterKelas::findOrFail($id);
        $tingkatList = ['X', 'XI', 'XII'];
        $programList = ['Cambridge', 'ICT', 'Tahfidz'];
        return view('admin.master.kelas.edit', compact('kelas', 'tingkatList', 'programList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'tingkat' => 'required|string|max:255',
            'jurusan' => 'required|string|max:255',
            'program' => 'required|string|in:Cambridge,ICT,Tahfidz',
            'kapasitas' => 'nullable|integer|min:1|max:40',
        ]);

        $kelas = \App\Models\MasterKelas::findOrFail($id);
        $kelas->update($request->only(['nama_kelas','tingkat','jurusan','program','kapasitas']));

        return redirect()->route('admin.master-kelas.index')
            ->with('success', 'Kelas berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kelas = \App\Models\MasterKelas::findOrFail($id);
        $kelas->delete();

        return redirect()->route('admin.master-kelas.index')
            ->with('success', 'Kelas berhasil dihapus');
    }

    public function export()
    {
        $kelas = \App\Models\MasterKelas::all();
        
        $filename = 'master_kelas_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        return response()->stream(function () use ($kelas) {
            $handle = fopen('php://output', 'w');
            
            // CSV header
            fputcsv($handle, ['ID', 'Nama Kelas', 'Tingkat', 'Jurusan', 'Program', 'Kapasitas']);
            
            // CSV data
            foreach ($kelas as $k) {
                fputcsv($handle, [
                    $k->id,
                    $k->nama_kelas,
                    $k->tingkat,
                    $k->jurusan,
                    $k->program,
                    $k->kapasitas ?? '-'
                ]);
            }
            
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
