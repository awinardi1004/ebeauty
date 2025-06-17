@extends('front.layouts.app')
@section('title', 'Show Order')
@section('content')

@endsection

<nav class="w-full fixed top-0 bg-[#f85270] px-6 py-4 z-10">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="text-lg font-semibold text-black shrink-0">
            <a href="{{ route('front.index') }}" class="flex w-[154px] shrink-0 items-center">
            <img src="{{asset('images/icon/logo.svg')}}" alt="logo" />
            </a>
        </div>
        <h1 class="text-black text-3xl font-semibold">Order Details</h1>
        @auth
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" lass="flex items-center text-sm font-medium text-gray-800 hover:text-gray-900">
                    <span>{{ Auth::user()->username }} </span>
                    <span class="ml-1 text-xs">▼</span>
                </button>

                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                    <a href="{{ route('index_order') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Order</a>
                    <a href="{{ route('show_cart') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cart</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>

<section class="w-full max-w-7xl mx-auto pt-20 px-5 pb-10 ">
    <div class="font-semibold border-b border-gray-300 pb-2 text-left">
        <div class="grid grid-cols-12 text-left">
            <H2 class="col-span-1">Penerima</H2>
            <h2 class="col-span-6">: {{ $order->user->name }}</h2>
        </div>
        <div class="grid grid-cols-12 text-left">
            <H2 class="col-span-1">Alamat</H2>
            <h2 class="col-span-6">: {{ $order->user->address }}</h2>
        </div>
        @php
            $status = $order->status;
        @endphp

        @if ($status === 'pending')
            <div class="bg-red-500 text-white px-4 py-1.5 rounded-full text-center inline-block text-sm font-medium mt-4">Pending</div>
        @elseif ($status === 'tf_uploaded')
             <div class="text-white px-4 py-1.5 rounded-full text-center inline-block text-sm font-medium mt-4" style="background-color: #facc15;">Pending Verification</div>
        @elseif ($status === 'failed')
            <div class="bg-red-500 text-white px-4 py-1.5 rounded-full text-center inline-block text-sm font-medium mt-4">Failed</div>
        @elseif ($status === 'success')
            <div class="bg-green-500 text-white px-4 py-1.5 rounded-full text-center inline-block text-sm font-medium mt-4">success</div>
        @endif
    </div>
</section>

<section class="w-full max-w-7xl mx-auto px-5 pb-10">
    <div class="grid grid-cols-12 font-semibold border-b border-black pb-2 text-center">
        <div class="col-span-6 text-left">Product</div>
        <div class="col-span-3">Quantity</div>
        <div class="col-span-3">Total Price</div>
    </div>

    @php
        $reviewedProductIds = $order->user->product_review->pluck('product_id')->toArray();
    @endphp

    @foreach ($order->transactions as $transaction)
        @php
            $product = $transaction->productVariant->product;
            $variantName = $transaction->productVariant->name;
            $alreadyReviewed = in_array($product->id, $reviewedProductIds);
        @endphp

        <div class="grid grid-cols-12 items-center py-4 border-b border-gray-300 gap-4 item-row">
            <div class="col-span-6 flex items-center gap-4">
                <img src="{{ asset('storage/' . optional($product->productImages->first())->image_path) }}" alt="gambar product" class="w-16 h-16 bg-red-300 rounded"/>
                <div>
                    <p class="text-sm font-medium">{{ $product->title }}</p>
                    <p class="text-xs text-gray-500">Variant: {{ $variantName }}</p>
                </div>
            </div>

            <div class="col-span-3 text-center text-sm">x{{ $transaction->qty }}</div>
            <div class="col-span-3 text-center text-sm">Rp. {{ number_format($transaction->amount) }}</div>

            @if (!$alreadyReviewed)
                <div class="col-span-12 mt-4">
                    <details class="bg-gray-100 p-4 rounded-md">
                        <summary class="cursor-pointer font-medium text-blue-600 hover:underline">Give Rating & Review</summary>
                        <form method="POST" action="{{ route('review.store') }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <input type="hidden" name="rating" id="rating-input-{{ $transaction->id }}">

                            <div class="flex items-center mb-2 space-x-1 stars-container" data-id="{{ $transaction->id }}">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="star cursor-pointer text-2xl text-gray-400" data-value="{{ $i }}">&#9733;</span>
                                @endfor
                            </div>

                            <textarea name="review" class="w-full p-2 border border-gray-300 rounded-md text-sm" rows="3" placeholder="Write your review..."></textarea>

                            <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">Submit Review</button>
                        </form>
                    </details>
                </div>
            @endif
        </div>
    @endforeach

     <!-- Total Amount -->
    <!-- Amount kiri, nilai kanan -->
    <div class="grid grid-cols-12 mt-6 border-t border-black pt-4">
        <div class="col-span-6"></div>
        <div class="col-span-3 text-center font-semibold text-sm">Amount:</div>
        <div class="col-span-3 text-center font-semibold text-sm">Rp. {{ number_format($order->transactions->sum('amount')) }}</div>
    </div>


    <div class="lex justify-between items-center mt-6 border-b border-gray-300 py-4">
        <div>Transfer to ABC Bank</div>
        <div>AN Bank acount</div>
        <div>9395959359</div>
    </div>
    @if (($order->status == 'pending' || $order->status == 'failed') &&
    is_null($order->payment_receipt) &&
    !Auth::user()->is_admin)
        <form action="{{ route('submit_payment_receipt', $order) }}" method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf

            <div class="mb-4">
                <label for="file-upload" class="block text-sm font-medium text-gray-900 mb-2">
                    Upload your payment receipt
                </label>
                <input
                    id="file-upload"
                    type="file"
                    name="payment_receipt"
                    class="block w-full text-sm text-gray-900 rounded-md border border-gray-300 cursor-pointer"
                    required
                />
            </div>

            <button
                type="submit"
                class="bg-lime-500 text-white text-sm px-4 py-2 rounded-md hover:bg-lime-600 transition duration-200"
            >
                Submit Payment
            </button>
        </form>
    @endif

</section>

@push('after-script')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.stars-container').forEach(container => {
            const stars = container.querySelectorAll('.star');
            const ratingInput = document.getElementById(`rating-input-${container.dataset.id}`);

            stars.forEach(star => {
                star.addEventListener('click', () => {
                    const rating = parseInt(star.dataset.value);
                    ratingInput.value = rating;

                    stars.forEach(s => {
                        s.classList.toggle('text-yellow-400', parseInt(s.dataset.value) <= rating);
                        s.classList.toggle('text-gray-400', parseInt(s.dataset.value) > rating);
                    });
                });
            });
        });
    });
</script>
@endpush