@extends('layouts.app')
@section('title', 'Detail Aktivitas — ' . $user->name)

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1><i data-feather="user"></i> Detail Aktivitas</h1>
        <p>{{ $user->name }} — {{ $user->ibu?->nama_ibu ?? $user->nomer }}</p>
    </div>
    <div class="topbar-actions" style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('monitoring.show', $user) }}?period=today" class="btn {{ $period==='today'?'btn-primary':'btn-outline' }} btn-sm">Hari Ini</a>
        <a href="{{ route('monitoring.show', $user) }}?period=week" class="btn {{ $period==='week'?'btn-primary':'btn-outline' }} btn-sm">7 Hari</a>
        <a href="{{ route('monitoring.show', $user) }}?period=month" class="btn {{ $period==='month'?'btn-primary':'btn-outline' }} btn-sm">30 Hari</a>
        <a href="{{ route('monitoring.show', $user) }}?period=all" class="btn {{ $period==='all'?'btn-primary':'btn-outline' }} btn-sm">Semua</a>
        <a href="{{ route('monitoring.index') }}" class="btn btn-outline btn-sm"><i data-feather="arrow-left"></i> Kembali</a>
    </div>
</div>

{{-- INFO HEADER --}}
<div class="card fade-up" style="margin-bottom:20px;">
    <div class="card-body">
        <div style="display:flex;align-items:center;gap:18px;flex-wrap:wrap;">
            <div class="avatar-sm" style="width:56px;height:56px;font-size:22px;flex-shrink:0;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div style="flex:1;">
                <div style="font-weight:800;font-size:18px;">{{ $user->name }}</div>
                <div style="font-size:13px;color:var(--text-muted);">
                    {{ $user->ibu?->nama_ibu ?? '-' }} • {{ $user->nomer }}
                </div>
                <div style="margin-top:6px;">
                    @if($user->is_online)
                        <span class="badge badge-success" style="font-size:11px;padding:3px 10px;">
                            <span style="display:inline-block;width:6px;height:6px;background:#fff;border-radius:50%;margin-right:4px;animation:pulse-dot 1.5s infinite;"></span>
                            Online Sekarang
                        </span>
                    @else
                        <span class="badge badge-neutral" style="font-size:11px;padding:3px 10px;">Offline</span>
                        @if($user->last_activity_at)
                            <span style="font-size:11.5px;color:var(--text-muted);margin-left:8px;">
                                Terakhir: {{ $user->last_activity_at->diffForHumans() }}
                            </span>
                        @endif
                    @endif
                </div>
            </div>
            <div style="display:flex;gap:20px;text-align:center;">
                <div style="background:var(--bg);border-radius:10px;padding:14px 20px;">
                    <div style="font-size:22px;font-weight:800;color:var(--primary);">{{ $totalVisits }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">Halaman Dibuka</div>
                </div>
                <div style="background:var(--bg);border-radius:10px;padding:14px 20px;">
                    <div style="font-size:22px;font-weight:800;color:#16a34a;">
                        @if($sessionDuration >= 60)
                            {{ floor($sessionDuration/60) }}j {{ $sessionDuration%60 }}m
                        @else
                            {{ $sessionDuration }}m
                        @endif
                    </div>
                    <div style="font-size:11px;color:var(--text-muted);">Durasi Sesi</div>
                </div>
                <div style="background:var(--bg);border-radius:10px;padding:14px 20px;">
                    <div style="font-size:22px;font-weight:800;color:#7c3aed;">{{ $topPages->first()?->page_title ?? '-' }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">Halaman Favorit</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CHARTS + HALAMAN FAVORIT --}}
<div class="grid-2" style="gap:20px;margin-bottom:20px;">
    {{-- Heatmap Jam Aktif --}}
    <div class="card fade-up">
        <div class="card-header">
            <div class="card-title"><i data-feather="clock"></i> Jam Aktif</div>
            <span style="font-size:11px;color:var(--text-muted);">Distribusi aktivitas per jam</span>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(12,1fr);gap:4px;">
                @for($h = 0; $h < 24; $h++)
                @php
                    $val = $heatmap[$h] ?? 0;
                    $maxH = max(1, max($heatmap));
                    $intensity = $val > 0 ? max(0.15, $val / $maxH) : 0;
                    $bg = $val > 0 ? "rgba(22,163,74,{$intensity})" : 'var(--bg)';
                @endphp
                <div style="text-align:center;padding:8px 2px;border-radius:6px;background:{{ $bg }};transition:all 0.3s;" title="{{ $val }} kunjungan jam {{ str_pad($h,2,'0',STR_PAD_LEFT) }}:00">
                    <div style="font-size:10px;font-weight:700;color:{{ $val > 0 ? '#16a34a' : 'var(--text-muted)' }};">{{ str_pad($h,2,'0',STR_PAD_LEFT) }}</div>
                    <div style="font-size:11px;font-weight:800;margin-top:2px;">{{ $val > 0 ? $val : '·' }}</div>
                </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- Halaman Favorit --}}
    <div class="card fade-up">
        <div class="card-header">
            <div class="card-title"><i data-feather="star"></i> Halaman yang Sering Dibuka</div>
        </div>
        <div class="card-body">
            @if($topPages->isNotEmpty())
            <div style="display:flex;flex-direction:column;gap:12px;">
                @foreach($topPages as $i => $page)
                @php
                    $maxVal = $topPages->max('total');
                    $pct = $maxVal > 0 ? round($page->total / $maxVal * 100) : 0;
                    $colors = ['#16a34a','#7c3aed','#f59e0b','#ef4444','#3b82f6'];
                    $color = $colors[$i % count($colors)];
                @endphp
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:12.5px;margin-bottom:4px;">
                        <span style="font-weight:600;">
                            <span style="display:inline-block;width:8px;height:8px;background:{{ $color }};border-radius:50%;margin-right:6px;"></span>
                            {{ $page->page_title }}
                        </span>
                        <span style="font-weight:700;color:{{ $color }};">{{ $page->total }}x</span>
                    </div>
                    <div class="progress-track">
                        <div style="width:{{ $pct }}%;height:100%;background:{{ $color }};border-radius:100px;transition:width 0.6s ease;"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state" style="padding:20px;">
                <div class="empty-icon"><i data-feather="bar-chart-2"></i></div>
                <div class="empty-title">Belum ada data</div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- TIMELINE AKTIVITAS --}}
