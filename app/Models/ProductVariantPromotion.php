<?php

namespace App\Models;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariantPromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'product_variant_id',
        'disc_product_variant',
        'start_date',
        'end_date'
    ];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
