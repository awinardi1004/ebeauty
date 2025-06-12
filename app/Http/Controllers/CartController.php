<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductPromotionDetail;

class CartController extends Controller
{
   public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'price_at_addition' => 'required|numeric',
        ]);

        $amount = $validated['price_at_addition'] * $validated['quantity'];

        Cart::create([
            'user_id' => auth()->id(),
            'product_variant_id' => $validated['product_variant_id'],
            'quantity' => $validated['quantity'],
            'price_at_addition' => $validated['price_at_addition'],
            'amount' => $amount,
        ]);

        return redirect()->back()->with('success', 'Product added to cart!');
    }
}
