@extends('layouts.app')
@section('title', 'Beri Feedback')

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1>💬 Beri Feedback</h1>
        <p>Sampaikan pendapat Anda untuk perbaikan sistem GROW-MOM</p>
    </div>
    <a href="{{ route('feedback.index') }}" class="btn btn-outline">📋 Lihat Semua</a>
</div>

<div class="grid-2" style="gap:24px;align-items:start;">
    <div class="card fade-up">
        <div class="card-header"><div class="card-title">📝 Form Feedback</div></div>
        <div class="card-body">
            <form method="POST" action="{{ route('feedback.store') }}">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Nama Anda" required>
                        @error('nama')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="email@anda.com" required>
                        @error('email')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    {{-- Star Rating --}}
                    <div class="form-group">
                        <label>Rating <span class="required">*</span></label>
                        <div id="starRating" style="display:flex;gap:6px;margin:6px 0;">
                            @for($i=1;$i<=5;$i++)
                            <span class="star-btn" data-val="{{ $i }}"
                                  style="font-size:30px;cursor:pointer;color:#e5e7eb;transition:color 0.15s;">★</span>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="{{ old('rating','5') }}">
                        @error('rating')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label>Kategori <span class="required">*</span></label>
                        <select name="kategori" required>
                            @foreach(['Fitur Aplikasi','Tampilan','Kemudahan Penggunaan','Konten Edukasi','Reminder','Recall Gizi','Lainnya'] as $kat)
                            <option value="{{ $kat }}" {{ old('kategori')==$kat?'selected':'' }}>{{ $kat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Pesan / Saran <span class="required">*</span></label>
                        <textarea name="pesan" rows="5" placeholder="Ceritakan pengalaman Anda menggunakan GROW-MOM..." required>{{ old('pesan') }}</textarea>
                        @error('pesan')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="divider"></div>
                <button type="submit" class="btn btn-primary" style="width:100%;">💚 Kirim Feedback</button>
            </form>
        </div>
    </div>

    {{-- Info --}}
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="card fade-up" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:white;border:none;">
            <div class="card-body" style="text-align:center;padding:32px 24px;">
                <div style="font-size:52px;margin-bottom:12px;">💚</div>
                <div style="font-size:20px;font-weight:800;margin-bottom:8px;">Bantu Kami Berkembang!</div>
                <div style="opacity:0.85;font-size:13.5px;line-height:1.7;">
                    Feedback Anda sangat berarti untuk meningkatkan kualitas layanan GROW-MOM demi tumbuh kembang anak yang optimal.
                </div>
            </div>
        </div>

        <div class="card fade-up">
            <div class="card-header"><div class="card-title">💡 Panduan Feedback</div></div>
            <div class="card-body">
                @foreach([['⭐⭐⭐⭐⭐','Sangat Puas — Semua fitur bekerja dengan baik'],['⭐⭐⭐⭐','Puas — Ada sedikit yang perlu diperbaiki'],['⭐⭐⭐','Cukup — Beberapa fitur perlu peningkatan'],['⭐⭐','Kurang — Banyak yang perlu diperbaiki'],['⭐','Tidak Puas — Butuh perbaikan menyeluruh']] as [$stars,$desc])
                <div style="display:flex;gap:10px;margin-bottom:10px;font-size:13px;">
                    <span>{{ $stars }}</span>
                    <span style="color:var(--text-muted);">{{ $desc }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const stars     = document.querySelectorAll('.star-btn');
const ratingIn  = document.getElementById('ratingInput');

function setRating(val) {
    ratingIn.value = val;
    stars.forEach(s => {
        s.style.color = parseInt(s.dataset.val) <= val ? '#f59e0b' : '#e5e7eb';
    });
}

setRating(parseInt(ratingIn.value) || 5);

stars.forEach(s => {
    s.addEventListener('click', () => setRating(parseInt(s.dataset.val)));
    s.addEventListener('mouseenter', () => {
        stars.forEach(x => x.style.color = parseInt(x.dataset.val) <= parseInt(s.dataset.val) ? '#fbbf24' : '#e5e7eb');
    });
    s.addEventListener('mouseleave', () => setRating(parseInt(ratingIn.value)));
});
</script>
@endpush
@endsection
