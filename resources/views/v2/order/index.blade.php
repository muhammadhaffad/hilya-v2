@extends('v2.layouts.customer.app', ['title' => 'Pesanan | Hillia Collection'])
@section('content')
    <div class="flex font-bold text-xl gap-1 items-center uppercase">
        <span class="material-icons !text-4xl">
            list_alt
        </span>
        Pesanan
    </div>
    <form id="form-search-order" action="" method="GET"></form>
    @foreach (request()->all() as $key => $value)
        @if (!in_array($key, ['start_date', 'end_date', 'search']))
            <input type="hidden" name="{{$key}}" value="{{$value}}" form="form-search-order">
        @endif
    @endforeach
    <section class="flex flex-col">
        <ul class="flex flex-wrap gap-1">
            <li>
                <a class="font-semibold" href="{{ request()->fullUrlWithQuery(['status' => '']) }}">
                    Semua ({{ $orders->count() }})
                </a>
            </li>
            <li>|</li>
            <li>
                <a class="font-semibold" href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}">Belum Bayar ({{ $orders->where('status', 'pending')->count() }})</a>
            </li>
            <li>|</li>
            <li>
                <a class="font-semibold" href="{{ request()->fullUrlWithQuery(['status' => 'paid']) }}">
                    Menunggu Konfirmasi ({{ $orders->where('status', 'paid')->count() }})
                </a>
            </li>
            <li>|</li>
            <li>
                <a class="font-semibold" href="{{ request()->fullUrlWithQuery(['status' => 'processing']) }}">
                    Sedang Diproses ({{ $orders->where('status', 'processing')->count() }})
                </a>
            </li>
            <li>|</li>
            <li>
                <a class="font-semibold" href="{{ request()->fullUrlWithQuery(['status' => 'shipping']) }}">
                    Sedang Dikirim ({{ $orders->where('status', 'shipping')->count() }})
                </a>
            </li>
            <li>|</li>
            <li>
                <a class="font-semibold" href="{{ request()->fullUrlWithQuery(['status' => 'ready']) }}">
                    Siap Diambil ({{ $orders->where('status', 'ready')->count() }})
                </a>
            </li>
        </ul>
        <div class="flex flex-wrap justify-between gap-2">
            <div class="flex flex-wrap gap-2">
                <div>
                    <label for="start-date" class="block text-sm">Dari tanggal:</label>
                    <input type="date" value="{{ @$_GET['start_date'] }}" name="start_date" id="start-date" class="p-2 border border-color-3 rounded" onchange="$('#form-search-order').submit()" form="form-search-order">
                </div>
                <div>
                    <label for="end-date" class="block text-sm">Sampai tanggal:</label>
                    <input type="date" value="{{ @$_GET['end_date'] }}" name="end_date" id="end-date" class="p-2 border border-color-3 rounded" onchange="$('#form-search-order').submit()" form="form-search-order">
                </div>
            </div>
            {{-- <div class="flex flex-col">
                <label for="status" class="text-sm">Status:</label>
                <select name="status" id="status" class="p-2 border border-color-3 rounded h-full">
                    <option value="pending">Belum Bayar</option>
                    <option value="paid">Menunggu Konfirmasi</option>
                    <option value="processing">Sedang Diproses</option>
                    <option value="shipping">Dalam Pengiriman</option>
                    <option value="delivered">Barang Diterima</option>
                </select>
            </div> --}}
            <div class="flex justify-self-end items-end gap-3 border-b">
                <span class="material-icons !text-3xl">search</span>
                <input type="search" name="search" value="{{ @$_GET['search'] }}" class="w-full py-2 text-left text-color-2 focus:outline-none" placeholder="Cari pesanan disini..." form="form-search-order">
            </div>
        </div>
    </section>
    @foreach ($orders as $index => $order)
        <div class="flex flex-wrap items-center justify-between w-full p-3 font-semibold text-white bg-color-4 uppercase rounded">
            <span>Kode Order: {{ $order->payment->order_code }}</span>
            @switch($order->status)
                @case('pending')
                    <span class="text-sm p-1 px-2 text-color-4 bg-white rounded">Belum Bayar</span>
                    @break
                @case('paid')
                    <span class="text-sm p-1 px-2 text-color-4 bg-white rounded">Menunggu Konfirmasi</span>
                    @break
                @default
                    <span class="text-sm p-1 px-2 text-color-4 bg-white rounded">{{ $order->status }}</span>
            @endswitch
        </div>
        <section class="p-5 flex flex-wrap justify-between border border-color-4 rounded-lg space-y-4">
            <ul>
                <li>Waktu Transaksi: <span class="font-semibold uppercase">{{ $order->payment->transactiontime }}</span></li>
                <li>Waktu Bayar: <span class="font-semibold uppercase">{{ $order->payment->settlementtime ?? '-' }}</span></li>
            </ul>
            <ul>
                <li class="flex flex-col">Total: <span class="font-semibold text-2xl">{{ Helper::rupiah($order->payment->amount) }}</span></li>
                <li>
                    <button class="underline" onclick="$('#order-{{$index}}').slideToggle()">
                        Lihat Rincian...        
                    </button>
                </li>
            </ul>
            <div class="w-full border-b border-color-3">
                <section id="order-{{$index}}" class="hidden w-full space-y-4">
                    @php $custom_properties = collect($order->custom_properties) @endphp
                    @foreach ($order->orderItems as $orderItem)
                        <div class="flex flex-wrap items-center justify-between gap-2 p-2 border border-color-4 rounded-lg">
                            <div class="flex flex-wrap items-center gap-2">
                                <img src="{{ asset('assets/images/sample.png') }}" alt=""
                                    class="w-24">
                                <ul class="max-w-[300px]">
                                    <li class="font-bold"><span
                                            class="uppercase">{{ $orderItem->productItem->product->productBrand->name }}</span>
                                        {{ $orderItem->productItem->product->name }}</li>
                                    <li class="capitalize">
                                        {{ ($orderItem->productItem->gender == 'koko' ? 'Laki-laki' : 'Perempuan') . ' ' . $orderItem->productItem->age }}
                                    </li>
                                    <li>{{ $orderItem->productItem->size }}</li>
                                </ul>
                            </div>
                            <div class="max-w-[160px]">
                                <ul>
                                    <li class="font-bold">{{ $orderItem->productItem->color }}</li>
                                    <li>{{ $orderItem->productItem->note_bene }}</li>
                                </ul>
                            </div>
                            <div class="w-max">
                                @php $property = $custom_properties->where('id', $orderItem->productItem->id)->first() @endphp
                                <ul>
                                    <li class="font-bold text-xl">
                                        {{ $orderItem->qty }} ×
                                        {{ Helper::rupiah($orderItem->productItem->price - (($property) ? (int)($property['price']*$property['discount']/100) : 0) ) }}
                                    </li>
                                    @if ($property)
                                        <li class="text-sm"><s>{{ Helper::rupiah($property['price']) }}</s> 
                                            <span class="font-bold text-red-500">{{ $property['discount'] }}%</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </section>
            </div>
            <button class="ml-auto py-2 px-4 uppercase font-medium border bg-color-4 border-color-4 text-white rounded" onclick="window.location='{{ route('customer.orders.show', ['code' => ($order->payment?->order_code ?: '-')]) }}'">
                Lihat Pesanan
            </button>
        </section>
    @endforeach
@endsection