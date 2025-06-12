<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;


class ProductController extends Controller
{
    public function show_product(Product $product)
    {
        return view('front.details', compact('product'));
    }
}
