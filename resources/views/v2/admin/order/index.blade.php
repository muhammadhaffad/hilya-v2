@extends('v2.layouts.admin.app', ['title' => 'Detail Pesanan | Hilya Collection'])
@section('content')
<div class="flex font-bold text-xl gap-1 items-center uppercase">
    <span class="material-icons !text-4xl">
        list_alt
    </span>
    Pesanan
</div>
<section class="flex flex-col">
    <ul class="flex flex-wrap gap-1">
        <li>
            <a class="font-semibold" href="{{ request()->fullUrlWithQuery(['status' => '']) }}">
                Semua ({{ \App\Models\Order::withoutStatus(['cart', 'checkout'])->count() }})
            </a>
        </li>
        <li>|</li>
        <li>
            <a class="font-semibold" href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}">
                Menunggu Pembayaran ({{ \App\Models\Order::where('status', 'pending')->count() }})
            </a>
        </li>
        <li>|</li>
        <li>
            <a class="font-semibold" href="{{ request()->fullUrlWithQuery(['status' => 'paid']) }}">
                Dalam Pesanan ({{ \App\Models\Order::where('status', 'paid')->count() }})
            </a>
        </li>
        <li>|</li>
        <li>
            <a class="font-semibold" href="{{ request()->fullUrlWithQuery(['status' => 'processing']) }}">
                Diproses ({{ \App\Models\Order::where('status', 'processing')->count() }})
            </a>
        </li>
        <li>|</li>
        <li>
            <a class="font-semibold" href="{{ request()->fullUrlWithQuery(['status' => 'shipping']) }}">
                Dikirim ({{ \App\Models\Order::where('status', 'shipping')->count() }})
            </a>
        </li>
        <li>|</li>
        <li>
            <a class="font-semibold" href="{{ request()->fullUrlWithQuery(['status' => 'delivered']) }}">
                Diterima ({{ \App\Models\Order::where('status', 'delivered')->count() }})
            </a>
        </li>
        <li>|</li>
        <li>
            <a class="font-semibold" href="{{ request()->fullUrlWithQuery(['status' => 'success']) }}">
                Selesai ({{ \App\Models\Order::where('status', 'success')->count() }})
            </a>
        </li>
        {{-- <li>|</li>
        <li>
            <a class="font-semibold" href="{{ request()->fullUrlWithQuery(['status' => 'ready']) }}">
                Siap Diambil ({{ $orders->where('status', 'ready')->count() }})
            </a>
        </li> --}}
    </ul>
</section>
<table id="example" class="display nowrap" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Order</th>
            <th>Username</th>
            <th><div class="flex flex-col">
                <span>Waktu Transaksi</span>
                <span class="text-xs font-semibold text-red-500">*(Tahun-Bulan-Hari Jam:Menit:Detik)</span>
            </div></th>
            <th><div class="flex flex-col">
                <span>Batas Waktu</span>
                <span class="text-xs font-semibold text-red-500">*(Tahun-Bulan-Hari Jam:Menit:Detik)</span>
            </div></th>
            <th><div class="flex flex-col">
                <span>Waktu Bayar</span>
                <span class="text-xs font-semibold text-red-500">*(Tahun-Bulan-Hari Jam:Menit:Detik)</span>
            </div></th>
            <th>Total</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<input type="hidden" name="order_data_url" value="{{ route('admin.order.data') }}?status={{request()->status}}">
@push('style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
@endpush
@push('script')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function () {
            $('#example').DataTable({
                ordering: false,
                serverSide: true,
                processing: true,
                scrollY: 420,
                scrollX: true,
                ajax: {
                    'url' : $('input[name="order_data_url"]').val()
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', width: '10px', orderable: false, searchable: false},
                    {data: 'payment.order_code', name: 'payment.order_code', orderable: false},
                    {data: 'user.username', name: 'user.username', orderable: false},
                    {data: 'payment.transactiontime', name: 'payment.transactiontime', orderable: false},
                    {data: 'payment.expiredtime', name: 'payment.expiredtime', orderable: false},
                    {data: 'payment.settlementtime', name: 'payment.settlementtime', orderable: false},
                    {data: 'grandtotal', name: 'grandtotal', orderable: false},
                    {data: 'status', name: 'status', orderable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                search: 'regex'
            });
        });
    </script>
@endpush
@endsection