<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    /**
     * Ambil value setting berdasarkan key, dengan default fallback.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    /**
     * Set value setting berdasarkan key. Buat baru jika belum ada.
     */
    public static function setValue(string $key, mixed $value, array $meta = []): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            array_merge(['value' => (string) $value], $meta)
        );
    }

    /**
     * Ambil semua settings dalam satu group sebagai associative array.
     */
    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->get()
            ->mapWithKeys(fn ($s) => [$s->key => static::castValue($s->value, $s->type)])
            ->toArray();
    }

    /**
     * Cast value berdasarkan type.
     */
    protected static function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json'    => json_decode($value, true),
            default   => $value,
        };
    }
}
