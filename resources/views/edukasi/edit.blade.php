@extends('layouts.app')
@section('title', 'Edit Artikel Edukasi')

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1>📝 Edit Artikel Edukasi</h1>
    </div>
    <a href="{{ route('edukasi.show', $edukasi) }}" class="btn btn-outline">← Kembali</a>
</div>

<div class="card fade-up" style="max-width:800px;">
    <div class="card-header"><div class="card-title">✏️ Form Edit MPASI</div></div>
    <div class="card-body">
        <form method="POST" action="{{ route('edukasi.update', $edukasi) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-group">
                    <label>Judul Artikel <span class="required">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul', $edukasi->judul) }}" placeholder="Contoh: Menu MPASI Bergizi untuk Bayi 6 Bulan" required>
                    @error('judul')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Kategori Usia <span class="required">*</span></label>
                    <select name="kategori_usia" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="6_bulan"     {{ old('kategori_usia', $edukasi->kategori_usia)=='6_bulan'?'selected':'' }}>6 Bulan</option>
                        <option value="7_9_bulan"   {{ old('kategori_usia', $edukasi->kategori_usia)=='7_9_bulan'?'selected':'' }}>7-9 Bulan</option>
                        <option value="10_12_bulan" {{ old('kategori_usia', $edukasi->kategori_usia)=='10_12_bulan'?'selected':'' }}>10-12 Bulan</option>
                        <option value="12_24_bulan" {{ old('kategori_usia', $edukasi->kategori_usia)=='12_24_bulan'?'selected':'' }}>12-24 Bulan</option>
                        <option value="umum"        {{ old('kategori_usia', $edukasi->kategori_usia)=='umum'?'selected':'' }}>Umum</option>
                    </select>
                    @error('kategori_usia')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Tags (pisahkan dengan koma)</label>
                    <input type="text" name="tags" value="{{ old('tags', $edukasi->tags) }}" placeholder="contoh: protein, sayuran, buah">
                    <span class="form-hint">Contoh: protein, wortel, mpasi awal</span>
                </div>
                <div class="form-group">
                    <label>Tekstur Makanan</label>
                    <input type="text" name="tekstur_makanan" value="{{ old('tekstur_makanan', $edukasi->tekstur_makanan) }}" placeholder="Contoh: Puree halus, Cincang kasar, Finger food">
                    @error('tekstur_makanan')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Bahan Makanan</label>
                    <textarea name="bahan_makanan" rows="4" placeholder="Contoh: - 50g Daging ayam&#10;- 20g Wortel&#10;- 1 sdm Minyak Zaitun">{{ old('bahan_makanan', $edukasi->bahan_makanan) }}</textarea>
                    @error('bahan_makanan')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Gambar (opsional)</label>
                    <input type="file" name="gambar" accept="image/*">
                    <span class="form-hint">Biarkan kosong jika tidak ingin mengubah gambar.</span>
                    @error('gambar')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Konten Artikel <span class="required">*</span></label>
                    <textarea name="konten" rows="14" placeholder="Tulis konten artikel di sini..." required style="min-height:280px;">{{ old('konten', $edukasi->konten) }}</textarea>
                    @error('konten')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="divider"></div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <a href="{{ route('edukasi.show', $edukasi) }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