<div class="card fade-up">
    <div class="card-header">
        <div class="card-title"><i data-feather="list"></i> Riwayat Aktivitas</div>
        <span style="font-size:12px;color:var(--text-muted);">{{ $activities->total() }} record</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Halaman</th>
                    <th>URL</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $act)
                <tr>
                    <td>
                        <div style="font-size:12.5px;font-weight:600;">{{ $act->visited_at->format('d M Y') }}</div>
                        <div style="font-size:11.5px;color:var(--text-muted);">{{ $act->visited_at->format('H:i:s') }}</div>
                    </td>
                    <td>
                        <span style="font-weight:600;font-size:13px;">
                            @php
                                $iconMap = [
                                    'Beranda' => 'home', 'Data Anak' => 'smile', 'Profil Anak' => 'user',
                                    'Pertumbuhan' => 'trending-up', 'Input Pengukuran' => 'bar-chart-2',
                                    'Recall Gizi' => 'clipboard', 'Input Recall Gizi' => 'coffee',
                                    'Edukasi MPASI' => 'book-open', 'Reminder' => 'bell',
                                    'Feedback' => 'message-square',
                                ];
                                $icon = $iconMap[$act->page_title] ?? 'file';
                            @endphp
                            <i data-feather="{{ $icon }}" style="width:14px;height:14px;"></i>
                            {{ $act->page_title }}
                        </span>
                    </td>
                    <td>
                        <code style="font-size:11.5px;background:var(--bg);padding:2px 8px;border-radius:4px;">{{ Str::limit($act->url, 50) }}</code>
                    </td>
                    <td style="font-size:12px;color:var(--text-muted);">{{ $act->ip_address }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state" style="padding:30px;">
                            <div class="empty-icon"><i data-feather="activity"></i></div>
                            <div class="empty-title">Belum ada aktivitas tercatat</div>
                            <div class="empty-sub">Aktivitas akan muncul saat user membuka halaman di web</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($activities->hasPages())
    <div style="padding:16px 22px;">{{ $activities->withQueryString()->links('vendor.pagination.custom') }}</div>
    @endif
</div>

<style>
@keyframes pulse-dot {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}
</style>
@endsection
