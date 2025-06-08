<?php

namespace App\Models;

use App\Models\User;
use App\Models\Transaction;
use App\Models\ProductReview;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id',
        'payment_receipt',
        'status',
        'amount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }

    public function product_review()
    {
        return $this->hasMany(ProductReview::class);
    }
}
