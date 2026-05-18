<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Lengkapi Profil — GROW-MOM</title>
@vite(['resources/css/app.css','resources/js/app.js'])
<style>
body{margin:0;min-height:100vh;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);font-family:'Segoe UI',sans-serif;padding:24px;box-sizing:border-box;}
.ob-card{background:#fff;border-radius:24px;max-width:560px;margin:0 auto;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,0.2);}
.ob-header{background:linear-gradient(135deg,#7c3aed,#4c1d95);padding:32px 36px;color:#fff;}
.ob-header h1{font-size:22px;font-weight:800;margin:0 0 6px;}
.ob-header p{font-size:13.5px;opacity:.85;margin:0;}
.ob-steps{display:flex;gap:8px;padding:20px 36px;background:#f9fafb;border-bottom:1px solid #e5e7eb;}
.step{flex:1;text-align:center;font-size:11.5px;color:#9ca3af;}
.step.active{color:#7c3aed;font-weight:700;}
.step-dot{width:28px;height:28px;border-radius:50%;background:#e5e7eb;color:#9ca3af;font-weight:700;font-size:13px;display:flex;align-items:center;justify-content:center;margin:0 auto 4px;}
.step.active .step-dot{background:#7c3aed;color:#fff;}
.ob-body{padding:32px 36px;}
.form-group{margin-bottom:20px;}
.form-group label{display:block;font-size:13.5px;font-weight:600;color:#374151;margin-bottom:6px;}
.form-group label .hint{font-size:12px;color:#9ca3af;font-weight:400;}
.form-control{width:100%;padding:12px 16px;border:1.5px solid #e5e7eb;border-radius:12px;font-size:14px;outline:none;box-sizing:border-box;transition:border-color .2s;background:#fff;}
.form-control:focus{border-color:#7c3aed;}
.form-control.error-field{border-color:#ef4444;}
.error-msg{font-size:12px;color:#ef4444;margin-top:4px;}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.btn-submit{width:100%;padding:15px;background:linear-gradient(135deg,#7c3aed,#4c1d95);color:#fff;border:none;border-radius:14px;font-size:15px;font-weight:700;cursor:pointer;transition:transform .15s,box-shadow .15s;}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(124,58,237,0.4);}
.welcome-msg{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:14px 16px;font-size:13px;color:#166534;margin-bottom:24px;}
.section-title{font-size:12px;font-weight:700;color:#7c3aed;text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;padding-bottom:6px;border-bottom:1px solid #ede9fe;}
@media (max-width: 640px) {
    .grid2 { grid-template-columns: 1fr; }
    .ob-card { border-radius: 0; min-height: 100vh; }
    body { padding: 0; }
}
</style>
</head>
<body>
<div class="ob-card">
    <div class="ob-header">
        <div style="font-size:36px;margin-bottom:8px;">👩‍🍼</div>
        <h1>Halo, {{ auth()->user()->name }}! 👋</h1>
        <p>Sebelum mulai, yuk isi informasi dasar Anda. Hanya butuh 2 menit!</p>
    </div>
    <div class="ob-steps">
        <div class="step active"><div class="step-dot">1</div>Profil Ibu</div>
        <div class="step"><div class="step-dot">2</div>Data Anak</div>
        <div class="step"><div class="step-dot">3</div>Selesai 🎉</div>
    </div>
    <div class="ob-body">
        @if(session('success'))
            <div class="welcome-msg">🎉 {{ session('success') }}</div>
        @endif

        <div class="welcome-msg">
            📝 <strong>Langkah 1 dari 2:</strong> Isi data diri Ibu. Data anak bisa ditambahkan setelah ini.
        </div>

        <form method="POST" action="{{ route('onboarding.store') }}">
            @csrf

            <div class="section-title">📋 Data Pribadi Ibu</div>
            <div class="grid2">
                <div class="form-group">
                    <label>NIK KTP <span class="hint">(16 digit)</span></label>
                    <input name="nik" class="form-control {{ $errors->has('nik') ? 'error-field' : '' }}"
                        value="{{ old('nik') }}" placeholder="Masukkan 16 digit NIK" required>
                    @error('nik')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Nama Lengkap Ibu <span class="hint">(sesuai KTP)</span></label>
                    <input name="nama_ibu" class="form-control {{ $errors->has('nama_ibu') ? 'error-field' : '' }}"
                        value="{{ old('nama_ibu') }}" placeholder="Contoh: Siti Rahayu Putri" required>
                    @error('nama_ibu')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="grid2">
                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control {{ $errors->has('tanggal_lahir') ? 'error-field' : '' }}"
                        value="{{ old('tanggal_lahir') }}" required>
                    @error('tanggal_lahir')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>No. WhatsApp</label>
                    <input name="no_telepon" class="form-control {{ $errors->has('no_telepon') ? 'error-field' : '' }}"
                        value="{{ old('no_telepon') }}" placeholder="08xxxxxxxxxx" required>
                    @error('no_telepon')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>Alamat Rumah</label>
                <input name="alamat" class="form-control {{ $errors->has('alamat') ? 'error-field' : '' }}"
                    value="{{ old('alamat') }}" placeholder="Contoh: Jl. Mawar No.5, Bandung" required>
                @error('alamat')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="section-title" style="margin-top:24px;">👩‍💼 Informasi Tambahan</div>
            <div class="grid2">
                <div class="form-group">
                    <label>Pekerjaan</label>
                    <select name="pekerjaan" class="form-control {{ $errors->has('pekerjaan') ? 'error-field' : '' }}" required>
                        <option value="">-- Pilih --</option>
                        <option value="ibu_rumah_tangga" {{ old('pekerjaan')=='ibu_rumah_tangga'?'selected':'' }}>🏠 Ibu Rumah Tangga</option>
                        <option value="pns"              {{ old('pekerjaan')=='pns'?'selected':'' }}>🏛️ PNS</option>
                        <option value="swasta"           {{ old('pekerjaan')=='swasta'?'selected':'' }}>🏢 Karyawan Swasta</option>
                        <option value="wiraswasta"       {{ old('pekerjaan')=='wiraswasta'?'selected':'' }}>🛒 Wiraswasta</option>
                        <option value="petani"           {{ old('pekerjaan')=='petani'?'selected':'' }}>🌾 Petani</option>
                        <option value="lainnya"          {{ old('pekerjaan')=='lainnya'?'selected':'' }}>✨ Lainnya</option>
                    </select>
                    @error('pekerjaan')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Pendidikan Terakhir</label>
                    <select name="pendidikan" class="form-control {{ $errors->has('pendidikan') ? 'error-field' : '' }}" required>
                        <option value="">-- Pilih --</option>
                        <option value="sd"  {{ old('pendidikan')=='sd'?'selected':'' }}>SD</option>
                        <option value="smp" {{ old('pendidikan')=='smp'?'selected':'' }}>SMP</option>
                        <option value="sma" {{ old('pendidikan')=='sma'?'selected':'' }}>SMA / SMK</option>
                        <option value="d3"  {{ old('pendidikan')=='d3'?'selected':'' }}>D3</option>
                        <option value="s1"  {{ old('pendidikan')=='s1'?'selected':'' }}>S1</option>
                        <option value="s2"  {{ old('pendidikan')=='s2'?'selected':'' }}>S2</option>
                        <option value="s3"  {{ old('pendidikan')=='s3'?'selected':'' }}>S3</option>
                    </select>
                    @error('pendidikan')<div class="error-msg">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>Status Pernikahan</label>
                <select name="status_pernikahan" class="form-control {{ $errors->has('status_pernikahan') ? 'error-field' : '' }}" required>
                    <option value="">-- Pilih --</option>
                    <option value="menikah"     {{ old('status_pernikahan')=='menikah'?'selected':'' }}>💍 Menikah</option>
                    <option value="cerai_hidup" {{ old('status_pernikahan')=='cerai_hidup'?'selected':'' }}>📋 Cerai Hidup</option>
                    <option value="cerai_mati"  {{ old('status_pernikahan')=='cerai_mati'?'selected':'' }}>🕊️ Cerai Mati</option>
                    <option value="janda"       {{ old('status_pernikahan')=='janda'?'selected':'' }}>👤 Janda</option>
                </select>
                @error('status_pernikahan')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn-submit">Lanjut: Tambah Data Anak →</button>
        </form>
    </div>
</div>
</body></html>