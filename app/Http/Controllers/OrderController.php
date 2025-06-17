<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class OrderController extends Controller
{
    public function checkout()
    {
        $user_id = Auth::id();
        $carts = Cart::where('user_id', $user_id)->get();


        if($carts == null)
        {
            return Redirect::back();
        }

        $order = Order::create([
            'user_id' => $user_id,
            'code' => 'TRX-' . mt_rand(10000, 99999),
        ]);

        foreach ($carts as $cart)
        {
            $product_variant = ProductVariant::find($cart->product_variant_id);

            $product_variant->update([
                'stock'=>$product_variant->stock - $cart->quantity
            ]);
            
            Transaction::create([
                'qty' => $cart->quantity,
                'order_id' => $order->id,
                'product_variant_id' => $cart->product_variant_id,
                'product_id' => $cart->product_variant->product_id,
                'price_at_addition' => $cart->price_at_addition,
                'amount' => $cart->amount,
            ]);

            $cart->delete();
        }

        return Redirect::route('front.show_order', $order);
    }

    public function index_order()
    {
        $user = Auth::user();

        $orders = Order::where('user_id', $user->id)->get();

        return view('front.index_order', compact('orders'));
    }

    public function show_order(Order $order)
    {
        $user = Auth::user();

        if ($order->user_id == $user->id )
        {
            $order->load([
                'user.product_review',
                'transactions.productVariant.product.productImages'
            ]);

            return view('front.show_order', compact('order'));
        }

        return Redirect::route('index_order');
    }

     public function submit_payment_receipt(Order $order, Request $request)
    {
        $request->validate([
            'payment_receipt' => 'required'
        ]);

        $file = $request->file('payment_receipt');
        $path = 'public/payment_receipts/' . time() . '_' . $order->id . '.' . $file->getClientOriginalExtension();

        Storage::put($path, file_get_contents($file));

        $order->update([
            'payment_receipt' => str_replace('public/', '', $path),
            'status' => 'tf_uploaded',
        ]);

        return redirect()->back()->with('success', 'Payment receipt uploaded successfully.');
    }

}
