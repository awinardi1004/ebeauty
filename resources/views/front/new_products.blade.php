@extends('front.layouts.app')
@section('title', 'Show Product')
@section('content')

<x-search-nav/>

 <section class="bg-gray-200 p-4 mt-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-3xl font-semibold text-black">New Arrivals</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-6 gap-4">

            @foreach ( $new_products as $new_product )
            @php
                $variant = $new_product->productVariants->first();
                $now = now();
                $activePromotion = null;

                if ($variant && $variant->productPromotionDetails) {
                    $activePromotion = $variant->productPromotionDetails->first(function ($detail) use ($now) {
                        return $detail->productPromotion &&
                            $detail->productPromotion->start_date <= $now &&
                            $detail->productPromotion->end_date >= $now;
                    });
                }
            @endphp

                <div class="relative group bg-red-300 rounded-md p-2 flex flex-col items-center">
                    <div class="w-full aspect-square overflow-hidden rounded-md mb-2">
                        <img src="{{ asset('storage/' . $new_product->productImages->first()->image_path) }}" alt="Foto Produk" class="w-full h-full object-cover" />
                    </div>
                     <p class="text-xs text-black text-center leading-tight" style="
                        display: -webkit-box;
                        -webkit-line-clamp: 2;
                        -webkit-box-orient: vertical;
                        overflow: hidden;
                        text-overflow: ellipsis;">
                        {{ $new_product->title }}
                    </p>
                    @if($activePromotion)
                        <p class="text-xs text-gray-600 line-through" style="text-decoration: line-through;">
                            Rp. {{ number_format($variant->price) }}
                        </p>

                        <p class="text-sm font-bold text-black">
                            Rp. {{ number_format($activePromotion->disc_product_variant) }}
                        </p>
                    @else
                        <p class="text-sm font-bold text-black">
                            Rp. {{ number_format($variant->price) }}
                        </p>
                    @endif

                    <a href="{{ route('front.details', $new_product) }}"
                    class="absolute bottom-2 bg-black text-white text-xs px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition duration-300">
                        Check Product
                    </a>
                </div>
                
            @endforeach

        </div>
    </section>

<x-footer/>
    
@endsection