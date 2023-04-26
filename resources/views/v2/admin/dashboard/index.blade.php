@extends('v2.layouts.admin.app', ['title' => 'Dashboard | Hillia Collection'])
@section('content')
<div class="flex font-bold text-xl gap-1 items-center uppercase">
    <span class="material-icons !text-4xl">
        dashboard
    </span>
    Dashboard
</div>
<div class="inline-block font-semibold text-xl">
    #Produk
</div>
<div class="grid-cols-1 md:grid-cols-2 lg:grid-cols-3 grid gap-4">
    <div class="p-3 w-full space-y-4 border border-color-3 rounded-md">
        <div class="flex items-center gap-2">
            <span class="material-icons text-5xl">
                checkroom
            </span>
            <h3 class="font-semibold text-xl">
                Produk
            </h3>
        </div>
        <hr>
        <h1 class="block text-right font-semibold text-5xl">
            {{ App\Models\Product::count() }}
        </h1>
    </div>
    <div class="p-3 w-full space-y-4 border border-color-3 rounded-md">
        <div class="flex items-center gap-2">
            <span class="material-icons text-5xl">
                sell
            </span>
            <h3 class="font-semibold text-xl">
                Brand
            </h3>
        </div>
        <hr>
        <h1 class="block text-right font-semibold text-5xl">
            {{ App\Models\ProductBrand::count() }}
        </h1>
    </div>
</div>
<div class="inline-block font-semibold text-xl">
    #Pesanan
</div>
<div class="grid-cols-1 md:grid-cols-2 lg:grid-cols-3 grid gap-4">
    <div class="p-3 w-full space-y-4 border border-color-3 rounded-md">
        <div class="flex items-center gap-2">
            <span class="material-icons text-5xl">
                list_alt
            </span>
            <h3 class="font-semibold text-xl">
                Semua
            </h3>
        </div>
        <hr>
        <h1 class="block text-right font-semibold text-5xl">
            {{ App\Models\Order::count() }}
        </h1>
    </div>
    <div class="p-3 w-full space-y-4 border border-color-3 rounded-md">
        <div class="flex items-center gap-2">
            <span class="material-icons text-5xl">
                hourglass_top
            </span>
            <h3 class="font-semibold text-xl">
                Menunggu Pembayaran
            </h3>
        </div>
        <hr>
        <h1 class="block text-right font-semibold text-5xl">
            {{ App\Models\Order::where('status', 'pending')->count() }}
        </h1>
    </div>
    <div class="p-3 w-full space-y-4 border border-color-3 rounded-md">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-5xl">
                other_admission
            </span>
            <h3 class="font-semibold text-xl">
                Dalam Pesanan
            </h3>
        </div>
        <hr>
        <h1 class="block text-right font-semibold text-5xl">
            {{ App\Models\Order::where('status', 'paid')->count() }}
        </h1>
    </div>
    <div class="p-3 w-full space-y-4 border border-color-3 rounded-md">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-5xl">
                preliminary
            </span>
            <h3 class="font-semibold text-xl">
                Diproses
            </h3>
        </div>
        <hr>
        <h1 class="block text-right font-semibold text-5xl">
            {{ App\Models\Order::where('status', 'processing')->count() }}
        </h1>
    </div>
    <div class="p-3 w-full space-y-4 border border-color-3 rounded-md">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-5xl">
                local_shipping
            </span>
            <h3 class="font-semibold text-xl">
                Dikirim
            </h3>
        </div>
        <hr>
        <h1 class="block text-right font-semibold text-5xl">
            {{ App\Models\Order::where('status', 'shipping')->count() }}
        </h1>
    </div>
    <div class="p-3 w-full space-y-4 border border-color-3 rounded-md">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-5xl">
                approval_delegation
            </span>
            <h3 class="font-semibold text-xl">
                Diterima
            </h3>
        </div>
        <hr>
        <h1 class="block text-right font-semibold text-5xl">
            {{ App\Models\Order::where('status', 'delivered')->count() }}
        </h1>
    </div>
    <div class="p-3 w-full space-y-4 border border-color-3 rounded-md">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-5xl">
                task_alt
            </span>
            <h3 class="font-semibold text-xl">
                Selesai
            </h3>
        </div>
        <hr>
        <h1 class="block text-right font-semibold text-5xl">
            {{ App\Models\Order::where('status', 'success')->count() }}
        </h1>
    </div>
</div>
@endsection