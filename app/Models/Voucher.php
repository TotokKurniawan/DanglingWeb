<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'vouchers';

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_purchase',
        'max_discount',
        'valid_until',
        'limit',
        'claimed_count',
        'is_active',
    ];

    protected $casts = [
        'value'         => 'integer',
        'min_purchase'  => 'integer',
        'max_discount'  => 'integer',
        'limit'         => 'integer',
        'claimed_count' => 'integer',
        'is_active'     => 'boolean',
        'valid_until'   => 'datetime',
    ];

    /**
     * Scope untuk mengambil voucher yang aktif dan valid (belum expire, limit belum habis).
     */
    public function scopeActiveAndValid($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>', now());
            })
            ->where(function ($q) {
                $q->whereNull('limit')
                  ->orWhereRaw('claimed_count < `limit`');
            });
    }

    /**
     * Menghitung nilai diskon berdasarkan subtotal order.
     */
    public function calculateDiscount(int $subtotal): int
    {
        if ($subtotal < $this->min_purchase) {
            return 0; // Tidak memenuhi minimal pembelian
        }

        if ($this->type === 'fixed') {
            return min($this->value, $subtotal); // Diskon maksimal seharga subtotal
        }

        if ($this->type === 'percentage') {
            $discount = (int) ($subtotal * ($this->value / 100));

            if ($this->max_discount !== null) {
                return min($discount, $this->max_discount);
            }

            return $discount;
        }

        return 0;
    }
}
