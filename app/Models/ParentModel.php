<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    protected $table = 'students'; // memakai tabel students

    protected $fillable = [
        'nama_ayah',
        'nama_ibu',
        'nomor_telepon_ortu',
        'parent_user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id_parent');
    }

    public function getNamaLengkapAttribute()
    {
        return $this->nama_ayah ?? $this->nama_ibu ?? 'Orang Tua Siswa';
    }
}
