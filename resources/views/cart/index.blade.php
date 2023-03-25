@extends('layouts.app', ['title' => 'Cart'])
@section('content')
    <span>{{ $cart->code }}</span>
    <div style="display: block">
        @if (session('errors')?->qtyErrors)
            @foreach (session('errors')->qtyErrors->getMessages() as $key => $value)
                {{ $key . ' : ' . implode(',', $value) }}
            @endforeach 
        @endif
        @if (session('errors')?->cartItemErrors)
            @php
                $cartItemErrors = collect(session('errors')?->cartItemErrors)->undot()->get('cart')
            @endphp
        @endif
    </div>
    <table>
        <tr>
            <th>Brand</th>
            <th>Nama Produk</th>
            <th>Pakaian</th>
            <th>Ukuran</th>
            <th>Warna</th>
            <th>Harga</th>
            <th>Keterangan</th>
            <th>Stok</th>
            <th>Qty</th>
            <th>Aksi</th>
        </tr>
        @foreach ($cart->orderItems as $cartItem)
        <tr>
            <td>
                {{ $cartItem->productItem->product->productBrand->name }}
            </td>
            <td>
                {{ $cartItem->productItem->product->name }}
            </td>
            <td>
                {{ $cartItem->productItem->gender . ' ' . $cartItem->productItem->age }}
            </td>
            <td>
                {{ $cartItem->productItem->size }}
            </td>
            <td>
                {{ $cartItem->productItem->color }}
            </td>
            <td>
                @if ($cartItem->productItem->product->ispromo)
                    <s>{{ $cartItem->productItem->price }}</s> {{ $cartItem->productItem->price - (int)($cartItem->productItem->price*$cartItem->productItem->discount/100) }}
                @else
                    {{ $cartItem->productItem->price }}
                @endif
            </td>
            <td>
                {{ $cartItem->productItem->note_bene ?: '-' }}
            </td>
            <td>
                {{ $cartItem->productItem->stock }}
            </td>
            <td>
                {{ $cartItem->qty }}
            </td>
            <td style="display: flex">
                <form action="{{ route('customer.cart.sub') }}" method="post">
                    @csrf
                    <input type="hidden" name="order_item_id" value="{{ $cartItem->id }}">
                    <button type="submit">Kurang</button>
                </form>
                <form action="{{ route('customer.cart.add') }}" method="post">
                    @csrf
                    <input type="hidden" name="order_item_id" value="{{ $cartItem->id }}">
                    <button type="submit">Tambah</button>
                </form>
                <form action="{{ route('customer.cart.remove') }}" method="post">
                    @csrf
                    <input type="hidden" name="order_item_id" value="{{ $cartItem->id }}">
                    <button type="submit">Hapus</button>
                </form>
                {{ implode(',', @$cartItemErrors[$cartItem->productItem->id] ?? []) }}
            </td>
        </tr>
        @endforeach
    </table>
    <form action="{{ route('customer.cart.checkout') }}" method="post">
        @csrf
        <button>Checkout</button>
    </form>
    @if (session('message'))
        <script>alert('{{ session('message') }}')</script>
    @endif
@endsection