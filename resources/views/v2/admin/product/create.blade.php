@extends('v2.layouts.admin.app', ['title' => 'Produk | Admin Hillia Collection'])
@section('content')
@push('script-head')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush
@push('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-hidden-accessible {
        position: fixed !important;
    }
    .select2-container .select2-selection--single {
        display: flex;
        height: 42px;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: unset;
    }
    .select2-container .select2-selection--multiple {
        min-height: 42px;
    }
    .select2-container .select2-selection--multiple .select2-selection__rendered {
        display: flex;
        flex-wrap: wrap;
    }
    /* Membuat readonly pada select2 */
    /* Membuat readonly pada select2 */
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow,
    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
        display: none;
    }
</style>
@endpush
<div class="flex font-bold text-xl gap-1 items-center uppercase">
    <span class="material-icons !text-4xl">
        inventory
    </span>
    Produk
</div>
<section>
    @if ($errors->any())
    <ul class="bg-red-500 text-white p-4 rounded">
        <li class="font-semibold underline">Error! data yang dimasukkan tidak valid.</li>
        {!! implode('', $errors->all('<li>:message</li>')) !!}
    </ul>
    @endif
    @if (session('message'))
    <ul class="bg-green-500 text-white p-4 rounded">
        <li>{{ session('message') }}</li>
    </ul>
    @endif
    <form enctype="multipart/form-data" id="product" action="{{ route('admin.product.store') }}" method="post">
        @csrf
    </form>
    <form method="post" id="product-items"></form>
    <form method="post" id="add-product-item-simple" onsubmit="addProductSimple(event)"></form>
    <form method="post" id="add-product-item-origin" onsubmit="addProductOrigin(event)"></form>
    <form method="post" id="add-product-item-bundle" onsubmit="addProductBundle(event)"></form>
</section>

<section>
    <button id="add-image" class="underline underline-offset-4" onclick="addImage(this)">+Tambah gambar</button>
</section>

<output class="flex gap-4 flex-nowrap w-max max-w-[calc(100vw-80px)] sm:max-w-[calc(100vw-396px)] whitespace-nowrap  overflow-x-auto">
    <div>
        <div class="relative flex justify-center overflow-hidden items-center aspect-square w-40 border-2 border-dashed border-color-3 rounded">
            <input hidden form="product" type="file" name="product_images[]">
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
</output>

<section class="grid-cols-1 lg:grid-cols-2 grid w-full lg:w-8/12 gap-3">
    <div>
        <label for="name" class="block font-semibold text-color-3">Nama Produk</label>
        <input required form="product" name="product_info[name]" type="text" value="{{ old('product_info.name', '') }}" class="w-full p-2 rounded border border-color-3 focus:outline-none">
    </div>
    <div>
        <label for="product_brand_id" class="block font-semibold text-color-3">Brand</label>
        <select required form="product" name="product_info[product_brand_id]" type="text" class="w-full h-[42px] p-2 rounded border border-color-3 focus:outline-none" style="width: 100%">
            @foreach (Helper::getBrands() as $brand)
            <option value="{{$brand->id}}" @selected($brand->id == old('product_info.product_brand_id', 0))>{{$brand->name}}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="availability" class="block font-semibold text-color-3">Status (Pre order/Ready)</label>
        <select required form="product" name="product_info[availability]" type="text" class="w-full h-[42px] p-2 rounded border border-color-3 focus:outline-none" style="width: 100%">
            <option value="ready" @selected(old('product_info.availability') == 'ready')>Ready</option>
            <option value="pre-order" @selected(old('product_info.availability') == 'pre-order')>Pre order</option>
        </select>
    </div>
    <div>
        <label for="discount" class="block font-semibold text-color-3"><input form="product" type="checkbox" name="product_info[ispromo]" value="1" @checked(old('product_info.ispromo', 0) == 1)   > Promo? (Dalam %)</label>
        <input form="product" @disabled(old('product_info.ispromo', 0) == 0) name="product_info[discount]" type="number" value="{{ old('product_info.discount', 0) }}" class="w-full h-[42px] p-2 rounded border disabled:cursor-not-allowed disabled:bg-color-1 border-color-3 focus:outline-none">
    </div>
    <div class="lg:col-start-1 lg:col-end-2">
        <label for="category" class="block font-semibold text-color-3">Kategori</label>
        <input required form="product" name="product_info[category]" type="text" value="{{ old('product_info.category') }}" class="w-full p-2 rounded border border-color-3 focus:outline-none">
    </div>
    <div class="lg:col-start-1 lg:col-span-2">
        <label for="description" class="block font-semibold text-color-3">Deskripsi</label>
        <textarea required form="product" name="product_info[description]" type="text" class="w-full p-2 rounded border border-color-3 focus:outline-none">{{ old('product_info.description') }}</textarea>
    </div>
