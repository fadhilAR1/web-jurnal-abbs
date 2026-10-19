<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKelas extends Model
{
    protected $table = 'master_kelas';
    protected $fillable = ['nama_kelas', 'tingkat', 'jurusan', 'program', 'kapasitas'];
}
