@extends('v2.layouts.customer.app', ['title' => 'Detail Pesanan | Hillia Collection'])
@section('content')
@php
    $provinces = Helper::getProvinces();
    $cities = Helper::getCities();
    $subdistricts = Helper::getSubdistricts($order->shipping->shippingAddress->pluck('city_id')->unique()->all());
@endphp
    <div class="flex font-bold text-xl gap-1 items-center uppercase">
        <span class="material-icons !text-4xl">
            list_alt
        </span>
        Pesanan
    </div>
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
    <section class="p-5 flex flex-wrap justify-between border border-color-4 rounded-lg">
        <ul>
            <li>Bank: <span class="font-semibold uppercase">{{ $order->payment->bank }}</span></li>
            <li>Nomor VA: <span class="flex items-center justify-between w-min px-1 gap-1 font-semibold border border-color-2 text-color-3 rounded"><span class="material-icons !text-base">content_copy</span>{{ $order->payment->vanumber }}</span></li>
        </ul>
        <ul>
            <li>Waktu Transaksi: <span class="font-semibold uppercase">{{ $order->payment->transactiontime }}</span></li>
            <li>Waktu Bayar: <span class="font-semibold uppercase">{{ $order->payment->settlementtime ?? '-' }}</span></li>
        </ul>
        <ul>
            <li class="flex flex-col">Total: <span class="font-semibold text-2xl">{{ Helper::rupiah($order->payment->amount) }}</span></li>
        </ul>
    </section>
    <section class="p-4 rounded-lg border border-color-4 space-y-4">
        <span class="w-min p-1 px-2 bg-color-4 text-white rounded">
            {{ $order->shipping->shippingAddress->addressname }}
        </span>
        <ul>
            <li>
                <span class="block font-semibold text-color-3">
                    Nama penerima & No. Tlp
                </span>
                <span>
                    {{ $order->shipping->shippingAddress->shippingname }}
                    ({{ $order->shipping->shippingAddress->phonenumber }})
                </span>
            </li>
            <li>
                <ul class="flex justify-between">
                    <li>
                        <span class="block font-semibold text-color-3">
                            Provinsi
                        </span>
                        <span>
                            {{ Helper::getProvince($provinces, $order->shipping->shippingAddress->province_id) }}
                        </span>
                    </li>
                    <li>
                        <span class="block font-semibold text-color-3">
                            Kabupaten/Kota
                        </span>
                        <span>
                            {{ Helper::getCity($cities, $order->shipping->shippingAddress->city_id) }}
                        </span>
                    </li>
                    <li>
                        <span class="block font-semibold text-color-3">
                            Kecamatan
                        </span>
                        <span>
                            {{ Helper::getSubdistrict($subdistricts, $order->shipping->shippingAddress->subdistrict_id) }}
                        </span>
                    </li>
                    <li>
                        <span class="block font-semibold text-color-3">
                            Kode Pos
                        </span>
                        <span>
                            {{ $order->shipping->shippingAddress->zip }}
                        </span>
                    </li>
                </ul>
            </li>
            <li>
                <span class="block font-semibold text-color-3">
                    Alamat lengkap
                </span>
                <span>
                    {{ $order->shipping->shippingAddress->fulladdress }}
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
                        @if ($orderItem->productItem->product->ispromo)
                            <li class="text-sm"><s>{{ Helper::rupiah($property['price']) }}</s> 
                                <span class="font-bold text-red-500">{{ $property['discount'] }}%</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        @endforeach
    </section>
@endsection