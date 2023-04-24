<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ @$title ?? 'Hilya Collection' }}</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    @vite('resources/css/app.css')
    @stack('script-head')
    @stack('style')
</head>

<body class="font-inter text-color-4">
    <section class="fixed z-10 w-full px-10 border bg-white">
        <nav class="py-3 h-[92px]">
            <div class="flex h-full items-center justify-between">
                <div class="flex sm:hidden">
                    <button class="material-icons !text-4xl" onclick="$('#side-menu').toggleClass('hidden')">menu</button>
                </div>
                <div class="hidden sm:flex items-center gap-3 cursor-pointer" onclick="window.location.href='{{route('customer.dashboard')}}'">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="">
                    <h1 class="text-xl"><span class="font-bold">Hillia</span> <span
                            class="text-color-2">Collection</span></h1>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-3 border-b cursor-pointer" onclick="$('#search-modal,#bg-search-modal').toggleClass('hidden')">
                        <span class="material-icons !text-3xl">search</span>
                        <button class="hidden sm:block w-40 text-left text-color-2">Cari produk disini...</button>
                    </div>
                    <button onclick="window.location.href='{{route('customer.cart')}}'" class="relative block"><span class="material-icons !text-4xl">shopping_bag</span><span class="absolute bottom-0 -right-1 min-w-[24px] bg-red-500 p-1 rounded-full text-xs text-white">{{App\Models\Order::where('status', 'cart')->first()?->orderItems()->count() ?? 0}}</span></button>
                    <button onclick="$('#menu,#menu-background').toggleClass('hidden')" class="block"><span class="material-icons !text-4xl">account_circle</span></button>
                </div>
                <div id="menu-background" class="hidden absolute top-0 bottom-0 left-0 right-0 opacity-100 h-screen w-screen" onclick="$('#menu,#menu-background').toggleClass('hidden')"></div>
                <div id="menu" class="hidden absolute w-full sm:max-w-xs z-10 top-full right-0 mt-0 sm:mt-4 mr-0 sm:mr-10 p-3 rounded border bg-white shadow-lg">
                    <ul class="space-y-1">
                        <li onclick="window.location.href='{{route('customer.dashboard')}}'" class="whitespace-nowrap overflow-hidden text-ellipsis font-semibold cursor-pointer">Selamat datang, {{auth()->user()->username}}</li>
                        <li>
                            <button class="flex w-full justify-between items-center cursor-pointer" onclick="window.location.href='{{route('customer.cart')}}?status=paid'">
                                <span>Keranjang</span>
                                <span class="min-w-[24px] bg-red-500 p-1 rounded-full text-xs text-white">{{App\Models\Order::where('status', 'cart')->first()?->orderItems()->count() ?? 0}}</span>
                            </button>
                        </li>
                        <li>
                            <button class="flex w-full justify-between items-center cursor-pointer" onclick="window.location.href='{{route('customer.orders')}}'">
                                <span>Pesanan</span>
                            </button>
                        </li>
                        <hr>
                        <li>
                            <form action="{{route('logout')}}" method="post" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
                            @csrf
                            <button class="text-red-500">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </section>
    <section class="">
        <input type="hidden" name="product_search_url" value="{{route('search-products')}}">
        <div id="bg-search-modal" class="hidden fixed z-10 top-0 bottom-0 right-0 left-0 bg-transparent" onclick="$('#search-modal,#bg-search-modal').toggleClass('hidden')"></div>
        <div id="search-modal" class="hidden fixed z-20 sm:left-1/2 sm:-translate-x-1/2 sm:top-9 w-full sm:w-[850px] h-fit p-4 space-y-4 bg-white shadow-lg">
            <button class="flex ml-auto material-icons" onclick="$('#search-modal,#bg-search-modal').toggleClass('hidden')">close</button>
            <div class="flex items-center gap-2 h-11 border-color-4 border-b">
                <span class="material-icons !text-3xl">search</span>
                <input id="search-input" type="search" placeholder="Cari produk disini..." class="w-full bg-transparent outline-none" type="text" onchange="searchProducts()">
            </div>
            <div id="results-wrapper" class="overflow-auto max-h-[420px] space-y-4">
                
            </div>
        </div>
    </section>
    <section class="flex h-screen">
        <aside id="side-menu" class="hidden fixed sm:block w-full sm:w-[300px] h-full px-5 pt-28 border-r bg-white">
            <menu>
                <ul class="space-y-[10px]">
                    <li class="p-1 rounded {{ request()->is('customer/dashboard') ? 'bg-color-4 text-white' : '' }}">
                        <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-1">
                            <div class="w-9 flex items-center justify-center">
                                <span class="material-icons !text-3xl">dashboard</span>
                            </div>
                            Dashboard
                        </a>
                    </li>
                    <li class="p-1 rounded {{ request()->is('customer/cart') ? 'bg-color-4 text-white' : '' }}">
                        <a href="{{ route('customer.cart') }}" class="flex items-center gap-1">
                            <div class="w-9 flex items-center justify-center">
                                <span class="material-icons !text-3xl">shopping_bag</span>
                            </div>
                            Keranjang
                        </a>
                    </li>
                    <li class="p-1 rounded {{ request()->is('customer/checkout') ? 'bg-color-4 text-white' : '' }}">
                        <a href="{{route('customer.checkout')}}" class="flex items-center gap-1">
                            <div class="w-9 flex items-center justify-center">
                                <span class="material-icons !text-3xl">shopping_cart_checkout</span>
                            </div>
                            Checkout
                        </a>
                    </li>
                    <li class="p-1 rounded {{ request()->is('customer/orders*') ? 'bg-color-4 text-white' : '' }}">
                        <a href="{{route('customer.orders')}}" class="flex items-center">
                            <div class="w-9 flex items-center justify-center">
                                <span class="material-icons !text-3xl">list_alt</span>
                            </div>
                            Pesanan
                        </a>
                    </li>
                    <li class="p-1 rounded {{ request()->is('customer/address-book*') ? 'bg-color-4 text-white' : '' }}">
                        <a href="{{route('customer.address-book')}}" class="flex items-center gap-1">
                            <div class="w-9 flex items-center justify-center">
                                <span class="material-icons !text-3xl">contacts</span>
                            </div>
                            Buku Alamat
                        </a>
                    </li>
                    <li class="p-1 rounded {{ request()->is('customer/account') ? 'bg-color-4 text-white' : '' }}">
                        <a href="{{route('customer.account.index')}}" class="flex items-center gap-1">
                            <div class="w-9 flex items-center justify-center">
                                <span class="material-icons !text-3xl">manage_accounts</span>
                            </div>
                            Pengaturan Akun
                        </a>
                    </li>
                    <hr>
                    <li class="p-1 rounded bg-red-500 text-white">
                        <form action="{{route('logout')}}" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')" method="post">
                            @csrf
                            <button class="flex w-full items-center gap-1">
                                <div class="w-9 flex items-center justify-center">
                                    <span class="material-icons !text-3xl">logout</span>
                                </div>
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </menu>
        </aside>
        <main class="w-full overflow-hidden h-fit sm:ml-[300px] px-10 pt-28 pb-5 space-y-5">
            @yield('content')
        </main>
    </section>
    <script src="{{asset('js/product-search.js')}}"></script>
    @stack('script')
</body>

</html>
