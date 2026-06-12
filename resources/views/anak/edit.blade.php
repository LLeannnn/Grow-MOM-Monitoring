@extends('layouts.app')
@section('title', 'Edit Data Anak')

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1><i data-feather="edit-2"></i> Edit Data Anak</h1>
    </div>
    <a href="{{ route('anak.show', $anak) }}" class="btn btn-outline"><i data-feather="arrow-left"></i> Kembali</a>
</div>
<div class="card fade-up" style="max-width:800px;">
    <div class="card-header"><div class="card-title"><i data-feather="edit"></i> Edit: {{ $anak->nama_anak }}</div></div>
    <div class="card-body">
        <form method="POST" action="{{ route('anak.update', $anak) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label>Nama Anak <span class="required">*</span></label>
                    <input type="text" name="nama_anak" value="{{ old('nama_anak', $anak->nama_anak) }}" required>
                </div>
                <div class="form-group">
                    <label>Data Ibu <span class="required">*</span></label>
                    <select name="ibu_id" required>
                        @foreach($ibuList as $ibu)
                        <option value="{{ $ibu->id }}" {{ old('ibu_id',$anak->ibu_id)==$ibu->id?'selected':'' }}>{{ $ibu->nama_ibu }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $anak->tanggal_lahir->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" required>
                        <option value="L" {{ old('jenis_kelamin',$anak->jenis_kelamin)=='L'?'selected':'' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin',$anak->jenis_kelamin)=='P'?'selected':'' }}>Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Berat Lahir (kg)</label>
                    <input type="number" name="berat_lahir" value="{{ old('berat_lahir', $anak->berat_lahir) }}" step="0.01">
                </div>
                <div class="form-group">
                    <label>Panjang Lahir (cm)</label>
                    <input type="number" name="panjang_lahir" value="{{ old('panjang_lahir', $anak->panjang_lahir) }}" step="0.1">
                </div>
                <div class="form-group">
                    <label>Golongan Darah</label>
                    <select name="golongan_darah">
                        @foreach(['A'=>'A','B'=>'B','AB'=>'AB','O'=>'O','tidak_diketahui'=>'Tidak Diketahui'] as $val=>$label)
                        <option value="{{ $val }}" {{ old('golongan_darah',$anak->golongan_darah)==$val?'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Foto Baru</label>
                    <input type="file" name="foto" accept="image/*">
                </div>
            </div>
            <div class="divider"></div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <a href="{{ route('anak.show', $anak) }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i data-feather="save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
