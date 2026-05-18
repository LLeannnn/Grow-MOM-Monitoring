@extends('layouts.app')
@section('title', 'Buat Reminder')

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1>🔔 Buat Reminder</h1>
        <p>Atur pengingat jadwal penting untuk ibu dan anak</p>
    </div>
    <a href="{{ route('reminder.index') }}" class="btn btn-outline">← Kembali</a>
</div>

<div class="card fade-up" style="max-width:760px;">
    <div class="card-header"><div class="card-title">📝 Form Reminder</div></div>
    <div class="card-body">
        <form method="POST" action="{{ route('reminder.store') }}">
            @csrf
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label>Data Ibu <span class="required">*</span></label>
                    <select name="ibu_id" id="selectIbu" required>
                        <option value="">-- Pilih Ibu --</option>
                        @foreach($ibuList as $ibu)
                        <option value="{{ $ibu->id }}" {{ old('ibu_id')==$ibu->id?'selected':'' }}>{{ $ibu->nama_ibu }}</option>
                        @endforeach
                    </select>
                    @error('ibu_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Data Anak <span class="required">*</span></label>
                    <select name="anak_id" id="selectAnak" required>
                        <option value="">-- Pilih Anak --</option>
                        @foreach($anakList as $a)
                        <option value="{{ $a->id }}" data-ibu="{{ $a->ibu_id }}" {{ old('anak_id')==$a->id?'selected':'' }}>{{ $a->nama_anak }} — {{ $a->ibu->nama_ibu }}</option>
                        @endforeach
                    </select>
                    @error('anak_id')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Jenis Reminder <span class="required">*</span></label>
                    <select name="tipe" required>
                        <option value="imunisasi" {{ old('tipe')=='imunisasi'?'selected':'' }}>💉 Imunisasi</option>
                        <option value="posyandu"  {{ old('tipe')=='posyandu'?'selected':'' }}>🏥 Posyandu</option>
                        <option value="mpasi"     {{ old('tipe')=='mpasi'?'selected':'' }}>🥕 MPASI</option>
                        <option value="kontrol"   {{ old('tipe')=='kontrol'?'selected':'' }}>👨‍⚕️ Kontrol Dokter</option>
                        <option value="lainnya"   {{ old('tipe')=='lainnya'?'selected':'' }}>🔔 Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal & Waktu <span class="required">*</span></label>
                    <input type="datetime-local" name="tanggal_reminder" value="{{ old('tanggal_reminder') }}" required>
                    @error('tanggal_reminder')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group full">
                    <label>Judul Reminder <span class="required">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul') }}" placeholder="contoh: Imunisasi DPT Bulan ke-3" required>
                    @error('judul')<span class="form-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group full">
                    <label>Pesan <span class="required">*</span></label>
                    <textarea name="pesan" placeholder="Isi pesan reminder..." required>{{ old('pesan') }}</textarea>
                    @error('pesan')<span class="form-error">{{ $message }}</span>@enderror
                </div>

            </div>

            <div class="divider"></div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <a href="{{ route('reminder.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">🔔 Buat Reminder</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectIbu = document.getElementById('selectIbu');
    const selectAnak = document.getElementById('selectAnak');
    
    // Simpan semua opsi anak dari awal untuk memudahkan filter
    const originalAnakOptions = Array.from(selectAnak.querySelectorAll('option'));

    function filterAnak() {
        const selectedIbuId = selectIbu.value;
        const currentSelectedAnak = selectAnak.value;
        
        // Kosongkan opsi anak
        selectAnak.innerHTML = '';
        
        originalAnakOptions.forEach(option => {
            // Tampilkan opsi jika value kosong (Pilih Anak), jika ibu belum dipilih, atau ibu_id cocok
            if (option.value === "" || !selectedIbuId || option.getAttribute('data-ibu') === selectedIbuId) {
                selectAnak.appendChild(option.cloneNode(true));
            }
        });
        
        // Kembalikan pilihan anak jika masih ada dalam daftar yang di-filter
        if (currentSelectedAnak) {
            const stillExists = Array.from(selectAnak.options).some(opt => opt.value === currentSelectedAnak);
            selectAnak.value = stillExists ? currentSelectedAnak : "";
        }
    }

    selectIbu.addEventListener('change', filterAnak);

    selectAnak.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.value !== "") {
            const ibuId = selectedOption.getAttribute('data-ibu');
            if (ibuId && selectIbu.value !== ibuId) {
                selectIbu.value = ibuId;
                
                // Update dropdown anak menyesuaikan ibu yang terpilih
                const currentAnakId = this.value;
                filterAnak();
                selectAnak.value = currentAnakId;
            }
        }
    });

    // Jalankan filter saat halaman pertama kali dimuat (untuk old input)
    if (selectIbu.value) {
        filterAnak();
    }
});
</script>
@endpush
@endsection
