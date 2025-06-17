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
        <h1 class="text-black text-3xl font-semibold">My Orders</h1>
        <div class="flex items-center gap-3 shrink-0">
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
    </div>
</nav>

<section class="w-full max-w-7xl mx-auto pt-20 px-5 pb-10">
    @foreach ($orders as $order)
        <div class="flex justify-between items-center py-4 border-y border-gray-300 gap-4 rounded-md">
            <div>
                <p class="text-sm font-semibold">
                Order Id : 
                <a href="{{ route('front.show_order', $order->id) }}" class="text-blue-600 hover:underline">{{ $order->code }}</a>
                </p>
                <p class="text-xs text-black mt-1">{{ $order->created_at}}</p>
            </div>
            <div>
                @php
                    $status = $order->status;
                @endphp

                @if ($status === 'pending')
                    <div class="min-w-[80px] text-center bg-red-500  text-white px-4 py-1.5 rounded-full text-sm font-medium">
                    Unpaid
                    </div>
                @elseif ($status === 'tf_uploaded')
                    <div class="min-w-[80px] text-center text-white px-4 py-1.5 rounded-full text-sm font-medium" style="background-color: #facc15;">
                        Pending Verification
                    </div>

                @elseif ($status === 'failed')
                    <div class="min-w-[80px] text-center bg-red-500 text-white px-4 py-1.5 rounded-full text-sm font-medium">
                        Failed
                    </div>
                @elseif ($status === 'success')
                    <div class="min-w-[80px] text-center bg-green-500 text-white px-4 py-1.5 rounded-full text-sm font-medium">
                        Success
                    </div>
                @endif
            </div>
        </div>
    @endforeach


</section>
@endsection

@push('after-script')

@endpush