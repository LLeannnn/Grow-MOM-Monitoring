@extends('layouts.app')
@section('title', 'Rekomendasi — ' . $anak->nama_anak)

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1><i data-feather="message-circle"></i> Rekomendasi untuk {{ $anak->nama_anak }}</h1>
        <p>{{ $anak->umur_label }} • {{ $anak->jenis_kelamin_label }} • Ibu: {{ $anak->ibu->nama_ibu }}</p>
    </div>
    <div class="topbar-actions">
        <a href="{{ route('pertumbuhan.create') }}?anak_id={{ $anak->id }}" class="btn btn-primary btn-sm"><i data-feather="bar-chart-2"></i> Input Pengukuran</a>
        <a href="{{ route('recall.create') }}?anak_id={{ $anak->id }}" class="btn btn-outline btn-sm"><i data-feather="clipboard"></i> Input Recall</a>
        <a href="{{ route('feedback.index') }}" class="btn btn-outline btn-sm"><i data-feather="arrow-left"></i> Kembali</a>
    </div>
</div>

{{-- NUTRISI SUMMARY CARDS --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;">
    @php
        $nutrisiConfig = [
            'kalori'      => ['icon' => '<i data-feather="zap"></i>', 'label' => 'Kalori', 'satuan' => 'kkal', 'color' => 'green'],
            'protein'     => ['icon' => '<i data-feather="hash"></i>', 'label' => 'Protein', 'satuan' => 'g', 'color' => 'purple'],
            'karbohidrat' => ['icon' => '<i data-feather="hash"></i>', 'label' => 'Karbohidrat', 'satuan' => 'g', 'color' => 'amber'],
            'lemak'       => ['icon' => '<i data-feather="hash"></i>', 'label' => 'Lemak', 'satuan' => 'g', 'color' => 'blue'],
        ];
    @endphp
    @foreach($nutrisiConfig as $key => $cfg)
    @php
        $stat   = $nutrisiStats[$key];
        $pct    = $stat['target'] > 0 ? min(100, round($stat['nilai'] / $stat['target'] * 100)) : 0;
        $barCls = $pct >= 90 ? 'green' : ($pct >= 60 ? 'amber' : 'red');
    @endphp
    <div class="card fade-up">
        <div class="card-body" style="padding:16px;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                <span style="font-size:20px;">{{ $cfg['icon'] }}</span>
                <span style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">{{ $cfg['label'] }}</span>
            </div>
            <div style="font-size:22px;font-weight:800;color:var(--text-main);">
                {{ $stat['nilai'] }} <span style="font-size:13px;font-weight:500;color:var(--text-muted);">{{ $cfg['satuan'] }}</span>
            </div>
            <div style="font-size:11px;color:var(--text-muted);margin:4px 0 8px;">
                Target: {{ $stat['target'] }} {{ $cfg['satuan'] }}/hari
            </div>
            <div class="progress-track">
                <div class="progress-fill {{ $barCls }}" style="width:{{ $pct }}%"></div>
            </div>
            <div style="font-size:11px;font-weight:600;color:{{ $pct >= 90 ? 'var(--primary)' : ($pct >= 60 ? '#d97706' : 'var(--danger)') }};margin-top:4px;">
                {{ $pct }}% terpenuhi
            </div>
        </div>
    </div>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start;">

    {{-- FEEDBACK CARDS --}}
    <div>
        {{-- BAGIAN 1: FEEDBACK MANUAL DARI ADMIN --}}
        <h2 style="font-size:15px;font-weight:700;margin-bottom:14px;display:flex;align-items:center;gap:8px;">
            <i data-feather="edit"></i> Catatan Khusus (Manual)
            <span class="badge badge-neutral" style="font-size:11px;">{{ $anak->feedbackAnak->count() }} catatan</span>
        </h2>

        {{-- Form Tambah Catatan Manual (Hanya Admin) --}}
        @if(auth()->user()->isAdmin())
        <div class="card fade-up" style="margin-bottom: 20px;">
            <div class="card-body" style="padding: 16px;">
                <form action="{{ route('feedback.manual', $anak) }}" method="POST">
                    @csrf
                    <div class="form-group" style="margin-bottom: 10px;">
                        <label for="pesan" style="font-size: 13px;">Tambahkan Catatan / Saran untuk Anak Ini <span class="required">*</span></label>
                        <textarea name="pesan" id="pesan" rows="3" placeholder="Tulis saran gizi, jadwal kontrol, atau catatan lainnya di sini..." required style="padding: 10px; font-size: 13px; min-height: 80px;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary btn-sm">Kirim Catatan</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- List Catatan Manual --}}
        @forelse($anak->feedbackAnak as $fbManual)
        <div style="background:#fff;border:1px solid #e2e8f0;border-left:4px solid var(--secondary);border-radius:var(--radius);padding:16px 20px;margin-bottom:14px;" class="fade-up">
            <div style="display:flex;align-items:flex-start;gap:12px;">
                <div style="width:38px;height:38px;background:var(--secondary-light);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                    <i data-feather="user"></i><i data-feather="activity"></i>
                </div>
                <div style="flex:1;">
                    <div style="display: flex; justify-content: space-between; margin-bottom:6px;">
                        <div style="font-weight:700;font-size:14px;color:var(--secondary);">Catatan dari Bidan / Admin</div>
                        <div style="font-size: 11px; color: var(--text-muted);">{{ $fbManual->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div style="font-size:13px;line-height:1.65;color:#374151;white-space: pre-wrap;">{{ $fbManual->pesan }}</div>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state" style="padding: 20px; border: 1px dashed var(--border); border-radius: var(--radius); margin-bottom: 24px;">
            <div class="empty-title" style="font-size:13px;">Belum ada catatan khusus.</div>
        </div>
        @endforelse

        <hr style="border: 0; border-top: 1px solid var(--border); margin: 30px 0;">

        {{-- BAGIAN 2: FEEDBACK OTOMATIS (SISTEM) --}}
        <h2 style="font-size:15px;font-weight:700;margin-bottom:14px;display:flex;align-items:center;gap:8px;">
            <i data-feather="cpu"></i> Saran Sistem (Otomatis)
            <span class="badge badge-neutral" style="font-size:11px;">{{ count($feedbacks) }} analisis</span>
        </h2>

        @foreach($feedbacks as $fb)
        @php
            $colors = [
                'success' => ['bg' => '#f0fdf4', 'border' => '#16a34a', 'icon_bg' => '#dcfce7', 'text' => '#15803d'],
                'warning' => ['bg' => '#fffbeb', 'border' => '#f59e0b', 'icon_bg' => '#fef3c7', 'text' => '#92400e'],
                'danger'  => ['bg' => '#fff1f2', 'border' => '#ef4444', 'icon_bg' => '#fee2e2', 'text' => '#b91c1c'],
                'info'    => ['bg' => '#eff6ff', 'border' => '#3b82f6', 'icon_bg' => '#dbeafe', 'text' => '#1e40af'],
                'neutral' => ['bg' => '#f8fafc',  'border' => '#94a3b8', 'icon_bg' => '#f1f5f9', 'text' => '#475569'],
            ];
            $c = $colors[$fb['tipe']] ?? $colors['neutral'];
        @endphp
        <div style="background:{{ $c['bg'] }};border:1px solid {{ $c['border'] }};border-left:4px solid {{ $c['border'] }};border-radius:var(--radius);padding:18px 20px;margin-bottom:14px;" class="fade-up">
            <div style="display:flex;align-items:flex-start;gap:12px;">
                <div style="width:38px;height:38px;background:{{ $c['icon_bg'] }};border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                    {{ $fb['icon'] }}
                </div>
                <div style="flex:1;">
                    <div style="font-weight:700;font-size:14px;color:{{ $c['text'] }};margin-bottom:6px;">{{ $fb['judul'] }}</div>
                    <div style="font-size:13px;line-height:1.65;color:#374151;margin-bottom:10px;">{{ $fb['pesan'] }}</div>

                    @if(!empty($fb['saran']))
                    <div style="background:rgba(255,255,255,0.7);border-radius:8px;padding:12px 14px;">
                        <div style="font-size:11.5px;font-weight:700;color:{{ $c['text'] }};margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px;">Saran Tindakan:</div>
                        @foreach($fb['saran'] as $saran)
                        <div style="font-size:12.5px;color:#374151;padding:3px 0;display:flex;align-items:flex-start;gap:6px;">
                            <span style="color:{{ $c['border'] }};margin-top:2px;flex-shrink:0;"><i data-feather="chevron-right"></i></span>
                            <span>{{ $saran }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- SIDEBAR INFO --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Profil Anak --}}
        <div class="card fade-up">
            <div class="card-header"><div class="card-title"><i data-feather="smile"></i> Profil Anak</div></div>
            <div class="card-body">
                <div style="text-align:center;margin-bottom:14px;">
                    <div class="anak-avatar" style="width:64px;height:64px;font-size:32px;margin:0 auto 8px;">
                        {!! $anak->jenis_kelamin === 'L' ? '<i data-feather="user"></i>' : '<i data-feather="user"></i>' !!}
                    </div>
                    <div style="font-weight:700;font-size:15px;">{{ $anak->nama_anak }}</div>
                    <div style="font-size:12px;color:var(--text-muted);">{{ $anak->umur_label }}</div>
                    @if($anak->pertumbuhan->isNotEmpty())
                        @php $badge = $anak->pertumbuhan->first()->status_gizi_badge; @endphp
                        <span class="badge {{ $badge['class'] }}" style="margin-top:6px;">{{ $badge['label'] }}</span>
                    @endif
                </div>
                <div class="detail-row"><span class="detail-label">Ibu</span><span class="detail-value">{{ $anak->ibu->nama_ibu }}</span></div>
                @if($anak->pertumbuhan->isNotEmpty())
                @php $p = $anak->pertumbuhan->first(); @endphp
                <div class="detail-row"><span class="detail-label">Berat Terakhir</span><span class="detail-value">{{ $p->berat_badan }} kg</span></div>
                <div class="detail-row"><span class="detail-label">Tinggi Terakhir</span><span class="detail-value">{{ $p->tinggi_badan }} cm</span></div>
                <div class="detail-row"><span class="detail-label">Tgl Ukur</span><span class="detail-value">{{ $p->tanggal_pengukuran->format('d M Y') }}</span></div>
                @endif
            </div>
        </div>

        {{-- Ringkasan Recall 7 Hari --}}
        <div class="card fade-up">
            <div class="card-header"><div class="card-title"><i data-feather="calendar"></i> Recall 7 Hari Terakhir</div></div>
            <div class="card-body">
                @if($recalls->isNotEmpty())
                    <div style="font-size:13px;color:var(--text-muted);margin-bottom:10px;">
                        <strong style="color:var(--text-main);">{{ $recalls->groupBy('tanggal')->count() }}</strong> hari dicatat,
                        <strong style="color:var(--text-main);">{{ $recalls->count() }}</strong> asupan total
                    </div>
                    @foreach($recalls->groupBy('tanggal')->take(7) as $tgl => $items)
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid var(--border);font-size:12.5px;">
                        <span style="color:var(--text-muted);">{{ \Carbon\Carbon::parse($tgl)->format('d M') }}</span>
                        <div style="display:flex;gap:6px;">
                            @foreach($items->unique('waktu_makan') as $item)
                            <span style="background:var(--bg);padding:2px 7px;border-radius:100px;font-size:11px;">{{ $item->waktu_makan }}</span>
                            @endforeach
                        </div>
                        <span style="font-weight:600;color:var(--primary);">{{ round($items->sum('kalori')) }} kkal</span>
                    </div>
                    @endforeach
                @else
                    <div class="empty-state" style="padding:20px;">
                        <div class="empty-icon" style="font-size:30px;"><i data-feather="clipboard"></i></div>
                        <div class="empty-title" style="font-size:13px;">Belum ada data recall</div>
                        <a href="{{ route('recall.create') }}?anak_id={{ $anak->id }}" class="btn btn-primary btn-sm" style="margin-top:8px;">+ Input Recall</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Disclaimer --}}
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:var(--radius);padding:14px 16px;">
            <div style="font-size:12px;color:#92400e;line-height:1.6;">
                <i data-feather="alert-triangle"></i> <strong>Perhatian:</strong> Rekomendasi ini bersifat informatif berdasarkan data yang diinput. Untuk kondisi medis serius, selalu konsultasikan ke dokter atau tenaga kesehatan.
            </div>
        </div>
    </div>

</div>
@endsection
