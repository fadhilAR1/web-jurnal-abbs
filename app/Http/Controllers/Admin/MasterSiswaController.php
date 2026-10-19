<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MasterSiswaController extends Controller
{
    public function index()
    {
        $siswas = Siswa::latest()->paginate(12);
        return view('admin.siswa.index', compact('siswas'));
    }

    public function create()
    {
        return view('admin.siswa.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'nis' => 'nullable|string|max:50|unique:siswas,nis',
        ]);

        Siswa::create($validated);

        return redirect()->route('admin.master-siswa.index')->with('success', 'Siswa berhasil ditambahkan');
    }

    public function show($id)
    {
        $siswa = Siswa::findOrFail($id);
        return view('admin.siswa.show', compact('siswa'));
    }

    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        return view('admin.siswa.edit', compact('siswa'));
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'nis' => 'nullable|string|max:50|unique:siswas,nis,' . $siswa->id,
        ]);

        $siswa->update($validated);

        return redirect()->route('admin.master-siswa.index')->with('success', 'Siswa berhasil diperbarui');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();
        return redirect()->route('admin.master-siswa.index')->with('success', 'Siswa berhasil dihapus');
    }

    public function export()
    {
        $siswas = Siswa::all();

        $filename = 'siswas_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response()->stream(function () use ($siswas) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Nama Siswa', 'Jenis Kelamin', 'NIS', 'Kelas']);

            foreach ($siswas as $s) {
                fputcsv($handle, [
                    $s->id,
                    $s->nama_siswa,
                    $s->jenis_kelamin ?? '-',
                    $s->nis ?? '-',
                    $s->kelas?->nama_kelas ?? '-',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
