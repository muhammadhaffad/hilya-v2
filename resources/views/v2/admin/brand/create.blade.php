@extends('v2.layouts.admin.app', ['title' => 'Brand | Admin Hillia Collection'])
@section('content')
<div class="flex font-bold text-xl gap-1 items-center uppercase">
    <span class="material-icons !text-4xl">
        sell
    </span>
    Tambah Brand Produk
</div>
@if (session('message'))
    <script>alert('{{ session("message") }}')</script>
@endif
<section>
    <form id="form-brand" enctype="multipart/form-data" method="post" action="{{route('admin.brand.store')}}">
        @csrf
    </form>
</section>
<section class="w-full">
    <div>
        <div class="relative flex justify-center overflow-hidden items-center aspect-square w-40 border-2 border-dashed border-color-3 rounded">
            <input hidden form="form-brand" type="file" name="image">
            <button onclick="uploadImage(this)" class="material-icons p-1 rounded aspect-square cursor-pointer bg-color-4 text-white">
                upload
            </button>
            <button class="absolute top-0 right-0 flex bg-red-500" onclick="removeImage(this)">
                <span class="material-icons font-bold text-white">
                    close
                </span>
            </button>
            <img src="" alt="" class="absolute w-40 -z-10">
        </div>
    </div>
</section>

<section class="grid-cols-1 lg:grid-cols-2 grid w-full lg:w-8/12 gap-3">
    <div>
        <label for="name" class="block font-semibold text-color-3">Nama Brand</label>
        <input required form="form-brand" name="name" type="text" value="{{ old('name', '') }}" class="w-full p-2 rounded border border-color-3 focus:outline-none">
    </div>
</section>
<button form="form-brand" type="submit" class="h-[42px] w-full sm:w-fit rounded text-white bg-color-5 p-2">Tambah</button>
@push('script')
    <script>
        function uploadImage(event) {
            let inputFile = $(event).prev();
            let image = $(event).next().next();
            inputFile.click();
            inputFile.change((event) => {
                const file = event.target.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function (event) {
                        const base64img = event.target.result;
                        image.attr('src', base64img);
                        // console.info(inputFile.nextUntil('img'));
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        function removeImage(event) {
            if (confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
                console.info($(event).parent().parent().remove())
            }
        }
    </script>
@endpush
@endsection