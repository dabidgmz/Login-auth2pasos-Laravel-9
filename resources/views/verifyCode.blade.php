<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
          integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('login.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #071014;
            background-image: linear-gradient(160deg, #071014 0%, #0db8de 100%);
        }

        .verification-page {
            width: 400px;
            padding: 8% 0 0;
            margin: auto;
        }

        .form {
            position: relative;
            z-index: 1;
            background: #1A2226;
            max-width: 400px;
            margin: 0 auto 100px;
            padding: 45px;
            text-align: center;
            border-radius: 15px;
            box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
        }

        .form h2 {
            color: #0DB8DE;
            font-size: 30px;
            letter-spacing: 2px;
            margin-top: 10px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .code-input {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .code-input input {
            width: 50px;
            height: 50px;
            font-size: 20px;
            text-align: center;
            border: 2px solid #0DB8DE;
            border-radius: 5px;
            background: #f2f2f2;
            color: #333;
        }

        .code-input input:focus {
            outline: none;
            border-color: #0e2941;
        }

        .form button {
            font-family: "Poppins", sans-serif;
            text-transform: uppercase;
            outline: 0;
            background: #0DB8DE;
            width: 100%;
            border: 0;
            padding: 15px;
            color: #FFFFFF;
            border-radius: 7px;
            font-size: 14px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .form button:disabled {
            background: #666;
            cursor: not-allowed;
        }

        .form button:hover:not(:disabled) {
            background: #0e2941;
        }
    </style>
</head>
<body>
<div class="verification-page">
    <div class="form">
        <h2><i class="fas fa-key"></i> Verification Code</h2>
        <form id="verification-form" method="POST" action="{{ route('verifyLoginCode') }}">
            @csrf
            <div class="code-input">
                <input type="text" maxlength="1" required />
                <input type="text" maxlength="1" required />
                <input type="text" maxlength="1" required />
                <input type="text" maxlength="1" required />
                <input type="text" maxlength="1" required />
            </div>
            <div>
              {!! NoCaptcha::renderJs() !!}
              {!! NoCaptcha::display() !!}
            </div>
            <input type="hidden" name="verify" id="verify">
            <button type="submit" disabled>Verify</button>
        </form>
    </div>
</div>

@if (session('success'))
    <script>
        Swal.fire({
            title: "¡Bienvenido!",
            text: "{{ session('success') }}",
            icon: "success",
            draggable: true,
            timer: 3000,
            showConfirmButton: false
        });
    </script>
  @endif

  @if (session('error'))
    <script>
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "{{ session('error') }}",
        });
    </script>
  @endif

  @if ($errors->any())
    <script>
        var errorMessages = "{{ implode(', ', $errors->all()) }}";
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: errorMessages,
        });
    </script>
@endif

<script>
    const inputs = document.querySelectorAll('.code-input input');
    const button = document.querySelector('button[type="submit"]');
    const hiddenInput = document.getElementById('verify');

    inputs.forEach((input, index) => {
        input.addEventListener('input', () => {
            if (input.value) {
                if (inputs[index + 1]) {
                    inputs[index + 1].focus();
                }
            } else {
                if (inputs[index - 1]) {
                    inputs[index - 1].focus();
                }
            }

            // Check if all inputs have values and enable the submit button
            const code = Array.from(inputs).map(input => input.value).join('');
            hiddenInput.value = code;

            button.disabled = code.length !== 5;
        });
    });
</script>
</body>
</html>
