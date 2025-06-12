<nav class="w-full fixed top-0 bg-[#f85270] px-6 py-4 z-10 shadow">
    <div class="max-w-7xl mx-auto flex items-center justify-between gap-6">
    <!-- Logo -->
    <div class="text-lg font-semibold text-black shrink-0">
        <a href="{{ route('front.index') }}" class="flex w-[154px] shrink-0 items-center">
        <img src="{{asset('images/icon/logo.svg')}}" alt="logo" />
        </a>
    </div>

    <!-- Search Bar -->
    <form class="flex-grow max-w-xl">
        <div class="relative w-full">
            <input
                type="text"
                placeholder="Type anything to search..."
                class="w-full rounded-full pl-5 pr-10 py-2 bg-pink-100 text-black placeholder:text-gray-500 focus:outline-none shadow-sm"
            />
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-black">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z">
                </path>
                </svg>
            </button>
            </div>
        </form>

        <!-- Auth Buttons -->
        <div class="flex items-center gap-3 shrink-0">
            <a href="#" class="text-black hover:underline">Log in</a>
            <a href="#" class="px-4 py-1 border border-black rounded hover:bg-black hover:text-white transition">
            Sign Up
            </a>
        </div>
    </div>
</nav>