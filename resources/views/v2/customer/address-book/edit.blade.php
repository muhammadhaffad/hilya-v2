@extends('v2.layouts.customer.app', ['title' => 'Buku Alamat | Hilya Collection'])
@section('content')
@php
    $provinces = Helper::getProvinces();
    $cities = Helper::getCities();
    $subdistricts = Helper::getSubdistricts($address->pluck('city_id')->unique()->all());
@endphp
@if (session('message'))
    <script>alert('{{session('message')}}')</script>
@endif
@if ($errors->any())
    <script>alert('Data yang diberikan tidak valid, periksa kembali masukan Anda.')</script>
@endif
<form action="{{ route('customer.address-book.update', ['addressId' => $address->id]) }}" method="post">
    @method('PUT')
    @csrf
    <div class="space-y-4">
        <div class="block rounded-lg border border-color-4">
            <div class="p-4 space-y-4">
                <div class="grid-cols-1 sm:grid-cols-2 md:grid-cols-3 grid gap-2">
                    <div>
                        <label for="addressname" class="block font-semibold text-color-3">Nama Alamat</label>
                        <input name="addressname" type="text" value="{{ $address->addressname }}" class="p-2 w-full rounded border border-color-3 focus:outline-none">
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="grid-cols-1 sm:grid-cols-2 md:grid-cols-3 grid gap-2">
                        <div>
                            <label for="shippingname" class="block font-semibold text-color-3">
                                Nama Penerima
                            </label>
                            <input id="shippingname" name="shippingname" type="text" value="{{ $address->shippingname }}" class="w-full p-2 border border-color-4 rounded focus:outline-none">
                        </div>
                        <div>
                            <label for="phonenumber" class="block font-semibold text-color-3">
                                Nomor Telepon
                            </label>
                            <input id="phonenumber" name="phonenumber" type="number" value="{{ $address->phonenumber }}" class="w-full p-2 border border-color-4 rounded focus:outline-none">
                        </div>
                    </div>
                    <div class="grid-cols-1 sm:grid-cols-2 md:grid-cols-3 grid gap-2">
                        <input id="data-province" type="hidden" value="{{ $address->province_id }}">
                        <input id="data-city" type="hidden" value="{{ $address->city_id }}">
                        <input id="data-subdistrict" type="hidden" value="{{ $address->subdistrict_id }}">
                        <div>
                            <label for="province" class="block font-semibold text-color-3">
                                Provinsi
                            </label>
                            <select name="province_id" id="province" class="w-full p-2 border border-color-4 rounded">
                                <option>--Pilih Provinsi--</option>
                                {{-- Get from /customer/region --}}
                            </select>
                        </div>
                        <div>
                            <label for="city" class="block font-semibold text-color-3">
                                Kabupaten/Kota
                            </label>
                            <select name="city_id" id="city" class="w-full p-2 border border-color-4 rounded">
                                <option>--Pilih Kabupaten/Kota--</option>
                                {{-- Get from /customer/region/provinceId --}}
                            </select>
                        </div>
                        <div>
                            <label for="subdistrict" class="block font-semibold text-color-3">
                                Kecamatan
                            </label>
                            <select name="subdistrict_id" id="subdistrict" class="w-full p-2 border border-color-4 rounded">
                                <option>--Pilih Kecamatan--</option>
                                {{-- Get from /customer/region/provinceId/cityId --}}
                            </select>
                        </div>
                        <div>
                            <label for="zip" class="block font-semibold text-color-3">
                                Kode Pos
                            </label>
                            <input id="zip" name="zip" type="number" value="{{ $address->zip }}" class="w-full p-2 border border-color-4 rounded focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label for="fulladdress" class="block font-semibold text-color-3">
                            Alamat lengkap
                        </label>
                        <textarea name="fulladdress" id="fulladdress" rows="5" class="w-full p-2 border border-color-4 rounded focus:outline-none">{{ $address->fulladdress }}</textarea>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button class="float-right py-2 px-4 uppercase font-medium bg-color-4 text-white rounded">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>
@push('script')
    <script>
        let provinceId = $('#data-province').val();
        let cityId = $('#data-city').val();
        let subdistrictId = $('#data-subdistrict').val();
        
        $.get('/customer/region', function($data) {
            const provinces = JSON.parse($data);
            for (const key in provinces){
                let option = `<option value="${key}">${provinces[key]}</option>`;
                if (key == provinceId) {
                    option = `<option value="${key}" selected>${provinces[key]}</option>`;
                }
                $('#province').append(option);
            }
        });
        $.get('/customer/region/'+provinceId, function($data) {
            const cities = JSON.parse($data);
            for (const key in cities){
                let option = `<option value="${key}">${cities[key]}</option>`;
                if (key == cityId) {
                    option = `<option value="${key}" selected>${cities[key]}</option>`;
                }
                $('#city').append(option);
            }
        });
        $.get(`/customer/region/${provinceId}/${cityId}`, function($data) {
            const subdistricts = JSON.parse($data);
            for (const key in subdistricts){
                let option = `<option value="${key}">${subdistricts[key]}</option>`;
                if (key == subdistrictId) {
                    option = `<option value="${key}" selected>${subdistricts[key]}</option>`;
                }
                $('#subdistrict').append(option);
            }
        });
        $('#province').change(function(event) {
            provinceId = event.target.value;
            $('#city > option:gt(0)').remove();
            $('#subdistrict > option:gt(0)').remove();
            $.get('/customer/region/'+provinceId, function($data) {
                const cities = JSON.parse($data);
                for (const key in cities){
                    let option = `<option value="${key}">${cities[key]}</option>`;
                    $('#city').append(option);
                }
            }); 
        });
        $('#city').change(function(event) {
            cityId = event.target.value;
            $('#subdistrict > option:gt(0)').remove();
            $.get(`/customer/region/${provinceId}/${cityId}`, function($data) {
                const subdistricts = JSON.parse($data);
                for (const key in subdistricts){
                    let option = `<option value="${key}">${subdistricts[key]}</option>`;
                    $('#subdistrict').append(option);
                }
            }); 
        })
    </script>
@endpush
@endsection