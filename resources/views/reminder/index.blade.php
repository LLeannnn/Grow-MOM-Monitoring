@extends('layouts.app')
@section('title', 'Reminder')

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1>🔔 Reminder</h1>
        <p>Pengingat imunisasi, posyandu, dan jadwal penting lainnya</p>
    </div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('reminder.create') }}" class="btn btn-primary">+ Buat Reminder</a>
    @endif
</div>

<form method="GET" class="filter-bar">
    <select name="status" style="padding:8px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
        <option value="">Semua Status</option>
        <option value="aktif"   {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
        <option value="selesai" {{ request('status')=='selesai'?'selected':'' }}>Selesai</option>
    </select>
    <select name="tipe" style="padding:8px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;">
        <option value="">Semua Tipe</option>
        @foreach(['imunisasi','posyandu','mpasi','kontrol','lainnya'] as $t)
        <option value="{{ $t }}" {{ request('tipe')==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
</form>

<div class="card fade-up">
    <div class="card-header">
        <div class="card-title">📅 Daftar Reminder ({{ $reminders->total() }})</div>
    </div>
    <div class="card-body" style="padding:0;">
        @forelse($reminders as $r)
        <div style="display:flex;align-items:center;gap:16px;padding:16px 22px;border-bottom:1px solid var(--border);">
            {{-- Tipe Badge --}}
            <div style="width:44px;height:44px;border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;"
                 class="{{ $r->tipe_label['class'] }}">
                {{ $r->tipe_label['icon'] }}
            </div>

            <div style="flex:1;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
                    <span style="font-weight:700;font-size:14px;">{{ $r->judul }}</span>
                    @if($r->status === 'selesai')
                        <span class="badge badge-success" style="font-size:10px;">✅ Selesai</span>
                    @elseif($r->is_expired)
                        <span class="badge badge-danger" style="font-size:10px;">⚠️ Terlambat</span>
                    @else
                        <span class="badge badge-info" style="font-size:10px;">🔔 Aktif</span>
                    @endif
                    @if($r->kirim_sms)
                        <span class="badge badge-purple" style="font-size:10px;">📱 SMS</span>
                    @endif
                </div>
                <div style="font-size:12.5px;color:var(--text-muted);">
                    👶 {{ $r->anak->nama_anak }} • 👩 {{ $r->ibu->nama_ibu }}
                </div>
                <div style="font-size:12.5px;color:var(--text-muted);">
                    📅 {{ $r->tanggal_reminder->format('d F Y, H:i') }}
                </div>
                <div style="font-size:12.5px;margin-top:4px;">{{ $r->pesan }}</div>
            </div>

            <div style="display:flex;gap:6px;flex-shrink:0;">
                @if($r->status === 'aktif')
                <form method="POST" action="{{ route('reminder.selesai', $r) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-outline btn-sm" title="Tandai Selesai">✅</button>
                </form>
                @endif
                @if(auth()->user()->isAdmin())
                <form method="POST" action="{{ route('reminder.destroy', $r) }}" onsubmit="return confirm('Hapus?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm">🗑</button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon">🔔</div>
            <div class="empty-title">Belum ada reminder</div>
            <div class="empty-desc">Buat reminder untuk mengingatkan jadwal imunisasi atau posyandu</div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('reminder.create') }}" class="btn btn-primary">Buat Reminder</a>
            @endif
        </div>
        @endforelse
    </div>
    <div style="padding:16px 22px;">{{ $reminders->withQueryString()->links('vendor.pagination.custom') }}</div>
</div>
@endsection
