<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ @$title ?? 'Hilya Collection' }}</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
    @vite('resources/css/app.css')
    @stack('script-head')
    @stack('style')
</head>

<body class="font-inter text-color-4">
    <nav class="fixed z-10 w-full px-10 border bg-white">
        <div class="py-3 h-[92px] overflow-auto">
            <div class="flex h-full items-center justify-between">
                <div class="flex lg:hidden">
                    <button class="material-icons !text-4xl" onclick="$('#nav-menu').toggleClass('scale-x-0')">menu</button>
                </div>
                <div id="nav-menu" class="absolute bg-white lg:static top-full left-0 right-0 flex flex-col lg:flex-row items-start lg:items-center px-10 py-5 lg:p-0 gap-3 border lg:border-0 scale-x-0 lg:scale-100 shadow-lg lg:shadow-none">
                    <div class="w-max">
                        <img class="cursor-pointer w-20" src="{{ asset('assets/images/logo.png') }}" onclick="window.location.href='{{route('home')}}'" alt="">
                    </div>
                    <a href="{{route('home')}}" class="uppercase cursor-pointer whitespace-nowrap overflow-hidden text-ellipsis">Home</a>
                    <a href="{{route('product.promo')}}" class="uppercase cursor-pointer whitespace-nowrap overflow-hidden text-ellipsis">Promo</a>
                    <a href="{{route('product.preorder')}}" class="uppercase cursor-pointer whitespace-nowrap overflow-hidden text-ellipsis">Pre-Order</a>
                    <a href="{{route('home').'#footer'}}" class="uppercase cursor-pointer whitespace-nowrap overflow-hidden text-ellipsis">Hubungi Kami</a>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-3 border-b cursor-pointer" onclick="$('#search-modal,#bg-search-modal').toggleClass('hidden')">
                        <span class="material-icons !text-3xl">search</span>
                        <button class="hidden sm:block w-40 text-left text-color-2">Cari produk disini...</button>
                    </div>
                    @auth
                        @if (auth()->user()->role == 'customer')
                        <button onclick="window.location.href='{{route('customer.cart')}}'" class="relative block"><span class="material-icons !text-4xl">shopping_bag</span><span class="absolute bottom-0 -right-1 min-w-[24px] bg-red-500 p-1 rounded-full text-xs text-white">{{App\Models\Order::where('status', 'cart')->first()?->orderItems()->count() ?? 0}}</span></button>
                        @endif
                    <button onclick="$('#menu,#menu-background').toggleClass('hidden')" class="block"><span class="material-icons !text-4xl">account_circle</span></button>
                        @if (auth()->user()->role == 'admin')
                        <div id="menu-background" class="hidden absolute top-0 bottom-0 left-0 right-0 opacity-100 h-screen w-screen" onclick="$('#menu,#menu-background').toggleClass('hidden')"></div>
                        <div id="menu" class="hidden absolute w-full sm:max-w-xs z-10 top-full right-0 mt-0 sm:mt-4 mr-0 sm:mr-10 p-3 rounded border bg-white shadow-lg">
                            <ul class="space-y-1">
                                <li onclick="window.location.href='{{route('admin.dashboard')}}'" class="whitespace-nowrap overflow-hidden text-ellipsis font-semibold cursor-pointer">Selamat datang, {{auth()->user()->username}}</li>
                                <li>
                                    <button class="flex w-full justify-between items-center cursor-pointer" onclick="window.location.href='{{route('admin.order')}}?status=paid'">
                                        <span>Pesanan Masuk</span>
                                        <span class="font-semibold bg-red-500 text-white p-1 rounded text-xs">{{App\Models\Order::where('status', 'paid')->count()}}</span>
                                    </button>
                                </li>
                                <li>
                                    <button class="flex w-full justify-between items-center cursor-pointer" onclick="window.location.href='{{route('admin.product.create')}}'">
                                        <span>Tambah Produk</span>
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
                        @else
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
                        @endif
                    @else
                    <button class="uppercase p-2 px-4 rounded border border-color-1 text-sm" onclick="window.location.href='{{route('login')}}'">Masuk</button>                    
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    <section>
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
    <main class="w-full max-w-[1228px] mx-auto overflow-hidden h-fit pt-28 pb-5">
        @yield('content')
    </main>
    <footer id="footer" class="px-10 text-white bg-color-4 py-10">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="space-y-3">
                <h2 class="font-bold text-2xl">Hubungi Kami</h2>
                <ul class="space-y-1">
                    <li class="font-semibold">Hillia Collection</li>
                    <li>Teras Keke Lamongan Jl. Nasional 1, Moropelang, Kec. Babat, Kab. Lamongan</li>
                    <li>+62 8120-7232-783</li>
                </ul>
            </div>
            <div class="space-y-3">
                <h2 class="font-bold text-2xl">Toko Kami</h2>
                <div style="width: 100%">
                    <iframe width="100%" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=100%25&amp;height=600&amp;hl=en&amp;q=Teras%20Keke%20Lamongan+(My%20Business%20Name)&amp;t=&amp;z=17&amp;ie=UTF8&amp;iwloc=B&amp;output=embed">
                        <a href="https://www.maps.ie/distance-area-calculator.html">measure area map</a>
                    </iframe>
                </div>
            </div>
            <div class="space-y-3">
                <h2 class="font-bold text-2xl">Media Sosial</h2>
                <div>

                </div>
            </div>
        </div>
    </footer>
    <script src="{{asset('js/product-search.js')}}"></script>
    <script>
    $(function() {
        $(".overflow-x-scroll").mousewheel(function(event, delta) {
            this.scrollLeft -= (delta * 50);
            event.preventDefault();
        });
    });
    </script>
    @stack('script')
</body>

</html>
