@extends('layouts.app', ['title' => 'Detail order ' . $order->code])
@push('style')
    <style>
        th {
            text-align: right;
        }
    </style>
@endpush
@section('content')
    <section>
        <table>
            <tr>
                <th>Kode:</th>
                <td>{{ $order->payment->order_code }}</td>
            </tr>
            <tr>
                <th>Status:</th>
                <td>{{ $order->status }}</td>
            </tr>
            <tr>
                <th>Bank:</th>
                <td>{{ str($order->payment->bank)->upper() }}</td>
            </tr>
            <tr>
                <th>Nomor VA:</th>
                <td>{{ $order->payment->vanumber }}</td>
            </tr>
            <tr>
                <th>Total yang dibayar:</th>
                <td>{{ $order->payment->amount }}</td>
            </tr>
            <tr>
                <th>Waktu transaksi:</th>
                <td>{{ $order->payment->transactiontime }}</td>
            </tr>
            <tr>
                <th>Waktu bayar:</th>
                <td>{{ $order->payment->settlementtime ?? '-' }}</td>
            </tr>
        </table>
        @php $custom_properties = collect($order->custom_properties); @endphp
        <table>
            <tr>
                <th colspan="9">Item yang dipesan</th>
            </tr>
            <tr>
                <th>Gambar</th>
                <th>Brand</th>
                <th>Nama</th>
                <th>Untuk</th>
                <th>Ukuran</th>
                <th>Warna</th>
                <th>Keterangan</th>
                <th>Harga</th>
                <th>Qty</th>
            </tr>
            @foreach ($order->orderItems as $item)
            <tr>
                <td>
                    <img src="{{ $item->productItem->product->productImage->image }}" alt="gambar">
                </td>
                <td>{{ $item->productItem->product->productBrand->name }}</td>
                <td>{{ $item->productItem->product->name }}</td>
                <td>{{ $item->productItem->gender == 'koko' ? 'Laki-laki' : 'Perempuan' }} {{ $item->productItem->age }}</td>
                <td>{{ $item->productItem->size }}</td>
                <td>{{ $item->productItem->color }}</td>
                <td>{{ $item->productItem->note_bene ?? '-' }}</td>
                <td>
                    @php $property = $custom_properties->where('id', $item->productItem->id)->first() @endphp
                    @if ($property)
                    <s>{{ $item->productItem->price }}</s> {{$property['price'] - (int)($property['price']*$property['discount']/100)}}
                    @else
                    {{ $item->productItem->price }}
                    @endif
                </td>
                <td>{{ $item->qty }}</td>
            </tr>
            @endforeach
        </table>
        <span>Subtotal: {{ $order->subtotal - $order->total_discount }}</span>
    </section>
    <section>
        <table>
            <tr>
                <th>Nama alamat: </th>
                <td>{{ $order->shipping->shippingAddress->addressname }}</td>
            </tr>
            <tr>
                <th>Nama penerima: </th>
                <td>{{ $order->shipping->shippingAddress->shippingname }}</td>
            </tr>
            <tr>
                <th>Nomor telepon: </th>
                <td>{{ $order->shipping->shippingAddress->phonenumber }}</td>
            </tr>
            <tr>
                <th>Alamat lengkap: </th>
                <td>{{ $order->shipping->shippingAddress->fulladdress }}</td>
            </tr>
            <tr>
                <th>Kode pos: </th>
                <td>{{ $order->shipping->shippingAddress->zip }}</td>
            </tr>
            <tr>
                <th>Kurir: </th>
                <td>{{ $order->shipping->courier }} {{ $order->shipping->service }}</td>
            </tr>
            <tr>
                <th>Biaya ongkir: </th>
                <td>{{ $order->shipping->shippingcost }}</td>
            </tr>
            <tr>
                <th>Nomor resi: </th>
                <td>{{ $order->shipping->trackingnumber ?? '-' }}</td>
            </tr>
        </table>
    </section>
@endsection