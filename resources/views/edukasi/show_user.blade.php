@extends('layouts.user')
@section('title', $edukasi->judul)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('edukasi.index') }}" class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-on-surface-variant active:scale-95 transition-transform">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <span class="text-sm font-bold text-on-surface">Detail Panduan</span>
    </div>

    <!-- Artikel Card -->
    <div class="bg-surface-container-lowest rounded-3xl overflow-hidden shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low">
        <!-- Gambar -->
        <div class="h-56 relative bg-primary/5 flex items-center justify-center text-6xl text-primary">
            @if($edukasi->gambar)
                <img src="{{ asset('storage/'.$edukasi->gambar) }}" class="w-full h-full object-cover">
            @else
                <i data-feather="coffee"></i>
            @endif
            <span class="absolute top-4 left-4 bg-tertiary-container text-white text-[9px] px-2.5 py-1 rounded-full font-bold uppercase tracking-wider shadow-md">{{ $edukasi->kategori_label }}</span>
        </div>

        <div class="p-6 space-y-4">
            <!-- Metadata & Tags -->
            <div class="flex flex-wrap gap-2 items-center text-[10px] font-bold text-on-surface-variant/80">
                <span><i data-feather="calendar"></i> {{ $edukasi->created_at->format('d F Y') }}</span>
                @foreach($edukasi->tags_array as $tag)
                    @if($tag)
                    <span class="bg-surface-container text-on-surface-variant px-2.5 py-0.5 rounded-full">#{{ trim($tag) }}</span>
                    @endif
                @endforeach
            </div>

            <!-- Judul -->
            <h1 class="text-xl font-extrabold text-on-surface leading-snug">{{ $edukasi->judul }}</h1>

            <!-- Tekstur & Bahan -->
            @if($edukasi->tekstur_makanan || $edukasi->bahan_makanan)
            <div class="bg-primary/5 border-l-4 border-primary p-4 rounded-2xl space-y-3">
                @if($edukasi->tekstur_makanan)
                <div>
                    <h3 class="text-xs font-bold text-primary flex items-center gap-1.5"><span class="material-symbols-outlined text-base">texture</span> Tekstur Makanan</h3>
                    <p class="text-xs text-on-surface font-semibold mt-1">{{ $edukasi->tekstur_makanan }}</p>
                </div>
                @endif
                @if($edukasi->bahan_makanan)
                <div>
                    <h3 class="text-xs font-bold text-primary flex items-center gap-1.5"><span class="material-symbols-outlined text-base">restaurant</span> Bahan Makanan</h3>
                    <p class="text-xs text-on-surface font-semibold mt-1 whitespace-pre-wrap leading-relaxed">{{ $edukasi->bahan_makanan }}</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Konten -->
            <div class="text-sm text-on-surface leading-relaxed whitespace-pre-wrap pt-2">
                {!! nl2br(e($edukasi->konten)) !!}
            </div>
        </div>
    </div>

    <!-- Artikel Terkait -->
    @if($related->count())
    <div class="space-y-4 pt-4">
        <h2 class="text-base font-bold text-on-surface flex items-center gap-2">
            <span class="text-xl"><i data-feather="book"></i></span> Artikel Terkait
        </h2>
        <div class="grid grid-cols-1 gap-4">
            @foreach($related as $r)
            <a href="{{ route('edukasi.show', $r) }}" class="flex bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm border border-surface-container-low active:scale-[0.98] transition-transform">
                <div class="w-24 h-24 bg-primary/5 flex items-center justify-center text-2xl text-primary shrink-0">
                    @if($r->gambar)
                        <img src="{{ asset('storage/'.$r->gambar) }}" class="w-full h-full object-cover">
                    @else
                        <i data-feather="coffee"></i>
                    @endif
                </div>
                <div class="p-3 flex flex-col justify-center gap-1 min-w-0">
                    <span class="text-[9px] font-bold text-tertiary-container uppercase tracking-wide">{{ $r->kategori_label }}</span>
                    <h3 class="font-bold text-xs text-on-surface line-clamp-1 leading-snug">{{ $r->judul }}</h3>
                    <p class="text-[10px] text-on-surface-variant line-clamp-2 leading-normal">{{ $r->konten_ringkas }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
