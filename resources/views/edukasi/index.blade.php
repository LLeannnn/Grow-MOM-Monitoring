@extends('layouts.app')
@section('title', 'Edukasi MPASI')

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1><i data-feather="book-open"></i> Edukasi MPASI</h1>
        <p>Panduan lengkap pemberian makanan pendamping ASI</p>
    </div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('edukasi.create') }}" class="btn btn-primary">+ Tambah Artikel</a>
    @endif
</div>

{{-- Filter Kategori --}}
<div class="filter-bar">
    <a href="{{ route('edukasi.index') }}" class="btn {{ !request('kategori') ? 'btn-primary' : 'btn-outline' }} btn-sm">Semua</a>
    @foreach($kategoriList as $val => $label)
    <a href="{{ route('edukasi.index') }}?kategori={{ $val }}" class="btn {{ request('kategori')==$val ? 'btn-primary' : 'btn-outline' }} btn-sm">{{ $label }}</a>
    @endforeach
    <form method="GET" style="margin-left:auto;display:flex;gap:8px;">
        @if(request('kategori'))
            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
        @endif
        <div class="search-bar">
            <span class="icon"><i data-feather="search"></i></span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel...">
        </div>
    </form>
</div>

<div class="edukasi-grid">
    @forelse($edukasi as $e)
    <a href="{{ route('edukasi.show', $e) }}" class="edukasi-card fade-up">
        <div class="edukasi-img">
            @if($e->gambar)
                <img src="{{ asset('storage/'.$e->gambar) }}" style="width:100%;height:170px;object-fit:cover;">
            @else
                <i data-feather="coffee"></i>
            @endif
        </div>
        <div class="edukasi-body">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span class="badge badge-success">{{ $e->kategori_label }}</span>
                <span style="font-size:11px;color:var(--text-muted);">{{ $e->created_at->format('d M Y') }}</span>
            </div>
            <div class="edukasi-title">{{ $e->judul }}</div>
            <div class="edukasi-desc">{{ $e->konten_ringkas }}</div>
            @if($e->tags_array)
            <div style="margin-top:10px;display:flex;flex-wrap:wrap;gap:5px;">
                @foreach(array_slice($e->tags_array,0,3) as $tag)
                <span style="background:var(--bg);padding:2px 8px;border-radius:100px;font-size:11px;color:var(--text-muted);">#{{ trim($tag) }}</span>
                @endforeach
            </div>
            @endif
        </div>
    </a>
    @empty
    <div class="empty-state" style="grid-column:1/-1;">
        <div class="empty-icon"><i data-feather="book-open"></i></div>
        <div class="empty-title">Belum ada konten edukasi</div>
        <div class="empty-desc">Tambahkan artikel edukasi MPASI untuk ibu dan bayi</div>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('edukasi.create') }}" class="btn btn-primary">Tambah Artikel</a>
        @endif
    </div>
    @endforelse
</div>

<div style="margin-top:20px;">{{ $edukasi->withQueryString()->links('vendor.pagination.custom') }}</div>
@endsection
