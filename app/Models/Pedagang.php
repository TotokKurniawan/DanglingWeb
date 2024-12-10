<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedagang extends Model
{
    use HasFactory;
    protected $table = 'pedagangs';


    protected $fillable = [
        'namaToko',
        'telfon',
        'alamat',
        'foto',
        'status',
        // 'latitude',
        // 'longtitude',
        'user_id' 
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }
    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_pedagang');
    }
}
