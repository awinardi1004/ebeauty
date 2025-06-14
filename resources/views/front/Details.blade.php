@extends('front.layouts.app')
@section('title', 'Show Product')
@section('content')

<x-search-nav/>

@if(session('success'))
    <div class="max-w-4xl mx-auto mt-4">
        <div class="flex items-center justify-between bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded shadow-md" role="alert">
            <div class="flex items-center">
                <span><strong>Berhasil!</strong> {{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-green-700 hover:text-green-900 text-xl font-bold ml-4">
                ×
            </button>
        </div>
    </div>
@endif


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
        <div class="flex flex-col justify-between w-full md:w-2/3">
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
            <div id="price-variant">
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
                                data-id="{{ $variant->id }}"
                                data-price="{{ $variant->price }}"
                                data-disc="{{ $promo ? $promo->disc_product_variant : '' }}"
                                data-name="{{ $variant->name }}"
                                data-stock="{{ $variant->stock }}">
                                {{ $variant->name }}
                            </button>
                        @endforeach
                    </div>
                    <p id="stockInfo" class="text-sm text-gray-600 mt-2">Stock: {{ $defaultVariant->stock }}</p>
                </div>
            </div>

            <!-- Rating -->
            <div class="flex items-center gap-1 mb-4" id="rating">
                @php
                    $averageRating = round($product->product_reviews->avg('rating') * 2) / 2; // Bulatkan ke 0.5
                    $fullStars = floor($averageRating);
                    $halfStar = ($averageRating - $fullStars) >= 0.5;
                    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                @endphp

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



        <!-- Deskripsi -->
        <div class="mt-4">
            <h2 class="text-sm font-medium mb-1">Description:</h2>
            <div class="text-sm text-gray-700 leading-relaxed max-h-40 overflow-y-auto pr-2">
                <p>
                    {{ $product->description}}
                </p>
            </div>
        </div>


        <!-- Quantity + Add to Cart -->
        <div class="flex flex-wrap items-end gap-4 mt-4">
            <!-- Quantity -->
            <div>
                <p class="text-sm font-medium mb-1">Quantity</p>
                <div class="flex items-center border rounded overflow-hidden">
                    <button type="button" onclick="decreaseQty()" class="px-3 py-1 text-lg">−</button>
                    <span id="qty" class="px-4 py-1">1</span>
                    <button type="button" onclick="increaseQty()" class="px-3 py-1 text-lg">+</button>
                </div>
            </div>

            <!-- Add to Cart -->
            <form method="POST" action="{{ route('add_to_cart') }}" class="self-end" id="addToCartForm">
                @csrf
                <input type="hidden" name="product_variant_id" id="selectedVariantId">
                <input type="hidden" name="quantity" id="quantityInput" value="1">
                <input type="hidden" name="price_at_addition" id="selectedVariantPrice">

                <p id="errorMessage" class="text-red-600 text-sm mt-2 hidden"></p>

                <button type="submit" class="bg-gray-300 hover:bg-gray-400 text-black px-6 py-2 rounded">
                    🛒 Add to cart
                </button>
            </form>
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
    document.addEventListener('DOMContentLoaded', () => {
        let quantity = 1;
        const qtyDisplay = document.getElementById("qty");
        const quantityInput = document.getElementById("quantityInput");
        const variantButtons = document.querySelectorAll('.variant-btn');
        const selectedVariantId = document.getElementById('selectedVariantId');
        const selectedVariantPrice = document.getElementById('selectedVariantPrice');
        const stockInfo = document.getElementById('stockInfo');
        const priceContainer = document.getElementById('price-container');
        const addToCartForm = document.getElementById('addToCartForm');
        const errorMessage = document.getElementById('errorMessage');

        function updateQtyDisplay() {
            qtyDisplay.innerText = quantity;
            quantityInput.value = quantity;
        }

        window.increaseQty = function () {
            quantity += 1;
            updateQtyDisplay();
        }

        window.decreaseQty = function () {
            if (quantity > 1) {
                quantity -= 1;
                updateQtyDisplay();
            }
        }

        // Atur default variant (jika ada tombol pertama)
        const defaultBtn = variantButtons[0];
        if (defaultBtn) {
            defaultBtn.classList.add('bg-black', 'text-white');
            selectedVariantId.value = defaultBtn.dataset.id;
            selectedVariantPrice.value = defaultBtn.dataset.disc || defaultBtn.dataset.price;
            stockInfo.innerText = 'Stock: ' + defaultBtn.dataset.stock;
        }

        // Event listener untuk setiap varian
        variantButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Reset style semua tombol
                variantButtons.forEach(b => b.classList.remove('bg-black', 'text-white'));
                this.classList.add('bg-black', 'text-white');

                // Ambil data dari tombol
                const variantId = this.dataset.id;
                const price = parseInt(this.dataset.price);
                const disc = this.dataset.disc;
                const stock = this.dataset.stock;

                // Update harga
                if (priceContainer) {
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
                }

                // Update info stok
                if (stockInfo) {
                    stockInfo.innerText = `Stock: ${stock}`;
                }

                // Update hidden input untuk form
                if (selectedVariantId) selectedVariantId.value = variantId;
                if (selectedVariantPrice) selectedVariantPrice.value = disc || price;
            });
        });

        addToCartForm.addEventListener('submit', function (e) {
            const stock = parseInt(document.querySelector('.variant-btn.bg-black')?.dataset.stock || '0');
            const quantity = parseInt(document.getElementById('quantityInput').value);
            const cartQuantity = parseInt(document.querySelector('.variant-btn.bg-black')?.dataset.cartQuantity || '0');
            const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

            // Reset error
            errorMessage.textContent = '';
            errorMessage.classList.add('hidden');

            // Cek login
            if (!isLoggedIn) {
                e.preventDefault();
                errorMessage.textContent = 'Silakan login terlebih dahulu untuk menambahkan ke keranjang.';
                errorMessage.classList.remove('hidden');
                return;
            }

            // Cek stock habis
            if (stock === 0) {
                e.preventDefault();
                errorMessage.textContent = 'Stok untuk varian ini habis.';
                errorMessage.classList.remove('hidden');
                return;
            }

            // Cek total quantity melebihi stok
            if (quantity + cartQuantity > stock) {
                e.preventDefault();
                errorMessage.textContent = `Jumlah melebihi stok tersedia. Anda sudah memiliki ${cartQuantity} item di keranjang.`;
                errorMessage.classList.remove('hidden');
                return;
            }
        });

    });

    setTimeout(() => {
        const alertBox = document.querySelector('[role="alert"]');
        if (alertBox) alertBox.remove();
    }, 3000);

</script>



@endpush