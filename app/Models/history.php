<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class history extends Model
{
    use HasFactory;
    protected $table = 'historys';
    protected $fillable = [
        'history',
        'status',
        'bentuk_pembayaran',
        'koordinat pembeli', 
        'koordinat pedagang', 
    ];

    // Relasi ke tabel pembelis
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli');
    }

    // Relasi ke tabel pedagangs
    public function pedagang()
    {
        return $this->belongsTo(Pedagang::class, 'id_pedagang');
    }
}
