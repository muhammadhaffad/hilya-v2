@extends('v2.layouts.customer.app', ['title' => 'Buku Alamat | Hillia Collection', 'notfound' => false])
@section('content')
<div class="flex font-bold text-xl gap-1 items-center uppercase">
    <span class="material-icons !text-4xl">
        manage_accounts
    </span>
    Pengaturan Akun
</div>
@if (session('message'))
    <script>alert('{{session('message')}}')</script>
@endif
@if ($errors->any())
    <ul class="p-2 rounded w-8/12 bg-red-500 text-white">
        @foreach ($errors->all() as $error)
            <li>{{$error}}</li>
        @endforeach
    </ul>
@endif
<div class="grid-cols-2 gap-4 grid w-8/12">
    <div class="w-full">
        <form action="{{ route('customer.account.update') }}" method="post" class="space-y-4">
            @method('PUT')
            @csrf
            <div>
                <label for="username" class="block">Username</label>
                <input required id="username" type="text" name="username" class="w-full p-2 focus:outline-none border border-color-3 rounded" value="{{auth()->user()->username}}" disabled>
            </div>
            <div>
                <label for="email" class="block">Email</label>
                <input required id="email" type="email" name="email" class="w-full p-2 focus:outline-none border border-color-3 rounded" value="{{auth()->user()->email}}">
            </div>
            <div>
                <label for="fullname" class="block">Nama Lengkap</label>
                <input required id="fullname" type="text" name="fullname" class="w-full p-2 focus:outline-none border border-color-3 rounded" value="{{auth()->user()->fullname}}">
            </div>
            <div>
                <label for="phonenumber" class="block">Nomor Telepon</label>
                <input required id="phonenumber" type="number" name="phonenumber" class="w-full p-2 focus:outline-none border border-color-3 rounded" value="{{auth()->user()->phonenumber}}">
            </div>
            <button type="button" onclick="$('#modal-save').show()" class="flex ml-auto py-2 px-4 uppercase font-medium border bg-color-4 border-color-4 text-white rounded">
                Simpan
            </button>
            <div id="modal-save" class="hidden absolute top-52 left-1/2 -translate-x-1/2 w-min space-y-4 p-4 bg-white rounded border border-color-3">
                <div>
                    <label for="password" class="block">Password</label>
                    <input required id="password" type="password" name="password" class="p-2 w-full focus:outline-none border border-color-3 rounded" placeholder="Masukkan password Anda saat ini...">
                </div>
                <div class="flex gap-2">
                    <button type="button" class="flex ml-auto py-2 px-4 uppercase font-medium border border-color-4 rounded" onclick="$('#modal-save').hide()">
                        Batal
                    </button>
                    <button class="flex ml-auto py-2 px-4 uppercase font-medium border bg-color-4 border-color-4 text-white rounded">
                        Konfirmasi
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="w-full">
        <form action="{{ route('customer.account.change-password') }}" method="post" class="space-y-4">
            @method('PUT')
            @csrf
            <div>
                <label for="old-password" class="block">Password Lama</label>
                <input required id="old-password" type="password" name="old_password" class="w-full p-2 focus:outline-none border border-color-3 rounded" placeholder="Masukkan password lama Anda">
            </div>
            <div>
                <label for="new-password" class="block">Password Baru</label>
                <input required id="new-password" type="password" name="new_password" class="w-full p-2 focus:outline-none border border-color-3 rounded" placeholder="Masukkan password baru Anda">
            </div>
            <div>
                <label for="new-password-confirm" class="block">Konfirmasi Password Baru</label>
                <input required id="new-password-confirm" type="password" name="new_password_confirmation" class="w-full p-2 focus:outline-none border border-color-3 rounded" placeholder="Konfirmasi password baru Anda">
            </div>
            <button type="submit" class="flex ml-auto py-2 px-4 uppercase font-medium border bg-color-4 border-color-4 text-white rounded">
                Simpan Password
            </button>
        </form>
    </div>
</div>

@endsection