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
        <nav class="p-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="">
                    <h1 class="text-xl"><span class="font-bold">Hillia</span> <span
                            class="text-color-2">Collection</span></h1>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-3 border-b">
                        <span class="material-icons !text-3xl">search</span>
                        <button class="w-80 text-left text-color-2">Cari produk disini...</button>
                    </div>
                    <span class="material-icons !text-3xl">shopping_bag</span>
                    <span class="material-icons !text-3xl">account_circle</span>
                </div>
            </div>
        </nav>
    </section>
    <section class="flex h-screen">
        <aside class="hidden sm:block fixed w-[300px] h-full px-5 pt-28 border-r bg-white">
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
                </ul>
            </menu>
        </aside>
        <main class="w-full h-fit sm:ml-[300px] px-10 pt-28 pb-5 space-y-5">
            @yield('content')
        </main>
    </section>
    @stack('script')
</body>

</html>
