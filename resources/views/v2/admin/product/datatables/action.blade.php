<div class="flex gap-2">
    <form action="{{route('admin.product.delete', ['id' => $data->id])}}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')" method="post">
        @csrf
        @method('delete')
        <button class="text-sm p-1 px-2 text-red-500 border border-red-500 rounded"><span class="material-icons !text-base">delete</span></button>
    </form>
    <button class="text-sm p-1 px-2 text-white bg-color-5 rounded" onclick="window.open('{{route('admin.product.edit', ['id' => $data->id])}}', '_blank')">Edit</button>
</div>