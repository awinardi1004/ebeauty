<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductPromotionDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;

     protected $fillable  = [
        'product_id',
        'name',
        'price',
        'stock',
        'sku'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productPromotionDetails()
    {
        return $this->hasMany(ProductPromotionDetail::class, 'product_variant_id');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
}
