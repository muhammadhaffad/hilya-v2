@extends('v2.layouts.customer.app', ['title' => 'Buku Alamat | Hillia Collection'])
@section('content')
<div class="flex font-bold text-xl gap-1 items-center uppercase">
    <span class="material-icons !text-4xl">
        dashboard
    </span>
    Dashboard
</div>
@php $icons = ['shopping_bag', 'timer', 'pending', 'task_alt', 'local_shipping']; $i = 0; @endphp
<div class="grid-cols-3 grid gap-4">
    @foreach ($datas as $key => $value)
    <div class="p-3 w-full space-y-4 border border-color-3 rounded-md">
        <div class="flex items-center gap-2">
            <span class="material-icons text-5xl">
                {{$icons[$i++]}}
            </span>
            <h3 class="font-semibold text-xl">
                {{$key}}
            </h3>
        </div>
        <hr>
        <h1 class="block text-right font-semibold text-5xl">
            {{$value}}
        </h1>
    </div>
    @endforeach
</div>
@endsection