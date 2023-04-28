@extends('v2.layouts.admin.app', ['title' => 'Brand | Admin Hilya Collection'])
@section('content')
<div class="flex font-bold text-xl gap-1 items-center uppercase">
    <span class="material-icons !text-4xl">
        sell
    </span>
    Daftar Brand Produk
</div>
@if (session('message'))
    <script>alert('{{ session("message") }}')</script>
@endif
<section class="flex justify-end items-end">
    <button onclick="window.open('{{route('admin.brand.create')}}', '_blank')" class="h-[42px] rounded text-white bg-color-5 p-2">+ Tambah Brand</button>
</section>
<table id="example" class="display nowrap" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Logo</th>
            <th>Nama</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<input type="hidden" name="brand_data_url" value="{{ route('admin.brand.data') }}">
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
                    'url' : $('input[name="brand_data_url"]').val()
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', width: '10px', searchable: false},
                    {data: 'image', name: 'image', width: '96px', searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        });
    </script>
@endpush
@endsection