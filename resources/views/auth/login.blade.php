<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — GROW-MOM</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#667eea 0%,#764ba2 50%,#f093fb 100%);font-family:'Segoe UI',sans-serif;}
        .auth-card{background:rgba(255,255,255,0.97);border-radius:24px;padding:40px;width:100%;max-width:420px;box-shadow:0 25px 60px rgba(0,0,0,0.2);}
        .auth-logo{text-align:center;margin-bottom:24px;}
        .auth-logo .icon{font-size:56px;display:block;margin-bottom:8px;}
        .auth-logo h1{font-size:26px;font-weight:800;color:#4c1d95;margin:0;}
        .auth-logo p{font-size:13px;color:#888;margin:4px 0 0;}
        .form-group{margin-bottom:18px;}
        .form-group label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;}
        .form-group input{width:100%;padding:13px 16px;border:1.5px solid #e5e7eb;border-radius:12px;font-size:14px;outline:none;box-sizing:border-box;transition:border-color .2s;}
        .form-group input:focus{border-color:#7c3aed;}
        .form-group .error{font-size:12px;color:#ef4444;margin-top:4px;}
        .btn-auth{width:100%;padding:14px;background:linear-gradient(135deg,#7c3aed,#4c1d95);color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;transition:transform .15s,box-shadow .15s;}
        .btn-auth:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(124,58,237,0.4);}
        .auth-footer{text-align:center;margin-top:20px;font-size:13px;color:#6b7280;}
        .auth-footer a{color:#7c3aed;font-weight:600;text-decoration:none;}
        .alert-err{background:#fef2f2;border:1px solid #fca5a5;color:#b91c1c;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:16px;}
        .alert-ok{background:#f0fdf4;border:1px solid #86efac;color:#166534;padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:16px;}
        .remember-row{display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;}
        .remember-row input{width:auto;}
    </style>
</head>
<body>
<div class="auth-card">
    <div class="auth-logo">
        <span class="icon">👩‍🍼</span>
        <h1>GROW-MOM</h1>
        <p>Sistem Monitoring Tumbuh Kembang Anak</p>
    </div>

    @if(session('success'))
        <div class="alert-ok">✅ {{ session('success') }}</div>
    @endif
    @if($errors->has('email'))
        <div class="alert-err">❌ {{ $errors->first('email') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label>📧 Alamat Email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="contoh@email.com" required autofocus>
            @error('email')<div class="error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label>🔒 Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>
            @error('password')<div class="error">{{ $message }}</div>@enderror
        </div>
        <div class="remember-row" style="margin-bottom:20px;">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Ingat saya di perangkat ini</label>
        </div>
        <button type="submit" class="btn-auth">🚀 Masuk ke Aplikasi</button>
    </form>

    <div class="auth-footer">
        Belum punya akun?
        <a href="{{ route('register') }}">Daftar di sini →</a>
    </div>
</div>
</body>
</html>