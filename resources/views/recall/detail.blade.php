@extends('layouts.app')
@section('title', 'Detail Recall Gizi')

@section('content')
@php
$meals = [
    'pagi'  => ['label'=>'Makan Pagi',  'icon'=>'sun',    'color'=>'#f59e0b', 'emoji'=>'🌅'],
    'siang' => ['label'=>'Makan Siang', 'icon'=>'sun',    'color'=>'#16a34a', 'emoji'=>'☀️'],
    'malam' => ['label'=>'Makan Malam', 'icon'=>'moon',   'color'=>'#7c3aed', 'emoji'=>'🌙'],
    'snack' => ['label'=>'Snack',       'icon'=>'coffee', 'color'=>'#ef4444', 'emoji'=>'🍎'],
];
@endphp

<div class="topbar">
    <div class="page-header">
        <h1><i data-feather="clipboard"></i> Detail Recall Gizi</h1>
        <p>{{ $anak->nama_anak }} — {{ $tanggalFormatted->translatedFormat('d F Y') }}</p>
    </div>
    <div class="topbar-actions">
        <a href="{{ route('recall.create') }}?anak_id={{ $anak->id }}" class="btn btn-primary"><i data-feather="plus"></i> Tambah Asupan</a>
        <a href="{{ route('recall.index', ['anak_id' => $anak->id]) }}" class="btn btn-outline"><i data-feather="arrow-left"></i> Kembali</a>
    </div>
</div>

{{-- Grand Total Summary --}}
<div class="card fade-up" style="margin-bottom:20px;">
    <div class="card-header">
        <div class="card-title"><i data-feather="bar-chart-2"></i> Total Gizi Hari Ini</div>
        <span style="font-size:12px;color:var(--text-muted);">{{ $tanggalFormatted->translatedFormat('l, d F Y') }}</span>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;">
            @foreach([
                ['label'=>'Energi',      'val'=>$totals['kalori'],      'akg'=>$akg['energi'], 'satuan'=>'kkal', 'color'=>'#f59e0b', 'icon'=>'zap'],
                ['label'=>'Protein',     'val'=>$totals['protein'],     'akg'=>$akg['protein'], 'satuan'=>'g',    'color'=>'#16a34a', 'icon'=>'target'],
                ['label'=>'Karbohidrat', 'val'=>$totals['karbohidrat'], 'akg'=>$akg['karbo'],   'satuan'=>'g',    'color'=>'#7c3aed', 'icon'=>'layers'],
                ['label'=>'Lemak',       'val'=>$totals['lemak'],       'akg'=>$akg['lemak'],   'satuan'=>'g',    'color'=>'#ef4444', 'icon'=>'droplet'],
            ] as $item)
            @php $pct = $item['akg'] > 0 ? min(round($item['val'] / $item['akg'] * 100), 100) : 0; @endphp
            <div style="background:var(--bg);border-radius:12px;padding:16px;text-align:center;border-left:4px solid {{ $item['color'] }};">
                <div style="font-size:13px;color:var(--text-muted);margin-bottom:4px;"><i data-feather="{{ $item['icon'] }}"></i> {{ $item['label'] }}</div>
                <div style="font-size:24px;font-weight:800;color:{{ $item['color'] }};">{{ number_format($item['val'], 1) }}</div>
                <div style="font-size:11px;color:var(--text-muted);">/ {{ $item['akg'] }} {{ $item['satuan'] }}</div>
                <div class="progress-track" style="margin-top:8px;"><div class="progress-fill" style="width:{{ $pct }}%;background:{{ $item['color'] }};"></div></div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:3px;">{{ $pct }}% AKG</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Meal Sections --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    @foreach($meals as $key => $m)
    <div class="card fade-up">
        <div class="card-header" style="border-bottom:3px solid {{ $m['color'] }};">
            <div class="card-title" style="color:{{ $m['color'] }};"><i data-feather="{{ $m['icon'] }}"></i> {{ $m['emoji'] }} {{ $m['label'] }}</div>
            @if($grouped->has($key))
            @php
                $mealItems = $grouped[$key];
                $mealKal = $mealItems->sum('kalori');
                $mealPro = $mealItems->sum('protein');
            @endphp
            <span style="font-size:11px;color:var(--text-muted);">{{ $mealItems->count() }} item • {{ number_format($mealKal,0) }} kkal</span>
            @endif
        </div>
        <div class="card-body">
            @if($grouped->has($key))
                @foreach($grouped[$key] as $item)
                <div style="display:flex;justify-content:space-between;align-items:start;padding:10px 0;border-bottom:1px solid var(--border);{{ $loop->last ? 'border-bottom:none;' : '' }}">
                    <div style="flex:1;">
                        <div style="font-weight:700;font-size:13px;">{{ $item->nama_makanan }}</div>
                        <div style="font-size:11.5px;color:var(--text-muted);margin-top:2px;">{{ $item->jumlah }} {{ $item->satuan }}</div>
                        <div style="display:flex;gap:10px;margin-top:4px;font-size:11px;color:var(--text-muted);">
                            <span><i data-feather="zap"></i> {{ $item->kalori }} kkal</span>
                            <span><i data-feather="target"></i> {{ $item->protein }}g</span>
                            <span><i data-feather="layers"></i> {{ $item->karbohidrat }}g</span>
                            <span><i data-feather="droplet"></i> {{ $item->lemak }}g</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('recall.destroy', $item) }}" onsubmit="return confirm('Hapus makanan ini?')" style="margin:0;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" style="padding:4px 8px;" title="Hapus"><i data-feather="trash-2"></i></button>
                    </form>
                </div>
                @endforeach

                {{-- Subtotal --}}
                <div style="background:{{ $m['color'] }}10;border-radius:8px;padding:10px 14px;margin-top:10px;font-size:12px;">
                    <div style="display:flex;gap:16px;flex-wrap:wrap;font-weight:600;">
                        <span style="color:{{ $m['color'] }};"><i data-feather="zap"></i> {{ number_format($grouped[$key]->sum('kalori'),1) }} kkal</span>
                        <span><i data-feather="target"></i> {{ number_format($grouped[$key]->sum('protein'),1) }}g pro</span>
                        <span><i data-feather="layers"></i> {{ number_format($grouped[$key]->sum('karbohidrat'),1) }}g karbo</span>
                        <span><i data-feather="droplet"></i> {{ number_format($grouped[$key]->sum('lemak'),1) }}g lemak</span>
                    </div>
                </div>
            @else
                <div style="text-align:center;padding:20px;color:var(--text-muted);">
                    <div style="font-size:24px;margin-bottom:6px;">{{ $m['emoji'] }}</div>
                    <div style="font-size:12px;">Belum ada data {{ strtolower($m['label']) }}</div>
                </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection
