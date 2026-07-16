<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Monitoring KIP-K</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f141e 0%, #1a2333 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f8fafc;
            padding: 40px 20px;
        }
        .login-card {
            background-color: rgba(30, 37, 50, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 40px rgba(59, 130, 246, 0.1);
            width: 100%;
            max-width: 440px;
            padding: 48px;
        }
        .logo-container {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto;
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.5);
            position: relative;
            overflow: hidden;
        }
        .logo-container::after {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 50%; height: 100%;
            background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0) 100%);
            transform: skewX(-25deg);
            animation: shine 3s infinite;
        }
        @keyframes shine {
            0% { left: -100%; }
            20% { left: 200%; }
            100% { left: 200%; }
        }
        .logo-icon {
            font-size: 2rem;
            color: white;
            z-index: 1;
        }
        .app-title {
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, #ffffff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }
        .form-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .form-control-custom {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #f8fafc;
            border-radius: 12px;
            padding: 14px 16px;
            transition: all 0.3s ease;
            font-weight: 400;
            font-size: 1rem;
        }
        .form-control-custom:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
            color: #ffffff;
        }
        .form-control-custom::placeholder {
            color: #475569;
        }
        .btn-custom {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            color: white;
            font-weight: 600;
            padding: 14px;
            border-radius: 12px;
            box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.39);
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.95rem;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            color: white;
        }
        .link-custom {
            color: #60a5fa;
            text-decoration: none;
            transition: color 0.2s;
            font-weight: 500;
        }
        .link-custom:hover {
            color: #93c5fd;
        }
        .text-divider {
            display: flex;
            align-items: center;
            color: #64748b;
            font-size: 0.75rem;
            margin: 2rem 0 1.5rem 0;
            font-weight: 600;
        }
        .text-divider::before, .text-divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .text-divider:not(:empty)::before {
            margin-right: 1rem;
        }
        .text-divider:not(:empty)::after {
            margin-left: 1rem;
        }
        .demo-btn {
            border: none;
            border-radius: 12px;
            padding: 8px 16px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: white;
            margin: 0 4px;
            transition: transform 0.2s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }
        .demo-btn:hover {
            transform: translateY(-2px) scale(1.05);
        }
        .demo-admin { background-color: #f43f5e; }
        .demo-pengurus { background-color: #f59e0b; }
        .demo-mahasiswa { background-color: #10b981; }
        .demo-wadir { background-color: #0ea5e9; }
        
        .form-check-input {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }
        .form-check-input:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }
        .password-toggle {
            cursor: pointer;
            color: #64748b;
            z-index: 5;
        }
        .password-toggle:hover {
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4 pb-2">
            <div class="logo-container">
                <i class="bi bi-mortarboard-fill logo-icon"></i>
            </div>
            <h3 class="app-title">KIP-K Monitor</h3>
            <p class="text-secondary small mb-0" style="color: #94a3b8 !important;">Sistem Manajemen Beasiswa & Ormawa</p>
        </div>

        @if(session('success'))
            <div class="alert rounded-3 mb-3 py-2 px-3 small d-flex align-items-center" style="background-color: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25);" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger bg-danger border-danger text-white rounded-3 mb-3 py-2 px-3 small" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">ALAMAT EMAIL</label>
                <input type="email" name="email" id="email" class="form-control form-control-custom" placeholder="pengurus@mail.com" value="{{ old('email') }}" required>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">KATA SANDI</label>
                <div class="position-relative">
                    <input type="password" name="password" id="password" class="form-control form-control-custom pe-5" placeholder="••••••••" required>
                    <i class="bi bi-eye position-absolute top-50 end-0 translate-middle-y me-3 password-toggle" id="togglePassword"></i>
                </div>
            </div>
            
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label class="form-check-label text-secondary small" for="remember" style="color: #94a3b8 !important;">Ingat Saya</label>
                </div>
                <a href="{{ route('password.request') }}" class="link-custom small">Lupa sandi?</a>
            </div>
            
            <button type="submit" class="btn btn-custom w-100 mb-4">Masuk <i class="bi bi-box-arrow-in-right ms-1"></i></button>
            
            <div class="text-center mb-2">
                <span class="text-secondary small" style="color: #94a3b8 !important;">Belum memiliki akun?</span> <a href="{{ route('register') }}" class="link-custom small">Daftar Sekarang</a>
            </div>
        </form>
    </div>


    <script>
        document.getElementById('togglePassword').addEventListener('click', function (e) {
            const password = document.getElementById('password');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>
