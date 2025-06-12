<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\Transaction;
use App\Models\ProductReview;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category_id',
        'description',
    ];

    public function category() 
    {
        return $this->belongsTo(Category::class);
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }

    public function product_reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
}
