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

        $user_id = Auth::id();
        $product_variant = ProductVariant::findOrFail($validated['product_variant_id']);
        $amount = $validated['price_at_addition'] * $validated['quantity'];

        $existing_cart = Cart::where('product_variant_id', $product_variant->id)
            ->where('user_id', $user_id)
            ->first();

        if (!$existing_cart) {
            // Validasi: pastikan tidak lebih dari stok
            $request->validate([
                'quantity' => 'required|gte:1|lte:' . $product_variant->stock
            ]);

            Cart::create([
                'user_id' => $user_id,
                'product_variant_id' => $product_variant->id,
                'quantity' => $validated['quantity'],
                'price_at_addition' => $validated['price_at_addition'],
                'amount' => $amount,
            ]);
        } else {
            // Total kuantitas yang ingin ditambahkan harus tetap dalam stok
            $newQuantity = $existing_cart->quantity + $validated['quantity'];

            $request->validate([
                'quantity' => 'required|gte:1|lte:' . ($product_variant->stock - $existing_cart->quantity)
            ]);

            $existing_cart->update([
                'quantity' => $newQuantity,
                'amount' => $newQuantity * $validated['price_at_addition'],
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    public function update_cart(Cart $cart, Request $request)
    {
        $request->validate([
            'amount' => 'required|gte:1|lte:' . $cart->product_variant->stock
        ]);

        $cart->update([
            'quantity' => $request->amount,
            'amount' => $request->amount * $cart->price_at_addition,
        ]);

        return response()->json([
            'message' => 'Cart updated',
            'updated_amount' => $cart->amount
        ]);
    }





    public function show_cart()
    {
        $user_id = Auth::id();
        $carts = Cart::where('user_id', $user_id)->get();
        return view('front.show_cart', compact('carts'));
    }

    public function delete_cart(Cart $cart)
    {
        $cart->delete();
        return redirect()->back();
    }
}
