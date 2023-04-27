@extends('v2.layouts.admin.app', ['title' => 'Detail Pesanan | Hillia Collection'])
@section('content')
@php
    $custom_properties = $order->custom_properties;
    $provinces = Helper::getProvinces();
    $cities = Helper::getCities();
    $subdistricts = Helper::getSubdistricts([$custom_properties['shipping_address']['city_id']]);
@endphp
    <div class="flex font-bold text-xl gap-1 items-center uppercase">
        <span class="material-icons !text-4xl">
            list_alt
        </span>
        Pesanan
    </div>
    <div class="flex flex-wrap items-center justify-between w-full gap-4 p-3 font-semibold text-white bg-color-4 uppercase rounded">
        <span>Kode Order: {{ $order->payment->order_code }}</span>
        @switch($order->status)
            @case('pending')
                <span class="text-sm p-1 px-2 text-color-4 bg-white rounded">Menunggu Pembayaran</span>
                @break
            @case('paid')
                <span class="text-sm p-1 px-2 text-color-4 bg-white rounded">Dalam Pesanan</span>
                @break
            @case('processing')
                <span class="text-sm p-1 px-2 text-color-4 bg-white rounded">Diproses</span>
                @break
            @case('shipping')
                <span class="text-sm p-1 px-2 text-color-4 bg-white rounded">Dikirim</span>
                @break
            @case('delivered')
                <span class="text-sm p-1 px-2 text-color-4 bg-white rounded">Diterima</span>
                @break
            @case('success')
                <span class="text-sm p-1 px-2 text-color-4 bg-white rounded">Selesai</span>
                @break
            @default
                <span class="text-sm p-1 px-2 text-color-4 bg-white rounded">{{ $order->status }}</span>
        @endswitch
    </div>
    <section class="p-5 gap-4 flex flex-wrap justify-between border border-color-4 rounded-lg">
        <ul class="max-w-full">
            <li>User Order: <span class="font-semibold">{{ $order->user->username }}</span></li>
            <li>Bank: <span class="font-semibold uppercase">{{ $order->payment->bank }}</span></li>
            <li>VA Number 
                <span class="flex flex-nowrap items-center justify-between w-full px-1 gap-1 font-semibold border border-color-2 text-color-3 rounded">
                    <span class="material-icons !text-base">content_copy</span>
                    <span class="overflow-auto">
                        {{ $order->payment->vanumber }}
                    </span>
                </span>
            </li>
        </ul>
        <ul>
            <li class="flex flex-wrap gap-x-2">Waktu Transaksi: <span class="font-semibold">{{ \Carbon\Carbon::parse($order->payment->transactiontime)->format('d F Y H:i:s') }}</span></li>
            <li class="flex flex-wrap gap-x-2">Batas Waktu: <span class="font-semibold">{{ \Carbon\Carbon::parse($order->payment->transactiontime)->addHours(24)->format('d F Y H:i:s') }}</span></li>
            <li class="flex flex-wrap gap-x-2">Waktu Bayar: <span class="font-semibold">{{ $order->payment->settlementtime ? \Carbon\Carbon::parse($order->payment->settlementtime)->format('d F Y H:i:s') : '-' }}</span></li>
        </ul>
        <ul>
            <li class="flex flex-col">Total: <span class="font-semibold text-2xl">{{ Helper::rupiah($order->payment->amount) }}</span></li>
        </ul>
    </section>
    <section class="p-4 rounded-lg border border-color-4 space-y-4">
        <span class="w-min p-1 px-2 bg-color-4 text-white rounded">
            {{ $custom_properties['shipping_address']['addressname'] }}
        </span>
        <ul class="space-y-2">
            <li>
                <span class="block font-semibold text-color-3">
                    Nama penerima & No. Tlp
                </span>
                <span>
                    {{ $custom_properties['shipping_address']['shippingname'] }}
                    ({{ $custom_properties['shipping_address']['phonenumber'] }})
                </span>
            </li>
            <li>
                <ul class="flex flex-wrap gap-2 justify-between">
                    <li>
                        <span class="block font-semibold text-color-3">
                            Provinsi
                        </span>
                        <span>
                            {{ Helper::getProvince($provinces, $custom_properties['shipping_address']['province_id']) }}
                        </span>
                    </li>
                    <li>
                        <span class="block font-semibold text-color-3">
                            Kabupaten/Kota
                        </span>
                        <span>
                            {{ Helper::getCity($cities, $custom_properties['shipping_address']['city_id']) }}
                        </span>
                    </li>
                    <li>
                        <span class="block font-semibold text-color-3">
                            Kecamatan
                        </span>
                        <span>
                            {{ Helper::getSubdistrict($subdistricts, $custom_properties['shipping_address']['subdistrict_id']) }}
                        </span>
                    </li>
                    <li>
                        <span class="block font-semibold text-color-3">
                            Kode Pos
                        </span>
                        <span>
                            {{ $custom_properties['shipping_address']['zip'] }}
                        </span>
                    </li>
                </ul>
            </li>
            <li>
                <span class="block font-semibold text-color-3">
                    Alamat lengkap
                </span>
                <span>
                    {{ $custom_properties['shipping_address']['fulladdress'] }}
                </span>
            </li>
        </ul>
    </section>
    <section class="flex flex-wrap gap-3">
        <ul class="space-y-2">
            <li class="font-bold">KURIR</li>
            <li>          
                <span class="p-1 px-2 block border border-color-3 rounded">{{ $order->shipping->courier }}</span>
            </li>
        </ul>
        <ul class="space-y-2">
            <li class="font-bold">LAYANAN</li>
            <li>          
                <span class="min-w-[100px] p-1 px-2 block border border-color-3 rounded">{{ $order->shipping->service }}</span>
            </li>
        </ul>
        <ul class="space-y-2">
            <li class="font-bold">NOMOR RESI</li>
            <li>          
                <span class="p-1 px-2 block border border-color-3 rounded">{{ $order->shipping->trackingnumber ?? 'Belum Dikirim' }}</span>
            </li>
        </ul>
    </section>
    <section class="space-y-4">
        @foreach ($order->orderItems as $orderItem)
            <div class="flex flex-wrap items-start justify-between gap-4 p-2 border border-color-4 rounded-lg">
                <div class="flex flex-wrap items-start gap-2">
                    <img src="{{ asset('storage/'.$orderItem->productItem->product->productImage->image) }}" alt=""
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
                <div class="grow">
                    <ul>
                        <li class="font-bold"><span class="font-normal">Warna: </span>{{ $orderItem->productItem->color }}</li>
                        <li><span class="font-normal">*Keterangan: </span>{{ $orderItem->productItem->note_bene }}</li>
                    </ul>
                </div>
                <div class="w-max">
                    @php $property = collect($custom_properties['product_items'])->where('id', $orderItem->productItem->id)->first() @endphp
                    <ul>
                        <li class="font-bold text-xl">
                            {{ $orderItem->qty }} ×
                            {{ Helper::rupiah($orderItem->productItem->price - (($property) ? (int)($property['price']*$property['discount']/100) : 0) ) }}
                        </li>
                        @if (isset($property['price']) && isset($property['discount']))
                            <li class="text-sm"><s>{{ Helper::rupiah($property['price']) }}</s> 
                                <span class="font-bold text-red-500">{{ $property['discount'] }}%</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        @endforeach
    </section>
    @if ($order->status == 'paid')
        <form action="{{route('admin.order.set-processing', ['code' => $order->payment->order_code])}}" method="post" class="w-full flex">
            @csrf
            <button class="ml-auto py-2 px-4 uppercase font-medium border bg-color-4 border-color-4 text-white rounded">
                Proses Pesanan
            </button>
        </form>
    @endif
    @if ($order->status == 'processing')
        <form action="{{route('admin.order.set-shipping', ['code' => $order->payment->order_code])}}" method="post" class="w-full flex flex-wrap space-y-4">
            @csrf
            <div class="w-full space-y-2">
                <label for="trackingnumber" class="block">Nomor Resi:</label>
                <input required name="trackingnumber" type="text" placeholder="Masukkan nomor resi pengiriman disini..." class="w-full h-[42px] p-2 border border-color-4 rounded">
            </div>
            <button class="ml-auto py-2 px-4 uppercase font-medium border bg-color-4 border-color-4 text-white rounded">
                Konfirmasi Pengiriman
            </button>
        </form>
    @endif
    @if ($order->status == 'delivered')
        <form action="{{route('admin.order.set-success', ['code' => $order->payment->order_code])}}" method="post" class="w-full flex">
            @csrf
            <button class="ml-auto py-2 px-4 uppercase font-medium border bg-color-4 border-color-4 text-white rounded">
                Selesaikan
            </button>
        </form>
    @endif
    <section>

    </section>
@endsection