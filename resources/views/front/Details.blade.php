@extends('front.layouts.app')
@section('title', 'Show Product')
@section('content')

<x-search-nav/>

<section id="Details" class="p-6 max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row gap-6">
        
        <!-- Gambar Produk -->
        <div class="relative w-full md:w-2/4">
            <div class="relative aspect-square bg-gray-200 rounded overflow-hidden group">
                <!-- Gambar -->
                <div id="carousel" class="w-full h-full relative">
                    @foreach($product->productImages as $image)
                        <img src="{{ asset('storage/' . $image->image_path)}}" alt="Produk"
                            class="w-full h-full object-cover absolute inset-0 transition-opacity duration-500 opacity-100" />
                    @endforeach
                </div>

                <!-- Tombol navigasi -->
                <button onclick="prevSlide()"
                    class="absolute left-2 top-1/2 -translate-y-1/2 text-4xl text-white bg-black/50 px-2 py-1 rounded hover:bg-black/70 z-50 group-hover:opacity-100 opacity-0 transition duration-300">
                    ‹
                </button>
                <button onclick="nextSlide()"
                    class="absolute right-2 top-1/2 -translate-y-1/2 text-4xl text-white bg-black/50 px-2 py-1 rounded hover:bg-black/70 z-50 group-hover:opacity-100 opacity-0 transition duration-300">
                    ›
                </button>
            </div>
        </div>


        <!-- Detail Produk -->
        @php
            $now = now();
            $variants = $product->productVariants;

            // Dapatkan varian dengan harga diskon terendah jika ada
            $variantsWithPromo = $variants->filter(function ($variant) use ($now) {
                return $variant->productPromotionDetails->first(function ($detail) use ($now) {
                    return $detail->productPromotion &&
                        $detail->productPromotion->start_date <= $now &&
                        $detail->productPromotion->end_date >= $now;
                });
            });

            $defaultVariant = null;

            if ($variantsWithPromo->count()) {
                // Cari varian dengan diskon terendah
                $defaultVariant = $variantsWithPromo->sortBy(function ($variant) use ($now) {
                    $promo = $variant->productPromotionDetails->first(function ($detail) use ($now) {
                        return $detail->productPromotion &&
                            $detail->productPromotion->start_date <= $now &&
                            $detail->productPromotion->end_date >= $now;
                    });
                    return $promo->disc_product_variant ?? $variant->price;
                })->first();
            } else {
                // Jika tidak ada promo, ambil varian dengan harga normal terendah
                $defaultVariant = $variants->sortBy('price')->first();
            }

            // Ambil promo aktif dari default variant
            $activePromo = $defaultVariant->productPromotionDetails->first(function ($detail) use ($now) {
                return $detail->productPromotion &&
                    $detail->productPromotion->start_date <= $now &&
                    $detail->productPromotion->end_date >= $now;
            });
        @endphp

        <div class="flex flex-col justify-between w-full md:w-2/3">
            <div>
                <p class="text-sm text-gray-500 mb-1">{{ $product->category->name }}</p>
                <h2 class="text-lg font-semibold mb-4">{{ $product->title }}</h2>
                <div id="price-container">
                    @if($activePromo)
                        <p class="text-sm text-gray-600 mb-1" style="text-decoration: line-through;">
                            Rp. {{ number_format($defaultVariant->price) }}
                        </p>
                        <p class="text-lg font-semibold text-black mb-4">
                            Rp. {{ number_format($activePromo->disc_product_variant) }}
                        </p>
                    @else
                        <p class="text-lg font-semibold text-black mb-4">
                            Rp. {{ number_format($defaultVariant->price) }}
                        </p>
                    @endif
                </div>


                <!-- Varian -->
                <div class="mb-2">
                    <p class="text-sm font-medium mb-2">Variant :</p>
                    <div class="flex gap-2 flex-wrap" id="variant-buttons">
                        @foreach ($product->productVariants as $variant)
                            @php
                                $promo = $variant->productPromotionDetails->first(function ($detail) use ($now) {
                                    return $detail->productPromotion &&
                                        $detail->productPromotion->start_date <= $now &&
                                        $detail->productPromotion->end_date >= $now;
                                });
                            @endphp
                            <button 
                                class="px-3 py-1 text-sm border rounded variant-btn" 
                                data-price="{{ $variant->price }}"
                                data-disc="{{ $promo ? $promo->disc_product_variant : '' }}"
                                data-name="{{ $variant->name }}">
                                {{ $variant->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

        <!-- Rating -->
        @php
            $averageRating = round($product->product_reviews->avg('rating') * 2) / 2; // Bulatkan ke 0.5
            $fullStars = floor($averageRating);
            $halfStar = ($averageRating - $fullStars) >= 0.5;
            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
        @endphp

        <div class="flex items-center gap-1 mb-4">
            {{-- Full stars --}}
            @for ($i = 0; $i < $fullStars; $i++)
                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.518 4.674h4.91c.969 0 1.371 1.24.588 1.81l-3.973 2.89 1.518 4.674c.3.921-.755 1.688-1.54 1.118L10 15.347l-3.973 2.89c-.784.57-1.838-.197-1.54-1.118l1.518-4.674-3.973-2.89c-.784-.57-.38-1.81.588-1.81h4.91L9.049 2.927z"/>
                </svg>
            @endfor

            {{-- Half star --}}
            @if ($halfStar)
                <svg class="w-5 h-5 text-yellow-400" viewBox="0 0 24 24" fill="currentColor">
                    <defs>
                        <linearGradient id="halfGrad">
                            <stop offset="50%" stop-color="currentColor"/>
                            <stop offset="50%" stop-color="transparent"/>
                        </linearGradient>
                    </defs>
                    <path fill="url(#halfGrad)" d="M12 .587l3.668 7.429L24 9.748l-6 5.847 1.417 8.268L12 18.896 4.583 23.863 6 15.595 0 9.748l8.332-1.732z"/>
                </svg>
            @endif

            {{-- Empty stars --}}
            @for ($i = 0; $i < $emptyStars; $i++)
                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.518 4.674h4.91c.969 0 1.371 1.24.588 1.81l-3.973 2.89 1.518 4.674c.3.921-.755 1.688-1.54 1.118L10 15.347l-3.973 2.89c-.784.57-1.838-.197-1.54-1.118l1.518-4.674-3.973-2.89c-.784-.57-.38-1.81.588-1.81h4.91L9.049 2.927z"/>
                </svg>
            @endfor

            {{-- Tampilkan angka rating --}}
            <span class="ml-2 text-sm text-gray-600 font-medium">
                {{ number_format($averageRating, 1) }}
            </span>
        </div>



        <!-- Deskripsi sebagai teks biasa -->
        <!-- Deskripsi sebagai teks biasa dengan scroll jika terlalu panjang -->
        <div class="mt-4">
            <h2 class="text-sm font-medium mb-1">Description:</h2>
            <div class="text-sm text-gray-700 leading-relaxed max-h-40 overflow-y-auto pr-2">
                <p>
                    {{ $product->description}}
                </p>
            </div>
        </div>


        <!-- Quantity + Add to Cart -->
        <div class="flex flex-wrap items-center gap-4 mt-4">
            <!-- Quantity -->
            <div>
            <p class="text-sm font-medium mb-1">Quantity</p>
            <div class="flex items-center border rounded overflow-hidden">
                <button onclick="decreaseQty()" class="px-3 py-1 text-lg">−</button>
                <span id="qty" class="px-4 py-1">1</span>
                <button onclick="increaseQty()" class="px-3 py-1 text-lg">+</button>
            </div>
            </div>

            <!-- Add to Cart -->
            <button class="bg-gray-300 hover:bg-gray-400 text-black px-6 py-2 rounded self-end">
            Add to cart
            </button>
        </div>
        </div>
    </div>
</section>

<section id="Review" class="p-4">
    <div class="rounded-lg p-4 bg-gray-100">
      <h2 class="text-xl font-semibold mb-4">Penilaian Produk</h2>

      <!-- Wrapper geser -->
        <div class="flex space-x-4 pb-4 cursor-grab overflow-x-scroll scroll-smooth" id="testimonialWrapper" style="scrollbar-width: none; -ms-overflow-style: none;" >  
            @foreach ($product->product_reviews as $review)
                <div class="w-[310px] h-[200px] bg-white p-4 rounded-lg shadow-md flex-shrink-0">
                    <div class="text-yellow-400 mb-2 text-lg">
                        {!! str_repeat('★', $review->rating) !!}
                        {!! str_repeat('☆', 5 - $review->rating) !!}
                    </div>
                    <p class="text-sm text-gray-700 mb-3 line-clamp-4">
                        {{ $review->review }}
                    </p>
                    <p class="font-bold text-black">{{ $review->user->name }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>


<x-footer/>
    
@endsection

@push('after-script')
<script>
    let quantity = 1;

    function updateQtyDisplay() {
        document.getElementById("qty").innerText = quantity;
    }

    function increaseQty() {
        quantity += 1;
        updateQtyDisplay();
    }

    function decreaseQty() {
        if (quantity > 1) {
        quantity -= 1;
        updateQtyDisplay();
        }
    }

    let currentSlide = 0;
    const slides = document.querySelectorAll("#carousel img");

    function showSlide(index) {
        slides.forEach((slide, i) => {
        slide.style.opacity = i === index ? "1" : "0";
        });
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        showSlide(currentSlide);
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }

    // Inisialisasi slide pertama
    showSlide(currentSlide);

    const slider = document.getElementById('testimonialWrapper');
    let isDown = false;
    let startX;
    let scrollLeft;

    slider.addEventListener('mousedown', (e) => {
        isDown = true;
        slider.classList.add('cursor-grabbing');
        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
    });

    slider.addEventListener('mouseleave', () => {
        isDown = false;
        slider.classList.remove('cursor-grabbing');
    });

    slider.addEventListener('mouseup', () => {
        isDown = false;
        slider.classList.remove('cursor-grabbing');
    });

    slider.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - slider.offsetLeft;
        const walk = (x - startX) * 2; // kecepatan geser
        slider.scrollLeft = scrollLeft - walk;
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.variant-btn');
        const priceText = document.getElementById('selected-price');

        buttons.forEach(button => {
            button.addEventListener('click', function () {
                // Reset all buttons
                buttons.forEach(btn => btn.classList.remove('bg-black', 'text-white'));

                // Highlight selected button
                this.classList.add('bg-black', 'text-white');

                // Update price
                const price = this.getAttribute('data-price');
                const name = this.getAttribute('data-name');
                priceText.innerText = `Rp. ${parseInt(price).toLocaleString()}`;
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const variantButtons = document.querySelectorAll('.variant-btn');
        const priceContainer = document.getElementById('price-container');

        variantButtons.forEach(button => {
            button.addEventListener('click', () => {
                const price = parseInt(button.getAttribute('data-price'));
                const disc = button.getAttribute('data-disc');

                if (disc) {
                    priceContainer.innerHTML = `
                        <p class="text-sm text-gray-600 mb-1" style="text-decoration: line-through;">
                            Rp. ${price.toLocaleString('id-ID')}
                        </p>
                        <p class="text-lg font-semibold text-black mb-4">
                            Rp. ${parseInt(disc).toLocaleString('id-ID')}
                        </p>
                    `;
                } else {
                    priceContainer.innerHTML = `
                        <p class="text-lg font-semibold text-black mb-4">
                            Rp. ${price.toLocaleString('id-ID')}
                        </p>
                    `;
                }
            });
        });
    });
</script>


@endpush