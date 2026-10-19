<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterRuangan extends Model
{
    use HasFactory;

    protected $table = 'master_ruangan';

    protected $fillable = [
        'kode_ruangan',
        'nama_ruangan',
        'jenis',
        'kapasitas',
        'fasilitas',
    ];
}