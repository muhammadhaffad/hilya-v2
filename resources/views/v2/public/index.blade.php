@extends('v2.layouts.public.app', ['title'=>'Hilya Collection'])
@section('content')
@push('style')
<link
rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css"
/>
@endpush
@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
@endpush
    <section id="hero" class="grid grid-cols-1 gap-4 items-center px-4">
        <div class="relative">
            <div class="flex flex-col gap-4 py-8 justify-center w-full sm:w-1/2">
                <p>Selamat Datang di <span class="font-semibold">Hilya Collection</span></p>
                <h1 class="text-4xl sm:text-6xl font-bold">Temukan Busana Muslim Pilihanmu</h1>
                <p>Reseller busana muslim terlengkap</p>
                <button class="hidden sm:block px-5 w-full sm:w-fit h-[42px] bg-color-4 text-white font-semibold uppercase rounded" onclick="window.location.href='{{route('home').'#ready-product'}}'">Mulai Belanja</button>
            </div>
            <img src="{{asset('assets/images/hero image.png')}}" class="absolute h-full bg-contain ml-auto bottom-0 -z-10 right-0">
        </div>
        <button class="sm:hidden px-5 w-full sm:w-fit h-[42px] bg-color-4 text-white font-semibold uppercase rounded" onclick="window.location.href='{{route('home').'#ready-product'}}'">Mulai Belanja</button>
    </section>

    <section class="mt-4 sm:mt-8 px-4">
        <div class="flex gap-4 flex-nowrap max-w-[1228px]">
            <div class="overflow-x-scroll w-full flex gap-4">
                @foreach($productBrands as $brand)
                <div>
                    <div class="w-28 sm:w-36 text-center space-y-2 cursor-pointer" onclick="window.location.href='{{route('product.brand', ['brand' => $brand->slug])}}'">
                        <div class="w-full h-28 sm:h-36 border rounded flex items-center">
                            <img src="{{asset('storage/'.$brand->image)}}" class="w-full">
                        </div>
                        <span class="block font-semibold">{{$brand->name}}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="ready-product" class="mt-16 sm:mt-36 space-y-8 px-4">
        <h1 class="block uppercase font-bold text-2xl">Produk Tersedia</h1>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 sm:gap-6">
            @foreach ($readyProducts as $key => $readyProduct)
            <div class="flex flex-col gap-2 h-full">
                <div class="relative cursor-pointer" onclick="window.location.href='{{$readyProduct->link}}'">
                    <div class="absolute top-0 left-0 p-2">
                        @if ($readyProduct->availability == 'pre-order')
                            <span class="bg-color-3 text-white p-1 px-2 rounded text-xs font-semibold">PRE-ORDER</span>
                        @endif
                        @if ($readyProduct->ispromo)
                            <span class="bg-red-500 text-white p-1 px-2 rounded text-xs font-semibold">PROMO</span>    
                        @endif
                    </div>
                    <img src="{{asset('storage/'.$readyProduct->productImages->first()->image)}}" alt="" class="w-full rounded">
                </div>
                <div class="flex flex-col gap-4 h-full">
                    <div class="flex flex-col gap-1 grow">
                        <p class="font-semibold">{{$readyProduct->productBrand->name}}</p>
                        <p>{{$readyProduct->name}}</p>
                        <div class="flex flex-col">
                            <span class="font-semibold">{{\Helper::rupiah($readyProduct->product_items_min_price - (int)($readyProduct->product_items_min_price*$readyProduct->productItems->first()->discount/100))}} - {{\Helper::rupiah($readyProduct->product_items_max_price - (int)($readyProduct->product_items_max_price*$readyProduct->productItems->first()->discount/100))}}</span>
                            @if ($readyProduct->ispromo)
                                <span class="font-semibold text-xs text-red-500">{{$readyProduct->productItems->first()->discount}}% <s class="font-normal text-color-5">{{\Helper::rupiah($readyProduct->product_items_min_price)}} - {{\Helper::rupiah($readyProduct->product_items_max_price)}}</s></span>
                            @endif
                        </div>
                        <div class="flex justify-between text-xs">
                            <span>{{$readyProduct->category ?? 'Busana'}}</span>
                            <button data-popover-target="stock-{{$key}}" data-popover-trigger="click">Lihat stok...</button>
                            <div data-popover id="stock-{{$key}}" role="tooltip" class="absolute z-[1] invisible inline-block w-64 text-sm transition-opacity duration-300 bg-white border !border-color-4 rounded shadow-sm opacity-0">
                                <div class="px-3 py-2 border-b bg-color-5">
                                    <h3 class="font-semibold text-white">Stok</h3>
                                </div>
                                <div class="px-3 py-2 flex flex-wrap gap-2 text-color-5">
                                    @foreach ($readyProduct->productItems as $productItem)
                                        <p class="p-1 px-2 border rounded border-color-4"><span class="font-bold">{{$productItem->size}}</span> {{$productItem->stock}}</p>
                                    @endforeach
                                </div>
                                <div data-popper-arrow></div>
                            </div>
                        </div>
                    </div>
                    <button class="px-5 w-full h-[42px] text-xs sm:text-base bg-color-4 text-white font-semibold uppercase border rounded" onclick="window.location.href='{{$readyProduct->link}}'">Lihat Produk</button>
                </div>
            </div>
            @endforeach
        </div>
        @php
            $productReadyCount = App\Models\Product::ready()->whereHas('productItems', fn ($q) => $q->inStock())->count();
        @endphp
        @if ($productReadyCount >= 20)
        <button class="px-5 flex mx-auto items-center h-[42px] font-semibold uppercase border border-color-4 hover:bg-color-4 hover:text-white rounded" onclick="window.location.href='{{route('product.ready')}}'">Lihat Semua</button>
        @endif
        {{-- @if ($productReadyCount < 40)
            @if ((request()->get('limit') ?? 10) < $productReadyCount)
                <button class="px-5 flex mx-auto items-center h-[42px] font-semibold uppercase border border-color-4 hover:bg-color-4 hover:text-white rounded" onclick="window.location.href='{{route('home').'?limit='.(request()->get('limit')+10)}}'">Lihat Lainnya</button>
            @endif
        @else
            @if ((request()->get('limit') ?? 10) < 40)
                <button class="px-5 flex mx-auto items-center h-[42px] font-semibold uppercase border border-color-4 hover:bg-color-4 hover:text-white rounded" onclick="window.location.href='{{route('home').'?limit='.(request()->get('limit')+10)}}'">Lihat Lainnya</button>
            @else
                <button class="px-5 flex mx-auto items-center h-[42px] font-semibold uppercase border border-color-4 hover:bg-color-4 hover:text-white rounded" onclick="window.location.href='{{route('product.ready')}}'">Lihat Semua</button>
            @endif
        @endif --}}
    </section>

    <section class="mt-16 sm:mt-36 space-y-8 px-4">
        <div class="flex justify-between items-center">
            <h1 class="block uppercase font-bold text-2xl">Produk Promo</h1>
            <button class="block uppercase font-semibold underline text-right" onclick="window.location.href='{{route('product.promo')}}'">Lihat Semua...</button>
        </div>
        <div class="flex gap-4 flex-nowrap w-full">
            <div class="overflow-x-scroll w-full flex gap-4 sm:gap-8 overflow-y-hidden">
                @forelse ($promoProducts as $key => $promoProduct)
                <div>
                    <div class="flex flex-col w-[290px] gap-2 h-full">
                        <div class="relative cursor-pointer" onclick="window.location.href='{{$promoProduct->link}}'">
                            <div class="absolute top-0 left-0 p-2">
                                @if ($promoProduct->availability == 'pre-order')
                                    <span class="bg-color-3 text-white p-1 px-2 rounded text-xs font-semibold">PRE-ORDER</span>
                                @endif
                                @if ($promoProduct->ispromo)
                                    <span class="bg-red-500 text-white p-1 px-2 rounded text-xs font-semibold">PROMO</span>    
                                @endif
                            </div>
                            <img src="{{asset('storage/'.$promoProduct->productImages->first()->image)}}" alt="" class="w-full rounded">
                        </div>
                        <div class="flex flex-col gap-4 h-full">
                            <div class="flex flex-col gap-1 grow">
                                <p class="font-semibold">{{$promoProduct->productBrand->name}}</p>
                                <p>{{$promoProduct->name}}</p>
                                <div class="flex flex-col">
                                    <span class="font-semibold">{{\Helper::rupiah($promoProduct->product_items_min_price - (int)($promoProduct->product_items_min_price*$promoProduct->productItems->first()->discount/100))}} - {{\Helper::rupiah($promoProduct->product_items_max_price - (int)($promoProduct->product_items_max_price*$promoProduct->productItems->first()->discount/100))}}</span>
                                    @if ($promoProduct->ispromo)
                                        <span class="font-semibold text-xs text-red-500">{{$promoProduct->productItems->first()->discount}}% <s class="font-normal text-color-5">{{\Helper::rupiah($promoProduct->product_items_min_price)}} - {{\Helper::rupiah($promoProduct->product_items_max_price)}}</s></span>
                                    @endif
                                </div>
                                <div class="flex justify-between relative">
                                    <span>{{$promoProduct->category ?? 'Busana'}}</span>
                                    <button data-popover-target="promo-stock-{{$key}}" data-popover-trigger="click">Lihat stok...</button>
                                    <div data-popover id="promo-stock-{{$key}}" role="tooltip" class="absolute z-[1] invisible inline-block w-64 text-sm transition-opacity duration-300 bg-white border !border-color-4 rounded shadow-sm opacity-0">
                                        <div class="px-3 py-2 border-b bg-color-5">
                                            <h3 class="font-semibold text-white">Stok</h3>
                                        </div>
                                        <div class="px-3 py-2 flex flex-wrap gap-2 text-color-5">
                                            @foreach ($promoProduct->productItems as $productItem)
                                                <p class="p-1 px-2 border rounded border-color-4"><span class="font-bold">{{$productItem->size}}</span> {{$productItem->stock}}</p>
                                            @endforeach
                                        </div>
                                        <div data-popper-arrow></div>
                                    </div>
                                    {{-- <div class="relative">
                                        <button target="stock-{{$key}}">Lihat stok...</button>
                                        <div id="stock-{{$key}}" class="absolute z-[1] -top-2 -translate-y-full -translate-x-1/2 left-1/2 inline-block w-64 text-sm transition-opacity duration-300 bg-white border !border-color-4 rounded shadow-sm">
                                            <div class="px-3 py-2 border-b bg-color-5">
                                                <h3 class="font-semibold text-white">Stok</h3>
                                            </div>
                                            <div class="px-3 py-2 flex flex-wrap gap-2 text-color-5">
                                                @foreach ($promoProduct->productItems as $productItem)
                                                    <p class="p-1 px-2 border rounded border-color-4"><span class="font-bold">{{$productItem->size}}</span> {{$productItem->stock}}</p>
                                                @endforeach
                                            </div>
                                            <div data-popper-arrow></div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                            <button class="px-5 w-full h-[42px] bg-color-4 text-white font-semibold uppercase border rounded" onclick="window.location.href='{{$promoProduct->link}}'">Lihat Produk</button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center text-center justify-center w-full h-52 text-color-2 uppercase font-semibold text-xl border border-color-3 rounded">
                    <h1 class="font-bold text-3xl">404</h1>
                    <span>PRODUK PROMO KOSONG</span>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="my-16 sm:my-36 space-y-8 px-4">
        <div class="flex justify-between items-center">
            <h1 class="block uppercase font-bold text-2xl">Produk Pre-Order</h1>
            <button class="block uppercase font-semibold underline" onclick="window.location.href='{{route('product.preorder')}}'">Lihat Semua...</button>
        </div>
        <div class="flex gap-4 flex-nowrap w-full">
            <div class="overflow-x-scroll w-full flex gap-8 overflow-y-hidden">
                @forelse ($preorderProducts as $key => $preorderProduct)
                <div>
                    <div class="flex flex-col w-[290px] gap-2 h-full">
                        <div class="relative cursor-pointer" onclick="window.location.href='{{$preorderProduct->link}}'">
                            <div class="absolute top-0 left-0 p-2">
                                @if ($preorderProduct->availability == 'pre-order')
                                    <span class="bg-color-3 text-white p-1 px-2 rounded text-xs font-semibold">PRE-ORDER</span>
                                @endif
                                @if ($preorderProduct->ispromo)
                                    <span class="bg-red-500 text-white p-1 px-2 rounded text-xs font-semibold">PROMO</span>    
                                @endif
                            </div>
                            <img src="{{asset('storage/'.$preorderProduct->productImages->first()->image)}}" alt="" class="w-full rounded">
                        </div>
                        <div class="flex flex-col gap-4 h-full">
                            <div class="flex flex-col gap-1 grow">
                                <p class="font-semibold">{{$preorderProduct->productBrand->name}}</p>
                                <p>{{$preorderProduct->name}}</p>
                                <div class="flex flex-col">
                                    <span class="font-semibold">{{\Helper::rupiah($preorderProduct->product_items_min_price - (int)($preorderProduct->product_items_min_price*$preorderProduct->productItems->first()->discount/100))}} - {{\Helper::rupiah($preorderProduct->product_items_max_price - (int)($preorderProduct->product_items_max_price*$preorderProduct->productItems->first()->discount/100))}}</span>
                                    @if ($preorderProduct->ispromo)
                                        <span class="font-semibold text-xs text-red-500">{{$preorderProduct->productItems->first()->discount}}% <s class="font-normal text-color-5">{{\Helper::rupiah($preorderProduct->product_items_min_price)}} - {{\Helper::rupiah($preorderProduct->product_items_max_price)}}</s></span>
                                    @endif
                                </div>
                                <div class="flex justify-between relative">
                                    <span>{{$preorderProduct->category ?? 'Busana'}}</span>
                                    <button data-popover-target="preorder-stock-{{$key}}" data-popover-trigger="click">Lihat stok...</button>
                                    <div data-popover id="preorder-stock-{{$key}}" role="tooltip" class="absolute z-[1] invisible inline-block w-64 text-sm transition-opacity duration-300 bg-white border !border-color-4 rounded shadow-sm opacity-0">
                                        <div class="px-3 py-2 border-b bg-color-5">
                                            <h3 class="font-semibold text-white">Stok</h3>
                                        </div>
                                        <div class="px-3 py-2 flex flex-wrap gap-2 text-color-5">
                                            @foreach ($preorderProduct->productItems as $productItem)
                                                <p class="p-1 px-2 border rounded border-color-4"><span class="font-bold">{{$productItem->size}}</span> {{$productItem->stock}}</p>
                                            @endforeach
                                        </div>
                                        <div data-popper-arrow></div>
                                    </div>
                                </div>
                            </div>
                            <button class="px-5 w-full h-[42px] bg-color-4 text-white font-semibold uppercase border rounded" onclick="window.location.href='{{$preorderProduct->link}}'">Lihat Produk</button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center text-center justify-center w-full h-52 text-color-2 uppercase font-semibold text-xl border border-color-3 rounded">
                    <h1 class="font-bold text-3xl">404</h1>
                    <span>PRODUK PRE-ORDER KOSONG</span>
                </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection