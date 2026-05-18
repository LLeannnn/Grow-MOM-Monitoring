@extends('layouts.user')
@section('title', 'Feedback & Rekomendasi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-on-surface">💬 Saran & Feedback</h1>
        <p class="text-sm text-on-surface-variant mt-1">Rekomendasi gizi personal untuk buah hati Anda</p>
    </div>

    <!-- Info Banner (Sleek dark mode gradient) -->
    <div class="bg-gradient-to-br from-[#0f2419] to-[#1a3a28] rounded-3xl p-6 shadow-lg flex gap-4 items-center border border-emerald-950">
        <span class="text-3xl shrink-0">🤖</span>
        <div class="space-y-1">
            <h4 class="text-white text-xs sm:text-sm font-bold">Sistem Analisis Otomatis</h4>
            <p class="text-[10px] sm:text-xs text-white/70 leading-relaxed">
                GROW-MOM menganalisis data <span class="text-[#4ade80] font-bold">tumbuh kembang</span>, 
                <span class="text-[#4ade80] font-bold">recall gizi</span>, dan <span class="text-[#4ade80] font-bold">pola makan</span> anak untuk merekomendasikan saran gizi terbaik.
            </p>
        </div>
    </div>

    <!-- Grid Anak -->
    @if($anakList->isEmpty())
    <div class="bg-surface-container-lowest rounded-3xl p-8 text-center shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low">
        <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center text-3xl mb-4">👶</div>
        <div class="font-bold text-on-surface mb-2">Belum ada data anak</div>
        <div class="text-xs text-on-surface-variant mb-6">Tambahkan data buah hati terlebih dahulu untuk melihat rekomendasi gizi otomatis</div>
        <a href="{{ route('anak.create') }}" class="w-full justify-center flex items-center gap-2 py-3 bg-primary text-on-primary rounded-xl text-sm font-bold active:scale-[0.98] transition-all">
            + Tambah Anak
        </a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($anakList as $anak)
        @php $r = $ringkasanFeedback[$anak->id]; @endphp
        <a href="{{ route('feedback.show', $anak) }}" class="block bg-surface-container-lowest rounded-[24px] p-5 shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low hover:border-primary/20 transition-all active:scale-[0.99]">
            <!-- Header Anak -->
            <div class="flex items-center gap-4 mb-4 border-b border-surface-container pb-3">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-2xl shrink-0">
                    {{ $anak->jenis_kelamin === 'L' ? '👦' : '👧' }}
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-sm sm:text-base text-on-surface leading-tight">{{ $anak->nama_anak }}</h3>
                    <p class="text-xs text-on-surface-variant font-medium mt-0.5">{{ $anak->umur_label }}</p>
                </div>
                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $r['badge']['class'] === 'badge-success' ? 'bg-green-500/10 text-green-600' : ($r['badge']['class'] === 'badge-warning' ? 'bg-amber-500/10 text-amber-600' : ($r['badge']['class'] === 'badge-danger' ? 'bg-red-500/10 text-red-600' : 'bg-surface-container text-on-surface-variant')) }}">
                    {{ $r['badge']['label'] }}
                </span>
            </div>

            <!-- Status Data Grid -->
            <div class="grid grid-cols-3 gap-2 text-center mb-4">
                <div class="bg-surface-container/60 p-3 rounded-2xl border border-surface-container/40">
                    <span class="text-lg block">{{ $anak->pertumbuhan->isNotEmpty() ? '✅' : '❌' }}</span>
                    <span class="text-[9px] font-bold text-on-surface-variant mt-1 block uppercase">Data BB/TB</span>
                </div>
                <div class="bg-surface-container/60 p-3 rounded-2xl border border-surface-container/40">
                    <span class="text-lg block">{{ $r['has_recall'] ? '✅' : '❌' }}</span>
                    <span class="text-[9px] font-bold text-on-surface-variant mt-1 block uppercase">Recall 7 Hari</span>
                </div>
                <div class="bg-surface-container/60 p-3 rounded-2xl border border-surface-container/40">
                    <span class="text-xs font-black block {{ $r['skor'] >= 100 ? 'text-green-600' : ($r['skor'] >= 50 ? 'text-amber-600' : 'text-red-600') }}">{{ $r['skor'] }}%</span>
                    <span class="text-[9px] font-bold text-on-surface-variant mt-1 block uppercase">Kelengkapan</span>
                </div>
            </div>

            <!-- Progress Track -->
            <div class="h-2 bg-surface-container rounded-full overflow-hidden mb-3">
                <div class="h-full rounded-full transition-all duration-300 {{ $r['skor'] >= 100 ? 'bg-green-500' : ($r['skor'] >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width:{{ $r['skor'] }}%"></div>
            </div>

            <div class="flex justify-between items-center text-[10px] sm:text-xs font-bold text-primary mt-2">
                <span>Lihat rekomendasi gizi lengkap</span>
                <span class="material-symbols-outlined text-base">arrow_forward</span>
            </div>
        </a>
        @endforeach
    </div>
    @endif

    <div class="bg-surface-container/40 p-4 rounded-3xl text-center text-xs font-semibold text-on-surface-variant/80 border border-surface-container/30">
        💡 <strong>Tips:</strong> Lengkapi data pertumbuhan & catat makan harian buah hati agar analisis feedback kami semakin akurat & personal.
    </div>
</div>
@endsection
