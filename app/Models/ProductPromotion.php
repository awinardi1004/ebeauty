<?php

namespace App\Models;

use App\Models\ProductPromotionDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductPromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
    ];

    public function productPromotionDetails()
    {
        return $this->hasMany(ProductPromotionDetail::class);
    }

}
