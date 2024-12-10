<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class message extends Model
{
    use HasFactory;

    // Tentukan tabel yang digunakan (opsional jika nama tabel sesuai dengan plural dari nama model)
    protected $table = 'messages';

    // Daftar kolom yang dapat diisi (fillable)
    protected $fillable = ['id_pembeli', 'id_pedagang', 'message'];

    /**
     * Relasi ke tabel pembelis
     * Message milik satu pembeli.
     */
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli');
    }

    /**
     * Relasi ke tabel pedagangs
     * Message milik satu pedagang.
     */
    public function pedagang()
    {
        return $this->belongsTo(Pedagang::class, 'id_pedagang');
    }
}
