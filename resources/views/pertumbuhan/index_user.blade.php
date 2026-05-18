@extends('layouts.user')
@section('title', 'Pertumbuhan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-on-surface">📈 Pertumbuhan</h1>
            <p class="text-sm text-on-surface-variant mt-1">Riwayat pengukuran BB & TB anak</p>
        </div>
        <a href="{{ route('pertumbuhan.create') }}" class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary active:scale-95 transition-transform">
            <span class="material-symbols-outlined text-2xl">add</span>
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" class="bg-surface-container-lowest p-4 rounded-3xl shadow-[0px_5px_15px_rgba(30,41,59,0.03)] space-y-3 border border-surface-container-low">
        <div class="flex gap-2">
            <select name="anak_id" class="w-1/2 bg-surface-container-low border-transparent rounded-xl text-sm px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold">
                <option value="">Semua Anak</option>
                @foreach($anakList as $a)
                <option value="{{ $a->id }}" {{ request('anak_id')==$a->id?'selected':'' }}>{{ $a->nama_anak }}</option>
                @endforeach
            </select>
            <select name="status_gizi" class="w-1/2 bg-surface-container-low border-transparent rounded-xl text-sm px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold">
                <option value="">Semua Status</option>
                @foreach(['normal'=>'Normal','stunting'=>'Stunting','wasting'=>'Wasting','underweight'=>'Underweight'] as $val=>$label)
                <option value="{{ $val }}" {{ request('status_gizi')==$val?'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-primary text-on-primary py-2.5 rounded-xl text-sm font-bold active:scale-[0.98] transition-transform">Filter</button>
            @if(request()->hasAny(['anak_id','status_gizi']))
                <a href="{{ route('pertumbuhan.index') }}" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-xl text-sm font-bold flex items-center justify-center active:scale-[0.98] transition-transform">Reset</a>
            @endif
        </div>
    </form>

    <!-- List -->
    <div class="space-y-4">
        @forelse($pertumbuhan as $p)
        @php
            $status = $p->status_gizi;
            $statusBg = 'bg-tertiary-container/10 text-tertiary-container';
            $statusBorder = 'border-l-tertiary-container';
            if(in_array($status, ['stunting', 'wasting', 'underweight', 'kurang', 'buruk', 'obesitas'])) {
                $statusBg = 'bg-secondary-container/10 text-secondary-container';
                $statusBorder = 'border-l-secondary-container';
            }
        @endphp
        <div class="bg-surface-container-lowest rounded-[20px] p-5 shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low border-l-4 {{ $statusBorder }}">
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary text-lg">
                        {{ $p->anak->jenis_kelamin === 'L' ? '👦' : '👧' }}
                    </div>
                    <div>
                        <div class="font-bold text-sm text-on-surface">{{ $p->anak->nama_anak }}</div>
                        <div class="text-[11px] text-on-surface-variant font-medium mt-0.5">{{ $p->tanggal_pengukuran->format('d M Y') }}</div>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-1">
                    <span class="{{ $statusBg }} px-2 py-0.5 rounded text-[10px] font-bold uppercase">{{ $p->status_gizi_badge['label'] }}</span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                <div class="bg-surface-container p-3 rounded-xl">
                    <div class="text-[10px] font-bold text-on-surface-variant uppercase mb-1">Berat</div>
                    <div class="font-bold text-primary">{{ $p->berat_badan }} <span class="text-xs font-normal">kg</span></div>
                </div>
                <div class="bg-surface-container p-3 rounded-xl">
                    <div class="text-[10px] font-bold text-on-surface-variant uppercase mb-1">Tinggi</div>
                    <div class="font-bold text-tertiary-container">{{ $p->tinggi_badan }} <span class="text-xs font-normal">cm</span></div>
                </div>
            </div>

            <div class="flex justify-between items-center pt-3 border-t border-surface-container-high">
                <a href="{{ route('anak.show', $p->anak) }}" class="text-xs font-bold text-primary flex items-center gap-1 active:scale-95 transition-transform">
                    <span class="material-symbols-outlined text-sm">visibility</span> Detail Anak
                </a>
                
                <form method="POST" action="{{ route('pertumbuhan.destroy', $p) }}" onsubmit="return confirm('Hapus data ini?')" class="m-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-bold text-error flex items-center gap-1 bg-error/10 px-3 py-1.5 rounded-lg active:scale-95 transition-transform">
                        <span class="material-symbols-outlined text-sm">delete</span> Hapus
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-surface-container-lowest rounded-3xl p-8 text-center shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low">
            <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center text-3xl mb-4">📏</div>
            <div class="font-bold text-on-surface mb-2">Belum ada data</div>
            <div class="text-xs text-on-surface-variant mb-6">Mulai pantau pertumbuhan anak Anda</div>
            <a href="{{ route('pertumbuhan.create') }}" class="w-full justify-center flex items-center gap-2 py-3 bg-primary text-on-primary rounded-xl text-sm font-bold shadow-md active:scale-[0.98] transition-all">
                Input Sekarang
            </a>
        </div>
        @endforelse
    </div>

    @if($pertumbuhan->hasPages())
    <div class="mt-6">
        <div class="flex justify-between items-center bg-surface-container-lowest rounded-xl p-2 shadow-sm">
            @if ($pertumbuhan->onFirstPage())
                <span class="px-4 py-2 text-outline-variant font-semibold text-sm">Sebelumnya</span>
            @else
                <a href="{{ $pertumbuhan->previousPageUrl() }}" class="px-4 py-2 text-primary font-bold text-sm active:scale-95 transition-all">Sebelumnya</a>
            @endif

            <span class="text-xs font-bold text-on-surface-variant">Hal {{ $pertumbuhan->currentPage() }}</span>

            @if ($pertumbuhan->hasMorePages())
                <a href="{{ $pertumbuhan->nextPageUrl() }}" class="px-4 py-2 text-primary font-bold text-sm active:scale-95 transition-all">Selanjutnya</a>
            @else
                <span class="px-4 py-2 text-outline-variant font-semibold text-sm">Selanjutnya</span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
