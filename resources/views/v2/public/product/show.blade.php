@extends('v2.layouts.public.app', ['title' => 'Hillia Collection'])
@section('content')
    @push('style')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    @endpush
    @push('script')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    @endpush
    @if ($errors->addToCartErrors->any())
        <span class="inline-block mb-4 w-full p-2 bg-red-500 text-white rounded">
            @foreach (session('errors')->addToCartErrors->getMessages() as $key => $value)
                {{ implode(',', $value) }}
            @endforeach
        </span>
    @endif
    <section class="grid grid-cols-1 mb-16 sm:grid-cols-2 sm:mt-8 sm:mb-36 gap-5 px-4">
        <div class="w-full space-y-4">
            <div class="w-full">
                <img id="main-image" src="{{asset('storage/'.$product->productImages->first()->image)}}" alt="" class="w-full">
            </div>
            <div class="flex gap-4 flex-nowrap max-w-full">
                <div class="overflow-x-scroll flex gap-4">
                    @foreach ($product->productImages as $k => $productImage)
                    <div>
                        <div class="w-28">
                            <input id="image-{{$k}}" type="radio" name="image" class="hidden peer" @checked($k == 0)>
                            <label for="image-{{$k}}" class="peer-checked:[&>img]:border peer-checked:[&>img]:border-color-4 cursor-pointer">
                                <img src="{{asset('storage/'.$productImage->image)}}" alt="" onclick="$('#main-image').attr('src', this.src)" class="rounded">
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="space-y-2">
            <h3 class="uppercase font-semibold text-xl">{{$product->productBrand->name}}</h3>
            <h1 class="text-4xl">{{$product->name}}</h1>
            <div>
                <h2 class="font-bold text-2xl">
                    @if ($product->product_items_min_price !== $product->product_items_max_price)
                    {{\Helper::rupiah($product->product_items_min_price - (int)($product->product_items_min_price*$product->productItems->first()->discount/100))}} - {{\Helper::rupiah($product->product_items_max_price - (int)($product->product_items_max_price*$product->productItems->first()->discount/100))}}
                    @else
                    {{\Helper::rupiah($product->product_items_min_price - (int)($product->product_items_min_price*$product->productItems->first()->discount/100))}}
                    @endif
                </h2>
                @if ($product->ispromo)
                <h4 class="font-bold text-red-500">{{$product->productItems->first()->discount}}% 
                <s class="font-normal text-color-5">
                    @if ($product->product_items_min_price !== $product->product_items_max_price)
                    {{\Helper::rupiah($product->product_items_min_price)}} - {{\Helper::rupiah($product->product_items_max_price)}}
                    @else
                    {{\Helper::rupiah($product->product_items_min_price)}} 
                    @endif
                </s></h4>
                @endif
            </div>
            <span class="block">{{$product->category ?? 'Busana'}}</span>
            <form action="" method="get" class="space-y-2">
                <div>
                    <label class="font-semibold" for="">Pilih untuk:</label>
                    <div id="gender" class="flex flex-wrap gap-x-2 gap-y-2">
                        <p class="text-sm italic">Kosong...</p>
                    </div>
                </div>
                <div>
                    <label class="font-semibold" for="">Pilih warna:</label>
                    <div id="color" class="flex gap-2">
                        <p class="text-sm italic">Pilih gender dan umur terlebih dahulu...</p>
                    </div>
                </div>
            </form>
            <form action="{{route('product.add-to-cart', ['product'=>$product->id])}}" method="POST" class="space-y-2">
                @csrf
                <div>
                    <label class="font-semibold" for="">Pilih Ukuran:</label>
                    <div id="size" class="flex flex-wrap gap-2">
                        <p class="text-sm italic">Pilih warna terlebih dahulu...</p>
                    </div>
                </div>
                <div id="qty">
                </div>
            </form>
            <div>
                <label class="font-semibold">Deskripsi</label>
                <p>{{$product->description}}</p>
            </div>
        </div>
    </section>
    <div class="hidden" id="product_detail">
        {!! $product->toJson() !!}
    </div>
    @if (session('message'))
        @push('script')
            <script>
                alert("{{ session('message') }}")
            </script>
        @endpush
    @endif
    @push('script')
        <script>
            const urlParam = new URLSearchParams(window.location.search);
            const productDetail = JSON.parse($('#product_detail').text());
            const productItems = productDetail.product_items;

            function generateGenderButton(key, data) {
                let button = `<button class="flex">
                    <input id="gender-${key}" type="radio" value="${data.gender}" name="gender" class="hidden peer" onclick="$(this).next().prop('checked', true)">
                    <input id="age-${key}" type="radio" value="${data.age}" name="age" class="hidden">
                    <label for="gender-${key}" class="px-3 py-1 whitespace-nowrap border-color-4 peer-checked:bg-color-4 peer-checked:text-white border rounded uppercase cursor-pointer">
                        ${data.gender+' '+data.age}
                    </label>
                </button>`;
                return button;
            }

            function generateColorButton(key, data) {
                let button = `<button class="flex">
                    <input id="color-${key}" type="radio" value="${data.color}" name="color" class="hidden peer">
                    <label for="color-${key}" class="px-3 py-1 whitespace-nowrap border-color-4 peer-checked:bg-color-4 peer-checked:text-white border rounded uppercase cursor-pointer">
                        ${data.color}
                    </label>
                </button>`;
                return button;
            }

            function generateSizeButton(key, data) {
                let promo = () => {
                    if (data.ispromo) {
                        return `<p class="font-semibold text-red-500 text-xs">${data.discount}% <s class="text-color-5 font-normal">${data.price}</s></p>`;
                    }
                    return '';
                }

                let tableOrigins = () => {
                    if (data.is_bundle) {
                        productOrigins = data.product_origins;
                        let table = `<table class="text-xs normal-case">
                        <thead class="text-left border">
                            <tr>
                                <th class="border border-color-2 p-1">Nama</th>
                                <th class="border border-color-2 p-1">Untuk</th>
                                <th class="border border-color-2 p-1">Ukuran</th>
                                <th class="border border-color-2 p-1">Warna</th>
                            </tr>
                        </thead>
                        <tbody>`;
                            productOrigins.forEach(element => {
                                let name = element.name;
                                name = name.split('|');
                                table += `<tr>
                                <td class="border border-color-2 p-1">${name[0]}</td>
                                <td class="border border-color-2 p-1">${name[1]}</td>
                                <td class="border border-color-2 p-1">${name[2]}</td>
                                <td class="border border-color-2 p-1">${name[3]}</td>
                            </tr>`
                            });
                        table += `</tbody>
                        </table>`
                        return table;
                    }
                    return '';
                }

                let button = `<button class="text-left flex" type="button">
                    <input id="size-${key}" type="radio" name="product_item_id" value="${data.id}" class="hidden peer">
                    <label for="size-${key}" class="p-1 h-fit border border-color-4 rounded uppercase peer-checked:bg-color-1 cursor-pointer">
                        <div class="flex gap-1 text-sm">
                            <span class="py-1 px-2 h-fit bg-color-4 text-white rounded whitespace-nowrap">${data.size} ${data.stock}</span>
                            <div class="grow">
                                <p class="font-semibold">${data.price - parseInt(data.price*data.discount/100)}</p>
                                ${promo()}
                            </div>
                            <span class="text-xs text-red-500 font-semibold">${data.is_bundle ? 'Bundle' : ''}</span>
                        </div>
                        <p class="text-xs normal-case">${'*'+data.note_bene}</p>
                        ${tableOrigins()}
                    </label>
                </button>`;
                return button;
            }

            function generateQtyButton() {
                return `<label class="font-semibold">Jumlah Pesanan:</label>
                <div class="flex flex-wrap gap-2">
                    <div class="flex">
                        <button type="button" class="minus flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 bg-color-4 text-white rounded">
                            <span class="material-icons !text-base">remove</span>
                        </button>
                        <input name="qty" type="number" min="1" class="flex text-center justify-center w-14 h-8 sm:w-20 sm:h-10 border-b border-color-4" value="1">
                        <button type="button" class="plus flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 bg-color-4 text-white rounded">
                            <span class="material-icons !text-base">add</span>
                        </button>
                    </div>
                    <button type="submit" class="flex items-center justify-center px-4 bg-color-4 text-white uppercase rounded">
                        Tambah ke Keranjang
                    </button>
                </div>`;
            }

            /* Membuat tombol pilihan gender */
            const genders = [... new Set(productItems.filter(obj => obj.stock > 0).map(item => JSON.stringify({'age':item.age, 'gender':item.gender})))].map(item => JSON.parse(item));
            if (genders.length > 0) {
                $('#gender p').remove();
            }
            Object.keys(genders).forEach(key => {
                $('#gender').append(generateGenderButton(key, genders[key]));
            });
            let ageSelected = urlParam.get('age');
            let genderSelected = urlParam.get('gender');
            console.info(ageSelected);
            console.info(genderSelected);
            [... $('button')].forEach((element) => {
                let age = $(element).find('input[name="age"]').val();
                let gender = $(element).find('input[name="gender"]').val();
                if (age === ageSelected && gender === genderSelected) {
                    $(element).find('input[name="age"]').prop('checked', true);
                    $(element).find('input[name="gender"]').prop('checked', true);
                }
            });
            
            /* Membuat tombol pilihan warna */
            const colors = [... new Set(productItems.filter(obj => obj.age === ageSelected && obj.gender === genderSelected && obj.stock > 0).map(item => JSON.stringify({'color':item.color})))].map(item => JSON.parse(item));
            if (colors.length > 0) {
                $('#color p').remove();
            }
            Object.keys(colors).forEach(key => {
                $('#color').append(generateColorButton(key, colors[key]))
            });
            let colorSelected = urlParam.get('color');
            [... $('button')].forEach((element) => {
                let color = $(element).find('input[name="color"]').val();
                if (color === colorSelected) {
                    $(element).find('input[name="color"]').prop('checked', true);
                }
            });
            
            /* Membuat tombol pilihan ukuran */
            const sizes = [... new Set(productItems.filter(obj => obj.age === ageSelected && obj.gender === genderSelected && obj.color === colorSelected && obj.stock > 0).map(item => JSON.stringify(item)))].map(item => JSON.parse(item));
            if (sizes.length > 0) {
                $('#size p').remove();
                $('#qty').append(generateQtyButton());
            }
            Object.keys(sizes).forEach(key => {
                $('#size').append(generateSizeButton(key, sizes[key]));
            });


            let n = 1;
            $('.plus').on('click', function() {
                $('input[name="qty"]').val(++n);
            })
            $('.minus').on('click', function() {
                if (n > 1) {
                    $('input[name="qty"]').val(--n);
                }
            })
        </script>
    @endpush
@endsection
