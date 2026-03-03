<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    protected $table = 'device_tokens';

    protected $fillable = [
        'user_id',
        'token',
        'platform',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Ambil semua token aktif milik user tertentu.
     */
    public static function getActiveTokens(int $userId): array
    {
        return static::where('user_id', $userId)
            ->where('is_active', true)
            ->pluck('token')
            ->toArray();
    }
}
