@extends('front.layouts.app')
@section('title', 'E-Beauty')
@section('content')

<x-search-nav/>

<header class="bg-[#ff859b] pb-8 px-4 pt-10" style="margin-top: -64px;">
    <div>
        <!-- Carousel Container -->
        <div class="relative w-full max-w-4xl mx-auto overflow-hidden border bg-gray-200 h-50">
            <!-- Gambar-gambar banner -->
            <div id="carousel" class="flex transition-transform duration-500">
                @foreach ($store_banners as $banner)
                    <img src="{{ asset('storage/' . $banner->path) }}" class="w-full flex-shrink-0" alt="Store Banner">
                @endforeach
            </div>

            <!-- Tombol panah -->
            <button onclick="prevSlide()" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-3xl text-white">&lt;</button>
            <button onclick="nextSlide()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-3xl text-white">&gt;</button>
        </div>

        <!-- Ikon -->
        <div class="flex justify-center gap-5 mt-6 flex-wrap">
            @foreach ($categories as $category)
                <div class="flex flex-col items-center mx-10">
                    <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                    <img src="{{ asset('storage/' . $category->icon)}}" alt="Icon 1" class="w-full h-full object-cover" />
                    </div>
                    <h4 class="mt-2 text-center text-sm">{{ $category->name }}</h4>
                </div>
            @endforeach
        </div>
    </div>
</header>

<section id="BestSeller" class="bg-gray-200 p-4 mt-8">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-sm font-semibold text-black">Best Seller</h2>
        <a href="{{ route("front.popular_products")}}" class="text-sm text-pink-600 hover:underline">See All</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-6 gap-4">


        <!-- Duplikat untuk produk lainnya -->
            @foreach($popular_products as  $popular_product)
            @php
                $variant = $popular_product->productVariants->first();
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

            @if ($variant)
                <div class="relative group bg-red-300 rounded-md p-2 flex flex-col items-center">
                    <div class="w-full aspect-square overflow-hidden rounded-md mb-2">
                        <img src="{{ asset('storage/' . optional($popular_product->productImages->first())->image_path) }}" 
                            alt="Foto Produk" 
                            class="w-full h-full object-cover" />
                    </div>

                    <p class="text-xs text-black text-center leading-tight" style="
                        display: -webkit-box;
                        -webkit-line-clamp: 2;
                        -webkit-box-orient: vertical;
                        overflow: hidden;
                        text-overflow: ellipsis;">
                        {{ $popular_product->title }}
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
                    <a href="{{ route('front.details', $popular_product) }}"
                    class="absolute bottom-2 bg-black text-white text-xs px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition duration-300">
                        Check Product
                    </a>

                </div>
            @endif
        @endforeach
    </div>
</section>

<section id="NewProduct" class="bg-gray-200 p-4 mt-8">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-sm font-semibold text-black">New Arrivals</h2>
        <a href="{{ route("front.new_products")}}" class="text-sm text-pink-600 hover:underline">See All</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-6 gap-4">

        @foreach($new_products as $new_product)
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

            @if ($variant)
                <div class="relative group bg-red-300 rounded-md p-2 flex flex-col items-center">
                    <div class="w-full aspect-square overflow-hidden rounded-md mb-2">
                        <img src="{{ asset('storage/' . optional($new_product->productImages->first())->image_path) }}" 
                            alt="Foto Produk" 
                            class="w-full h-full object-cover" />
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
            @endif
        @endforeach
    </div>
</section>

<x-footer/>
    
@endsection

@push('after-script')

<script>
    const carousel = document.getElementById('carousel');
    let index = 0;

    function showSlide(i) {
        const width = carousel.clientWidth;
        carousel.style.transform = `translateX(-${i * width}px)`;
    }

    function nextSlide() {
        index = (index + 1) % carousel.children.length;
        showSlide(index);
    }

    function prevSlide() {
        index = (index - 1 + carousel.children.length) % carousel.children.length;
        showSlide(index);
    }
</script>

@endpush