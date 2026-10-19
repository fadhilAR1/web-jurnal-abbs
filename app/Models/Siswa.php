<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswas';

    protected $fillable = [
        'nama_siswa',
        'jenis_kelamin',
        'nis',
        'kelas_id',
    ];

    public function kelas()
    {
        return $this->belongsTo(MasterKelas::class, 'kelas_id');
    }
}