</section>

<section class="flex flex-wrap gap-3 w-full">
    <div class="w-full">
        <input type="radio" name="_" id="simple" class="peer hidden" checked>
        <label for="simple" class="w-full block peer-checked:text-white peer-checked:bg-blue-600 peer-checked:border-blue-600 sm:w-max p-2 border border-color-4 rounded cursor-pointer">
            Produk Simple
        </label>
    </div>
    <div class="w-full">
        <input type="radio" name="_" id="bundle" class="peer hidden">
        <label for="bundle" class="w-full block peer-checked:text-white peer-checked:bg-blue-600 peer-checked:border-blue-600 sm:w-max p-2 border border-color-4 rounded cursor-pointer">
            Produk Bundle
        </label>
    </div>
</section>

<div id="product-bundle" class="space-y-4 hidden">
    <span class="block font-semibold text-xl">Produk Individu</span>
    <section class="flex gap-4 flex-nowrap w-max max-w-[calc(100vw-80px)] sm:max-w-[calc(100vw-396px)] p-4 whitespace-nowrap  border border-color-4 rounded overflow-x-auto">
        <div>
            <label for="product_origin_name" class="block font-semibold text-color-3">Nama</label>
            <input required form="add-product-item-origin" name="product_origin_name" type="text" value="{{ old('product_origin_name') }}" class="w-40 h-[42px] p-2 rounded border border-color-3 focus:outline-none">
        </div>
        <div>
            <label for="gender" class="block font-semibold text-color-3">Untuk</label>
            <select required form="add-product-item-origin" name="gender" type="text" value="{{ old('gender') }}" class="w-max h-[42px] p-2 rounded border border-color-3 focus:outline-none">
                <option value="1">Laki Laki Dewasa</option>
                <option value="0">Perempuan Dewasa</option>
                <option value="3">Laki Laki Anak</option>
                <option value="2">Perempuan Anak</option>
            </select>
        </div>
        <div>
            <label for="size" class="block font-semibold text-color-3">Ukuran</label>
            <select required form="add-product-item-origin" name="size" type="text" value="{{ old('size') }}" class="w-max h-[42px] p-2 rounded border border-color-3 focus:outline-none">
                @php $sizes = ['ALL','XS','S','M','L','XL','XXL','3XL','4XL','0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16'] @endphp
                @foreach ($sizes as $size)
                    <option value="{{$size}}">{{$size}}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="color" class="block font-semibold text-color-3">Warna</label>
            <input required form="add-product-item-origin" name="color" type="text" value="{{ old('color') }}" class="w-40 h-[42px] p-2 rounded border border-color-3 focus:outline-none">
        </div>
        <div>
            <label for="stock" class="block font-semibold text-color-3">Stok</label>
            <input required form="add-product-item-origin" name="stock" type="number" value="{{ old('stock') }}" class="w-40 h-[42px] p-2 rounded border border-color-3 focus:outline-none">
        </div>
        <div class="h-auto flex items-end">
            <button form="add-product-item-origin" type="submit" class="w-40 h-[42px] bg-color-4 text-white font-semibold uppercase border rounded">Tambah</button>    
        </div>
    </section>
    <section class="flex gap-4 flex-nowrap max-w-[calc(100vw-80px)] sm:max-w-[calc(100vw-396px)] whitespace-nowrap overflow-x-auto">
        <table id="table-preview-product-origins" class="table-auto border border-color-2 border-separate">
            <thead>
                <tr>
                    <th class="w-0 px-2 border bg-color-1 border-color-3">Nama</th>
                    <th class="w-0 px-2 border bg-color-1 border-color-3">Stock</th>
                    <th class="w-0 px-2 border bg-color-1 border-color-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach (old('product_origins', []) as $product_origin)
                <tr>
                    <td class="px-2 border border-color-3">
                        <input type="hidden" name="product_origins[{{$product_origin['index']}}][index]" value="{{$product_origin['index']}}" form="product-items">
                        <input type="hidden" name="product_origins[{{$product_origin['index']}}][name]" value="{{$product_origin['name']}}" form="product-items">
                        {{$product_origin['name']}}
                    </td>
                    <td class="px-2 border border-color-3">
                        <input type="hidden" name="product_origins[{{$product_origin['index']}}][stock]" value="{{$product_origin['stock']}}" form="product-items">
                        {{$product_origin['stock']}}
                    </td>
                    <td class="px-2 border border-color-3">
                        <button onclick="removeProductOrigin(this)" class="flex items-center justify-center w-[42px] h-[42px] bg-red-500 text-white font-semibold uppercase border rounded">
                            <span class="material-icons">
                                delete
                            </span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>
    <span class="block font-semibold text-xl">Tambah Produk Bundle</span>
    <section class="flex gap-4 flex-nowrap w-max max-w-[calc(100vw-80px)] sm:max-w-[calc(100vw-396px)] p-4 whitespace-nowrap  border border-color-4 rounded overflow-x-auto">
        <div>
            <label for="product_origins" class="block font-semibold text-color-3">Produk Individu</label>
            <select required form="add-product-item-bundle" multiple name="product_origins[]" class="w-max h-[42px] p-2 rounded border border-color-3 focus:outline-none" style="width: 100%">
                @foreach (old('product_origins', []) as $product_origin)
                    <option value="{{$product_origin['index']}}">{{ $product_origin['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="gender" class="block font-semibold text-color-3">Untuk</label>
            <select required form="add-product-item-bundle" name="gender" type="text" value="{{ old('gender') }}" class="w-max h-[42px] p-2 rounded border border-color-3 focus:outline-none" style="width: max-content">
                <option value="1">Laki Laki Dewasa</option>
                <option value="0">Perempuan Dewasa</option>
                <option value="3">Laki Laki Anak</option>
                <option value="2">Perempuan Anak</option>
            </select>
        </div>
        <div>
            <label for="size" class="block font-semibold text-color-3">Ukuran</label>
            <select required form="add-product-item-bundle" name="size" type="text" value="{{ old('size') }}" class="w-max h-[42px] p-2 rounded border border-color-3 focus:outline-none">
                @php $sizes = ['ALL','XS','S','M','L','XL','XXL','3XL','4XL','0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16'] @endphp
                @foreach ($sizes as $size)
                    <option value="{{$size}}">{{$size}}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="color" class="block font-semibold text-color-3">Warna</label>
            <input required form="add-product-item-bundle" name="color" type="text" value="{{ old('color') }}" class="w-40 h-[42px] p-2 rounded border border-color-3 focus:outline-none">
        </div>
        <div>
            <label for="price" class="block font-semibold text-color-3">Harga</label>
            <input required form="add-product-item-bundle" name="price" type="number" value="{{ old('price') }}" class="w-40 h-[42px] p-2 rounded border border-color-3 focus:outline-none">
        </div>
        <div>
            <label for="note_bene" class="block font-semibold text-color-3">Keterangan</label>
            <input required form="add-product-item-bundle" name="note_bene" type="text" value="{{ old('note_bene') }}" class="w-40 h-[42px] p-2 rounded border border-color-3 focus:outline-none">
        </div>
        <div>
            <label class="block opacity-0">.</label>
            <button form="add-product-item-bundle" type="submit" class="w-40 h-[42px] bg-color-4 text-white font-semibold uppercase border rounded">Tambah</button>    
        </div>
    </section>
</div>

<div id="product-simple" class="space-y-4">
    <span class="block font-semibold text-xl">Tambah Produk Simple</span>
    <section class="flex gap-4 flex-nowrap w-max max-w-[calc(100vw-80px)] sm:max-w-[calc(100vw-396px)] p-4 whitespace-nowrap  border border-color-4 rounded overflow-x-auto">
        <div>
            <label for="gender" class="block font-semibold text-color-3">Untuk</label>
            <select required form="add-product-item-simple" name="gender" type="text" value="{{ old('gender') }}" class="w-max h-[42px] p-2 rounded border border-color-3 focus:outline-none">
                <option value="1">Laki Laki Dewasa</option>
                <option value="0">Perempuan Dewasa</option>
                <option value="3">Laki Laki Anak</option>
                <option value="2">Perempuan Anak</option>
            </select>
        </div>
        <div>
            <label for="size" class="block font-semibold text-color-3">Ukuran</label>
            <select required form="add-product-item-simple" name="size" type="text" value="{{ old('size') }}" class="w-max h-[42px] p-2 rounded border border-color-3 focus:outline-none">
                @php $sizes = ['ALL','XS','S','M','L','XL','XXL','3XL','4XL','0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16'] @endphp
                @foreach ($sizes as $size)
                    <option value="{{$size}}">{{$size}}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="color" class="block font-semibold text-color-3">Warna</label>
            <input required placeholder="Warna" form="add-product-item-simple" name="color" type="text" value="{{ old('color') }}" class="w-40 h-[42px] p-2 rounded border border-color-3 focus:outline-none">
        </div>
        <div>
            <label for="price" class="block font-semibold text-color-3">Harga</label>
            <input required placeholder="Harga" form="add-product-item-simple" name="price" type="number" value="{{ old('price') }}" class="w-40 h-[42px] p-2 rounded border border-color-3 focus:outline-none">
        </div>
        <div>
            <label for="stock" class="block font-semibold text-color-3">Stok</label>
            <input required placeholder="Stok" form="add-product-item-simple" name="stock" type="number" value="{{ old('stock') }}" class="w-40 h-[42px] p-2 rounded border border-color-3 focus:outline-none">
        </div>
        <div>
            <label for="note_bene" class="block font-semibold text-color-3">Keterangan</label>
            <input required placeholder="Keterangan" form="add-product-item-simple" name="note_bene" type="text" value="{{ old('note_bene') }}" class="w-40 h-[42px] p-2 rounded border border-color-3 focus:outline-none">
        </div>
        <div class="h-auto flex items-end">
            <button form="add-product-item-simple" type="submit" class="w-40 h-[42px] bg-color-4 text-white font-semibold uppercase border rounded">Tambah</button>    
        </div>
    </section>
</div>

<section class="flex flex-nowrap max-w-[calc(100vw-80px)] sm:max-w-[calc(100vw-380px)] whitespace-nowrap overflow-x-auto">
    <table id="table-preview" class="w-full border border-color-2 border-separate">
        <thead>
            <tr>
                <th class="w-0 px-2 border bg-color-1 border-color-3">Untuk</th>
                <th class="w-0 px-2 border bg-color-1 border-color-3">Ukuran</th>
                <th class="w-0 px-2 border bg-color-1 border-color-3">Warna</th>
                <th class="w-0 px-2 border bg-color-1 border-color-3">Harga</th>
                <th class="w-0 px-2 border bg-color-1 border-color-3">Keterangan</th>
                <th class="w-0 px-2 border bg-color-1 border-color-3">Stok</th>
                <th class="w-0 px-2 border bg-color-1 border-color-3">Bundle?</th>
                <th class="w-0 px-2 border bg-color-1 border-color-3">Produk Individu</th>
                <th class="w-0 px-2 border bg-color-1 border-color-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach (old('product_items', []) as $key => $productItem)
            <tr>
                <td class="px-2 border border-color-3">
                    <input type="hidden" name="product_items[{{$key}}][gender]" value="{{$productItem['gender']}}" form="product-items">
                    @switch($productItem['gender'])
                        @case(0)
                            Perempuan Dewasa
                            @break
                        @case(1)
                            Laki Laki Dewasa
                            @break
                        @case(2)
                            Perempuan Anak
                            @break
                        @case(3)
                            Perempuan Dewasa
                            @break
                        @default
                            -
                    @endswitch
                </td>
                <td class="px-2 text-center border border-color-3">
                    <input type="hidden" name="product_items[{{$key}}][size]" value="{{$productItem['size']}}" form="product-items">
                    {{$productItem['size']}}
                </td>
                <td class="px-2 text-center border border-color-3">
                    <input type="hidden" name="product_items[{{$key}}][color]" value="{{$productItem['color']}}" form="product-items">
                    {{$productItem['color']}}
                </td>
                <td class="px-2 text-center border border-color-3">
                    <input type="hidden" name="product_items[{{$key}}][price]" value="{{$productItem['price']}}" form="product-items">
                    {{$productItem['price']}}
                </td>
                <td class="px-2 text-center border border-color-3">
                    <input type="hidden" name="product_items[{{$key}}][note_bene]" value="{{$productItem['note_bene']}}" form="product-items">
                    {{$productItem['note_bene']}}
                </td>
                <td class="px-2 text-center border border-color-3">
                    <input type="hidden" name="product_items[{{$key}}][stock]" value="{{$productItem['stock']}}" form="product-items">
                    <span>{{$productItem['stock']}}</span>
                </td>
                <td class="px-2 text-center border border-color-3">
                    @if (isset($productItem['product_origins']))
                        Iya
                    @else
                        Tidak
                    @endif
                </td>
                <td class="px-2 border border-color-3">
                    @if (isset($productItem['product_origins']))
                    <select name="product_items[{{$key}}][product_origins][]" onchange="changeStockProductOrigin(event)" form="product-items" readonly multiple>
                        @foreach (old('product_origins', []) as $product_origin)
                        <option value="{{$product_origin['index']}}" @selected(in_array($product_origin['index'], $productItem['product_origins']))>{{ $product_origin['name'] }}</option>
                        @endforeach
                    </select>
                    @else
                        -
                    @endif
                </td>
                <td class="border border-color-3">
                    <button onclick="removeProduct(this)" type="submit" class="mx-auto flex items-center justify-center w-[42px] h-[42px] bg-red-500 text-white font-semibold uppercase border rounded">
                        <span class="material-icons">
                            delete
                        </span>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</section>

<div id="indexSimpleProduct" data="{{ @end(array_keys(old('product_items', [-1 => 0]))) + 1 }}"></div>
<div id="indexBundleProduct" data="{{ @end(array_keys(old('product_origins', [-1 => 0]))) + 1 }}"></div>

<section class="flex w-full">
    <button form="product" type="button" class="ml-auto px-5 w-full sm:w-fit h-[42px] bg-color-4 text-white font-semibold uppercase border rounded" onclick="submitProduct(this)">Tambah Produk</button>
</section>

@push('script')
    <script>
        let indexSimpleProduct = parseInt($('#indexSimpleProduct').attr('data'));
        let indexBundleProduct = parseInt($('#indexBundleProduct').attr('data'));

        $(document).ready(function() {
            $('select').select2({
                width: 'resolve'
            });
        })
        $('input[name="product_info[ispromo]"]').click(function() {
            $('input[name="product_info[discount]"]').prop('disabled', !this.checked);
            $('input[name="product_info[discount]"]').val(0);
        })
        $('#bundle').click(function() {
            $('#product-simple').hide();
            $('#product-bundle').show();
        });
        $('#simple').click(function() {
            $('#product-bundle').hide();
            $('#product-simple').show();
        });

        function addImage(event) {
            let image = `<div>
        <div class="relative flex justify-center overflow-hidden items-center aspect-square w-40 border-2 border-dashed border-color-3 rounded">
            <input hidden form="product" type="file" name="product_images[]">
            <button onclick="uploadImage(this)" class="material-icons p-1 rounded aspect-square cursor-pointer bg-color-4 text-white">
                upload
            </button>
            <button class="absolute top-0 right-0 flex bg-red-500" onclick="removeImage(this)">
                <span class="material-icons font-bold text-white">
                    close
                </span>
            </button>
            <img src="" alt="" class="absolute w-40 -z-10">
        </div></div>`;
            $('output').append(image);
        }

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

        function removeProduct(event) {
            if (confirm("Apakah Anda yakin menghapus produk ini?"))
                console.info(event.closest('tr').remove());
        }

        function checkForms() {
            let result = true;
            const productImages = $('input[type="file"][name="product_images[]"]');
            const productInfo = $('input[name*="product_info"], select[name*="product_info"], textarea[name*="product_info"]');
            const productItems = $('input[name*="product_items"]');
            [...productImages].every(element => {
                if (element.files.length === 0) {
                    alert('Tidak ada gambar produk, silahkan upload gambar.');
                    result = false;
                    return false;
                }
                return true;
            });
            [...productInfo].every(element => {
                if (element.required && !element.value) {
                    alert('Informasi produk tidak lengkap, silahkan cek kembali.');
                    result = false;
                    return false;
                }
                return true;
            });
            if (productItems.length == 0) {
                alert('Tidak ada produk item, silahkan tambah item produk.');
                result = false;
            }
            return result;
        }

        function submitProduct(event) {
            let formProduct = document.querySelector('form#product');
            let formProductItems = new FormData(document.querySelector('form#product-items'));
            for (let field of formProductItems) {
                let input = document.createElement('input');
                input.style.visibility = 'hidden';
                input.name = field[0];
                input.value = field[1];
                formProduct.appendChild(input);
            }
            if (checkForms()) {
                formProduct.submit();
            }
        }

        /* Add Product Item Simple */
        function createTableRowProductSimple(index, attr) {
            gender = '';
            switch (attr.gender) {
                case '0':
                    gender = 'Perempuan Dewasa';
                    break;
                case '1':
                    gender = 'Laki-Laki Dewasa';
                    break;
                case '2':
                    gender = 'Perempuan Anak';
                    break;
                case '3':
                    gender = 'Laki-Laki Anak';
                    break;
                default:
                    break;
            }
            $('#table-preview tbody').append(`<tr>
                <td class="px-2 border border-color-3">
                    <input type="hidden" name="product_items[${index}][gender]" value="${attr.gender}" form="product-items">
                    ${gender}
                </td>
                <td class="px-2 text-center border border-color-3">
                    <input type="hidden" name="product_items[${index}][size]" value="${attr.size}" form="product-items">
                    ${attr.size}
                </td>
                <td class="px-2 text-center border border-color-3">
                    <input type="hidden" name="product_items[${index}][color]" value="${attr.color}" form="product-items">
                    ${attr.color}
                </td>
                <td class="px-2 text-center border border-color-3">
                    <input type="hidden" name="product_items[${index}][price]" value="${attr.price}" form="product-items">
                    ${attr.price}
                </td>
                <td class="px-2 text-center border border-color-3">
                    <input type="hidden" name="product_items[${index}][note_bene]" value="${attr.note_bene}" form="product-items">
                    ${attr.note_bene}
                </td>
                <td class="px-2 text-center border border-color-3">
                    <input type="hidden" name="product_items[${index}][stock]" value="${attr.stock}" form="product-items">
                    ${attr.stock}
                </td>
                <td class="px-2 text-center border border-color-3">
                    Tidak
                </td>
                <td class="px-2 border border-color-3">
                    -
                </td>
                <td class="border border-color-3">
                    <button type="submit" onclick="removeProduct(this)" class="mx-auto flex items-center justify-center w-[42px] h-[42px] bg-red-500 text-white font-semibold uppercase border rounded">
                        <span class="material-icons">
                            delete
                        </span>
                    </button>
                </td>
            </tr>`);
        }

        function addProductSimple(event) {
            event.preventDefault();
            let attr = {};
            const formData = new FormData(event.target);
            for (const data of formData) {
                attr[data[0]] = data[1];
            }
            console.info(attr);
            createTableRowProductSimple(indexSimpleProduct++, attr);
        }

        /* Add Product Item Origin */
        function createTableRowProductOrigin(index, attr) {
            $('#table-preview-product-origins tbody').append(`<tr>
                <td class="px-2 border border-color-3">
                    <input type="hidden" name="product_origins[${index}][index]" value="${index}" form="product-items">
                    <input type="hidden" name="product_origins[${index}][name]" value="${attr.name}" form="product-items">
                    ${attr.name}
                </td>
                <td class="px-2 border border-color-3">
                    <input type="hidden" name="product_origins[${index}][stock]" value="${attr.stock}" form="product-items">
                    ${attr.stock}
                </td>
                <td class="px-2 border border-color-3">
                    <button onclick="removeProductOrigin(this)" class="flex items-center justify-center w-[42px] h-[42px] bg-red-500 text-white font-semibold uppercase border rounded">
                        <span class="material-icons">
                            delete
                        </span>
                    </button>
                </td>
            </tr>`);
        }

        function addProductOrigin(event) {
            event.preventDefault();
            let attr = {};
            const formData = new FormData(event.target);
            for (const data of formData) {
                attr[data[0]] = data[1];
            }
            gender = '';
            switch (attr.gender) {
                case '0':
                    gender = 'Perempuan Dewasa';
                    break;
                case '1':
                    gender = 'Laki-Laki Dewasa';
                    break;
                case '2':
                    gender = 'Perempuan Anak';
                    break;
                case '3':
                    gender = 'Laki-Laki Anak';
                    break;
                default:
                    break;
            }
            attr['name'] = `${attr['product_origin_name']}|${gender}|${attr['size']}|${attr['color']}`;
            createTableRowProductOrigin(indexBundleProduct, attr);
            let option = `<option value="${indexBundleProduct++}">${attr['name']}</option>`;
            $('select[name="product_origins[]"]').append(option);
            $('select[name="product_origins[]"]').trigger('change');
            $('select[name^="product_items["][name$="][product_origins][]"]').append(option);
            $('select[name^="product_items["][name$="][product_origins][]"]').trigger(option);
            alert('Produk individu berhasil ditambah!');
        }

        /* Add Product Item Bundle */
        function createTableRowProductBundle(index, attr) {
            /* Menghitung stok */
            let productOrigins = document.querySelectorAll('#table-preview-product-origins tr');
            let stock = Infinity;
            for (let productOrigin of productOrigins) {
                if (attr['product_origins[]'].includes(productOrigin.querySelector('input')?.value.toString())) {
                    let productOriginStock = parseInt(productOrigin.querySelectorAll('input')[2]?.value);
                    if (stock > productOriginStock) {
                        stock = productOriginStock;
                    }
                }    
            }

            /* Gender */
            let gender = '';
            switch (attr.gender) {
                case '0':
                    gender = 'PEREMPUAN (DEWASA)';
                    break;
                case '1':
                    gender = 'LAKI LAKI (DEWASA)';
                    break;
                case '2':
                    gender = 'PEREMPUAN (ANAK)';
                    break;
                case '3':
                    gender = 'LAKI LAKI (ANAK)';
                    break;

                default:
                    break;
            }

            let productOriginSelect = $('select[name="product_origins[]"] option');
            let productOriginSelected = $(`<select name="product_items[${index}][product_origins][]" onchange="changeStockProductOrigin(event)" form="product-items" readonly multiple>`);
            for (let option of productOriginSelect) {
                if ($.inArray((option.value).toString(), attr['product_origins[]']) !== -1) {
                    productOriginSelected.append(`<option value="${option.value}" selected="selected">${option.text}</option>`);
                } else {
                    productOriginSelected.append(`<option value="${option.value}">${option.text}</option>`);
                }
            }

            $('#table-preview').append(`<tr>
                <td class="px-2 border border-color-3">
                    <input type="hidden" name="product_items[${index}][gender]" value="${attr.gender}" form="product-items">
                    ${gender}
                </td>
                <td class="px-2 text-center border border-color-3">
                    <input type="hidden" name="product_items[${index}][size]" value="${attr.size}" form="product-items">
                    ${attr.size}
                </td>
                <td class="px-2 text-center border border-color-3">
                    <input type="hidden" name="product_items[${index}][color]" value="${attr.color}" form="product-items">
                    ${attr.color}
                </td>
                <td class="px-2 text-center border border-color-3">
                    <input type="hidden" name="product_items[${index}][price]" value="${attr.price}" form="product-items">
                    ${attr.price}
                </td>
                <td class="px-2 text-center border border-color-3">
                    <input type="hidden" name="product_items[${index}][note_bene]" value="${attr.note_bene}" form="product-items">
                    ${attr.note_bene}
                </td>
                <td class="px-2 text-center border border-color-3">
                    <input type="hidden" name="product_items[${index}][stock]" value="${stock}" form="product-items">
                    <span>${stock}</span>
                </td>
                <td class="px-2 text-center border border-color-3">
                    Iya
                </td>
                <td class="px-2 border border-color-3">
                    ${productOriginSelected.prop('outerHTML')}
                </td>
                <td class="border border-color-3">
                    <button onclick="removeProduct(this)" type="submit" class="mx-auto flex items-center justify-center w-[42px] h-[42px] bg-red-500 text-white font-semibold uppercase border rounded">
                        <span class="material-icons">
                            delete
                        </span>
                    </button>
                </td>
            </tr>`);
            $('select[name^="product_items["][name$="][product_origins][]"]').select2();
        }

        function addProductBundle(event) {
            event.preventDefault();
            let attr = {};
            const formData = new FormData(event.target);
            for (const data of formData) {
                if (data[0].includes('product_origins')) {
                    attr[data[0]] = formData.getAll(data[0]);
                } else {
                    attr[data[0]] = data[1];
                }
            }
            createTableRowProductBundle(indexSimpleProduct++, attr);
        }

        function removeProductOrigin(event) {
            if (confirm("Apakah Anda yakin menghapus produk individu ini?")) {
                let rowRemoved = $(event).closest('tr');
                let productOriginRemovedId = $(rowRemoved.find('td')[0]).find('input')[0].value;
                $('select[name="product_origins[]"]').find('option')
                    .each(function(index, option) {
                        if (option.value == productOriginRemovedId) {
                            option.remove();
                        }
                    });
                
                let previewTableRow = $('#table-preview tr');
                previewTableRow.each(function(index, row) {
                    let productOriginOptions = $(row).find('option');
                    productOriginOptions.each(function(index, option) {
                        if (option.value == productOriginRemovedId) {
                            option.remove();
                        }
                    })
                });
                $('select[name="product_origins[]"]').trigger('change');
                $('select[name^="product_items["][name$="][product_origins][]"]').trigger('change');
                $(event).closest('tr').remove();
                alert('Produk individu berhasil dihapus!');
            }
        }

        function changeStockProductOrigin(event) {
            /* Mendapatkan semua stock produk origin */
            let productOriginStocks = []
            $('#table-preview-product-origins').find('tr').each(function(index, row) {
                if (index !== 0) {
                    productOriginStocks.push({ 
                        id : $(row).find('input[name*="index"]').get(0).value, 
                        stock : parseInt($(row).find('input[name*="stock"]').get(0).value)
                    });
                }
            });
            /* Mengupdate jumlah stock */
            $('#table-preview tbody tr').each(function(index, row) {
                let productOriginSelected = $(row).find('select[name*="product_origins"]').val();
                if (productOriginSelected) {
                    /* Jika row memiliki product origin */
                    productOriginSelected = $.grep(productOriginStocks, function(item) {
                        return (productOriginSelected).includes(item.id);
                    });
                    let minStock = Math.min(...$.map(productOriginSelected, function(item) {
                        return item.stock;
                    }));
                    if (minStock == Infinity) {
                        $(row).remove();
                    }
                    $(row).find('input[name*=stock]').get(0).value = minStock;
                    $(row).find('span:first').text(minStock);
                }
            }); 
        }
    </script>
@endpush
@endsection