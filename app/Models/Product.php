<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'produks';

    protected $fillable = [
        'nama_produk',
        'harga_produk',
        'kategori_produk',
        'foto',
        'id_pedagang',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'id_pedagang');
    }
}
