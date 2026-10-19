<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterMapel extends Model
{
    protected $table = 'master_mapels';
    protected $fillable = ['nama_mapel', 'kode_mapel', 'jenjang', 'program'];
}
