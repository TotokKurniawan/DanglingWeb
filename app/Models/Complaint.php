<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $table = 'complaints';

    // Status constants
    public const STATUS_OPEN        = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED    = 'resolved';
    public const STATUS_DISMISSED   = 'dismissed';

    protected $fillable = [
        'description',
        'rating',
        'status',
        'buyer_id',
        'seller_id',
        'order_id',
        'handled_by',
        'handled_at',
    ];

    protected $casts = [
        'rating'     => 'integer',
        'handled_at' => 'datetime',
    ];

    // ─── Relasi ──────────────────────────────────────────────────────────────

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isResolved(): bool
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    // ─── Deprecated aliases ──────────────────────────────────────────────────

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
