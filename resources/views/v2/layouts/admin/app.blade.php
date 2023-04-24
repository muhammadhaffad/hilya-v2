<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ @$title ?? 'Hilya Collection' }}</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    @vite('resources/css/app.css')
    @stack('script-head')
    @stack('style')
</head>

<body class="font-inter text-color-4">
    <section class="fixed z-30 px-10 w-full border bg-white">
        <nav class="py-3 h-[92px]">
            <div class="flex h-full items-center justify-between">
                <div class="flex sm:hidden">
                    <button class="material-icons !text-4xl" onclick="$('#side-menu').toggleClass('hidden')">menu</button>
                </div>
                <div class="hidden sm:flex items-center gap-3 cursor-pointer" onclick="window.location.href='{{route('admin.dashboard')}}'">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="">
                    <h1 class="text-xl"><span class="font-bold">Hillia</span> <span
                            class="text-color-2">Collection</span></h1>
                </div>
                <div class="flex items-center gap-3">
                    <button class="material-icons !text-4xl cursor-pointer" onclick="$('#menu,#menu-background').toggleClass('hidden')">account_circle</button>
                </div>
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
            </div>
        </nav>
    </section>
    <section class="flex h-screen">
        <aside id="side-menu" class="hidden fixed z-20 sm:block w-full sm:w-[300px] h-full px-5 pt-28 border-r bg-white">
            <menu>
                <ul class="space-y-[10px] h-[calc(100vh-132px)] overflow-auto">
                    <li class="p-1 rounded {{ request()->is('admin/dashboard') ? 'bg-color-4 text-white' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1">
                            <div class="w-9 flex items-center justify-center">
                                <span class="material-icons !text-3xl">dashboard</span>
                            </div>
                            Dashboard
                        </a>
                    </li>
                    <li class="p-1 rounded {{ request()->is('admin/products*') ? 'bg-color-4 text-white' : '' }}">
                        <a href="{{route('admin.product')}}" class="flex items-center">
                            <div class="w-9 flex items-center justify-center">
                                <span class="material-icons !text-3xl">checkroom</span>
                            </div>
                            Produk
                        </a>
                    </li>
                    <li class="p-1 rounded {{ request()->is('admin/brands*') ? 'bg-color-4 text-white' : '' }}">
                        <a href="{{route('admin.brand')}}" class="flex items-center">
                            <div class="w-9 flex items-center justify-center">
                                <span class="material-icons !text-3xl">sell</span>
                            </div>
                            Brand
                        </a>
                    </li>
                    <li class="p-1 rounded {{ request()->is('admin/orders*') ? 'bg-color-4 text-white' : '' }}">
                        <a href="{{route('admin.order')}}" class="flex items-center">
                            <div class="w-9 flex items-center justify-center">
                                <span class="material-icons !text-3xl">list_alt</span>
                            </div>
                            Pesanan
                        </a>
                    </li>
                    <li class="p-1 rounded {{ request()->is('admin/account') ? 'bg-color-4 text-white' : '' }}">
                        <a href="{{route('admin.account')}}" class="flex items-center gap-1">
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
    @stack('script')
</body>

</html>
