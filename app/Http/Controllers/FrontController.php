<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\StorePromotion;
use Illuminate\Support\Facades\DB;

class FrontController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $store_banners = StorePromotion::where('is_active', true)->get();
        $new_products = Product::with([
            'productImages',
            'productVariants.productPromotionDetails.productPromotion'
        ])->orderBy('created_at', 'desc')->take(6)->get();
        $popular_product_ids = DB::table('transactions')
            ->join('orders', 'transactions.order_id', '=', 'orders.id')
            ->where('orders.status', 'success')
            ->select('transactions.product_id', DB::raw('COUNT(DISTINCT transactions.order_id) as total_orders'))
            ->groupBy('transactions.product_id')
            ->orderByDesc('total_orders')
            ->limit(6)
            ->pluck('product_id')
            ->toArray();
        $popular_products = Product::with([
            'productImages',
            'productVariants.productPromotionDetails.productPromotion'
        ])
        ->whereIn('id', $popular_product_ids)
        ->get();
        $categories = Category::all();

        return view('front.index',[
        'products' => $products,
        'store_banners' => $store_banners,
        'new_products' => $new_products,
        'popular_products' => $popular_products,
        'categories' => $categories]);
    }


    public function new_products()
    {
        $new_products = Product::with([
            'productImages',
            'productVariants.productPromotionDetails.productPromotion'
        ])->orderBy('created_at', 'desc')->get();

        return view('front.new_products',
        ['new_products' => $new_products]);
    }

    public function popular_products()
    {
        $popular_product_ids = DB::table('transactions')
            ->join('orders', 'transactions.order_id', '=', 'orders.id')
            ->where('orders.status', 'success')
            ->select('transactions.product_id', DB::raw('COUNT(DISTINCT transactions.order_id) as total_orders'))
            ->groupBy('transactions.product_id')
            ->orderByDesc('total_orders')
            ->limit(6)
            ->pluck('product_id')
            ->toArray();
        $popular_products = Product::with([
            'productImages',
            'productVariants.productPromotionDetails.productPromotion'
        ])
        ->whereIn('id', $popular_product_ids)
        ->get();

        return view('front.popular_products', ['popular_products' => $popular_products]);
    }
}
