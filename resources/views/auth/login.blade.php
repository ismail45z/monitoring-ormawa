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
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f8fafc;
            padding: 20px;
        }
        .login-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            padding: 40px;
        }
        .form-control-custom {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s;
        }
        .form-control-custom:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: #3b82f6;
            color: #fff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }
        .btn-custom {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
            color: white;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
            opacity: 0.95;
        }
        .demo-badge {
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.75rem;
            margin-right: 5px;
            margin-bottom: 5px;
            display: inline-block;
        }
        .demo-badge:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="display-5 text-primary mb-2">
                <i class="bi bi-mortarboard-fill text-white"></i>
            </div>
            <h3 class="fw-bold mb-1">KIP-K Monitor</h3>
            <p class="text-muted">Monitoring Keaktifan Mahasiswa</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success bg-success bg-opacity-20 border-success border-opacity-30 text-success rounded-3 mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger bg-danger bg-opacity-20 border-danger border-opacity-30 text-danger rounded-3 mb-3" role="alert">
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
                <label for="email" class="form-label text-muted small">Alamat Email</label>
                <input type="email" name="email" id="email" class="form-control form-control-custom" placeholder="contoh@mail.com" value="{{ old('email') }}" required>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label text-muted small">Kata Sandi</label>
                <input type="password" name="password" id="password" class="form-control form-control-custom" placeholder="••••••••" required>
            </div>
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label class="form-check-label small text-muted" for="remember">Ingat Saya</label>
                </div>
            </div>
            <button type="submit" class="btn btn-custom w-100 mb-3">Masuk <i class="bi bi-box-arrow-in-right ms-1"></i></button>
        </form>

        <!-- Demo accounts quick access -->
        <div class="mt-4 pt-3 border-top border-secondary border-opacity-30">
            <small class="text-muted d-block mb-2 text-center">Akun Testing (Klik untuk Mengisi):</small>
            <div class="d-flex flex-wrap justify-content-center">
                <span class="badge bg-danger demo-badge" onclick="fillCred('admin@mail.com')">Admin</span>
                <span class="badge bg-warning text-dark demo-badge" onclick="fillCred('pengurus@mail.com')">Pengurus</span>
                <span class="badge bg-success demo-badge" onclick="fillCred('mahasiswa@mail.com')">Mahasiswa</span>
                <span class="badge bg-info text-dark demo-badge" onclick="fillCred('wadir@mail.com')">Wadir</span>
            </div>
        </div>
    </div>

    <script>
        function fillCred(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    </script>
</body>
</html>
