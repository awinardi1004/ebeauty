<?php

namespace App\Models;

use App\Models\ProductVariant;
use App\Models\ProductPromotion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductPromotionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_promotion_id',
        'product_variant_id',
        'disc_product_variant',
        'product_id',
    ];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function product()
    {
        return $this->productVariant?->product();
    }

    public function productPromotion()
    {
        return $this->belongsTo(ProductPromotion::class);
    }
}
