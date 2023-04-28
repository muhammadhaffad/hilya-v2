@extends('v2.layouts.admin.app', ['title' => 'Produk | Admin Hilya Collection'])
@section('content')
<div class="flex font-bold text-xl gap-1 items-center uppercase">
    <span class="material-icons !text-4xl">
        inventory
    </span>
    Daftar Produk
</div>
<section class="flex flex-wrap gap-4 justify-between items-end">
    <form action="" method="get" class="flex flex-wrap gap-2 grow">
        <div class="flex flex-col w-full md:w-max">
            <label for="brand">Brand:</label>
            <select name="brand" id="brand" onchange="submit()" class="h-[42px] p-2 rounded border border-color-5">
                <option value="">--Pilih--</option>
                @foreach (\App\Models\ProductBrand::all() as $productBrand)
                    <option value="{{$productBrand->id}}" @selected($productBrand->id == request()->brand)>{{\Str::title($productBrand->name)}}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col w-full md:w-max">
            <label for="status">Status:</label>
            <select name="status" id="status" onchange="submit()" class="h-[42px] p-2 rounded border border-color-5">
                <option value="">--Pilih--</option>
                <option value="ready" @selected(request()->status == 'ready')>Ready</option>
                <option value="pre-order" @selected(request()->status == 'pre-order')>Pre-Order</option>
            </select>
        </div>
        <div class="flex flex-col w-full md:w-max">
            <label for="ispromo">Promo(?):</label>
            <select name="ispromo" id="ispromo" onchange="submit()" class="h-[42px] p-2 rounded border border-color-5">
                <option value="">--Pilih--</option>
                <option value="true" @selected(request()->ispromo == 'true')>Iya</option>
                <option value="false" @selected(request()->ispromo == 'false')>Tidak</option>
            </select>
        </div>
    </form>
    <button onclick="window.open('{{route('admin.product.create')}}', '_blank')" class="h-[42px] ml-auto rounded text-white bg-color-5 p-2 w-full md:w-max">+ Tambah Produk</button>
</section>
<table id="example" class="display nowrap" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Gambar</th>
            <th>Nama</th>
            <th>Brand</th>
            <th>Status</th>
            <th>Promo?</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<input type="hidden" name="product_data_url" value="{{ route('admin.product.data') }}?brand={{request()->brand}}&status={{request()->status}}&ispromo={{request()->ispromo}}">
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
                scrollY: 512,
                scrollX: true,
                ajax: {
                    'url' : $('input[name="product_data_url"]').val()
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', width: '10px', searchable: false},
                    {data: 'productImage.image', name: 'productImage.image', width: '96px', searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'productBrand.name', name: 'productBrand.name'},
                    {data: 'availability', name: 'availability'},
                    {data: 'ispromo', name: 'ispromo', searchable: false},
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        });
    </script>
@endpush
@endsection