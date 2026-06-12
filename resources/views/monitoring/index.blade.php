@extends('layouts.app')
@section('title', 'Monitoring Aktivitas User')

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1><i data-feather="eye"></i> Monitoring Aktivitas User</h1>
        <p>Pantau aktivitas browsing ibu secara real-time</p>
    </div>
    <div class="topbar-actions" style="display:flex;gap:8px;">
        <a href="{{ route('monitoring.index', ['period'=>'today']) }}" class="btn {{ $period==='today'?'btn-primary':'btn-outline' }} btn-sm">Hari Ini</a>
        <a href="{{ route('monitoring.index', ['period'=>'week']) }}" class="btn {{ $period==='week'?'btn-primary':'btn-outline' }} btn-sm">7 Hari</a>
        <a href="{{ route('monitoring.index', ['period'=>'month']) }}" class="btn {{ $period==='month'?'btn-primary':'btn-outline' }} btn-sm">30 Hari</a>
        <a href="{{ route('monitoring.index', ['period'=>'all']) }}" class="btn {{ $period==='all'?'btn-primary':'btn-outline' }} btn-sm">Semua</a>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="stat-grid" style="grid-template-columns:repeat(4,1fr);">
    {{-- Online Sekarang --}}
    <div class="stat-card green fade-up">
        <div class="stat-icon green"><i data-feather="wifi"></i></div>
        <div class="stat-info">
            <div class="stat-label">Online Sekarang</div>
            <div class="stat-value" id="onlineCount">{{ $onlineCount }}</div>
            <div class="stat-sub">user aktif</div>
        </div>
    </div>

    {{-- Total Kunjungan --}}
    <div class="stat-card purple fade-up">
        <div class="stat-icon purple"><i data-feather="mouse-pointer"></i></div>
        <div class="stat-info">
            <div class="stat-label">Total Kunjungan</div>
            <div class="stat-value">{{ number_format($totalVisits) }}</div>
            <div class="stat-sub">halaman dibuka</div>
        </div>
    </div>

    {{-- Rata-rata Durasi --}}
    <div class="stat-card amber fade-up">
        <div class="stat-icon amber"><i data-feather="clock"></i></div>
        <div class="stat-info">
            <div class="stat-label">Rata-rata Durasi</div>
            <div class="stat-value">{{ $avgDuration }}</div>
            <div class="stat-sub">menit / sesi</div>
        </div>
    </div>

    {{-- Halaman Terpopuler --}}
    <div class="stat-card fade-up" style="border-left:4px solid var(--primary);">
        <div class="stat-icon" style="background:rgba(var(--primary-rgb, 79,70,229),0.1);color:var(--primary);"><i data-feather="star"></i></div>
        <div class="stat-info">
            <div class="stat-label">Halaman Terpopuler</div>
            <div class="stat-value" style="font-size:16px;">{{ $topPages->first()?->page_title ?? '-' }}</div>
            <div class="stat-sub">{{ $topPages->first()?->total ?? 0 }} kunjungan</div>
        </div>
    </div>
</div>

{{-- CHARTS ROW --}}
<div class="chart-grid" style="margin-bottom:20px;">
    {{-- Tren Kunjungan --}}
    <div class="card fade-up">
        <div class="card-header">
            <div class="card-title"><i data-feather="trending-up"></i> Tren Kunjungan (7 Hari)</div>
        </div>
        <div class="card-body">
            <div class="chart-wrapper" style="height:220px;">
                <canvas id="chartTrend"></canvas>
            </div>
        </div>
    </div>

    {{-- Halaman Terpopuler --}}
    <div class="card fade-up">
        <div class="card-header">
            <div class="card-title"><i data-feather="bar-chart-2"></i> Halaman Terpopuler</div>
        </div>
        <div class="card-body">
            @if($topPages->isNotEmpty())
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($topPages as $page)
                @php
                    $maxVal = $topPages->max('total');
                    $pct = $maxVal > 0 ? round($page->total / $maxVal * 100) : 0;
                @endphp
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:12.5px;margin-bottom:4px;">
                        <span style="font-weight:600;">{{ $page->page_title }}</span>
                        <span style="color:var(--text-muted);">{{ $page->total }}x</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill green" style="width:{{ $pct }}%;transition:width 0.6s ease;"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state" style="padding:20px;">
                <div class="empty-icon"><i data-feather="bar-chart-2"></i></div>
                <div class="empty-title">Belum ada data kunjungan</div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- TABEL DAFTAR USER --}}
