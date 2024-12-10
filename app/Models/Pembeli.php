<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembeli extends Model
{
    use HasFactory;

    protected $table = 'pembelis'; // Nama tabel dalam database

    // Kolom yang bisa diisi secara massal
    protected $fillable = [
        'nama',
        'telfon',
        'alamat',
        'foto',
        'latitude',
        'longtitude',
        'user_id', 
    ];

    // Relasi ke model User, jika diperlukan
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
