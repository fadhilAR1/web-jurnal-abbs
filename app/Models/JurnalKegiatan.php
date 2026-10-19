<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalKegiatan extends Model
{
    use HasFactory;

    protected $table = 'jurnal_kegiatan';

    protected $fillable = [
        'guru_id',
        'jenis_kegiatan',
        'nama_kegiatan',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'peserta',
        'deskripsi',
        'foto_kegiatan',
        'dokumentasi',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'peserta' => 'array',
        'foto_kegiatan' => 'array',
        'dokumentasi' => 'array',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}