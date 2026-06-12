<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Daftar Akun — GROW-MOM</title>
@vite(['resources/css/app.css','resources/js/app.js'])
<style>
body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#f093fb 0%,#f5576c 50%,#4facfe 100%);font-family:'Segoe UI',sans-serif;padding:20px;box-sizing:border-box;}
.auth-card{background:rgba(255,255,255,0.97);border-radius:24px;padding:40px;width:100%;max-width:440px;box-shadow:0 25px 60px rgba(0,0,0,0.2);}
.auth-logo{text-align:center;margin-bottom:24px;}.auth-logo .icon{font-size:50px;display:block;margin-bottom:8px;}
.auth-logo h1{font-size:24px;font-weight:800;color:#4c1d95;margin:0;}
.auth-logo p{font-size:13px;color:#888;margin:4px 0 0;}
.form-group{margin-bottom:16px;}
.form-group label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;}
.form-group input{width:100%;padding:12px 16px;border:1.5px solid #e5e7eb;border-radius:12px;font-size:14px;outline:none;box-sizing:border-box;transition:border-color .2s;}
.form-group input:focus{border-color:#7c3aed;}
.form-group .error{font-size:12px;color:#ef4444;margin-top:4px;}
.btn-auth{width:100%;padding:14px;background:linear-gradient(135deg,#f5576c,#f093fb);color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;transition:transform .15s,box-shadow .15s;}
.btn-auth:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(245,87,108,0.4);}
.auth-footer{text-align:center;margin-top:18px;font-size:13px;color:#6b7280;}
.auth-footer a{color:#7c3aed;font-weight:600;text-decoration:none;}
.info-box{background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px 14px;font-size:12.5px;color:#1d4ed8;margin-bottom:18px;}

/* Loading Overlay */
.loading-overlay{display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;flex-direction:column;gap:20px;}
.loading-overlay.active{display:flex;}
.loading-backdrop{position:absolute;inset:0;background:rgba(255,255,255,0.25);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);}
.loading-spinner-wrap{position:relative;z-index:1;display:flex;flex-direction:column;align-items:center;gap:16px;}
.loading-spinner{width:64px;height:64px;border:5px solid rgba(124,58,237,0.2);border-top-color:#7c3aed;border-radius:50%;animation:spin 0.85s linear infinite;}
@keyframes spin{to{transform:rotate(360deg);}}
.loading-text{font-size:15px;font-weight:700;color:#4c1d95;letter-spacing:.3px;text-shadow:0 1px 3px rgba(255,255,255,0.8);}
.btn-auth:disabled{opacity:0.7;cursor:not-allowed;transform:none!important;box-shadow:none!important;}
</style>
</head>
<body>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-backdrop"></div>
    <div class="loading-spinner-wrap">
        <div class="loading-spinner"></div>
        <div class="loading-text">Membuat akun, harap tunggu…</div>
    </div>
</div>
<div class="auth-card">
    <div class="auth-logo">
        <span class="icon"><i data-feather="user"></i><i data-feather="heart"></i></span>
        <h1>Daftar Akun Baru</h1>
        <p>GROW-MOM — Monitoring Tumbuh Kembang Anak</p>
    </div>
    <div class="info-box">
        <i data-feather="info"></i> <strong>Gratis!</strong> Daftar sekarang dan pantau tumbuh kembang buah hati Anda dengan mudah.
    </div>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-group">
            <label><i data-feather="user"></i> Nama Lengkap Anda</label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Siti Rahayu" required autofocus>
            @error('name')<div class="error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label><i data-feather="smartphone"></i> No. WhatsApp</label>
            <input type="tel" name="nomer" value="{{ old('nomer') }}" placeholder="08xxxxxxxxxx" required>
            @error('nomer')<div class="error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label><i data-feather="lock"></i> Password <span style="color:#9ca3af;font-weight:400;">(min. 6 karakter)</span></label>
            <input type="password" name="password" placeholder="Buat password yang mudah diingat" required>
            @error('password')<div class="error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label><i data-feather="lock"></i> Ulangi Password</label>
            <input type="password" name="password_confirmation" placeholder="Ketik ulang password Anda" required>
        </div>
        <button type="submit" class="btn-auth" id="submitBtn"><i data-feather="star"></i> Buat Akun Sekarang</button>
    </form>
    <div class="auth-footer">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini <i data-feather="arrow-right"></i></a>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form');
        const btn  = document.getElementById('submitBtn');
        const overlay = document.getElementById('loadingOverlay');

        form.addEventListener('submit', function (e) {
            // Cek validasi HTML5 dulu — jika tidak valid, jangan tampilkan overlay
            if (!form.checkValidity()) return;

            // Tampilkan overlay & nonaktifkan tombol
            overlay.classList.add('active');
            btn.disabled = true;
        });
    });
</script>
</body></html>