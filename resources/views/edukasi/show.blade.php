@extends('layouts.app')
@section('title', $edukasi->judul)

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1>🥕 Edukasi MPASI</h1>
    </div>
    <div class="topbar-actions">
        @if(auth()->user()->isAdmin())
        <div style="display:flex;gap:8px;">
            <a href="{{ route('edukasi.edit', $edukasi) }}" class="btn btn-warning btn-sm">✏️ Edit</a>
            <form method="POST" action="{{ route('edukasi.destroy', $edukasi) }}" onsubmit="return confirm('Hapus artikel ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm">🗑 Hapus</button>
            </form>
        </div>
        @endif
        <a href="{{ route('edukasi.index') }}" class="btn btn-outline">← Kembali</a>
    </div>
</div>

<div class="grid-2" style="gap:20px;align-items:start;">
    <div class="card fade-up" style="grid-column:1/3;">
        {{-- Header Artikel --}}
        @if($edukasi->gambar)
        <div style="width:100%;height:280px;overflow:hidden;border-radius:var(--radius-lg) var(--radius-lg) 0 0;">
            <img src="{{ asset('storage/'.$edukasi->gambar) }}" style="width:100%;height:100%;object-fit:cover;">
        </div>
        @else
        <div style="width:100%;height:180px;background:linear-gradient(135deg,var(--primary-light),var(--secondary-light));display:flex;align-items:center;justify-content:center;font-size:72px;border-radius:var(--radius-lg) var(--radius-lg) 0 0;">🥣</div>
        @endif

        <div class="card-body">
            <div style="display:flex;gap:10px;align-items:center;margin-bottom:12px;flex-wrap:wrap;">
                <span class="badge badge-success">🍼 {{ $edukasi->kategori_label }}</span>
                <span style="font-size:12px;color:var(--text-muted);">📅 {{ $edukasi->created_at->format('d F Y') }}</span>
                @foreach($edukasi->tags_array as $tag)
                    @if($tag)
                    <span style="background:var(--bg);padding:2px 10px;border-radius:100px;font-size:11.5px;color:var(--text-muted);">#{{ trim($tag) }}</span>
                    @endif
                @endforeach
            </div>

            <h1 style="font-size:26px;font-weight:800;margin-bottom:20px;line-height:1.3;color:var(--text-main);">{{ $edukasi->judul }}</h1>

            @if($edukasi->tekstur_makanan || $edukasi->bahan_makanan)
            <div style="background:var(--bg);padding:16px;border-radius:var(--radius-md);margin-bottom:20px;border-left:4px solid var(--primary);">
                @if($edukasi->tekstur_makanan)
                <div style="margin-bottom:12px;">
                    <strong>🥣 Tekstur Makanan:</strong>
                    <div style="margin-top:4px;color:var(--text-main);">{{ $edukasi->tekstur_makanan }}</div>
                </div>
                @endif
                @if($edukasi->bahan_makanan)
                <div>
                    <strong>🥕 Bahan Makanan:</strong>
                    <div style="margin-top:4px;color:var(--text-main);white-space:pre-wrap;">{{ $edukasi->bahan_makanan }}</div>
                </div>
                @endif
            </div>
            @endif

            <div style="font-size:15px;line-height:1.85;color:var(--text-main);">
                {!! nl2br(e($edukasi->konten)) !!}
            </div>
        </div>
    </div>
</div>

@if($related->count())
<div style="margin-top:24px;">
    <h2 style="font-size:16px;font-weight:700;margin-bottom:16px;">📚 Artikel Terkait</h2>
    <div class="edukasi-grid">
        @foreach($related as $r)
        <a href="{{ route('edukasi.show', $r) }}" class="edukasi-card">
            <div class="edukasi-img">
                @if($r->gambar)
                    <img src="{{ asset('storage/'.$r->gambar) }}" style="width:100%;height:170px;object-fit:cover;">
                @else
                    🥣
                @endif
            </div>
            <div class="edukasi-body">
                <span class="badge badge-success">{{ $r->kategori_label }}</span>
                <div class="edukasi-title">{{ $r->judul }}</div>
                <div class="edukasi-desc">{{ $r->konten_ringkas }}</div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif
@endsection
