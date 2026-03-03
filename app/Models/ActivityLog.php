<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $table = 'activity_logs';

    protected $fillable = [
        'event',
        'subject_type',
        'subject_id',
        'user_id',
        'properties',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    // ─── Relasi ──────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subject()
    {
        return $this->morphTo('subject');
    }

    // ─── Static helper ───────────────────────────────────────────────────────

    /**
     * Tulis log aktivitas.
     *
     * @param  string      $event       Nama event, mis. 'order.created'
     * @param  Model|null  $subject     Model terkait (opsional)
     * @param  array       $properties  Data tambahan
     */
    public static function log(string $event, ?Model $subject = null, array $properties = []): static
    {
        return static::create([
            'event'        => $event,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->getKey(),
            'user_id'      => auth()->id(),
            'properties'   => $properties ?: null,
            'ip_address'   => request()->ip(),
            'created_at'   => now(),
        ]);
    }
}
