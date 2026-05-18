@extends('layouts.app')
@section('title', 'Input Pengukuran')

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1>📏 Input Pengukuran</h1>
        <p>Catat data antropometri anak hari ini</p>
    </div>
    <a href="{{ route('pertumbuhan.index') }}" class="btn btn-outline">← Kembali</a>
</div>

<div class="grid-2" style="gap:20px;align-items:start;">
    <div class="card fade-up">
        <div class="card-header"><div class="card-title">📝 Form Pengukuran</div></div>
        <div class="card-body">
            <form method="POST" action="{{ route('pertumbuhan.store') }}">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label>Pilih Anak <span class="required">*</span></label>
                        <select name="anak_id" required id="selectAnak">
                            <option value="">-- Pilih Anak --</option>
                            @foreach($anakList as $a)
                            <option value="{{ $a->id }}"
                                data-umur="{{ $a->umur_bulan }}"
                                data-nama="{{ $a->nama_anak }}"
                                {{ (old('anak_id', request('anak_id')))==$a->id?'selected':'' }}>
                                {{ $a->nama_anak }} ({{ $a->umur_label }}) — {{ $a->ibu->nama_ibu }}
                            </option>
                            @endforeach
                        </select>
                        @error('anak_id')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Tanggal Pengukuran <span class="required">*</span></label>
                        <input type="date" name="tanggal_pengukuran" value="{{ old('tanggal_pengukuran', today()->format('Y-m-d')) }}" required>
                        @error('tanggal_pengukuran')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Berat Badan (kg) <span class="required">*</span></label>
                        <input type="number" name="berat_badan" value="{{ old('berat_badan') }}" step="0.01" placeholder="contoh: 8.5" required>
                        @error('berat_badan')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Tinggi Badan (cm) <span class="required">*</span></label>
                        <input type="number" name="tinggi_badan" value="{{ old('tinggi_badan') }}" step="0.1" placeholder="contoh: 72.5" required>
                        @error('tinggi_badan')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="catatan" placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
                    </div>
                </div>
                <div class="divider"></div>
                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <a href="{{ route('pertumbuhan.index') }}" class="btn btn-outline">Batal</a>
                    <button type="submit" class="btn btn-primary">💾 Simpan & Hitung Status</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Info Standar WHO --}}
    <div class="card fade-up">
        <div class="card-header"><div class="card-title">ℹ️ Referensi WHO</div></div>
        <div class="card-body">
            <div style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">
                Status gizi dihitung otomatis berdasarkan berat badan dan usia menggunakan referensi standar WHO.
            </div>
            <div class="divider"></div>
            <div style="display:flex;flex-direction:column;gap:10px;margin-top:12px;">
                @foreach(['normal'=>['badge-success','✅','Gizi Normal','BB & TB sesuai standar usia'],'stunting'=>['badge-warning','⚠️','Stunting','TB/U < -2 SD (Tinggi Rendah)'],'wasting'=>['badge-danger','🚨','Wasting','BB/U < -3 SD (Berat Sangat Rendah)'],'underweight'=>['badge-warning','⚠️','Underweight','BB/U < -2 SD (Berat Rendah)']] as $status=>[$cls,$icon,$label,$desc])
                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="badge {{ $cls }}">{{ $icon }} {{ $label }}</span>
                    <span style="font-size:12px;color:var(--text-muted);">{{ $desc }}</span>
                </div>
                @endforeach
            </div>
            <div class="divider"></div>
                <strong>Anthropometri yang diukur:</strong><br>
                • BB = Berat Badan<br>
                • TB = Tinggi/Panjang Badan
            </div>
        </div>
    </div>
</div>
@endsection
