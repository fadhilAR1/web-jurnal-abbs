<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaEkstrakurikuler extends Model
{
    protected $table = 'siswa_ekstrakurikuler';
    protected $fillable = [
        'ekstrakurikuler_id',
        'siswa_id',
    ];

    public function ekstrakurikuler()
    {
        return $this->belongsTo(MasterEkskul::class, 'ekstrakurikuler_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
