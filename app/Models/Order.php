<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'histories';

    public const STATUS_PENDING = 'Menunggu';
    public const STATUS_ACCEPTED = 'Diterima';
    public const STATUS_REJECTED = 'Ditolak';
    public const STATUS_COMPLETED = 'Selesai';
    public const STATUS_CANCELLED = 'Dibatalkan';

    protected $fillable = [
        'status',
        'bentuk_pembayaran',
        'alasan_tolak',
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

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Hanya order dengan status Menunggu boleh diterima/ditolak/dibatalkan pembeli.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Hanya order dengan status Diterima boleh diselesaikan.
     */
    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    /**
     * Seller boleh cancel jika masih Menunggu atau Diterima.
     */
    public function canBeCancelledBySeller(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_ACCEPTED], true);
    }

    /**
     * Pembeli boleh cancel hanya jika status Menunggu.
     */
    public function canBeCancelledByBuyer(): bool
    {
        return $this->isPending();
    }

    /**
     * Order boleh diterima/ditolak hanya jika status Menunggu (alias isPending untuk kejelasan doc).
     */
    public function canBeAccepted(): bool
    {
        return $this->isPending();
    }
}
