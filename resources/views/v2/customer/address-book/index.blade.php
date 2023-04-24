@extends('v2.layouts.customer.app', ['title' => 'Buku Alamat | Hillia Collection'])
@section('content')
@php
    $provinces = Helper::getProvinces();
    $cities = Helper::getCities();
    $subdistricts = Helper::getSubdistricts($addresses->pluck('city_id')->unique()->all());
@endphp
<div class="flex font-bold text-xl gap-1 items-center uppercase">
    <span class="material-icons !text-4xl">
        contacts
    </span>
    Buku Alamat
</div>
@if (session('message'))
    <script>alert('{{session('message')}}')</script>
@endif
<div class="flex justify-end">
    <a href="{{ route('customer.address-book.create') }}" class="ml-auto py-2 px-4 uppercase font-medium bg-color-4 text-white rounded">+ Tambah Alamat</a>
</div>
<ul class="space-y-4">
    @foreach ($addresses as $index => $address)
    <li>
        <div for="alamat-{{ $index }}"
            class="{{$address->isselect ? 'bg-color-1' : ''}} block rounded-lg border border-color-4">
            <div class="p-4 space-y-4">
                <div class="flex flex-wrap gap-2 justify-between">
                    <span class="w-min p-1 px-2 bg-color-4 text-white rounded">
                        {{ $address->addressname }}
                    </span>
                    <div class="flex gap-2 ml-auto">
                        <form action="{{ route('customer.address-book.destroy', ['addressId' => $address->id]) }}" method="post">
                            @method('delete')
                            @csrf
                            <button {{ $address->type == 'primary' ? 'disabled' : '' }} type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus alamat ini?')" class="{{ $address->type == 'primary' ? 'cursor-not-allowed bg-red-400' : '' }} flex items-center aspect-square w-8 bg-red-600 rounded">
                                <span class="material-icons mx-auto text-white !text-base">
                                    delete
                                </span>
                            </button>
                        </form>
                        <a href="{{ route('customer.address-book.edit', ['addressId' => $address->id]) }}" class="flex items-center aspect-square w-8 bg-color-4 rounded">
                            <span class="material-icons mx-auto text-white !text-base">
                                edit
                            </span>
                        </a>
                    </div>
                </div>
                <ul class="space-y-2">
                    <li>
                        <span class="block font-semibold text-color-3">
                            Nama penerima & No. Tlp
                        </span>
                        <span>
                            {{ $address->shippingname }}
                            ({{ $address->phonenumber }})
                        </span>
                    </li>
                    <li>
                        <ul class="flex flex-wrap gap-2 justify-between">
                            <li>
                                <span class="block font-semibold text-color-3">
                                    Provinsi
                                </span>
                                <span>
                                    {{ Helper::getProvince($provinces, $address->province_id) }}
                                </span>
                            </li>
                            <li>
                                <span class="block font-semibold text-color-3">
                                    Kabupaten/Kota
                                </span>
                                <span>
                                    {{ Helper::getCity($cities, $address->city_id) }}
                                </span>
                            </li>
                            <li>
                                <span class="block font-semibold text-color-3">
                                    Kecamatan
                                </span>
                                <span>
                                    {{ Helper::getSubdistrict($subdistricts, $address->subdistrict_id) }}
                                </span>
                            </li>
                            <li>
                                <span class="block font-semibold text-color-3">
                                    Kode Pos
                                </span>
                                <span>
                                    {{ $address->zip }}
                                </span>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <span class="block font-semibold text-color-3">
                            Alamat lengkap
                        </span>
                        <span>
                            {{ $address->fulladdress }}
                        </span>
                    </li>
                </ul>
                <form action="{{ route('customer.address-book.select', ['addressId' => $address->id]) }}" method="post">
                    @method('put')
                    @csrf
                    <div class="flex justify-end">
                        <button {{ $address->isselect ? 'disabled' : '' }} class="{{ $address->isselect ? 'bg-color-3 cursor-not-allowed' : '' }} float-right py-2 px-4 uppercase font-medium bg-color-4 text-white rounded">Pilih Alamat</button>
                    </div>
                </form>
            </div>
        </div>
    </li>
    @endforeach
</ul>
@endsection