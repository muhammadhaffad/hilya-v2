@extends('layouts.app', ['title' => $product->name])
@section('content')
    <section>
        @foreach ($product->productImages as $product_image)
            <img src="{{ $product_image }}" alt="Gambar {{ $product_image->id }} {{ $product->name }}">
        @endforeach
    </section>
    <section>
        <table>
            <tr>
                <td>Brand</td>
                <td>{{ str($product->productBrand->name)->title() }}</td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>{{ str($product->name)->title() }}</td>
            </tr>
            <tr>
                <td>Deskripsi</td>
                <td>{{ $product->description }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>{{ str($product->availability)->title() }} {{ $product->ispromo ? '(PROMO)' : '' }}</td>
            </tr>
            <tr>
                @if ($product->ispromo)
                    @php
                        $discount = $product->productItems->first()->discount / 100;
                    @endphp
                    <td>Harga</td>
                    <td>
                        <s>{{ $product->product_items_min_price }} - {{ $product->product_items_max_price }}</s>
                        {{ $product->product_items_min_price - $product->product_items_min_price*$discount }} - {{ $product->product_items_max_price - $product->product_items_max_price*$discount }}
                    </td>
                @else
                    <td>Harga</td>
                    <td>{{ $product->product_items_min_price }} - {{ $product->product_items_max_price }}</td>
                @endif
            </tr>
        </table>
    </section>
    <span>
        @if ($errors?->addToCartErrors)
            @foreach ($errors->addToCartErrors->getMessages() as $key => $value)
                {{ $key }} : {{ implode(',', $value) }}
            @endforeach
        @endif
    </span>
    <section>
        <form action="{{ route('product.add-to-cart', ['product'=>$product->id]) }}" method="post">
            @csrf
            <table>
                <tr>
                    <th></th>
                    <th>Untuk</th>
                    <th>Ukuran</th>
                    <th>Warna</th>
                    <th>Harga</th>
                    <th>Stock</th>
                    <th>Keterangan</th>
                </tr>
                @foreach ($product->productItems as $productItem)
                    <tr>
                        <td><input type="radio" name="product_item_id" value="{{ $productItem->id }}"></td>
                        <td>{{ $productItem->gender == 'koko' ? 'Laki-laki' : 'Perempuan' }} {{ $productItem->age }}</td>
                        <td>{{ $productItem->size }}</td>
                        <td>{{ str($productItem->color)->title() }}</td>
                        <td>
                            @if ($product->ispromo)
                                <s>{{ $productItem->price }}</s>
                                {{ $productItem->price - $productItem->price*$discount }}
                            @else
                                {{ $productItem->price }}
                            @endif
                        </td>
                        <td>{{ $productItem->stock }}</td>
                        <td>{{ $productItem->note_bene ?? '-' }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td></td>
                </tr>
            </table>
            <input type="number" name="qty">
            <button>Tambah ke keranjang</button>
        </form>
    </section>
    @if (session('message'))
        @push('script')
            <script>
                alert("{{ session('message') }}")
            </script>
        @endpush
    @endif
@endsection