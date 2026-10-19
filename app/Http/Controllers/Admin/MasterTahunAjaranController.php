<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterTahunAjaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahunAjarans = \App\Models\MasterTahunAjaran::all();
        return view('admin.master.tahun-ajaran.index', compact('tahunAjarans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.master.tahun-ajaran.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|max:20|unique:master_tahun_ajarans',
        ]);

        \App\Models\MasterTahunAjaran::create([
            'tahun_ajaran' => $request->tahun_ajaran,
            'is_active' => false,
        ]);

        return redirect()->route('admin.master-tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tahunAjaran = \App\Models\MasterTahunAjaran::findOrFail($id);
        return view('admin.master.tahun-ajaran.edit', compact('tahunAjaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'tahun_ajaran' => 'required|string|max:20|unique:master_tahun_ajarans,tahun_ajaran,' . $id,
        ]);

        $tahunAjaran = \App\Models\MasterTahunAjaran::findOrFail($id);
        $tahunAjaran->update($request->only('tahun_ajaran'));

        return redirect()->route('admin.master-tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tahunAjaran = \App\Models\MasterTahunAjaran::findOrFail($id);
        if ($tahunAjaran->is_active) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus tahun ajaran yang sedang aktif');
        }
        $tahunAjaran->delete();

        return redirect()->route('admin.master-tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus');
    }

    public function setActive(\App\Models\MasterTahunAjaran $master_tahun_ajaran)
    {
        // Set all to inactive
        \App\Models\MasterTahunAjaran::where('is_active', true)->update(['is_active' => false]);

        // Set this one to active
        $master_tahun_ajaran->update(['is_active' => true]);

        return redirect()->back()->with('success', 'Tahun ajaran berhasil diaktifkan');
    }
}
