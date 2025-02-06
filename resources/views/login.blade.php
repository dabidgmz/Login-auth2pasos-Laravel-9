<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
          integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('login.css') }}">
    <script src="https://js.hcaptcha.com/1/api.js?hl=en" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="display:flex; align-items:center; justify-content:center;">
<div class="login-page">
    <div class="form">
        <form class="login-form" method="POST" action="{{ route('login.post') }}">
            @csrf
            <h2><i class="fas fa-lock"></i> Login</h2>
            <input type="text" name="email" placeholder="Email" value="{{ old('email') }}"  />
            <input type="password" name="password" placeholder="Password"  />
            <div class="h-captcha bg-none" data-sitekey="{{ env('HCAPTCHA_SITEKEY') }}" data-theme="dark"></div>
            <button type="submit">Login</button>
            <p class="message">Not registered? <a href="{{ url('/register') }}">Create an account</a></p>
        </form>
    </div>
</div>

@if (session('success'))
    <script>
        Swal.fire({
            title: "Welcome!",
            text: "{{ session('success') }}",
            icon: "success",
            draggable: true,
            timer: 3000,
            showConfirmButton: false
        });
    </script>
@endif

@if(session('error_code') == \App\Constants\Errors\V1\ErrorCodes::E1001)
    <script>
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "The provided credentials are incorrect.",
        });
    </script>
@endif

@if ($errors->any())
    <script>
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "{{ implode(', ', $errors->all()) }}", 
        });
    </script>
@endif

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/js/main.js"></script>
</body>
</html>
