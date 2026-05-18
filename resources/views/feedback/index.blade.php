@extends('layouts.app')
@section('title', 'Feedback & Rekomendasi Otomatis')

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1>💬 Feedback & Rekomendasi Otomatis</h1>
        <p>Analisis personal berbasis data pertumbuhan dan pola makan anak</p>
    </div>
    <div class="topbar-actions">
        <div class="search-bar" style="width:260px;">
            <span class="icon">🔍</span>
            <form method="GET" style="flex:1;">
                <input type="text" name="search" placeholder="Cari nama anak..." value="{{ request('search') }}">
            </form>
        </div>
    </div>
</div>

{{-- INFO BANNER --}}
<div style="background:linear-gradient(135deg,#0f2419,#1a3a28);border-radius:var(--radius-lg);padding:22px 26px;margin-bottom:24px;display:flex;align-items:center;gap:20px;">
    <div style="font-size:42px;">🤖</div>
    <div>
        <div style="color:#fff;font-weight:700;font-size:15px;margin-bottom:4px;">Sistem Feedback Otomatis GROW-MOM</div>
        <div style="color:rgba(255,255,255,0.65);font-size:13px;line-height:1.6;">
            Sistem menganalisis data <strong style="color:#4ade80;">monitoring pertumbuhan</strong>,
            <strong style="color:#4ade80;">recall gizi 7 hari terakhir</strong>, dan
            <strong style="color:#4ade80;">frekuensi pemberian makan</strong> untuk menghasilkan
            rekomendasi personal yang membantu ibu memperbaiki praktik pemberian makan anak.
        </div>
    </div>
</div>

{{-- GRID ANAK --}}
@if($anakList->isEmpty())
<div class="card">
    <div class="empty-state">
        <div class="empty-icon">👶</div>
        <div class="empty-title">Belum ada data anak</div>
        <div class="empty-desc">Tambahkan data anak terlebih dahulu untuk mendapatkan feedback otomatis.</div>
        <a href="{{ route('anak.create') }}" class="btn btn-primary">+ Tambah Anak</a>
    </div>
</div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px;">
    @foreach($anakList as $anak)
    @php $r = $ringkasanFeedback[$anak->id]; @endphp
    <a href="{{ route('feedback.show', $anak) }}" class="card fade-up" style="text-decoration:none;color:inherit;transition:all 0.2s;display:block;"
       onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--shadow-lg)'"
       onmouseout="this.style.transform='';this.style.boxShadow=''">

        {{-- Header anak --}}
        <div style="padding:20px 22px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:14px;">
            <div class="anak-avatar" style="width:52px;height:52px;font-size:26px;margin:0;flex-shrink:0;">
                {{ $anak->jenis_kelamin === 'L' ? '👦' : '👧' }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-weight:700;font-size:15px;">{{ $anak->nama_anak }}</div>
                <div style="font-size:12px;color:var(--text-muted);">{{ $anak->umur_label }} • {{ $anak->ibu->nama_ibu }}</div>
            </div>
            <span class="badge {{ $r['badge']['class'] }}">{{ $r['badge']['label'] }}</span>
        </div>

        {{-- Status data --}}
        <div style="padding:16px 22px;">
            <div style="display:flex;gap:10px;margin-bottom:14px;">
                <div style="flex:1;background:var(--bg);border-radius:8px;padding:10px 12px;text-align:center;">
                    <div style="font-size:18px;">{{ $anak->pertumbuhan->isNotEmpty() ? '✅' : '❌' }}</div>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">Data BB/TB</div>
                </div>
                <div style="flex:1;background:var(--bg);border-radius:8px;padding:10px 12px;text-align:center;">
                    <div style="font-size:18px;">{{ $r['has_recall'] ? '✅' : '❌' }}</div>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">Recall 7 Hari</div>
                </div>
                <div style="flex:1;background:var(--bg);border-radius:8px;padding:10px 12px;text-align:center;">
                    <div style="font-size:15px;font-weight:800;color:{{ $r['skor'] >= 100 ? 'var(--primary)' : ($r['skor'] >= 50 ? '#f59e0b' : 'var(--danger)') }};">{{ $r['skor'] }}%</div>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">Kelengkapan</div>
                </div>
            </div>

            {{-- Progress kelengkapan --}}
            <div class="progress-track">
                <div class="progress-fill {{ $r['skor'] >= 100 ? 'green' : ($r['skor'] >= 50 ? 'amber' : 'red') }}"
                     style="width:{{ $r['skor'] }}%"></div>
            </div>

            <div style="margin-top:14px;display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:12px;color:var(--text-muted);">Klik untuk lihat rekomendasi lengkap</span>
                <span style="font-size:18px;color:var(--primary);">→</span>
            </div>
        </div>
    </a>
    @endforeach
</div>

<div style="margin-top:20px;padding:16px 20px;background:var(--bg);border-radius:var(--radius);font-size:12.5px;color:var(--text-muted);text-align:center;">
    💡 <strong>Tips:</strong> Lengkapi data pertumbuhan dan recall gizi harian untuk mendapatkan rekomendasi yang lebih akurat dan personal.
</div>
@endif
@endsection
