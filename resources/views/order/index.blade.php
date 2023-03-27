@extends('layouts.app', ['title' => 'My Orders'])
@section('content')
@push('style')
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>
@endpush
    <section>
        <form action="" method="get">
            <label>Pencarian</label>
            <input type="text" name="search">
            <label>Status</label>
            <select name="status">
                <option value=''>- Pilih status -</option>
                <option value='pending'>Pending</option>
                <option value='paid'>Paid</option>
                <option value='processing'>Processing</option>
                <option value='shipped'>Shipped</option>
                <option value='delivered'>Delivered</option>
                <option value='cancelled'>Cancelled</option>
                <option value='completed'>Completed</option>
            </select>
            <label>Rentang waktu</label>
            <input type="date" name="start_date">
            s/d
            <input type="date" name="end_date">
            <button>Cari</button>
        </form>
    </section>
    <section>
        @foreach ($orders as $order)
            <table>
                <tr>
                    <th colspan="9">
                        Kode : {{ $order->payment->order_code }}
                    </th>
                </tr>
                @php $custom_properties = collect($order->custom_properties); @endphp
                <tr>
                    <th colspan="2">Status</th>
                    <th colspan="2">Waktu transaksi</th>
                    <th colspan="2">Waktu pembayaran</th>
                    <th colspan="3">Total yang dibayar</th>
                </tr>
                <tr>
                    <td colspan="2">{{ $order->status }}</td>
                    <td colspan="2">{{ $order->payment->transactiontime }}</td>
                    <td colspan="2">{{ $order->payment->setlementtime ?? '-' }}</td>
                    <td colspan="3">{{ $order->payment->amount }}</td>
                </tr>
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
            <button style="margin-bottom: 32px" onclick="window.location='{{ route('customer.orders.show', ['code' => ($order->payment?->order_code ?: '-')]) }}'">Lihat Detail</button>
        @endforeach
    </section>
@endsection