@extends('front.layouts.app')
@section('title', 'Show Product')
@section('content')

<nav class="w-full fixed top-0 bg-[#f85270] px-6 py-4 z-10">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="text-lg font-semibold text-black shrink-0">
            <a href="{{ route('front.index') }}" class="flex w-[154px] shrink-0 items-center">
            <img src="{{asset('images/icon/logo.svg')}}" alt="logo" />
            </a>
        </div>
        <h1 class="text-black text-3xl font-semibold">My Cart</h1>
        @auth
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" lass="flex items-center text-sm font-medium text-gray-800 hover:text-gray-900">
                    <span>{{ Auth::user()->username }} </span>
                    <span class="ml-1 text-xs">▼</span>
                </button>

                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Order</a>
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

<section class="w-full max-w-8xl mx-auto pt-8 px-5 pb-10">
    <!-- Header Table -->
    <div class="grid grid-cols-12 font-semibold border-b border-black pb-2 text-center">
      <div class="col-span-5 text-left">Product</div>
      <div class="col-span-2">Price</div>
      <div class="col-span-2">Quantity</div>
      <div class="col-span-2">Total</div>
      <div class="col-span-1">Action</div>
    </div>

    @foreach ($carts as $cart)
        <div class="grid grid-cols-12 items-center py-4 border-b border-gray-300 gap-4 item-row">
            <div class="col-span-5 flex items-center gap-4">
                <img src="{{ asset('storage/' . optional($cart->product_variant->product->productImages->first())->image_path) }}" alt="gambar product" class="w-16 h-16 bg-red-300 rounded"/>
            <div class="max-w-[150px]">
            <p class="text-sm truncate whitespace-nowrap overflow-hidden text-ellipsis"  style="
                            display: -webkit-box;
                            -webkit-line-clamp: 1;
                            -webkit-box-orient: vertical;
                            overflow: hidden;
                            text-overflow: ellipsis;">
            {{ $cart->product_variant->product->title}}
            <p class="text-xs text-gray-500">{{ $cart->product_variant->name}}</p>
        </div>
        </div>
        <div class="col-span-2 text-center text-sm price" data-price="{{ $cart->price_at_addition }}">Rp.{{ number_format($cart->price_at_addition, 0, ',', '.') }}</div>
        <div class="col-span-2 flex justify-center items-center gap-2">
          <button 
              class="decrement bg-gray-300 px-2 py-1 rounded hover:bg-gray-400"
              data-cart-id="{{ $cart->id }}">-</button>

          <span class="quantity text-sm" data-cart-id="{{ $cart->id }}">{{ $cart->quantity }}</span>

          <button 
              class="increment bg-gray-300 px-2 py-1 rounded hover:bg-gray-400"
              data-cart-id="{{ $cart->id }}">+</button>
      </div>

        <div class="col-span-2 text-center text-sm total"></div>
        <div class="col-span-1 text-center">
            <form action="{{ route('delete_cart', $cart) }}" method="post">
                @method('delete')
                @csrf
                <button  type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
            </form>
        </div>
        </div>
    @endforeach


    <!-- Total Amount -->
    <div class="flex justify-between items-center mt-6 border-y border-gray-300 py-4">
      <div class="text-lg font-semibold">Amount :</div>
      <div class="flex items-center gap-4">
        <span id="grand-total" class="text-lg font-semibold">Rp.0</span>
        <button class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Checkout</button>
      </div>
    </div>
  </section>
@endsection


@push('after-script')

<script>

  document.addEventListener('DOMContentLoaded', function () {
      const csrfToken = '{{ csrf_token() }}';

      document.querySelectorAll('.increment, .decrement').forEach(button => {
          button.addEventListener('click', function () {
              const cartId = this.dataset.cartId;
              const isIncrement = this.classList.contains('increment');
              const quantityEl = document.querySelector(`.quantity[data-cart-id="${cartId}"]`);
              let currentQty = parseInt(quantityEl.textContent);

              let newQty = isIncrement ? currentQty + 1 : currentQty - 1;
              if (newQty < 1) return; // minimal 1

              fetch(`{{ route('update_cart', ['cart' => '__id__']) }}`.replace('__id__', cartId), {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': csrfToken
                  },
                  body: JSON.stringify({
                      amount: newQty
                  })
              })
              .then(res => {
                  if (!res.ok) throw new Error('Gagal update');
                  return res.json();
              })
              .then(data => {
                  quantityEl.textContent = newQty;
                  // Optional: update total per item / subtotal
                  // document.querySelector(...).textContent = data.updated_amount
              })
              .catch(err => {
                  alert('Gagal mengupdate kuantitas.');
                  console.error(err);
              });
          });
      });
  });
    
  function formatRupiah(value) {
    return 'Rp.' + value.toLocaleString('id-ID');
  }

  function updateTotal() {
    const itemRows = document.querySelectorAll('.item-row');
    let grandTotal = 0;

    itemRows.forEach(row => {
      const priceEl = row.querySelector('.price');
      const quantityEl = row.querySelector('.quantity');
      const totalEl = row.querySelector('.total');

      const price = parseInt(priceEl.dataset.price);
      const quantity = parseInt(quantityEl.textContent);
      const total = price * quantity;

      totalEl.textContent = formatRupiah(total);
      grandTotal += total;
    });

    document.getElementById('grand-total').textContent = formatRupiah(grandTotal);
  }

  window.onload = function () {
    document.querySelectorAll('.item-row').forEach(row => {
      const incrementBtn = row.querySelector('.increment');
      const decrementBtn = row.querySelector('.decrement');
      const quantityEl = row.querySelector('.quantity');

      incrementBtn.addEventListener('click', () => {
        let qty = parseInt(quantityEl.textContent);
        qty++;
        quantityEl.textContent = qty;
        updateTotal();
      });

      decrementBtn.addEventListener('click', () => {
        let qty = parseInt(quantityEl.textContent);
        if (qty > 1) {
          qty--;
          quantityEl.textContent = qty;
          updateTotal();
        }
      });
    });

    updateTotal();
  };
</script>
@endpush