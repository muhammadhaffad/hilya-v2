@extends('layouts.app', ['title' => 'Checkout'])
@push('script-head')
<script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
@endpush
@section('content')
    <section>
        <table>
            <tr>
                <th>Brand</th>
                <th>Nama</th>
                <th>Untuk</th>
                <th>Ukuran</th>
                <th>Warna</th>
                <th>Stock</th>
                <th>Keteranga</th>
                <th>Harga</th>
                <th>Qty</th>
            </tr>
            @foreach ($checkoutItems->orderItems as $item)
                <tr>
                    <td>{{ $item->product->productBrand->name }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->productItem->gender=='koko' ? 'Laki-laki' : 'Perempuan' }} {{ $item->age }}</td>
                    <td>{{ $item->productItem->size }}</td>
                    <td>{{ $item->productItem->color }}</td>
                    <td>{{ $item->productItem->stock }}</td>
                    <td>{{ $item->productItem->note_bene ?? '-' }}</td>
                    <td>
                        @if ($item->productItem->product->ispromo)
                            <s>{{ $item->productItem->price }}</s> {{ $item->productItem->price - (int)($item->productItem->price*$item->productItem->discount/100) }}   
                        @else
                            {{ $item->productItem->price }}
                        @endif
                    </td>
                    <td>{{ $item->qty }}</td>
                </tr>
            @endforeach
        </table>
        <span>Sub total: {{ $checkoutItems->subtotal - $checkoutItems->total_discount }}</span>
    </section>
    <section>
        <form action="{{ route('customer.checkout.change-address-shipping') }}" method="post">
            @csrf
            <table>
                <tr>
                    <th></th>
                    <th>Nama Alamat</th>
                    <th>Nama Pengiriman</th>
                    <th>Nomor Telepon</th>
                    <th>Alamat</th>
                </tr>
                @foreach ($addresses as $address)
                <tr>
                    <td><input type="radio" value="{{ $address->id }}" name="shipping_address_id" @checked($address->id == $checkoutItems->shipping->shipping_address_id)></td>
                    <td>{{ $address->addressname }}</td>
                    <td>{{ $address->shippingname }}</td>
                    <td>{{ $address->phonenumber }}</td>
                    <td>{{ $address->fulladdress }}</td>
                </tr>
                @endforeach
            </table>
            <button>Simpan Alamat</button>
        </form>
    </section>
    <form id="place-order" action="{{ route('customer.checkout.place-order') }}" method="post">
    @csrf
    </form>
    <section>
        <span> {{ implode(', ', @$errors?->shippingErrors->getMessages()['courier'] ?? []) }} </span>
        <table>
            <tr>
                <th></th>
                <th>Kurir</th>
                <th>Layanan</th>
            </tr>
            @if ($errors?->formErrors->getMessages())
                <tr>
                    <td></td>
                    <td>
                        {{ implode(', ', @$errors->formErrors->getMessages()['courier'] ?? []) }}
                    </td>
                    <td>
                        {{ implode(', ', @$errors->formErrors->getMessages()['service'] ?? []) }}
                    </td>
                </tr>
            @endif
            @php $couriers = ['jne', 'pos'] @endphp
            @foreach ($shippingCost as $key => $cost)
                <tr>
                    <td><input id="{{ $couriers[$key] }}" type="radio" name="courier" onchange="courierChecked(this)" value="{{ $couriers[$key] }}" form="place-order"></td>
                    <td>{{ $cost['courier'] }}</td>
                    <td id="{{ $cost['courier'] }}">
                        @foreach ($cost['services'] as $service => $price)
                            <div style="display: block">
                                <input type="radio" name="service" class="{{ $couriers[$key] }}" value="{{ $service }}" form="place-order">
                                {{ $service }} : {{ $price }}
                            </div>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </table>
    </section>
    {{ implode(', ', @$errors?->formErrors->getMessages()['bank'] ?? []) }}
    <section>
        <input type="radio" name="bank" value="bri" form="place-order">
        <span>BRI</span>
        <input type="radio" name="bank" value="bni" form="place-order">
        <span>BNI</span>
    </section>
    <div style="display: flex">
        <form action="{{ route('customer.checkout.back-to-cart') }}" method="post">
        @csrf
            <button>Kembali ke keranjang</button>
        </form>
        <button type="submit" form="place-order">Buat Pesanan</button>
    </div>
    @push('script')
        <script>
            function courierChecked(event) {
                let className = $(event).attr('id');
                $('input[name="service"]').prop('disabled', true);
                $(`input[name="service"].${className}`).prop('disabled', false);
            }
        </script>
    @endpush
@endsection
