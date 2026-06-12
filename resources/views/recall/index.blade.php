@extends('layouts.app')
@section('title', 'Recall Gizi')

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1><i data-feather="clipboard"></i> Recall Gizi</h1>
        <p>Pencatatan asupan makanan harian anak</p>
    </div>
    <a href="{{ route('recall.create') }}" class="btn btn-primary">+ Tambah Asupan</a>
</div>

{{-- Filter --}}
<form method="GET" class="filter-bar">
    <select name="anak_id" style="padding:8px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;min-width:200px;">
        <option value="">Semua Anak</option>
        @foreach($anakList as $a)
        <option value="{{ $a->id }}" {{ request('anak_id')==$a->id?'selected':'' }}>{{ $a->nama_anak }}</option>
        @endforeach
    </select>
    <input type="date" name="tanggal" value="{{ request('tanggal') }}"
           style="padding:8px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    @if(request()->hasAny(['anak_id','tanggal']))
        <a href="{{ route('recall.index') }}" class="btn btn-outline btn-sm">Reset</a>
        @if(request('anak_id'))
        <a href="{{ route('recall.export-pdf') }}?anak_id={{ request('anak_id') }}" class="btn btn-outline btn-sm"><i data-feather="file-text"></i> PDF</a>
        @endif
    @endif
</form>

{{-- Ringkasan Hari Ini --}}
@if($ringkasanHariIni && $ringkasanHariIni->total_kalori > 0)
<div class="card fade-up" style="margin-bottom:20px;">
    <div class="card-header">
        <div class="card-title"><i data-feather="bar-chart-2"></i> Ringkasan Gizi Hari Ini</div>
        <span style="font-size:12px;color:var(--text-muted);">{{ today()->format('d F Y') }}</span>
    </div>
    <div class="card-body">
        <div class="grid-2" style="gap:16px;">
            <div>
                <div class="progress-bar-wrap">
                    <div class="progress-label"><span><i data-feather="zap"></i> Kalori</span><span>{{ number_format($ringkasanHariIni->total_kalori,1) }} / {{ $akg['energi'] ?? 1000 }} kkal</span></div>
                    <div class="progress-track"><div class="progress-fill amber" style="width:{{ min(round($ringkasanHariIni->total_kalori/($akg['energi']??1000)*100),100) }}%"></div></div>
                    <div style="font-size:11px;color:var(--text-muted);">{{ round($ringkasanHariIni->total_kalori/($akg['energi']??1000)*100) }}% dari AKG ({{ $akg['label'] ?? '' }})</div>
                </div>
                <div class="progress-bar-wrap">
                    <div class="progress-label"><span><i data-feather="target"></i> Protein</span><span>{{ number_format($ringkasanHariIni->total_protein,1) }} / {{ $akg['protein'] ?? 20 }} g</span></div>
                    <div class="progress-track"><div class="progress-fill green" style="width:{{ min(round($ringkasanHariIni->total_protein/($akg['protein']??20)*100),100) }}%"></div></div>
                </div>
            </div>
            <div>
                <div class="progress-bar-wrap">
                    <div class="progress-label"><span><i data-feather="layers"></i> Karbohidrat</span><span>{{ number_format($ringkasanHariIni->total_karbo,1) }} / {{ $akg['karbo'] ?? 130 }} g</span></div>
                    <div class="progress-track"><div class="progress-fill purple" style="width:{{ min(round($ringkasanHariIni->total_karbo/($akg['karbo']??130)*100),100) }}%"></div></div>
                </div>
                <div class="progress-bar-wrap">
                    <div class="progress-label"><span><i data-feather="droplet"></i> Lemak</span><span>{{ number_format($ringkasanHariIni->total_lemak,1) }} / {{ $akg['lemak'] ?? 30 }} g</span></div>
                    <div class="progress-track"><div class="progress-fill red" style="width:{{ min(round($ringkasanHariIni->total_lemak/($akg['lemak']??30)*100),100) }}%"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="card fade-up">
    <div class="card-header">
        <div class="card-title"><i data-feather="coffee"></i> Daftar Asupan ({{ $recalls->total() }})</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr>
                <th>Anak</th><th>Tanggal</th><th>Waktu</th><th>Makanan</th>
                <th>Kalori</th><th>Protein</th><th>Karbo</th><th>Lemak</th><th>Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($recalls as $r)
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $r->anak->nama_anak }}</div>
                    </td>
                    <td>{{ $r->tanggal->format('d M Y') }}</td>
                    <td><span class="badge badge-neutral">{{ $r->waktu_makan_label }}</span></td>
                    <td>
                        <div style="font-weight:600;">{{ $r->nama_makanan }}</div>
                        <div style="font-size:11.5px;color:var(--text-muted);">{{ $r->jumlah }} {{ $r->satuan }}</div>
                    </td>
                    <td><span style="font-weight:700;color:var(--accent);">{{ $r->kalori }}</span> kkal</td>
                    <td>{{ $r->protein }} g</td>
                    <td>{{ $r->karbohidrat }} g</td>
                    <td>{{ $r->lemak }} g</td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center;">
                            <a href="{{ route('anak.show', $r->anak) }}" class="btn btn-outline btn-sm" title="Detail Anak"><i data-feather="eye"></i> Detail</a>
                            <form method="POST" action="{{ route('recall.destroy', $r) }}" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i data-feather="trash-2"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9">
                    <div class="empty-state">
                        <div class="empty-icon"><i data-feather="coffee"></i></div>
                        <div class="empty-title">Belum ada data asupan</div>
                        <a href="{{ route('recall.create') }}" class="btn btn-primary btn-sm">Tambah Asupan</a>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 22px;">{{ $recalls->withQueryString()->links('vendor.pagination.custom') }}</div>
</div>
@endsection
