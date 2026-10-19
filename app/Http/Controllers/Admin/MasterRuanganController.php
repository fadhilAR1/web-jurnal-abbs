<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterRuangan;
use Illuminate\Http\Request;

class MasterRuanganController extends Controller
{
    public function index()
    {
        $ruangan = MasterRuangan::latest()->paginate(10);
        return view('admin.master.ruangan.index', compact('ruangan'));
    }

    public function create()
    {
        return view('admin.master.ruangan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_ruangan' => 'required|unique:master_ruangan',
            'nama_ruangan' => 'required',
            'jenis' => 'required',
        ]);

        MasterRuangan::create($request->all());

        return redirect()->route('admin.master-ruangan.index')
            ->with('success', 'Ruangan berhasil ditambahkan');
    }

    public function edit(MasterRuangan $masterRuangan)
    {
        return view('admin.master.ruangan.edit', compact('masterRuangan'));
    }

    public function update(Request $request, MasterRuangan $masterRuangan)
    {
        $request->validate([
            'kode_ruangan' => 'required|unique:master_ruangan,kode_ruangan,' . $masterRuangan->id,
            'nama_ruangan' => 'required',
            'jenis' => 'required',
        ]);

        $masterRuangan->update($request->all());

        return redirect()->route('admin.master-ruangan.index')
            ->with('success', 'Ruangan berhasil diupdate');
    }

    public function destroy(MasterRuangan $masterRuangan)
    {
        $masterRuangan->delete();

        return redirect()->route('admin.master-ruangan.index')
            ->with('success', 'Ruangan berhasil dihapus');
    }
}