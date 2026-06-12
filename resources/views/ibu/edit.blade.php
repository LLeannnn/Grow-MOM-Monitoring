@extends('layouts.app')
@section('title', 'Edit Data Ibu')

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1><i data-feather="edit-2"></i> Edit Data Ibu</h1>
        <p>Perbarui informasi profil ibu</p>
    </div>
    <a href="{{ route('ibu.show', $ibu) }}" class="btn btn-outline"><i data-feather="arrow-left"></i> Kembali</a>
</div>

<div class="card fade-up" style="max-width:800px;">
    <div class="card-header">
        <div class="card-title"><i data-feather="edit"></i> Edit: {{ $ibu->nama_ibu }}</div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('ibu.update', $ibu) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label>Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="nama_ibu" value="{{ old('nama_ibu', $ibu->nama_ibu) }}" required>
                    @error('nama_ibu')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>NIK <span class="required">*</span></label>
                    <input type="text" name="nik" value="{{ old('nik', $ibu->nik) }}" maxlength="16" required>
                    @error('nik')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $ibu->tanggal_lahir->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label>No. WhatsApp</label>
                    <input type="tel" name="no_telepon" value="{{ old('no_telepon', $ibu->no_telepon) }}" required>
                </div>
                <div class="form-group">
                    <label>Pekerjaan</label>
                    <select name="pekerjaan" required>
                        @foreach(['ibu_rumah_tangga'=>'Ibu Rumah Tangga','pns'=>'PNS','swasta'=>'Swasta','wiraswasta'=>'Wiraswasta','petani'=>'Petani','lainnya'=>'Lainnya'] as $val=>$label)
                        <option value="{{ $val }}" {{ old('pekerjaan',$ibu->pekerjaan)==$val?'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Pendidikan</label>
                    <select name="pendidikan" required>
                        @foreach(['sd'=>'SD','smp'=>'SMP','sma'=>'SMA','d3'=>'D3','s1'=>'S1','s2'=>'S2','s3'=>'S3'] as $val=>$label)
                        <option value="{{ $val }}" {{ old('pendidikan',$ibu->pendidikan)==$val?'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Status Pernikahan</label>
                    <select name="status_pernikahan" required>
                        @foreach(['menikah'=>'Menikah','belum_menikah'=>'Belum Menikah','cerai'=>'Cerai'] as $val=>$label)
                        <option value="{{ $val }}" {{ old('status_pernikahan',$ibu->status_pernikahan)==$val?'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Foto Baru (opsional)</label>
                    <input type="file" name="foto" accept="image/*">
                    @if($ibu->foto)
                        <div style="margin-top:6px;">
                            <img src="{{ asset('storage/'.$ibu->foto) }}" style="width:60px;height:60px;border-radius:50%;object-fit:cover;">
                        </div>
                    @endif
                </div>
                <div class="form-group full">
                    <label>Alamat</label>
                    <textarea name="alamat" required>{{ old('alamat', $ibu->alamat) }}</textarea>
                </div>
            </div>
            <div class="divider"></div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <a href="{{ route('ibu.show', $ibu) }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i data-feather="save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
