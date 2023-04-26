@extends('v2.layouts.customer.app', ['title' => 'Checkout | Hillia Collection'])
@section('content')
    @php
        $provinces = Helper::getProvinces();
        $cities = Helper::getCities();
        $subdistricts = Helper::getSubdistricts($addresses->pluck('city_id')->unique()->all());
    @endphp
    <div id="modalAddress"
        class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-20 w-[800px] p-6 space-y-4 border border-color-3 bg-white rounded">
        <form id="form-change-address" action="{{ route('customer.checkout.change-address-shipping') }}" method="post">
            @csrf
        </form>
        <div class="flex justify-end">
            <button class="float-right px-2 uppercase text-2xl font-medium border border-color-4 rounded" onclick="$('#modalAddress').toggleClass('hidden')">
                ×
            </button>
        </div>
        <div class="w-full h-[400px] overflow-y-scroll">
            <ul class="space-y-4">
                @foreach ($addresses as $index => $address)
                    <li class="">
                        <input id="alamat-{{ $index }}" type="radio" name="shipping_address_id"
                            value="{{ $address->id }}" class="hidden peer" form="form-change-address">
                        <label for="alamat-{{ $index }}"
                            class="block rounded-lg border border-color-4 cursor-pointer peer-checked:bg-color-1">
                            <div class="p-4 space-y-4">
                                <span class="w-min p-1 px-2 bg-color-4 text-white rounded">
                                    {{ $address->addressname }}
                                </span>
                                <ul>
                                    <li>
                                        <span class="block font-semibold text-color-3">
                                            Nama penerima & No. Tlp
                                        </span>
                                        <span>
                                            {{ $address->shippingname }}
                                            ({{ $address->phonenumber }})
                                        </span>
                                    </li>
                                    <li>
                                        <ul class="flex justify-between">
                                            <li>
                                                <span class="block font-semibold text-color-3">
                                                    Provinsi
                                                </span>
                                                <span>
                                                    {{ Helper::getProvince($provinces, $address->province_id) }}
                                                </span>
                                            </li>
                                            <li>
                                                <span class="block font-semibold text-color-3">
                                                    Kabupaten/Kota
                                                </span>
                                                <span>
                                                    {{ Helper::getCity($cities, $address->city_id) }}
                                                </span>
                                            </li>
                                            <li>
                                                <span class="block font-semibold text-color-3">
                                                    Kecamatan
                                                </span>
                                                <span>
                                                    {{ Helper::getSubdistrict($subdistricts, $address->subdistrict_id) }}
                                                </span>
                                            </li>
                                            <li>
                                                <span class="block font-semibold text-color-3">
                                                    Kode Pos
                                                </span>
                                                <span>
                                                    {{ $address->zip }}
                                                </span>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <span class="block font-semibold text-color-3">
                                            Alamat lengkap
                                        </span>
                                        <span>
                                            {{ $address->fulladdress }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </label>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="flex justify-end">
            <button type="submit" class="float-right py-2 px-4 uppercase font-medium bg-color-4 text-white rounded" form="form-change-address">Simpan Alamat</button>
        </div>
    </div>
    <div class="flex font-bold text-xl gap-1 items-center uppercase">
        <span class="material-icons !text-4xl">
            shopping_cart_checkout
        </span>
        Checkout
    </div>
    <form id="form-place-order" action="{{ route('customer.checkout.place-order') }}" onsubmit="return confirm('Apakah Anda sudah mengecek ulang informasi yang diberikan?')" method="post">
        @csrf
    </form>
    @if (session('errors')?->checkoutItemErrors)
        @php
            $checkoutItemErrors = collect(session('errors')?->checkoutItemErrors)
                ->undot()
                ->get('cart');
        @endphp
    @endif
    @foreach ($checkoutItems->orderItems as $checkoutItem)
        <div class="{{ $checkoutItem->productItem->stock < $checkoutItem->qty ? 'bg-color-1' : '' }} flex flex-wrap items-start justify-between gap-2 p-2 border border-color-4 rounded-lg">
            @if (@$checkoutItemErrors[$cartItem->productItem->id])
                <span class="w-full text-red-500">
                    {{ implode(',', @$checkoutItemErrors[$cartItem->productItem->id] ?? []) }}
                </span>
            @endif
            <div class="flex flex-wrap items-start gap-2">
                <img src="{{ asset('assets/images/sample.png') }}" alt=""
                    class="{{ $checkoutItem->productItem->stock < $checkoutItem->qty ? 'grayscale' : '' }} w-24">
                <ul class="max-w-[200px]">
                    <li class="font-bold"><span
                            class="uppercase">{{ $checkoutItem->productItem->product->productBrand->name }}</span>
                        {{ $checkoutItem->productItem->product->name }}</li>
                    <li class="capitalize">
                        {{ ($checkoutItem->productItem->gender == 'koko' ? 'Laki-laki' : 'Perempuan') . ' ' . $checkoutItem->productItem->age }}
                    </li>
                    <li>{{ $checkoutItem->productItem->size }}</li>
                </ul>
            </div>
            <div class="grow">
                <ul>
                    <li class="font-bold"><span class="font-normal">Warna: </span>{{ $checkoutItem->productItem->color }}</li>
                    <li><span class="font-normal">*Keterangan: </span>{{ $checkoutItem->productItem->note_bene }}</li>
                </ul>
            </div>
            <div class="w-max">
                <ul>
                    <li class="font-bold text-xl">
                        {{ $checkoutItem->qty }} ×
                        {{ Helper::rupiah($checkoutItem->productItem->price - (int) (($checkoutItem->productItem->price * $checkoutItem->productItem->discount) / 100)) }}
                    </li>
                    @if ($checkoutItem->productItem->product->ispromo)
                        <li class="text-sm"><s>{{ Helper::rupiah($checkoutItem->productItem->price) }}</s> <span
                                class="font-bold text-red-500">{{ $checkoutItem->productItem->discount }}%</span></li>
                    @endif
                    <li>
                        Sisa: <span class="font-bold">{{ $checkoutItem->productItem->stock }}</span>
                    </li>
                </ul>
            </div>
        </div>
    @endforeach
    <div class="flex flex-wrap justify-end gap-2">
        <span>Subtotal: </span>
        <span id="subtotal" data-subtotal="{{$checkoutItems->subtotal - $checkoutItems->total_discount}}" class="block font-bold text-2xl">
            {{ Helper::rupiah($checkoutItems->subtotal - $checkoutItems->total_discount) }}
        </span>
    </div>
    <div class="p-4 rounded-lg border border-color-4 space-y-4">
        <span class="w-min p-1 px-2 bg-color-4 text-white rounded">
            {{ $checkoutItems->shipping->shippingAddress->addressname }}
        </span>
        <ul class="space-y-2">
            <li>
                <span class="block font-semibold text-color-3">
                    Nama penerima & No. Tlp
                </span>
                <span class="block">
                    {{ $checkoutItems->shipping->shippingAddress->shippingname }}
                    ({{ $checkoutItems->shipping->shippingAddress->phonenumber }})
                </span>
            </li>
            <li>
                <ul class="flex flex-wrap justify-between gap-2">
                    <li>
                        <span class="block font-semibold text-color-3">
                            Provinsi
                        </span>
                        <span>
                            {{ Helper::getProvince($provinces, $checkoutItems->shipping->shippingAddress->province_id) }}
                        </span>
                    </li>
                    <li>
                        <span class="block font-semibold text-color-3">
                            Kabupaten/Kota
                        </span>
                        <span>
                            {{ Helper::getCity($cities, $checkoutItems->shipping->shippingAddress->city_id) }}
                        </span>
                    </li>
                    <li>
                        <span class="block font-semibold text-color-3">
                            Kecamatan
                        </span>
                        <span>
                            {{ Helper::getSubdistrict($subdistricts, $checkoutItems->shipping->shippingAddress->subdistrict_id) }}
                        </span>
                    </li>
                    <li>
                        <span class="block font-semibold text-color-3">
                            Kode Pos
                        </span>
                        <span>
                            {{ $checkoutItems->shipping->shippingAddress->zip }}
                        </span>
                    </li>
                </ul>
            </li>
            <li>
                <span class="block font-semibold text-color-3">
                    Alamat lengkap
                </span>
                <span class="block">
                    {{ $checkoutItems->shipping->shippingAddress->fulladdress }}
                </span>
            </li>
        </ul>
        <div class="w-full flex justify-end">
            <button id="changeAddress" class="py-2 px-4 uppercase font-medium bg-color-4 text-white rounded">Ubah Alamat</button>
        </div>
    </div>
    @php $couriers = ['jne', 'pos'] @endphp
    <div class="w-full space-y-4">
        @if (session('errors')?->formErrors->getMessages())
            <span class="p-2 bg-red-500 text-white rounded">
                @foreach (session('errors')->formErrors->getMessages() as $key => $value)
                    {{ implode(',', $value) }}
                @endforeach
            </span>
        @endif
        @if (session('errors')?->shippingErrors->getMessages())
            <span class="p-2 bg-red-500 text-white rounded">
                @foreach (session('errors')->shippingErrors->getMessages() as $key => $value)
                    {{ implode(',', $value) }}
                @endforeach
            </span>
        @endif
        @foreach ($shippingCost as $key => $cost)
            <div class="flex flex-wrap gap-4">
                <div class="w-full flex flex-col gap-2 sm:w-1/2 uppercase mb-2">
                    <input id="kurir-{{$key}}" type="radio" name="courier" value="{{ $couriers[$key] }}" class="hidden peer" onchange="courierChecked(this)" form="form-place-order" required>
                    Kurir
                    <label for="kurir-{{$key}}" class="uppercase py-2 px-4 w-fit gap-2 font-medium border border-color-4 text-color-4 rounded cursor-pointer peer-checked:bg-color-1 peer-checked:text-blue-600 peer-checked:border-blue-600">
                        {{ $cost['courier'] }}
                    </label>
                </div>
                <ul class="flex flex-col gap-2 uppercase">
                    Layanan
                    @foreach ($cost['services'] as $service => $price)
                        <li class="flex">
                            <input id="{{ \Str::slug($service, '-') }}" type="radio" name="service" value="{{ $service }}" data-biaya-ongkir="{{ $price }}" class="hidden peer kurir-{{$key}}" form="form-place-order" disabled required>
                            <label for="{{ \Str::slug($service, '-') }}" class="py-2 px-4 uppercase font-medium border border-color-4 text-color-4 rounded cursor-pointer peer-disabled:bg-color-1 peer-disabled:cursor-not-allowed peer-checked:bg-color-1 peer-checked:text-blue-600 peer-checked:border-blue-600">
                                {{ $service }} - {{ Helper::rupiah($price) }}
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
    <div class="w-full space-y-4">
        @if (session('errors')?->transactionErrors->getMessages())
            <span class="p-2 bg-red-500 text-white rounded">
                @foreach (session('errors')->transactionErrors->getMessages() as $key => $value)
                    {{ implode(',', $value) }}
                @endforeach
            </span>
        @endif
        <div class="flex flex-col gap-2">
            <span class="uppercase">
                Bank
            </span>
            <ul class="flex flex-wrap gap-2">
                <li>
                    <input type="radio" name="bank" id="bni" value="bni" class="hidden peer" form="form-place-order" required>
                    <label for="bni" class="flex py-2 px-4 h-full uppercase font-medium border border-color-4 text-color-4 rounded peer-checked:bg-color-1 cursor-pointer">
                        <div class="flex items-center justify-center">
                            <img src="{{ asset('assets/icons/bni.svg') }}" alt="bank bni">
                        </div>
                    </label>
                </li>
                <li>
                    <input type="radio" name="bank" id="bri" value="bri" class="hidden peer" form="form-place-order" required>
                    <label for="bri" class="flex py-2 px-4 h-full uppercase font-medium border border-[#00529C] text-color-4 bg-[#00529C] rounded peer-checked:bg-blue-950 cursor-pointer">
                        <div class="flex items-center justify-center">
                            <img src="{{ asset('assets/icons/bri.svg') }}" alt="bank bni">
                        </div>
                    </label>
                </li>
            </ul>
        </div>
    </div>
    <div class="flex justify-end">
        <div class="flex flex-col">
            <div class="w-min ml-auto">
                <span class="block">Grandtotal:</span>
                <span id="grandtotal" class="block font-bold text-2xl text-right">-</span>
            </div>
            <span class="block text-sm">
                *Harga belum termasuk biaya transfer
            </span>
        </div>
    </div>
    <hr class="border-color-4">
    <div class="flex flex-wrap justify-end gap-2">
        <form action="{{ route('customer.checkout.back-to-cart') }}" onsubmit="return confirm('Apakah Anda yakin ingin kembali ke keranjang?')" method="post" class="w-full sm:w-fit">
        @csrf
            <button class="w-full py-2 px-4 uppercase font-medium border border-color-4 rounded">
                Kembali ke Keranjang
            </button>
        </form>
        <button type="submit" class="py-2 px-4 w-full sm:w-fit uppercase font-medium border bg-color-4 border-color-4 text-white rounded" form="form-place-order">
            Buat Pesanan
        </button>
    </div>
    @push('script')
        @if (session('message'))
            <script>
                alert('{{ session('message') }}')
            </script>
        @endif
        <script>
            $('#changeAddress').click(function() {
                $('#modalAddress').toggleClass('hidden');
            });
            function rupiah(angka){
                let rupiah = '';		
                let angkarev = angka.toString().split('').reverse().join('');
                for(let i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+'.';
                return 'Rp'+rupiah.split('',rupiah.length-1).reverse().join('');
            }
            $('input[name="service"]').change(function(e) {
                let subtotal = parseInt($('#subtotal').attr('data-subtotal'));
                let biayaOngkir = parseInt(e.target.getAttribute('data-biaya-ongkir'));
                $('#grandtotal').text(rupiah(subtotal+biayaOngkir));
            });
        </script>
        <script>
            function courierChecked(event) {
                let className = $(event).attr('id');
                $('input[name="service"]').prop('checked', false);
                $('input[name="service"]').prop('disabled', true);
                $(`input[name="service"].${className}`).prop('disabled', false);
            }
        </script>
    @endpush
@endsection
