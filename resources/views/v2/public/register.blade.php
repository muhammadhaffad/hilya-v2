<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register | Hilya Collection</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
    @vite('resources/css/app.css')
</head>

<body class="font-inter text-color-4">
    <main class="w-full max-w-[1228px] mx-auto overflow-hidden h-fit">
        <section class="mx-auto my-20 max-w-[389px] space-y-8">
            <div>
                <img src="{{asset('assets/images/logo.png')}}" alt="" class="mx-auto w-40">
            </div>
            <form action="{{route('register.store')}}" method="post">
                @csrf
                <div class="space-y-4 p-5 shadow-lg rounded">
                    <div>
                        <h1 class="uppercase text-xl font-bold">Daftar Akun</h1>
                    </div>
                    <div>
                        <label for="fullname">Nama Lengkap</label>
                        <input id="fullname" type="text" name="fullname" class="w-full p-2 focus:outline-none border border-color-3 rounded">
                        <span class="text-red-500 text-sm">{{implode(', ', $errors->get('fullname'))}}</span>
                    </div>
                    <div>
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" class="w-full p-2 focus:outline-none border border-color-3 rounded">
                        <span class="text-red-500 text-sm">{{implode(', ', $errors->get('email'))}}</span>
                    </div>
                    <div>
                        <label for="phonenumber">Nomor Telepon</label>
                        <input id="phonenumber" type="number" name="phonenumber" class="w-full p-2 focus:outline-none border border-color-3 rounded">
                        <span class="text-red-500 text-sm">{{implode(', ', $errors->get('phonenumber'))}}</span>
                    </div>
                    <div>
                        <label for="username">Username</label>
                        <input id="username" type="text" name="username" onchange="check()" class="w-full p-2 focus:outline-none border border-color-3 rounded">
                        <div id="check">
                        </div>
                        <span class="text-red-500 text-sm">{{implode(', ', $errors->get('username'))}}</span>
                    </div>
                    <div>
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" class="w-full p-2 focus:outline-none border border-color-3 rounded">
                    </div>
                    <div>
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="w-full p-2 focus:outline-none border border-color-3 rounded">
                        <span class="text-red-500 text-sm">{{implode(', ', $errors->get('password'))}}</span>
                    </div>
                    <div>
                        <button class="px-5 w-full h-[42px] bg-color-4 text-white font-semibold uppercase border rounded">Daftar</button>
                    </div>
                    <p>
                        Sudah punya akun?, silahkan <a href="{{route('login')}}" class="text-blue-500 underline">masuk di sini</a>
                    </p>
                    <p class="text-center">
                        <a href="{{route('home')}}" class="text-blue-500 underline">← Kembali ke halaman awal</a>
                    </p>
                </div>
            </form>
            <input hidden type="text" name="url_check" value="{{route('username.check')}}">
        </section>
    </main>
    <script>
    $(function() {
        $(".overflow-x-scroll").mousewheel(function(event, delta) {
            this.scrollLeft -= (delta * 50);
            event.preventDefault();
        });
    });
    async function check() {
        let username = $('input[name="username"]').val();
        let url = $('input[name="url_check"]').val();
        $('#check').children().remove();
        try {
            const response = await fetch(url+'?username='+username);
            if (response.status == 200) {
                const result = await response.json();
                let wrapper = $('#check');
                if (result.message == 'Username tersedia') {
                    wrapper.append(`<span class="text-green-600 text-sm">${result.message}</span>`);           
                } else {
                    wrapper.append(`<span class="text-red-500 text-sm">${result.message}</span>`);           
                }
            }
        } catch (error) {
            console.log(error);
        }
    }

    </script>
</body>
</html>
