<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'original_price',
        'stock',
        'category',
        'category_id',
        'photo_path',
        'is_active',
        'seller_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price'          => 'integer',
        'original_price' => 'integer',
        'stock'          => 'integer',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Scope: hanya produk aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
