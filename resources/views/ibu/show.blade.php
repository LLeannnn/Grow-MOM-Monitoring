@extends('layouts.app')
@section('title', $ibu->nama_ibu)

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1><i data-feather="user"></i> Detail Ibu</h1>
        <p>Profil lengkap dan data anak</p>
    </div>
    <div class="topbar-actions">
        <a href="{{ route('ibu.edit', $ibu) }}" class="btn btn-outline"><i data-feather="edit-2"></i> Edit</a>
        <a href="{{ route('ibu.index') }}" class="btn btn-outline"><i data-feather="arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="grid-2" style="gap:20px;align-items:start;">
    {{-- Profil Ibu --}}
    <div class="card fade-up">
        <div class="card-header">
            <div class="card-title"><i data-feather="clipboard"></i> Profil Ibu</div>
        </div>
        <div class="card-body">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border);">
                @if($ibu->foto)
                    <img src="{{ asset('storage/'.$ibu->foto) }}" class="avatar lg" style="object-fit:cover;">
                @else
                    <div class="avatar lg">{{ strtoupper(substr($ibu->nama_ibu,0,1)) }}</div>
                @endif
                <div>
                    <div style="font-size:20px;font-weight:800;">{{ $ibu->nama_ibu }}</div>
                    <div style="color:var(--text-muted);font-size:13px;">{{ $ibu->umur }} tahun • {{ $ibu->status_pernikahan }}</div>
                </div>
            </div>
            <div class="detail-row"><span class="detail-label">NIK</span><span class="detail-value">{{ $ibu->nik }}</span></div>
            <div class="detail-row"><span class="detail-label">Tanggal Lahir</span><span class="detail-value">{{ $ibu->tanggal_lahir->format('d F Y') }}</span></div>
            <div class="detail-row"><span class="detail-label">No. WhatsApp</span><span class="detail-value">{{ $ibu->no_telepon }}</span></div>
            <div class="detail-row"><span class="detail-label">Pekerjaan</span><span class="detail-value">{{ $ibu->pekerjaan_label }}</span></div>
            <div class="detail-row"><span class="detail-label">Pendidikan</span><span class="detail-value">{{ $ibu->pendidikan_label }}</span></div>
            <div class="detail-row"><span class="detail-label">Alamat</span><span class="detail-value">{{ $ibu->alamat }}</span></div>
        </div>
    </div>

    {{-- Daftar Anak --}}
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="card fade-up">
            <div class="card-header">
                <div class="card-title"><i data-feather="smile"></i> Daftar Anak ({{ $ibu->anak->count() }})</div>
                <a href="{{ route('anak.create') }}?ibu_id={{ $ibu->id }}" class="btn btn-primary btn-sm">+ Tambah</a>
            </div>
            <div class="card-body" style="padding:0;">
                @forelse($ibu->anak as $anak)
                <div style="display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid var(--border);">
                    <div style="font-size:28px;">{!! $anak->jenis_kelamin === 'L' ? '<i data-feather="user"></i>' : '<i data-feather="user"></i>' !!}</div>
                    <div style="flex:1;">
                        <div style="font-weight:700;">{{ $anak->nama_anak }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">{{ $anak->umur_label }} • {{ $anak->jenis_kelamin_label }}</div>
                        @if($anak->pertumbuhan_terakhir)
                        <div style="font-size:12px;color:var(--text-muted);">
                            BB: {{ $anak->pertumbuhan_terakhir->berat_badan }}kg •
                            TB: {{ $anak->pertumbuhan_terakhir->tinggi_badan }}cm •
                            <span class="badge badge-{{ $anak->pertumbuhan_terakhir->status_gizi_badge['class'] === 'badge-success' ? 'success' : 'warning' }}" style="font-size:10px;">
                                {{ $anak->pertumbuhan_terakhir->status_gizi }}
                            </span>
                        </div>
                        @endif
                    </div>
                    <a href="{{ route('anak.show', $anak) }}" class="btn btn-outline btn-sm">Detail</a>
                </div>
                @empty
                <div class="empty-state" style="padding:24px;">
                    <div class="empty-icon" style="font-size:32px;"><i data-feather="smile"></i></div>
                    <div class="empty-title" style="font-size:14px;">Belum ada data anak</div>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Reminder --}}
        <div class="card fade-up">
            <div class="card-header">
                <div class="card-title"><i data-feather="bell"></i> Reminder Aktif</div>
                <a href="{{ route('reminder.create') }}" class="btn btn-outline btn-sm">+ Buat</a>
            </div>
            <div class="card-body" style="padding:0;">
                @forelse($ibu->reminders->where('status','aktif')->take(3) as $r)
                <div style="padding:12px 20px;border-bottom:1px solid var(--border);font-size:13px;">
                    <div style="font-weight:600;">{{ $r->tipe_label['icon'] }} {{ $r->judul }}</div>
                    <div style="color:var(--text-muted);font-size:12px;">{{ $r->tanggal_reminder->format('d M Y, H:i') }}</div>
                </div>
                @empty
                <div style="padding:16px 20px;color:var(--text-muted);font-size:13px;">Tidak ada reminder aktif</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
