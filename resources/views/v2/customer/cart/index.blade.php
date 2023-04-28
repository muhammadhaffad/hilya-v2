@extends('v2.layouts.customer.app', ['title' => 'Cart | Hilya Collection'])
@section('content')
    <div class="flex font-bold text-xl gap-1 items-center uppercase">
        <span class="material-icons !text-4xl">
            shopping_bag
        </span>
        Keranjang
    </div>
    
    @if ($errors->qtyErrors->any())
        <span class="inline-block p-2 bg-red-500 text-white rounded">
            @foreach (session('errors')->qtyErrors->getMessages() as $key => $value)
                {{ implode(',', $value) }}
            @endforeach
        </span>
    @endif
    @if ($errors->cartItemErrors->any())
        @php
            $cartItemErrors = collect(session('errors')?->cartItemErrors)
                ->undot()
                ->get('cart');
        @endphp
    @endif
    @forelse ($cartItems?->orderItems as $cartItem)
        <div
            class="{{ $cartItem->productItem->stock < $cartItem->qty ? 'bg-color-1' : '' }} flex flex-wrap items-stretch justify-between gap-4 p-5 border border-color-4 rounded-lg">
            @if (@$cartItemErrors[$cartItem->productItem->id])
                <span class="inline-block w-full p-2 bg-red-500 text-white rounded">
                    {{ implode(',', @$cartItemErrors[$cartItem->productItem->id] ?? []) }}
                </span>
            @endif
            <div class="flex flex-wrap items-stretch gap-2">
                <img src="{{ asset('storage/'.$cartItem->productItem->product->productImage->image) }}" alt=""
                    class="{{ $cartItem->productItem->stock < $cartItem->qty ? 'grayscale' : '' }} w-24">
                <ul class="max-w-[200px]">
                    <li class="font-bold"><span
                            class="uppercase">{{ $cartItem->productItem->product->productBrand->name }}</span>
                        {{ $cartItem->productItem->product->name }}</li>
                    <li class="capitalize">
                        {{ ($cartItem->productItem->gender == 'koko' ? 'Laki-laki' : 'Perempuan') . ' ' . $cartItem->productItem->age }}
                    </li>
                    <li>{{ $cartItem->productItem->size }}</li>
                </ul>
            </div>
            <div class="grow">
                <ul>
                    <li class="font-bold"><span class="font-normal">Warna: </span>{{ $cartItem->productItem->color }}</li>
                    <li><span class="font-normal">*Keterangan: </span>{{ $cartItem->productItem->note_bene }}</li>
                </ul>
            </div>
            <div class="w-max">
                <ul>
                    <li class="font-bold text-xl">
                        {{ Helper::rupiah($cartItem->productItem->price - (int) (($cartItem->productItem->price * $cartItem->productItem->discount) / 100)) }}
                    </li>
                    @if ($cartItem->productItem->product->ispromo)
                        <li class="text-sm"><s>{{ Helper::rupiah($cartItem->productItem->price) }}</s> <span
                                class="font-bold text-red-500">{{ $cartItem->productItem->discount }}%</span></li>
                    @endif
                    <li>
                        Sisa: <span class="font-bold">{{ $cartItem->productItem->stock }}</span>
                    </li>
                </ul>
            </div>
            <div class="flex flex-nowrap items-center w-max gap-2 ml-auto">
                <form action="{{ route('customer.cart.remove') }}" method="post">
                    @csrf
                    <input type="hidden" name="order_item_id" value="{{ $cartItem->id }}">
                    <button class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 bg-red-500 text-white rounded"
                        onclick="return confirm('Apakah kamu yakin ingin menghapus item ini?')">
                        <span class="material-icons">delete</span>
                    </button>
                </form>
                <div class="flex">
                    <form action="{{ route('customer.cart.sub') }}" method="post">
                        @csrf
                        <input type="hidden" name="order_item_id" value="{{ $cartItem->id }}">
                        <button class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 bg-color-4 text-white rounded">
                            <span class="material-icons !text-base">remove</span>
                        </button>
                    </form>
                    <div class="flex items-center justify-center w-14 h-8 sm:w-20 sm:h-10 border-b border-color-4">
                        <span>{{ $cartItem->qty }}</span>
                    </div>
                    <form action="{{ route('customer.cart.add') }}" method="post">
                        @csrf
                        <input type="hidden" name="order_item_id" value="{{ $cartItem->id }}">
                        <button class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 bg-color-4 text-white rounded">
                            <span class="material-icons !text-base">add</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @if ($loop->last)
            <form action="{{ route('customer.cart.checkout') }}" method="post">
                @csrf
                <div class="w-full flex justify-end">
                    <button class="py-2 px-4 uppercase font-medium bg-color-4 text-white rounded">Checkout</button>
                </div>
            </form>
        @endif
    @empty
    <div class="flex justify-center items-center h-96">
        <span class="uppercase font-semibold text-3xl text-color-1">404 | Keranjang Kosong</span>
    </div>
    @endforelse
    @push('script')
        @if (session('message'))
            <script>
                alert('{{ session('message') }}')
            </script>
        @endif
    @endpush
@endsection
