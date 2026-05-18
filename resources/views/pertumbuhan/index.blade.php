@extends('layouts.app')
@section('title', 'Monitoring Pertumbuhan')

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1>📈 Monitoring Pertumbuhan</h1>
        <p>Riwayat pengukuran berat badan, tinggi, dan status gizi anak</p>
    </div>
    <a href="{{ route('pertumbuhan.create') }}" class="btn btn-primary">+ Input Pengukuran</a>
</div>

{{-- Filter --}}
<form method="GET" class="filter-bar">
    <select name="anak_id" style="padding:8px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;min-width:200px;">
        <option value="">Semua Anak</option>
        @foreach($anakList as $a)
        <option value="{{ $a->id }}" {{ request('anak_id')==$a->id?'selected':'' }}>{{ $a->nama_anak }}</option>
        @endforeach
    </select>
    <select name="status_gizi" style="padding:8px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
        <option value="">Semua Status</option>
        @foreach(['normal'=>'Normal','stunting'=>'Stunting','wasting'=>'Wasting','underweight'=>'Underweight'] as $val=>$label)
        <option value="{{ $val }}" {{ request('status_gizi')==$val?'selected':'' }}>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    @if(request()->hasAny(['anak_id','status_gizi']))
        <a href="{{ route('pertumbuhan.index') }}" class="btn btn-outline btn-sm">Reset</a>
        @if(request('anak_id'))
        <a href="{{ route('pertumbuhan.export-pdf') }}?anak_id={{ request('anak_id') }}" class="btn btn-outline btn-sm">📄 Export PDF</a>
        @endif
    @endif
</form>

<div class="card fade-up">
    <div class="card-header">
        <div class="card-title">📊 Data Pengukuran ({{ $pertumbuhan->total() }})</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr>
                <th>Anak</th><th>Tanggal</th><th>BB (kg)</th><th>TB (cm)</th>
                <th>Status Gizi</th><th>Catatan</th><th>Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($pertumbuhan as $p)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $p->anak->nama_anak }}</div>
                        <div style="font-size:11.5px;color:var(--text-muted);">{{ $p->anak->ibu->nama_ibu }}</div>
                    </td>
                    <td>{{ $p->tanggal_pengukuran->format('d M Y') }}</td>
                    <td><strong>{{ $p->berat_badan }}</strong></td>
                    <td>{{ $p->tinggi_badan }}</td>
                    <td>
                        @php $b = $p->status_gizi_badge; @endphp
                        <span class="badge {{ $b['class'] }}">{{ $b['label'] }}</span>
                    </td>
                    <td style="font-size:12px;color:var(--text-muted);max-width:150px;">{{ Str::limit($p->catatan, 50) ?? '-' }}</td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center;">
                            <a href="{{ route('anak.show', $p->anak) }}" class="btn btn-outline btn-sm" title="Detail Anak">👁 Detail</a>
                            <a href="{{ route('pertumbuhan.show', $p) }}" class="btn btn-outline btn-sm" title="Analisis WHO">📊 WHO</a>
                            <form method="POST" action="{{ route('pertumbuhan.destroy', $p) }}"
                                  onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">🗑</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9">
                    <div class="empty-state">
                        <div class="empty-icon">📏</div>
                        <div class="empty-title">Belum ada data pengukuran</div>
                        <a href="{{ route('pertumbuhan.create') }}" class="btn btn-primary btn-sm">Input Sekarang</a>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 22px;">{{ $pertumbuhan->withQueryString()->links('vendor.pagination.custom') }}</div>
</div>
@endsection
