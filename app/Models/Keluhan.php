<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keluhan extends Model
{
    use HasFactory;
    protected $table = 'keluhans';
    protected $fillable = [
        'deskripsi', // typo 'deksripsi' diperbaiki menjadi 'deskripsi'
        'rating',
        'id pembeli', // nama kolom diubah untuk mengikuti konvensi penamaan
        'id pedagang', // sama seperti di atas
    ];

    // Relasi ke tabel pembelis
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id pembeli');
    }

    // Relasi ke tabel pedagangs
    public function pedagang()
    {
        return $this->belongsTo(Pedagang::class, 'id pedagang');
    }
}
