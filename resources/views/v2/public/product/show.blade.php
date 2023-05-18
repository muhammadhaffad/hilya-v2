@extends('v2.layouts.public.app', ['title' => 'Hilya Collection'])
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
        <div  id="choose-product" class="space-y-2">
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
            <p class="font-semibold text-red-500">
                *Untuk memastikan produk tersedia, silahkan hubungi kami terlebih dahulu
                <form action="https://wa.me/6285732698149" method="get">
                    <input class="hidden" type="text" name="text" value="Assalamualaikum, apakah produk ini masih tersedia? {{url()->full()}}">
                    <button type="submit" class="flex items-center gap-1 justify-center px-3 py-1 font-normal bg-green-600 text-white rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0,0,256,256" width="24px" height="24px" fill-rule="evenodd"><g fill="#ffffff" fill-rule="evenodd" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(8,8)"><path d="M24.50391,7.50391c-2.25781,-2.25781 -5.25781,-3.50391 -8.45312,-3.50391c-6.58594,0 -11.94922,5.35938 -11.94922,11.94531c-0.00391,2.10547 0.54688,4.16016 1.59375,5.97266l-1.69531,6.19141l6.33594,-1.66406c1.74219,0.95313 3.71094,1.45313 5.71094,1.45703h0.00391c6.58594,0 11.94531,-5.35937 11.94922,-11.94922c0,-3.19141 -1.24219,-6.19141 -3.49609,-8.44922zM16.05078,25.88281h-0.00391c-1.78125,0 -3.53125,-0.48047 -5.05469,-1.38281l-0.36328,-0.21484l-3.76172,0.98438l1.00391,-3.66406l-0.23437,-0.375c-0.99609,-1.58203 -1.51953,-3.41016 -1.51953,-5.28516c0,-5.47266 4.45703,-9.92578 9.9375,-9.92578c2.65234,0 5.14453,1.03516 7.01953,2.91016c1.875,1.87891 2.90625,4.37109 2.90625,7.02344c0,5.47656 -4.45703,9.92969 -9.92969,9.92969zM21.49609,18.44531c-0.29687,-0.14844 -1.76562,-0.87109 -2.03906,-0.96875c-0.27344,-0.10156 -0.47266,-0.14844 -0.67187,0.14844c-0.19922,0.30078 -0.76953,0.97266 -0.94531,1.17188c-0.17187,0.19531 -0.34766,0.22266 -0.64453,0.07422c-0.30078,-0.14844 -1.26172,-0.46484 -2.40234,-1.48437c-0.88672,-0.78906 -1.48828,-1.76953 -1.66016,-2.06641c-0.17578,-0.30078 -0.01953,-0.46094 0.12891,-0.60937c0.13672,-0.13281 0.30078,-0.34766 0.44922,-0.52344c0.14844,-0.17187 0.19922,-0.29687 0.30078,-0.49609c0.09766,-0.19922 0.04688,-0.375 -0.02734,-0.52344c-0.07422,-0.14844 -0.67187,-1.62109 -0.92187,-2.21875c-0.24219,-0.58203 -0.48828,-0.5 -0.67187,-0.51172c-0.17187,-0.00781 -0.37109,-0.00781 -0.57031,-0.00781c-0.19922,0 -0.52344,0.07422 -0.79687,0.375c-0.27344,0.29688 -1.04297,1.01953 -1.04297,2.48828c0,1.46875 1.07031,2.89063 1.21875,3.08984c0.14844,0.19531 2.10547,3.21094 5.10156,4.50391c0.71094,0.30859 1.26563,0.49219 1.69922,0.62891c0.71484,0.22656 1.36719,0.19531 1.88281,0.12109c0.57422,-0.08594 1.76563,-0.72266 2.01563,-1.42187c0.24609,-0.69531 0.24609,-1.29297 0.17188,-1.41797c-0.07422,-0.125 -0.27344,-0.19922 -0.57422,-0.35156z"></path></g></g></svg>
                        Hubungi Kami
                    </button>
                </form>
            </p>
            <form action="#choose-product" method="get" class="space-y-2">
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
                {!!$product->description!!}
            </div>
        </div>
    </section>
    <textarea class="hidden" id="product_detail">
        {!! $product->toJson() !!}
    </textarea>
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

            function rupiah(number){
                let rupiah = '';		
                let numberrev = number.toString().split('').reverse().join('');
                for(let i = 0; i < numberrev.length; i++) if(i%3 == 0) rupiah += numberrev.substr(i,3)+'.';
                return 'Rp'+rupiah.split('',rupiah.length-1).reverse().join('');
            }

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
                    if (parseInt(data.is_bundle)) {
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
                            <span class="py-1 px-2 h-fit bg-color-4 text-white rounded whitespace-nowrap">${data.size} | ${data.stock}</span>
                            <div class="grow">
                                <p class="font-semibold">${rupiah(data.price - parseInt(data.price*data.discount/100))}</p>
                                ${promo()}
                            </div>
                            <span class="text-xs text-red-500 font-semibold">${parseInt(data.is_bundle) ? 'Bundle' : ''}</span>
                        </div>
                        <p class="text-xs normal-case">${data.note_bene != 'null' ? '' : data.note_bene}</p>
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
                    <button type="submit" class="flex items-center justify-center px-2 py-2 bg-color-4 text-white uppercase rounded">
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
