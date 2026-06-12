@extends('layouts.user')
@section('title', 'Data Anak')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-on-surface"><i data-feather="smile"></i> Data Anak</h1>
            <p class="text-sm text-on-surface-variant mt-1">Daftar buah hati Anda</p>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" class="bg-surface-container-lowest p-4 rounded-3xl shadow-[0px_5px_15px_rgba(30,41,59,0.03)] space-y-3 border border-surface-container-low">
        <div class="flex gap-2">
            <div class="flex-1 relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant"><i data-feather="search"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama..." class="w-full pl-9 pr-4 py-2.5 bg-surface-container-low border-transparent rounded-xl text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
            </div>
            <select name="jenis_kelamin" class="w-1/3 bg-surface-container-low border-transparent rounded-xl text-sm px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                <option value="">Semua</option>
                <option value="L" {{ request('jenis_kelamin')=='L'?'selected':'' }}>Laki-laki</option>
                <option value="P" {{ request('jenis_kelamin')=='P'?'selected':'' }}>Perempuan</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-primary text-on-primary py-2.5 rounded-xl text-sm font-bold active:scale-[0.98] transition-transform">Filter</button>
            @if(request()->hasAny(['search','jenis_kelamin']))
                <a href="{{ route('anak.index') }}" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-xl text-sm font-bold flex items-center justify-center active:scale-[0.98] transition-transform">Reset</a>
            @endif
        </div>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 md:gap-6">
        @forelse($anak as $a)
        @php
            $status = $a->pertumbuhan_terakhir?->status_gizi ?? null;
            $statusColor = 'tertiary-container'; 
            $statusBg = 'bg-tertiary-container/10 text-tertiary-container';
            if(in_array($status, ['stunting', 'wasting', 'underweight', 'kurang', 'buruk', 'obesitas'])) {
                $statusColor = 'secondary-container';
                $statusBg = 'bg-secondary-container/10 text-secondary-container';
            }
        @endphp
        <div onclick="window.location.href='{{ route('anak.show', $a) }}'" class="cursor-pointer hover:bg-surface-container transition-all active:scale-[0.98] bg-surface-container-lowest rounded-[24px] p-5 shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border-l-4 border-[color:var(--tw-colors-{{$statusColor}})] flex flex-col h-full relative overflow-hidden">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary/20 to-primary/60 flex items-center justify-center text-white text-xl shadow-inner mb-3">
                {!! $a->jenis_kelamin === 'L' ? '<i data-feather="user"></i>' : '<i data-feather="user"></i>' !!}
            </div>
            
            <div class="mb-4">
                <h3 class="text-base font-bold leading-tight text-on-surface">{{ Str::limit($a->nama_anak, 15) }}</h3>
                <p class="text-[11px] text-on-surface-variant font-medium mt-1">{{ $a->umur_label ?? '-' }}</p>
            </div>

            @if($a->pertumbuhan_terakhir)
            <div class="mt-auto pt-4 border-t border-surface-container-high space-y-2">
                <div class="flex justify-between text-[11px] font-semibold text-on-surface-variant">
                    <span><i data-feather="activity"></i> {{ $a->pertumbuhan_terakhir->berat_badan }} kg</span>
                    <span><i data-feather="bar-chart-2"></i> {{ $a->pertumbuhan_terakhir->tinggi_badan }} cm</span>
                </div>
                <div class="pt-1">
                    @php $badge = $a->pertumbuhan_terakhir->status_gizi_badge; @endphp
                    <span class="{{ $statusBg }} px-2.5 py-1 rounded-full text-[9px] font-bold uppercase tracking-wide">{{ $badge['label'] }}</span>
                </div>
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-2 bg-surface-container-lowest rounded-3xl p-8 text-center shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low">
            <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center text-3xl mb-4"><i data-feather="smile"></i></div>
            <div class="font-bold text-on-surface mb-2">Belum ada data anak</div>
            <div class="text-xs text-on-surface-variant mb-6">Tambahkan data anak pertama untuk mulai monitoring</div>
            <a href="{{ route('anak.create') }}" class="w-full justify-center flex items-center gap-2 py-3 bg-primary text-on-primary rounded-xl text-sm font-bold shadow-md active:scale-[0.98] transition-all">
                Tambah Anak
            </a>
        </div>
        @endforelse
    </div>

    @if($anak->hasPages())
    <div class="mt-6">
        <div class="flex justify-between items-center bg-surface-container-lowest rounded-xl p-2 shadow-sm">
            @if ($anak->onFirstPage())
                <span class="px-4 py-2 text-outline-variant font-semibold text-sm">Sebelumnya</span>
            @else
                <a href="{{ $anak->previousPageUrl() }}" class="px-4 py-2 text-primary font-bold text-sm active:scale-95 transition-all">Sebelumnya</a>
            @endif

            <span class="text-xs font-bold text-on-surface-variant">Hal {{ $anak->currentPage() }}</span>

            @if ($anak->hasMorePages())
                <a href="{{ $anak->nextPageUrl() }}" class="px-4 py-2 text-primary font-bold text-sm active:scale-95 transition-all">Selanjutnya</a>
            @else
                <span class="px-4 py-2 text-outline-variant font-semibold text-sm">Selanjutnya</span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
