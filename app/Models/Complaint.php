<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $table = 'keluhans';

    protected $fillable = [
        'deskripsi',
        'rating',
        'id_pembeli',
        'id_pedagang',
    ];

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'id_pembeli');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'id_pedagang');
    }

    /** @deprecated Use buyer() */
    public function pembeli()
    {
        return $this->buyer();
    }

    /** @deprecated Use seller() */
    public function pedagang()
    {
        return $this->seller();
    }
}
