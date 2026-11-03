@extends('layouts.user')
@section('title', 'Detail Recall Gizi')

@section('content')
@php
$meals = [
    'pagi'  => ['label'=>'Makan Pagi',  'icon'=>'wb_sunny',   'color'=>'text-amber-500',  'bg'=>'bg-amber-500/10',  'border'=>'border-amber-500', 'emoji'=>'🌅'],
    'siang' => ['label'=>'Makan Siang', 'icon'=>'light_mode',  'color'=>'text-green-500',  'bg'=>'bg-green-500/10',  'border'=>'border-green-500',  'emoji'=>'☀️'],
    'malam' => ['label'=>'Makan Malam', 'icon'=>'nights_stay', 'color'=>'text-purple-500', 'bg'=>'bg-purple-500/10', 'border'=>'border-purple-500', 'emoji'=>'🌙'],
    'snack' => ['label'=>'Snack',       'icon'=>'cookie',      'color'=>'text-red-500',    'bg'=>'bg-red-500/10',    'border'=>'border-red-500',    'emoji'=>'🍎'],
];
@endphp

<div class="space-y-6 pb-24">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('recall.index', ['anak_id' => $anak->id]) }}" class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-on-surface-variant active:scale-95 transition-transform">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-bold text-on-surface">{{ $tanggalFormatted->translatedFormat('d M Y') }}</h1>
            <p class="text-xs text-on-surface-variant">{{ $anak->nama_anak }} • {{ $tanggalFormatted->translatedFormat('l') }}</p>
        </div>
        <a href="{{ route('recall.create') }}?anak_id={{ $anak->id }}" class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary active:scale-95 transition-transform">
            <span class="material-symbols-outlined">add</span>
        </a>
    </div>

    <!-- Grand Total -->
    <div class="bg-surface-container-lowest rounded-3xl p-5 shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low">
        <h2 class="text-sm font-bold text-on-surface flex items-center gap-1.5 mb-4">
            <span class="material-symbols-outlined text-base">bar_chart</span> Total Gizi
        </h2>
        <div class="grid grid-cols-2 gap-3">
            @php
                $statsArr = [
                    ['label'=>'Kalori',  'val'=>$totals['kalori'],      'akg'=>$akg['energi'], 'satuan'=>'kkal', 'color'=>'bg-amber-500', 'emoji'=>'⚡'],
                    ['label'=>'Protein', 'val'=>$totals['protein'],     'akg'=>$akg['protein'], 'satuan'=>'g',    'color'=>'bg-green-500', 'emoji'=>'🥩'],
                    ['label'=>'Karbo',   'val'=>$totals['karbohidrat'], 'akg'=>$akg['karbo'],   'satuan'=>'g',    'color'=>'bg-purple-500','emoji'=>'🌾'],
                    ['label'=>'Lemak',   'val'=>$totals['lemak'],       'akg'=>$akg['lemak'],   'satuan'=>'g',    'color'=>'bg-red-500',   'emoji'=>'🫒'],
                ];
            @endphp
            @foreach($statsArr as $stat)
            @php $pct = $stat['akg'] > 0 ? min(round($stat['val'] / $stat['akg'] * 100), 100) : 0; @endphp
            <div class="bg-surface-container p-3.5 rounded-2xl">
                <div class="text-[10px] font-bold text-on-surface-variant mb-1">{{ $stat['emoji'] }} {{ $stat['label'] }}</div>
                <div class="text-lg font-extrabold text-on-surface">{{ number_format($stat['val'], 1) }} <span class="text-[10px] font-bold text-on-surface-variant">{{ $stat['satuan'] }}</span></div>
                <div class="h-1.5 {{ $stat['color'] }}/20 rounded-full overflow-hidden mt-1.5">
                    <div class="h-full {{ $stat['color'] }} rounded-full" style="width:{{ $pct }}%"></div>
                </div>
                <div class="text-[9px] font-bold text-on-surface-variant mt-1">{{ $pct }}% dari AKG ({{ $stat['akg'] }} {{ $stat['satuan'] }})</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Meal Sections -->
    @foreach($meals as $key => $m)
    <div class="bg-surface-container-lowest rounded-3xl shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low overflow-hidden">
        <!-- Meal Header -->
        <div class="flex justify-between items-center px-5 py-4 {{ $m['bg'] }} border-b {{ $m['border'] }}/20">
            <span class="text-sm font-bold flex items-center gap-2 {{ $m['color'] }}">
                <span class="material-symbols-outlined">{{ $m['icon'] }}</span> {{ $m['emoji'] }} {{ $m['label'] }}
            </span>
            @if($grouped->has($key))
            <span class="text-[10px] font-bold text-on-surface-variant bg-white/60 px-2.5 py-0.5 rounded-full">
                {{ $grouped[$key]->count() }} item
            </span>
            @endif
        </div>

        <div class="p-5">
            @if($grouped->has($key))
                @foreach($grouped[$key] as $item)
                <div class="flex justify-between items-start {{ !$loop->last ? 'pb-4 mb-4 border-b border-surface-container-high' : '' }}">
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-on-surface">{{ $item->nama_makanan }}</h4>
                        <p class="text-[11px] text-on-surface-variant font-medium mt-0.5">{{ $item->jumlah }} {{ $item->satuan }}</p>
                        <div class="flex gap-3 mt-2 text-[10px] font-bold text-on-surface-variant">
                            <span class="inline-flex items-center gap-0.5 bg-amber-500/8 text-amber-600 px-2 py-0.5 rounded-full">⚡ {{ $item->kalori }} kkal</span>
                            <span class="inline-flex items-center gap-0.5 bg-green-500/8 text-green-600 px-2 py-0.5 rounded-full">🥩 {{ $item->protein }}g</span>
                            <span class="inline-flex items-center gap-0.5 bg-purple-500/8 text-purple-600 px-2 py-0.5 rounded-full">🌾 {{ $item->karbohidrat }}g</span>
                            <span class="inline-flex items-center gap-0.5 bg-red-500/8 text-red-600 px-2 py-0.5 rounded-full">🫒 {{ $item->lemak }}g</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('recall.destroy', $item) }}" onsubmit="return confirm('Hapus makanan ini?')" class="m-0 ml-2">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-8 h-8 rounded-full bg-error/10 text-error flex items-center justify-center active:scale-90 transition-all">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </form>
                </div>
                @endforeach

                <!-- Subtotal per meal -->
                <div class="mt-4 p-3 rounded-2xl {{ $m['bg'] }} {{ $m['color'] }} text-xs font-bold">
                    <div class="grid grid-cols-4 gap-2 text-center">
                        <div>
                            <span class="text-[9px] block opacity-70 uppercase mb-0.5">⚡ Kal</span>
                            {{ number_format($grouped[$key]->sum('kalori'),1) }}
                        </div>
                        <div>
                            <span class="text-[9px] block opacity-70 uppercase mb-0.5">🥩 Pro</span>
                            {{ number_format($grouped[$key]->sum('protein'),1) }}g
                        </div>
                        <div>
                            <span class="text-[9px] block opacity-70 uppercase mb-0.5">🌾 Karbo</span>
                            {{ number_format($grouped[$key]->sum('karbohidrat'),1) }}g
                        </div>
                        <div>
                            <span class="text-[9px] block opacity-70 uppercase mb-0.5">🫒 Lemak</span>
                            {{ number_format($grouped[$key]->sum('lemak'),1) }}g
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-6">
                    <div class="text-3xl mb-2 opacity-40">{{ $m['emoji'] }}</div>
                    <p class="text-xs text-on-surface-variant font-medium">Belum ada data {{ strtolower($m['label']) }}</p>
                </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection
