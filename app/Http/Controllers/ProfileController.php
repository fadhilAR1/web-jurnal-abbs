<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\BiodataGuru;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $biodata = $user->biodata;
        
        return view('profile.edit', compact('biodata'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'nip' => 'nullable|string|max:20',
            'jabatan' => 'required|string|max:100',
            'no_hp' => 'required|string|max:15',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'required|string',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $data = $request->only([
            'nip', 'jabatan', 'no_hp', 'tempat_lahir', 'tanggal_lahir', 
            'alamat', 'pendidikan_terakhir'
        ]);
        
        // Handle file upload
        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($user->biodata && $user->biodata->foto) {
                Storage::delete($user->biodata->foto);
            }
            
            $path = $request->file('foto')->store('profile-photos', 'public');
            $data['foto'] = $path;
        }
        
        // Update or create biodata
        if ($user->biodata) {
            $user->biodata()->update($data);
        } else {
            $user->biodata()->create($data);
        }
        
        // Mark profile as completed
        if (!$user->profile_completed) {
            $user->update(['profile_completed' => true]);
        }
        
        return redirect()->route('profile.edit')
            ->with('success', 'Profil berhasil diperbarui');
    }
}