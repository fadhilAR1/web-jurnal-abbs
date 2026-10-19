<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SiswaEkstrakurikuler;

class MasterEkskul extends Model
{
    protected $table = 'master_ekskul';
    protected $fillable = ['nama_ekskul', 'kapasitas'];

    public function participants()
    {
        return $this->hasMany(SiswaEkstrakurikuler::class, 'ekstrakurikuler_id');
    }
}
