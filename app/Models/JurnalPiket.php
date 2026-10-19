<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalPiket extends Model
{
    use HasFactory;

    protected $table = 'jurnal_piket';

    protected $fillable = [
        'guru_id',
        'hari',
        'tanggal',
        'guru_tidak_hadir',
        'jumlah_siswa_tidak_hadir',
        'siswa_tidak_hadir_detail',
        'tamu_sekolah',
        'kejadian_penting',
        'catatan',
        'status',
    ];

    protected $casts = [
        'guru_tidak_hadir' => 'array',
        'siswa_tidak_hadir_detail' => 'array',
        'tamu_sekolah' => 'array',
        'kejadian_penting' => 'array',
        'tanggal' => 'date',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}