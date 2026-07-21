<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Monitoring KIP-K</title>
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
            max-width: 520px;
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
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(to right, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.3) 50%, rgba(255, 255, 255, 0) 100%);
            transform: skewX(-25deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% {
                left: -100%;
            }

            20% {
                left: 200%;
            }

            100% {
                left: 200%;
            }
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
            font-size: 0.85rem;
            text-transform: capitalize;
            color: #cbd5e1;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-control-custom,
        .form-select-custom {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #f8fafc;
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s ease;
            font-weight: 400;
        }

        .form-control-custom:focus,
        .form-select-custom:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
            color: #ffffff;
        }

        .form-control-custom::placeholder {
            color: #64748b;
        }

        .form-control-with-icon {
            padding-left: 44px;
        }

        .input-icon-left {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 1.1rem;
            z-index: 4;
            transition: color 0.3s;
        }

        .position-relative:focus-within .input-icon-left {
            color: #3b82f6;
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
            font-size: 0.9rem;
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

        .password-toggle {
            cursor: pointer;
            color: #64748b;
            z-index: 5;
        }

        .password-toggle:hover {
            color: #94a3b8;
        }

        select option {
            background-color: #1e2532;
            color: white;
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #1e2532 inset !important;
            -webkit-text-fill-color: #f8fafc !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        input[type="file"].form-control-custom::file-selector-button {
            padding: 12px 16px;
            margin: -12px -16px;
            margin-inline-end: 16px;
            background-color: rgba(255, 255, 255, 0.1);
            color: #f8fafc;
            border: none;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px 0 0 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        input[type="file"].form-control-custom:hover::file-selector-button {
            background-color: rgba(255, 255, 255, 0.15);
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
            <p class="text-secondary small mb-0" style="color: #94a3b8 !important;">Sistem Registrasi Mahasiswa</p>
        </div>

        @if(session('success'))
            <div class="alert rounded-3 mb-3 py-2 px-3 small d-flex align-items-center"
                style="background-color: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25);"
                role="alert">
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

        <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap</label>
                <div class="position-relative">
                    <i class="bi bi-person input-icon-left"></i>
                    <input type="text" name="name" id="name"
                        class="form-control form-control-custom form-control-with-icon"
                        placeholder="Masukkan nama sesuai KTP" value="{{ old('name') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Alamat Email</label>
                <div class="position-relative">
                    <i class="bi bi-envelope input-icon-left"></i>
                    <input type="email" name="email" id="email"
                        class="form-control form-control-custom form-control-with-icon" placeholder="contoh@mail.com"
                        value="{{ old('email') }}" required>
                </div>
            </div>

            <!-- Mahasiswa KIP-K Fields -->
            <div id="mahasiswaFields">
                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="nim" class="form-label">NIM (Nomor Induk Mahasiswa)</label>
                        <div class="position-relative">
                            <i class="bi bi-person-badge input-icon-left"></i>
                            <input type="text" name="nim" id="nim"
                                class="form-control form-control-custom form-control-with-icon"
                                placeholder="Nomor Induk" value="{{ old('nim') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="nomor_kip" class="form-label">Nomor KIP-K</label>
                        <div class="position-relative">
                            <i class="bi bi-card-heading input-icon-left"></i>
                            <input type="text" name="nomor_kip" id="nomor_kip"
                                class="form-control form-control-custom form-control-with-icon"
                                placeholder="Masukkan Nomor KIP-K" value="{{ old('nomor_kip') }}" required>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="jurusan" class="form-label">Jurusan</label>
                        <select name="jurusan" id="jurusan" class="form-select form-select-custom" required>
                            <option value="">Pilih Jurusan</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan->nama }}" {{ old('jurusan') == $jurusan->nama ? 'selected' : '' }}>{{ $jurusan->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="prodi" class="form-label">Program Studi</label>
                        <select name="prodi" id="prodi" class="form-select form-select-custom" required>
                            <option value="">Pilih Prodi</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="bukti_kip" class="form-label">Upload Bukti KIP <span
                            class="text-danger">*</span></label>
                    <input type="file" name="bukti_kip" id="bukti_kip" class="form-control form-control-custom"
                        accept="image/jpeg, image/png, image/jpg" required>
                    <small class="text-secondary mt-1 d-block"
                        style="color: #64748b !important; font-size: 0.75rem;">Format JPG/PNG maks 2MB.</small>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Kata Sandi</label>
                <div class="position-relative">
                    <i class="bi bi-lock input-icon-left"></i>
                    <input type="password" name="password" id="password"
                        class="form-control form-control-custom form-control-with-icon pe-5" placeholder="••••••••"
                        required>
                    <i class="bi bi-eye position-absolute top-50 end-0 translate-middle-y me-3 password-toggle"
                        id="togglePassword"></i>
                </div>
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                <div class="position-relative">
                    <i class="bi bi-arrow-counterclockwise input-icon-left"></i>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="form-control form-control-custom form-control-with-icon pe-5" placeholder="••••••••"
                        required>
                    <i class="bi bi-eye position-absolute top-50 end-0 translate-middle-y me-3 password-toggle"
                        id="togglePasswordConf"></i>
                </div>
            </div>

            <button type="submit" class="btn btn-custom w-100 mb-4">Daftar Sekarang <i
                    class="bi bi-person-plus ms-1"></i></button>

        </form>

        <div class="text-center mt-4">
            <p class="text-secondary small mb-2" style="color: #94a3b8 !important;">Sudah memiliki akun?</p>
            <a href="{{ route('login') }}" class="link-custom"><i class="bi bi-box-arrow-in-left me-1"></i> Kembali ke
                Masuk</a>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function (e) {
            const password = document.getElementById('password');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        document.getElementById('togglePasswordConf').addEventListener('click', function (e) {
            const passwordConf = document.getElementById('password_confirmation');
            const type = passwordConf.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConf.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        // AJAX for Prodi
        document.getElementById('jurusan').addEventListener('change', function() {
            const jurusan = this.value;
            const prodiSelect = document.getElementById('prodi');
            
            // Clear current prodi
            prodiSelect.innerHTML = '<option value="">Memuat...</option>';
            
            if(jurusan) {
                fetch(`/api/prodi?jurusan=${encodeURIComponent(jurusan)}`)
                    .then(response => response.json())
                    .then(data => {
                        prodiSelect.innerHTML = '<option value="">Pilih Prodi</option>';
                        data.forEach(prodi => {
                            const option = document.createElement('option');
                            option.value = prodi;
                            option.textContent = prodi;
                            prodiSelect.appendChild(option);
                        });
                        
                        // Retain old value if exists
                        const oldProdi = '{{ old('prodi') }}';
                        if(oldProdi) {
                            prodiSelect.value = oldProdi;
                        }
                    });
            } else {
                prodiSelect.innerHTML = '<option value="">Pilih Prodi</option>';
            }
        });

        // Trigger change on load if old jurusan exists
        if(document.getElementById('jurusan').value) {
            document.getElementById('jurusan').dispatchEvent(new Event('change'));
        }

    </script>
</body>

</html>