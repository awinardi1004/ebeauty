<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorePromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'start_date',
        'end_date'
    ];
}
