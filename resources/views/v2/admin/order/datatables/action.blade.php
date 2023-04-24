<div class="flex gap-2">
    <button class="text-sm p-1 px-2 text-white bg-color-5 rounded" onclick="window.open('{{route('admin.order.show', ['code' => $data->payment->order_code])}}', '_blank')">Lihat</button>
    @if ($data->status == 'paid')
        <form action="{{route('admin.order.set-processing', ['code' => $data->payment->order_code])}}" method="post" onsubmit="return confirm('Apakah Anda yakin ingin memproses pesanan ini?')">
            @csrf
            <button class="text-sm p-1 px-2 text-color-5 border border-color-5 rounded">Proses Pesanan</button>
        </form>
    @endif
    @if ($data->status == 'processing')
        <button class="text-sm p-1 px-2 text-color-5 border border-color-5 rounded" onclick="window.open('{{route('admin.order.show', ['code' => $data->payment->order_code])}}', '_blank')">Konfirmasi Pengiriman</button>
    @endif
    @if ($data->status == 'delivered')
        <form action="{{route('admin.order.set-success', ['code' => $data->payment->order_code])}}" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan transaksi pesanan ini?')">
            @csrf
            <button class="text-sm p-1 px-2 text-color-5 border border-color-5 rounded">Selesaikan</button>
        </form>
    @endif
</div>