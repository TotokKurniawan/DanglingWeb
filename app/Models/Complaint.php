<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $table = 'complaints';

    protected $fillable = [
        'description',
        'rating',
        'buyer_id',
        'seller_id',
    ];

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
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
