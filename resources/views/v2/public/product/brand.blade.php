@extends('v2.layouts.public.app', ['title'=>$brand->name.' | Hilya Collection'])
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
    <section class="space-y-8 px-4">
        <h1 class="block uppercase font-bold text-2xl">Produk {{$brand->name}}</h1>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-6">
            @forelse ($brandProducts as $key => $brandProduct)
            <div class="flex flex-col gap-2 h-full">
                <div class="relative cursor-pointer" onclick="window.location.href='{{$brandProduct->link}}'">
                    <div class="absolute top-0 left-0 p-2">
                        @if ($brandProduct->availability == 'pre-order')
                            <span class="bg-color-3 text-white p-1 px-2 rounded text-xs font-semibold">PRE-ORDER</span>
                        @endif
                        @if ($brandProduct->ispromo)
                            <span class="bg-red-500 text-white p-1 px-2 rounded text-xs font-semibold">PROMO</span>    
                        @endif
                    </div>
                    <img src="{{asset('storage/'.$brandProduct->productImages->first()->image)}}" alt="" class="w-full rounded">
                </div>
                <div class="flex flex-col gap-4 h-full">
                    <div class="flex flex-col gap-1 grow">
                        <p class="font-semibold">{{$brandProduct->productBrand->name}}</p>
                        <p>{{$brandProduct->name}}</p>
                        <div class="flex flex-col">
                            <span class="font-semibold">{{\Helper::rupiah($brandProduct->product_items_min_price - (int)($brandProduct->product_items_min_price*$brandProduct->productItems->first()->discount/100))}} - {{\Helper::rupiah($brandProduct->product_items_max_price - (int)($brandProduct->product_items_max_price*$brandProduct->productItems->first()->discount/100))}}</span>
                            @if ($brandProduct->ispromo)
                                <span class="font-semibold text-xs text-red-500">{{$brandProduct->productItems->first()->discount}}% <s class="font-normal text-color-5">{{\Helper::rupiah($brandProduct->product_items_min_price)}} - {{\Helper::rupiah($brandProduct->product_items_max_price)}}</s></span>
                            @endif
                        </div>
                        <div class="flex justify-between text-xs">
                            <span>{{$brandProduct->category ?? 'Busana'}}</span>
                            <button data-popover-target="stock-{{$key}}" data-popover-trigger="click">Lihat stok...</button>
                            <div data-popover id="stock-{{$key}}" role="tooltip" class="absolute z-[1] invisible inline-block w-64 text-sm transition-opacity duration-300 bg-white border !border-color-4 rounded shadow-sm opacity-0">
                                <div class="px-3 py-2 border-b bg-color-5">
                                    <h3 class="font-semibold text-white">Stok</h3>
                                </div>
                                <div class="px-3 py-2 flex flex-wrap gap-2 text-color-5">
                                    @foreach ($brandProduct->productItems as $productItem)
                                        <p class="p-1 px-2 border rounded border-color-4"><span class="font-bold">{{$productItem->size}}</span> {{$productItem->stock}}</p>
                                    @endforeach
                                </div>
                                <div data-popper-arrow></div>
                            </div>
                        </div>
                    </div>
                    <button class="px-5 w-full h-[42px] text-xs sm:text-base bg-color-4 text-white font-semibold uppercase border rounded" onclick="window.location.href='{{$brandProduct->link}}'">Lihat Produk</button>
                </div>
            </div>
            @empty
            <div class="flex flex-col text-xl text-color-2 mt-8 justify-center items-center md:col-span-4 sm:col-span-2 col-span-1">
                <h1 class="font-bold text-3xl">404</h1>
                <span>PRODUK KOSONG</span>
            </div>
            @endforelse
        </div>
        @empty(!$brandProducts)
        {!! $brandProducts->links() !!}
        @endempty
    </section>

    <section class="my-16 sm:my-36 px-4">
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
@endsection