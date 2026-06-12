@extends('layouts.app')
@section('title', 'Data Anak')

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1><i data-feather="smile"></i> Data Anak</h1>
        <p>Daftar semua anak yang terdaftar dalam sistem</p>
    </div>
    <a href="{{ route('anak.create') }}" class="btn btn-primary">+ Tambah Anak</a>
</div>

{{-- Filter --}}
<form method="GET" class="filter-bar">
    <div class="search-bar" style="flex:1;max-width:320px;">
        <span class="icon"><i data-feather="search"></i></span>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama anak...">
    </div>
    <select name="jenis_kelamin" style="padding:8px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
        <option value="">Semua Jenis Kelamin</option>
        <option value="L" {{ request('jenis_kelamin')=='L'?'selected':'' }}>Laki-laki</option>
        <option value="P" {{ request('jenis_kelamin')=='P'?'selected':'' }}>Perempuan</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    @if(request()->hasAny(['search','jenis_kelamin']))
        <a href="{{ route('anak.index') }}" class="btn btn-outline btn-sm">Reset</a>
    @endif
</form>

<div class="anak-grid">
    @forelse($anak as $a)
    <a href="{{ route('anak.show', $a) }}" class="anak-card fade-up">
        <div class="anak-avatar">
            {!! $a->jenis_kelamin === 'L' ? '<i data-feather="user"></i>' : '<i data-feather="user"></i>' !!}
        </div>
        <div class="anak-name">{{ $a->nama_anak }}</div>
        <div class="anak-sub">{{ $a->umur_label }}</div>
        <div style="margin-top:8px;">
            <span class="badge {{ $a->jenis_kelamin === 'L' ? 'badge-info' : 'badge-purple' }}">
                {{ $a->jenis_kelamin_label }}
            </span>
        </div>
        <div style="margin-top:10px;font-size:12px;color:var(--text-muted);"><i data-feather="user"></i> {{ $a->ibu->nama_ibu }}</div>

        @if($a->pertumbuhan_terakhir)
        <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border);display:flex;gap:12px;justify-content:center;font-size:12px;">
            <div><i data-feather="activity"></i> {{ $a->pertumbuhan_terakhir->berat_badan }} kg</div>
            <div><i data-feather="bar-chart-2"></i> {{ $a->pertumbuhan_terakhir->tinggi_badan }} cm</div>
        </div>
        <div style="margin-top:6px;">
            @php $badge = $a->pertumbuhan_terakhir->status_gizi_badge; @endphp
            <span class="badge {{ $badge['class'] }}" style="font-size:11px;">{{ $badge['label'] }}</span>
        </div>
        @endif
    </a>
    @empty
    <div class="empty-state" style="grid-column:1/-1;">
        <div class="empty-icon"><i data-feather="smile"></i></div>
        <div class="empty-title">Belum ada data anak</div>
        <div class="empty-desc">Tambahkan data anak pertama untuk mulai monitoring</div>
        <a href="{{ route('anak.create') }}" class="btn btn-primary">Tambah Anak</a>
    </div>
    @endforelse
</div>

<div style="margin-top:16px;">
    {{ $anak->withQueryString()->links('vendor.pagination.custom') }}
</div>
@endsection
