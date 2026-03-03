<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $table = 'sellers';

    protected $fillable = [
        'store_name',
        'phone',
        'address',
        'photo_path',
        'status',
        'is_online',
        'is_suspended',
        'suspended_reason',
        'rating_average',
        'rating_count',
        'open_time',
        'close_time',
        'latitude',
        'longitude',
        'location_updated_at',
        'user_id',
    ];

    protected $casts = [
        'is_online'           => 'boolean',
        'is_suspended'        => 'boolean',
        'rating_average'      => 'float',
        'rating_count'        => 'integer',
        'location_updated_at' => 'datetime',
    ];

    // ─── Relasi ──────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'seller_id');
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    /**
     * Apakah seller sedang online dan siap menerima order.
     */
    public function isOnline(): bool
    {
        return (bool) $this->is_online;
    }

    /**
     * Apakah seller memiliki rating.
     */
    public function hasRating(): bool
    {
        return $this->rating_count > 0;
    }

    /**
     * Hitung jarak (km) dari seller ke koordinat tertentu menggunakan Haversine.
     * Mengembalikan null jika seller tidak memiliki koordinat.
     */
    public function distanceTo(float $lat, float $lng): ?float
    {
        if ($this->latitude === null || $this->longitude === null) {
            return null;
        }

        $earthRadius = 6371; // km
        $dLat = deg2rad($lat - $this->latitude);
        $dLng = deg2rad($lng - $this->longitude);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($this->latitude)) * cos(deg2rad($lat)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * asin(sqrt($a));
    }
}
