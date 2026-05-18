@extends('layouts.app')
@section('title', 'Tambah Data Anak')

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1>👶 Tambah Data Anak</h1>
        <p>Daftarkan data anak untuk mulai monitoring</p>
    </div>
    <a href="{{ route('anak.index') }}" class="btn btn-outline">← Kembali</a>
</div>

<div class="card fade-up" style="max-width:800px;">
    <div class="card-header"><div class="card-title">📝 Form Data Anak</div></div>
    <div class="card-body">
        <form method="POST" action="{{ route('anak.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label>Nama Anak <span class="required">*</span></label>
                    <input type="text" name="nama_anak" value="{{ old('nama_anak') }}" placeholder="Nama lengkap anak" required>
                    @error('nama_anak')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Data Ibu <span class="required">*</span></label>
                    <select name="ibu_id" required>
                        <option value="">-- Pilih Ibu --</option>
                        @foreach($ibuList as $ibu)
                        <option value="{{ $ibu->id }}"
                            {{ (old('ibu_id', request('ibu_id')))==$ibu->id ? 'selected' : '' }}>
                            {{ $ibu->nama_ibu }}
                        </option>
                        @endforeach
                    </select>
                    @error('ibu_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir <span class="required">*</span></label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                    @error('tanggal_lahir')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin <span class="required">*</span></label>
                    <select name="jenis_kelamin" required>
                        <option value="">-- Pilih --</option>
                        <option value="L" {{ old('jenis_kelamin')=='L'?'selected':'' }}>👦 Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin')=='P'?'selected':'' }}>👧 Perempuan</option>
                    </select>
                    @error('jenis_kelamin')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Berat Lahir (kg)</label>
                    <input type="number" name="berat_lahir" value="{{ old('berat_lahir') }}" step="0.01" placeholder="contoh: 3.2">
                    @error('berat_lahir')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Panjang Lahir (cm)</label>
                    <input type="number" name="panjang_lahir" value="{{ old('panjang_lahir') }}" step="0.1" placeholder="contoh: 49">
                    @error('panjang_lahir')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Golongan Darah</label>
                    <select name="golongan_darah">
                        @foreach(['A'=>'A','B'=>'B','AB'=>'AB','O'=>'O','tidak_diketahui'=>'Tidak Diketahui'] as $val=>$label)
                        <option value="{{ $val }}" {{ old('golongan_darah','tidak_diketahui')==$val?'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Foto (opsional)</label>
                    <input type="file" name="foto" accept="image/*">
                </div>
            </div>
            <div class="divider"></div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <a href="{{ route('anak.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">💾 Simpan Data</button>
            </div>
        </form>
    </div>
</div>
@endsection
