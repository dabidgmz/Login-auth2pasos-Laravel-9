<!doctype html>
<html lang="en">
<head>
  <title>Register</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
  <link href="{{ asset('register.css') }}" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script src="https://js.hcaptcha.com/1/api.js?hl=en" async defer></script>
</head>
<body class="d-flex align-items-center">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8 login-box">
        <div class="col-lg-12 login-title">
          Sign Up
        </div>
        <div class="col-lg-12 login-form">
          <form method="POST" action="{{ route('register.post') }}">
            @csrf
            <div class="form-group">
              <label class="form-control-label">Name</label>
              <input type="text" name="name" class="form-control" placeholder="Enter your name" value="{{ old('name') }}">
            </div>
            <div class="form-group">
              <label class="form-control-label">Email</label>
              <input type="text" name="email" class="form-control" placeholder="Enter your email" value="{{ old('email') }}">
            </div>
            <div class="form-group">
              <label class="form-control-label">Phone number</label>
              <input type="text" name="phone" class="form-control" placeholder="Enter your phone number" value="{{ old('phone') }}">
            </div>
            <div class="form-group">
              <label class="form-control-label">Password</label>
              <div class="input-group">
                <input id="password" type="password" name="password" class="form-control" placeholder="Enter your password">
                <button type="button" id="toggle-password" class="btn btn-outline-secondary">
                  <i id="password-icon" class="fas fa-eye"></i>
                </button>
              </div>
              <small id="password-hint" class="password-hint text-muted">
                Your password should include:
                <ul>
                  <li>At least 8 characters</li>
                  <li>One uppercase letter</li>
                  <li>One lowercase letter</li>
                  <li>One number</li>
                  <li>One special character (!, @, #, $, etc.)</li>
                </ul>
              </small>
              <div class="progress mt-2">
                <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
              </div>
            </div>
            <!-- reCAPTCHA -->
            <div class="h-captcha bg-none" data-sitekey="{{ env('HCAPTCHA_SITEKEY') }}" data-theme="dark"></div>
            <div class="col-12 login-btm login-button d-flex justify-content-center">
              <button type="submit" id="register-btn" class="btn btn-outline-primary" disabled>Register</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
    integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous"></script>

  <!-- Sweetalert for session success/error -->
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
    const passwordInput = document.getElementById("password");
    const togglePasswordButton = document.getElementById("toggle-password");
    const passwordIcon = document.getElementById("password-icon");
    const passwordHint = document.getElementById("password-hint");
    const strengthBar = document.getElementById("password-strength-bar");
    const registerButton = document.getElementById("register-btn");

    togglePasswordButton.addEventListener("click", () => {
      const type = passwordInput.type === "password" ? "text" : "password";
      passwordInput.type = type;
      passwordIcon.className = type === "password" ? "fas fa-eye" : "fas fa-eye-slash";
    });

    passwordInput.addEventListener("input", () => {
      const password = passwordInput.value;
      const strength = calculateStrength(password);

      passwordHint.style.display = strength.isComplete ? "none" : "block";

      strengthBar.style.width = `${strength.percent}%`;
      strengthBar.className = `progress-bar ${strength.colorClass}`;

      // Enable/disable the register button based on password strength
      registerButton.disabled = !strength.isComplete;
    });

    function calculateStrength(password) {
      let score = 0;

      if (password.length >= 8) score++;
      if (/[A-Z]/.test(password)) score++;
      if (/[a-z]/.test(password)) score++;
      if (/\d/.test(password)) score++;
      if (/[\W_]/.test(password)) score++;

      const percent = (score / 5) * 100;
      const isComplete = score === 5;

      const strength = {
        5: { colorClass: "bg-success", isComplete },
        3: { colorClass: "bg-warning", isComplete: false },
        0: { colorClass: "bg-danger", isComplete: false },
      };

      return { ...strength[Math.min(score, 5)], percent };
    }
  </script>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="/js/main.js"></script>
</body>
</html>
