<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiodataGuru extends Model
{
    protected $table = 'biodata_guru';

    protected $fillable = [
        'user_id',
        'nip',
        'jabatan',
        'no_hp',
        'alamat',
        'foto',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'pendidikan_terakhir',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
