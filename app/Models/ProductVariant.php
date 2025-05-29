<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillabel = [
        'product_id',
        'variant',
        'price',
        'stock',
        'sku'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function product_variant_promotion()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
