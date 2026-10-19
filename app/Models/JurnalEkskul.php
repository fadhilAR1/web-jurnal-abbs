<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalEkskul extends Model
{
    use HasFactory;

    protected $table = 'jurnal_ekskul';

    protected $fillable = [
        'guru_id',
        'ekskul_id',
        'tanggal',
        // public-facing attributes (jam_*) kept for compatibility
        'jam_mulai',
        'jam_selesai',
        // underlying DB column names
        'waktu_mulai',
        'waktu_selesai',
        'materi',
        'absensi',
        'foto_kegiatan',
        'catatan',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        // cast the real DB columns
        'waktu_mulai' => 'datetime:H:i',
        'waktu_selesai' => 'datetime:H:i',
        'absensi' => 'array',
        'foto_kegiatan' => 'array',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function ekskul()
    {
        return $this->belongsTo(MasterEkskul::class, 'ekskul_id');
    }

    public function getAbsensiDetail()
    {
        if (!$this->absensi || !is_array($this->absensi)) {
            return 'Tidak ada data absensi';
        }

        $hadir = 0;
        $tidakHadir = 0;
        $izin = 0;
        $sakit = 0;

        foreach ($this->absensi as $absensiData) {
            $status = strtolower($absensiData['status'] ?? '');
            switch ($status) {
                case 'hadir':
                    $hadir++;
                    break;
                case 'tidak_hadir':
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

    // Accessors & mutators to map public attributes jam_* to DB columns waktu_*
    public function getJamMulaiAttribute()
    {
        $w = $this->attributes['waktu_mulai'] ?? null;
        return $w ? \Carbon\Carbon::parse($w)->format('H:i') : null;
    }

    public function setJamMulaiAttribute($value)
    {
        $this->attributes['waktu_mulai'] = $value;
    }

    public function getJamSelesaiAttribute()
    {
        $w = $this->attributes['waktu_selesai'] ?? null;
        return $w ? \Carbon\Carbon::parse($w)->format('H:i') : null;
    }

    public function setJamSelesaiAttribute($value)
    {
        $this->attributes['waktu_selesai'] = $value;
    }
}