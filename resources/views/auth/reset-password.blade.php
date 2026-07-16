<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - KIP-K</title>
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Style -->
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
            background: #fff;
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            padding: 30px;
            text-align: center;
            color: #fff;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
        }
        .form-control:focus {
            background-color: #fff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
    </style>
</head>
<body>

    <div class="container d-flex justify-content-center">
        <div class="login-card">
            <div class="login-header">
                <i class="bi bi-shield-lock-fill display-4 mb-2"></i>
                <h3 class="fw-bold mb-1">Reset Password</h3>
                <p class="mb-0 text-white-50 small">Buat kata sandi baru untuk akun Anda.</p>
            </div>
            
            <div class="login-body">
                @if(session('error'))
                    <div class="alert alert-danger mb-4 small rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                    </div>
                @endif
                
                @if ($errors->any())
                    <div class="alert alert-danger mb-4 small rounded-3">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Email Anda</label>
                        <input type="email" class="form-control" value="{{ $email }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"></i></span>
                            <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 ps-0" placeholder="Minimal 6 karakter" required>
                            <span class="input-group-text bg-white" style="cursor: pointer;" onclick="togglePasswordVisibility('password', this)"><i class="bi bi-eye text-muted"></i></span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-semibold">Konfirmasi Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-key-fill text-muted"></i></span>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0 border-end-0 ps-0" placeholder="Ketik ulang password baru" required>
                            <span class="input-group-text bg-white" style="cursor: pointer;" onclick="togglePasswordVisibility('password_confirmation', this)"><i class="bi bi-eye text-muted"></i></span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login mb-3">Reset Password</button>

                    <div class="text-center mt-2">
                        <a href="{{ route('login') }}" class="text-decoration-none small text-primary fw-medium"><i class="bi bi-arrow-left me-1"></i> Kembali ke Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function togglePasswordVisibility(inputId, iconSpan) {
            const input = document.getElementById(inputId);
            const icon = iconSpan.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
