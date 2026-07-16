<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - Sistem Monitoring KIP-K</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0f141e; /* Darker background */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f8fafc;
            padding: 20px;
        }
        .login-card {
            background-color: #1e2532; /* Lighter card background */
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 420px;
            padding: 40px;
        }
        .logo-container {
            width: 60px;
            height: 60px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto;
        }
        .form-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }
        .form-control-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #1e293b;
            border-radius: 8px;
            padding: 12px 16px;
            transition: all 0.3s;
            font-weight: 500;
        }
        .form-control-custom::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }
        .form-control-custom:focus {
            background: #ffffff;
            border-color: #3b82f6;
            color: #1e293b;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
            outline: none;
        }
        .btn-custom {
            background: #3b82f6;
            border: none;
            color: white;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-custom:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            color: white;
        }
        .link-custom {
            color: #60a5fa;
            text-decoration: none;
            transition: color 0.2s;
        }
        .link-custom:hover {
            color: #93c5fd;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="logo-container">
                <i class="bi bi-shield-lock-fill text-white fs-3"></i>
            </div>
            <h4 class="fw-bold mb-1 text-white">Lupa Kata Sandi</h4>
            <p class="text-secondary small mb-0" style="color: #94a3b8 !important;">Masukkan alamat email Anda yang terdaftar.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success bg-success bg-opacity-20 border-success border-opacity-30 text-success rounded-3 mb-3 py-2 px-3 small" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> {{ session('success') }}
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

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="email" class="form-label">ALAMAT EMAIL</label>
                <input type="email" name="email" id="email" class="form-control form-control-custom" placeholder="contoh@mail.com" value="{{ old('email') }}" required>
            </div>
            
            <button type="submit" class="btn btn-custom w-100 mb-4">Kirim Tautan <i class="bi bi-send ms-1"></i></button>
            
            <div class="text-center mb-2">
                <a href="{{ route('login') }}" class="link-custom small"><i class="bi bi-arrow-left me-1"></i> Kembali ke Masuk</a>
            </div>
        </form>
    </div>
</body>
</html>
