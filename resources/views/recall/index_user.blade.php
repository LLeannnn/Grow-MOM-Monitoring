@extends('layouts.user')
@section('title', 'Recall Gizi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-on-surface"><i data-feather="coffee"></i> Catat Makan</h1>
            <p class="text-sm text-on-surface-variant mt-1">Input asupan gizi harian anak</p>
        </div>
        <a href="{{ route('recall.create') }}" class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary active:scale-95 transition-transform">
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
            <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="w-1/2 bg-surface-container-low border-transparent rounded-xl text-sm px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-primary text-on-primary py-2.5 rounded-xl text-sm font-bold active:scale-[0.98] transition-transform">Filter</button>
            @if(request()->hasAny(['anak_id','tanggal']))
                <a href="{{ route('recall.index') }}" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-xl text-sm font-bold flex items-center justify-center active:scale-[0.98] transition-transform">Reset</a>
            @endif
        </div>
    </form>

    <!-- Ringkasan Hari Ini -->
    @if($ringkasanHariIni && $ringkasanHariIni->total_kalori > 0)
    <div class="bg-surface-container-lowest rounded-3xl p-6 shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-base font-bold text-on-surface flex items-center gap-2">
                <span class="text-xl"><i data-feather="bar-chart-2"></i></span> Ringkasan Gizi Hari Ini
            </h2>
            <span class="text-[11px] font-semibold text-on-surface-variant bg-surface-container py-1 px-3 rounded-full">{{ today()->format('d M Y') }}</span>
        </div>

        <div class="space-y-5">
            <!-- Kalori -->
            <div>
                <div class="flex justify-between text-sm mb-1.5 font-semibold">
                    <span class="text-on-surface"><i data-feather="zap"></i> Kalori</span>
                    <span class="text-primary">{{ number_format($ringkasanHariIni->total_kalori,0) }} / {{ $akg['energi'] ?? 1000 }} kkal</span>
                </div>
                <div class="h-2.5 bg-surface-container-low rounded-full overflow-hidden">
                    @php $pctKal = min(round($ringkasanHariIni->total_kalori/($akg['energi']??1000)*100),100); @endphp
                    <div class="h-full bg-[#f59e0b] rounded-full" style="width:{{ $pctKal }}%"></div>
                </div>
                <p class="text-[10px] text-on-surface-variant font-medium mt-1">{{ round($ringkasanHariIni->total_kalori/($akg['energi']??1000)*100) }}% dari AKG ({{ $akg['label'] ?? '' }})</p>
            </div>

            <!-- Makro Nutrien -->
            <div class="grid grid-cols-3 gap-4">
                <!-- Protein -->
                <div>
                    <div class="text-[10px] font-bold text-on-surface-variant mb-1 flex justify-between">
                        <span><i data-feather="target"></i> Pro</span>
                        <span>{{ number_format($ringkasanHariIni->total_protein,1) }}g</span>
                    </div>
                    <div class="h-1.5 bg-surface-container-low rounded-full overflow-hidden">
                        @php $pctPro = min(round($ringkasanHariIni->total_protein/($akg['protein']??20)*100),100); @endphp
                        <div class="h-full bg-[#16a34a] rounded-full" style="width:{{ $pctPro }}%"></div>
                    </div>
                </div>
                <!-- Karbo -->
                <div>
                    <div class="text-[10px] font-bold text-on-surface-variant mb-1 flex justify-between">
                        <span><i data-feather="layers"></i> Karbo</span>
                        <span>{{ number_format($ringkasanHariIni->total_karbo,1) }}g</span>
                    </div>
                    <div class="h-1.5 bg-surface-container-low rounded-full overflow-hidden">
                        @php $pctKar = min(round($ringkasanHariIni->total_karbo/($akg['karbo']??130)*100),100); @endphp
                        <div class="h-full bg-[#9333ea] rounded-full" style="width:{{ $pctKar }}%"></div>
                    </div>
                </div>
                <!-- Lemak -->
                <div>
                    <div class="text-[10px] font-bold text-on-surface-variant mb-1 flex justify-between">
                        <span><i data-feather="droplet"></i> Lemak</span>
                        <span>{{ number_format($ringkasanHariIni->total_lemak,1) }}g</span>
                    </div>
                    <div class="h-1.5 bg-surface-container-low rounded-full overflow-hidden">
                        @php $pctLem = min(round($ringkasanHariIni->total_lemak/($akg['lemak']??30)*100),100); @endphp
                        <div class="h-full bg-[#ef4444] rounded-full" style="width:{{ $pctLem }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- List Asupan -->
    <div class="space-y-4">
        @forelse($recalls as $r)
        <div class="bg-surface-container-lowest rounded-[24px] p-5 shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <span class="inline-block bg-primary/10 text-primary px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide mb-2">{{ $r->waktu_makan_label }}</span>
                    <h3 class="text-base font-bold text-on-surface leading-tight">{{ $r->nama_makanan }}</h3>
                    <p class="text-xs text-on-surface-variant font-medium mt-1">{{ $r->jumlah }} {{ $r->satuan }} • {{ $r->anak->nama_anak }}</p>
                </div>
                <div class="text-right">
                    <span class="text-lg font-extrabold text-[#f59e0b]">{{ $r->kalori }}</span> <span class="text-[10px] text-on-surface-variant font-medium">kkal</span>
                    <p class="text-[10px] text-on-surface-variant font-semibold mt-1">{{ $r->tanggal->format('d M Y') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2 bg-surface-container p-3 rounded-2xl text-center text-xs font-semibold text-on-surface-variant mb-4">
                <div>
                    <span class="text-[9px] block text-on-surface-variant/60 font-bold uppercase">Pro</span>
                    {{ $r->protein }}g
                </div>
                <div>
                    <span class="text-[9px] block text-on-surface-variant/60 font-bold uppercase">Karbo</span>
                    {{ $r->karbohidrat }}g
                </div>
                <div>
                    <span class="text-[9px] block text-on-surface-variant/60 font-bold uppercase">Lemak</span>
                    {{ $r->lemak }}g
                </div>
            </div>

            <div class="flex justify-between items-center pt-3 border-t border-surface-container-high">
                <a href="{{ route('anak.show', $r->anak) }}" class="text-xs font-bold text-primary flex items-center gap-1 active:scale-95 transition-transform">
                    <span class="material-symbols-outlined text-sm">visibility</span> Detail Anak
                </a>
                
                <form method="POST" action="{{ route('recall.destroy', $r) }}" onsubmit="return confirm('Hapus data ini?')" class="m-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-bold text-error flex items-center gap-1 bg-error/10 px-3 py-1.5 rounded-lg active:scale-95 transition-transform">
                        <span class="material-symbols-outlined text-sm">delete</span> Hapus
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-surface-container-lowest rounded-3xl p-8 text-center shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low">
            <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center text-3xl mb-4"><i data-feather="coffee"></i></div>
            <div class="font-bold text-on-surface mb-2">Belum ada data asupan</div>
            <div class="text-xs text-on-surface-variant mb-6">Catat asupan gizi anak hari ini</div>
            <a href="{{ route('recall.create') }}" class="w-full justify-center flex items-center gap-2 py-3 bg-primary text-on-primary rounded-xl text-sm font-bold shadow-md active:scale-[0.98] transition-all">
                Input Sekarang
            </a>
        </div>
        @endforelse
    </div>

    @if($recalls->hasPages())
    <div class="mt-6">
        <div class="flex justify-between items-center bg-surface-container-lowest rounded-xl p-2 shadow-sm">
            @if ($recalls->onFirstPage())
                <span class="px-4 py-2 text-outline-variant font-semibold text-sm">Sebelumnya</span>
            @else
                <a href="{{ $recalls->previousPageUrl() }}" class="px-4 py-2 text-primary font-bold text-sm active:scale-95 transition-all">Sebelumnya</a>
            @endif

            <span class="text-xs font-bold text-on-surface-variant">Hal {{ $recalls->currentPage() }}</span>

            @if ($recalls->hasMorePages())
                <a href="{{ $recalls->nextPageUrl() }}" class="px-4 py-2 text-primary font-bold text-sm active:scale-95 transition-all">Selanjutnya</a>
            @else
                <span class="px-4 py-2 text-outline-variant font-semibold text-sm">Selanjutnya</span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