<div class="card fade-up">
    <div class="card-header">
        <div class="card-title"><i data-feather="users"></i> Daftar Aktivitas Ibu</div>
        <span style="font-size:12px;color:var(--text-muted);">
            <span style="display:inline-block;width:8px;height:8px;background:#16a34a;border-radius:50%;margin-right:4px;"></span>
            Online (aktif < 5 menit)
        </span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Status</th>
                    <th>Terakhir Aktif</th>
                    <th>Kunjungan</th>
                    <th>Durasi Sesi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="avatar-sm" style="width:34px;height:34px;font-size:13px;flex-shrink:0;">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13px;">{{ $u->name }}</div>
                                <div style="font-size:11.5px;color:var(--text-muted);">{{ $u->ibu?->nama_ibu ?? $u->nomer }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($u->is_online)
                            <span class="badge badge-success" style="font-size:11px;padding:3px 10px;">
                                <span style="display:inline-block;width:6px;height:6px;background:#fff;border-radius:50%;margin-right:4px;animation:pulse-dot 1.5s infinite;"></span>
                                Online
                            </span>
                        @else
                            <span class="badge badge-neutral" style="font-size:11px;padding:3px 10px;">Offline</span>
                        @endif
                    </td>
                    <td>
                        @if($u->last_activity_at)
                            <div style="font-size:12.5px;">{{ $u->last_activity_at->format('d M Y') }}</div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $u->last_activity_at->format('H:i') }} ({{ $u->last_activity_at->diffForHumans() }})</div>
                        @else
                            <span style="color:var(--text-muted);font-size:12px;">Belum pernah</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-weight:700;font-size:14px;color:var(--primary);">{{ $u->visit_count }}</span>
                        <span style="font-size:11px;color:var(--text-muted);"> halaman</span>
                    </td>
                    <td>
                        @php $dur = $sessionDurations[$u->id] ?? 0; @endphp
                        @if($dur > 0)
                            <span style="font-weight:600;font-size:13px;">
                                @if($dur >= 60)
                                    {{ floor($dur/60) }}j {{ $dur%60 }}m
                                @else
                                    {{ $dur }} menit
                                @endif
                            </span>
                        @else
                            <span style="color:var(--text-muted);font-size:12px;">-</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('monitoring.show', $u) }}?period={{ $period }}" class="btn btn-outline btn-sm">
                            <i data-feather="eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state" style="padding:30px;">
                            <div class="empty-icon"><i data-feather="users"></i></div>
                            <div class="empty-title">Belum ada user terdaftar</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div style="padding:16px 22px;">{{ $users->withQueryString()->links('vendor.pagination.custom') }}</div>
    @endif
</div>

<style>
@keyframes pulse-dot {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}
</style>

@push('scripts')
<script>
window.addEventListener('load', function () {
    // ── Tren Chart ─────────────────────────────────────
    const ctx = document.getElementById('chartTrend')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($trendLabels),
                datasets: [
                    {
                        label: 'Total Kunjungan',
                        data: @json($trendVisits),
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22,163,74,0.08)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#16a34a',
                        pointRadius: 4,
                        tension: 0.4,
                        fill: true,
                    },
                    {
                        label: 'User Unik',
                        data: @json($trendUsers),
                        borderColor: '#7c3aed',
                        backgroundColor: 'rgba(124,58,237,0.06)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#7c3aed',
                        pointRadius: 4,
                        tension: 0.4,
                        fill: true,
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top', labels: { font: { family: 'Plus Jakarta Sans', size: 11 } } } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, stepSize: 1 } }
                }
            }
        });
    }

    // ── Auto refresh online count tiap 60 detik ─────
    setInterval(() => {
        fetch('{{ route("monitoring.api.online") }}')
            .then(r => r.json())
            .then(data => {
                const el = document.getElementById('onlineCount');
                if (el) el.textContent = data.online;
            })
            .catch(() => {});
    }, 60000);
});
</script>
@endpush
@endsection
