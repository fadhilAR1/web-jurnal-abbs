<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterEkskulController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ekskuls = \App\Models\MasterEkskul::all();
        return view('admin.master.ekskul.index', compact('ekskuls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.master.ekskul.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_ekskul' => 'required|string|max:255',
            'kapasitas' => 'nullable|integer|min:0',
        ]);

        \App\Models\MasterEkskul::create($request->only(['nama_ekskul', 'kapasitas']));

        return redirect()->route('admin.master-ekskul.index')
            ->with('success', 'Ekskul berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ekskul = \App\Models\MasterEkskul::with('participants.siswa')->findOrFail($id);
        return view('admin.master.ekskul.show', compact('ekskul'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ekskul = \App\Models\MasterEkskul::findOrFail($id);
        return view('admin.master.ekskul.edit', compact('ekskul'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_ekskul' => 'required|string|max:255',
            'kapasitas' => 'nullable|integer|min:0',
        ]);

        $ekskul = \App\Models\MasterEkskul::findOrFail($id);
        $ekskul->update($request->only(['nama_ekskul', 'kapasitas']));

        return redirect()->route('admin.master-ekskul.index')
            ->with('success', 'Ekskul berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ekskul = \App\Models\MasterEkskul::findOrFail($id);

        // remove dependent records first to avoid foreign key constraint errors
        \DB::transaction(function () use ($ekskul) {
            \App\Models\JurnalEkskul::where('ekskul_id', $ekskul->id)->delete();
            \App\Models\SiswaEkstrakurikuler::where('ekstrakurikuler_id', $ekskul->id)->delete();
            $ekskul->delete();
        });

        return redirect()->route('admin.master-ekskul.index')
            ->with('success', 'Ekskul dan data terkait berhasil dihapus');
    }

    /**
     * Show peserta management for a specific ekskul
     */
    public function participants(string $id)
    {
        $ekskul = \App\Models\MasterEkskul::findOrFail($id);

        // current peserta mappings
        $participants = \App\Models\SiswaEkstrakurikuler::where('ekstrakurikuler_id', $id)
            ->with(['siswa.kelas'])
            ->get();

        $participantIds = $participants->pluck('siswa_id')->toArray();

        // available siswa not yet in this ekskul
        $availableSiswa = \App\Models\Siswa::when(count($participantIds) > 0, function($q) use ($participantIds) {
            return $q->whereNotIn('id', $participantIds);
        })->with('kelas')->orderBy('nama_siswa')->get();

        return view('admin.master.ekskul.participants', compact('ekskul', 'participants', 'availableSiswa'));
    }

    /**
     * Add peserta to ekskul
     */
    public function storeParticipants(\Illuminate\Http\Request $request, string $id)
    {
        $request->validate([
            'siswa_id' => 'required|array',
            'siswa_id.*' => 'exists:siswas,id',
        ]);

        // enforce kapasitas if set
        $ekskul = \App\Models\MasterEkskul::findOrFail($id);
        $currentCount = \App\Models\SiswaEkstrakurikuler::where('ekstrakurikuler_id', $id)->count();
        $toAddCount = count($request->siswa_id);

        $kapasitas = (int) ($ekskul->kapasitas ?? 0);
        if ($kapasitas > 0 && ($currentCount + $toAddCount) > $kapasitas) {
            return redirect()->route('admin.master-ekskul.participants', $id)
                ->withInput()
                ->withErrors(['kapasitas' => "Tidak bisa menambahkan peserta: kapasitas ({$kapasitas}) akan terlampaui."]);
        }

        foreach ($request->siswa_id as $siswaId) {
            \App\Models\SiswaEkstrakurikuler::firstOrCreate([
                'ekstrakurikuler_id' => $id,
                'siswa_id' => $siswaId,
            ]);
        }

        return redirect()->route('admin.master-ekskul.participants', $id)->with('success', 'Peserta berhasil ditambahkan');
    }

    /**
     * Remove peserta from ekskul
     */
    public function destroyParticipant(string $id, string $siswa)
    {
        $row = \App\Models\SiswaEkstrakurikuler::where('ekstrakurikuler_id', $id)->where('siswa_id', $siswa)->first();
        if ($row) {
            $row->delete();
        }

        return redirect()->route('admin.master-ekskul.participants', $id)->with('success', 'Peserta berhasil dihapus');
    }

    public function export()
    {
        $ekskuls = \App\Models\MasterEkskul::all();
        
        $filename = 'master_ekskul_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        return response()->stream(function () use ($ekskuls) {
            $handle = fopen('php://output', 'w');
            
            // CSV header
            fputcsv($handle, ['ID', 'Nama Ekskul', 'Kapasitas']);
            
            // CSV data
            foreach ($ekskuls as $ekskul) {
                fputcsv($handle, [
                    $ekskul->id,
                    $ekskul->nama_ekskul,
                    $ekskul->kapasitas ?? '-'
                ]);
            }
            
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
