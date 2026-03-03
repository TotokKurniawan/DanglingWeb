<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // Payment methods
    public const PAYMENT_COD      = 'COD';
    public const PAYMENT_TRANSFER = 'TRANSFER';

    // Payment statuses
    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PAID   = 'paid';

    protected $fillable = [
        'status',
        'payment_method',
        'payment_status',
        'rejection_reason',
        'cancelled_by',
        'cancel_reason',
        'reject_reason',
        'buyer_id',
        'seller_id',
        'accepted_at',
        'completed_at',
    ];

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
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
        if (! $this->isPending()) {
            return false;
        }

        $limit = config('order.buyer_cancel_timeout_minutes');

        if ($limit === null) {
            return true;
        }

        if (! $this->created_at) {
            return true;
        }

        return $this->created_at->diffInMinutes(now()) <= (int) $limit;
    }

    /**
     * Order boleh diterima/ditolak hanya jika status Menunggu (alias isPending untuk kejelasan doc).
     */
    public function canBeAccepted(): bool
    {
        return $this->isPending();
    }
}
