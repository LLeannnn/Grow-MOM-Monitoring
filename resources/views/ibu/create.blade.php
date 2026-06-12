@extends('layouts.app')
@section('title', 'Tambah Data Ibu')

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1><i data-feather="user"></i> Tambah Data Ibu</h1>
        <p>Isi form berikut untuk mendaftarkan data ibu baru</p>
    </div>
    <a href="{{ route('ibu.index') }}" class="btn btn-outline"><i data-feather="arrow-left"></i> Kembali</a>
</div>

<div class="card fade-up" style="max-width:800px;">
    <div class="card-header">
        <div class="card-title"><i data-feather="edit"></i> Form Data Ibu</div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('ibu.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label>Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}" placeholder="Nama lengkap ibu" required>
                    @error('nama_ibu')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>NIK <span class="required">*</span></label>
                    <input type="text" name="nik" value="{{ old('nik') }}" placeholder="16 digit NIK" maxlength="16" required>
                    @error('nik')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir <span class="required">*</span></label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                    @error('tanggal_lahir')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>No. WhatsApp <span class="required">*</span></label>
                    <input type="tel" name="no_telepon" value="{{ old('no_telepon') }}" placeholder="08xx-xxxx-xxxx" required>
                    @error('no_telepon')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Pekerjaan <span class="required">*</span></label>
                    <select name="pekerjaan" required>
                        <option value="">-- Pilih Pekerjaan --</option>
                        <option value="ibu_rumah_tangga" {{ old('pekerjaan')=='ibu_rumah_tangga'?'selected':'' }}>Ibu Rumah Tangga</option>
                        <option value="pns" {{ old('pekerjaan')=='pns'?'selected':'' }}>PNS</option>
                        <option value="swasta" {{ old('pekerjaan')=='swasta'?'selected':'' }}>Swasta</option>
                        <option value="wiraswasta" {{ old('pekerjaan')=='wiraswasta'?'selected':'' }}>Wiraswasta</option>
                        <option value="petani" {{ old('pekerjaan')=='petani'?'selected':'' }}>Petani</option>
                        <option value="lainnya" {{ old('pekerjaan')=='lainnya'?'selected':'' }}>Lainnya</option>
                    </select>
                    @error('pekerjaan')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Pendidikan Terakhir <span class="required">*</span></label>
                    <select name="pendidikan" required>
                        <option value="">-- Pilih Pendidikan --</option>
                        @foreach(['sd'=>'SD','smp'=>'SMP','sma'=>'SMA','d3'=>'D3','s1'=>'S1','s2'=>'S2','s3'=>'S3'] as $val=>$label)
                        <option value="{{ $val }}" {{ old('pendidikan')==$val?'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('pendidikan')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Status Pernikahan <span class="required">*</span></label>
                    <select name="status_pernikahan" required>
                        <option value="menikah" {{ old('status_pernikahan','menikah')=='menikah'?'selected':'' }}>Menikah</option>
                        <option value="belum_menikah" {{ old('status_pernikahan')=='belum_menikah'?'selected':'' }}>Belum Menikah</option>
                        <option value="cerai" {{ old('status_pernikahan')=='cerai'?'selected':'' }}>Cerai</option>
                    </select>
                    @error('status_pernikahan')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Foto (opsional)</label>
                    <input type="file" name="foto" accept="image/*">
                    @error('foto')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group full">
                    <label>Alamat Lengkap <span class="required">*</span></label>
                    <textarea name="alamat" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan..." required>{{ old('alamat') }}</textarea>
                    @error('alamat')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="divider"></div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <a href="{{ route('ibu.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i data-feather="save"></i> Simpan Data</button>
            </div>
        </form>
    </div>
</div>
@endsection
