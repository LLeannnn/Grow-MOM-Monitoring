@extends('layouts.user')
@section('title', 'Edukasi MPASI')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-on-surface"><i data-feather="book"></i> Edukasi MPASI</h1>
        <p class="text-sm text-on-surface-variant mt-1">Panduan lengkap pemberian makanan bayi</p>
    </div>

    <!-- Filter Kategori & Search -->
    <div class="space-y-4">
        <!-- Search -->
        <form method="GET" class="relative">
            @if(request('kategori'))
                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            @endif
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant opacity-70 material-symbols-outlined text-lg">search</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari panduan..." class="w-full pl-11 pr-4 py-3 bg-surface-container-lowest border border-surface-container-low rounded-2xl text-xs sm:text-sm font-semibold focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all placeholder:font-normal shadow-sm">
        </form>

        <!-- Kategori Scrollable -->
        <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
            <a href="{{ route('edukasi.index') }}" class="whitespace-nowrap px-4 py-2.5 rounded-2xl text-xs font-bold transition-all {{ !request('kategori') ? 'bg-primary text-on-primary shadow-md shadow-primary/10' : 'bg-surface-container-lowest text-on-surface-variant border border-surface-container-low' }}">Semua</a>
            @foreach($kategoriList as $val => $label)
            <a href="{{ route('edukasi.index') }}?kategori={{ $val }}" class="whitespace-nowrap px-4 py-2.5 rounded-2xl text-xs font-bold transition-all {{ request('kategori')==$val ? 'bg-primary text-on-primary shadow-md shadow-primary/10' : 'bg-surface-container-lowest text-on-surface-variant border border-surface-container-low' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <!-- Grid Artikel -->
    <div class="space-y-4">
        @forelse($edukasi as $e)
        <a href="{{ route('edukasi.show', $e) }}" class="block bg-surface-container-lowest rounded-3xl overflow-hidden shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low hover:border-primary/20 transition-colors">
            <div class="h-44 relative bg-primary/5 flex items-center justify-center text-4xl text-primary">
                @if($e->gambar)
                    <img src="{{ asset('storage/'.$e->gambar) }}" class="w-full h-full object-cover">
                @else
                    <i data-feather="coffee"></i>
                @endif
                <span class="absolute top-3 left-3 bg-tertiary-container text-white text-[9px] px-2.5 py-1 rounded-full font-bold uppercase tracking-wider shadow-sm">{{ $e->kategori_label }}</span>
            </div>
            
            <div class="p-5 space-y-2">
                <span class="text-[9px] font-bold text-on-surface-variant/80">{{ $e->created_at->format('d M Y') }}</span>
                <h3 class="font-bold text-base text-on-surface leading-snug line-clamp-2">{{ $e->judul }}</h3>
                <p class="text-xs text-on-surface-variant line-clamp-2 leading-relaxed">{{ $e->konten_ringkas }}</p>
                
                @if($e->tags_array)
                <div class="flex flex-wrap gap-1.5 pt-2">
                    @foreach(array_slice($e->tags_array,0,3) as $tag)
                    <span class="bg-surface-container text-on-surface-variant/80 px-2 py-0.5 rounded-md text-[10px] font-semibold">#{{ trim($tag) }}</span>
                    @endforeach
                </div>
                @endif
            </div>
        </a>
        @empty
        <div class="bg-surface-container-lowest rounded-3xl p-8 text-center shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low">
            <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center text-3xl mb-4"><i data-feather="coffee"></i></div>
            <div class="font-bold text-on-surface mb-2">Belum ada panduan</div>
            <div class="text-xs text-on-surface-variant">Artikel edukasi MPASI akan segera tersedia untuk Anda</div>
        </div>
        @endforelse
    </div>

    @if($edukasi->hasPages())
    <div class="mt-6">
        <div class="flex justify-between items-center bg-surface-container-lowest rounded-xl p-2 shadow-sm">
            @if ($edukasi->onFirstPage())
                <span class="px-4 py-2 text-outline-variant font-semibold text-sm">Sebelumnya</span>
            @else
                <a href="{{ $edukasi->previousPageUrl() }}" class="px-4 py-2 text-primary font-bold text-sm active:scale-95 transition-all">Sebelumnya</a>
            @endif

            <span class="text-xs font-bold text-on-surface-variant">Hal {{ $edukasi->currentPage() }}</span>

            @if ($edukasi->hasMorePages())
                <a href="{{ $edukasi->nextPageUrl() }}" class="px-4 py-2 text-primary font-bold text-sm active:scale-95 transition-all">Selanjutnya</a>
            @else
                <span class="px-4 py-2 text-outline-variant font-semibold text-sm">Selanjutnya</span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
