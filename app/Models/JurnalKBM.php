<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalKBM extends Model
{
    use HasFactory;

    protected $table = 'jurnal_kbm';

    protected $fillable = [
        'guru_id',
        'kelas_id',
        'mapel_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'materi',
        'absensi',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'absensi' => 'array',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function kelas()
    {
        return $this->belongsTo(MasterKelas::class, 'kelas_id');
    }

    public function mapel()
    {
        return $this->belongsTo(MasterMapel::class, 'mapel_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(MasterTahunAjaran::class, 'master_tahun_ajaran_id');
    }

    public function getAbsensiDetail()
    {
        $absensiData = $this->absensi;
        
        // Decode JSON if it's a string
        if (is_string($absensiData)) {
            $absensiData = json_decode($absensiData, true) ?? [];
        }
        
        if (!$absensiData || !is_array($absensiData)) {
            return 'Tidak ada data absensi';
        }

        $hadir = 0;
        $tidakHadir = 0;
        $izin = 0;
        $sakit = 0;

        foreach ($absensiData as $item) {
            $status = strtolower($item['status'] ?? '');
            switch ($status) {
                case 'hadir':
                    $hadir++;
                    break;
                case 'tidak hadir':
                case 'alpha':
                    $tidakHadir++;
                    break;
                case 'izin':
                    $izin++;
                    break;
                case 'sakit':
                    $sakit++;
                    break;
            }
        }

        return "Hadir: {$hadir}, Sakit: {$sakit}, Izin: {$izin}, Alpha: {$tidakHadir}";
    }
}
