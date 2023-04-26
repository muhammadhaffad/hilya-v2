<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Masuk | Hilya Collection</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
    @vite('resources/css/app.css')
</head>

<body class="font-inter text-color-4">
    <main class="w-full max-w-[1228px] mx-auto overflow-hidden h-fit">
        <section class="mx-auto mt-20 w-96 space-y-8">
            <div>
                <img src="{{asset('assets/images/logo.png')}}" alt="" class="mx-auto w-40">
            </div>
            <form action="{{route('login.auth')}}" method="post">
                @csrf
                <div class="space-y-4 p-5 shadow-lg rounded">
                    @error('auth')
                    <div class="p-2 bg-red-500 text-white rounded">
                        <span>{{$message}}</span>
                    </div>
                    @enderror
                    <div>
                        <label for="username">Username</label>
                        <input id="username" type="text" name="username" class="w-full p-2 focus:outline-none border border-color-3 rounded">
                    </div>
                    <div>
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" class="w-full p-2 focus:outline-none border border-color-3 rounded">
                    </div>
                    <div>
                        <button class="px-5 w-full h-[42px] bg-color-4 text-white font-semibold uppercase border rounded">Masuk</button>
                    </div>
                    <p>
                        Belum punya akun?, silahkan <a href="{{route('register')}}" class="text-blue-500 underline">daftar di sini</a>
                    </p>
                    <p class="text-center">
                        <a href="{{route('home')}}" class="text-blue-500 underline">← Kembali ke halaman awal</a>
                    </p>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
