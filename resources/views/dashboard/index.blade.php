@extends('layouts.app')
@section('title', 'Dashboard Statistik')

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1>Dashboard Statistik 📊</h1>
        <p>Selamat datang di GROW-MOM Monitoring System — {{ now()->format('l, d F Y') }}</p>
    </div>
    <div class="topbar-actions">
        <a href="{{ route('anak.create') }}" class="btn btn-primary">
            <span>+</span> Tambah Anak
        </a>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="stat-grid">
    <div class="stat-card green fade-up">
        <div class="stat-icon green">👩</div>
        <div class="stat-info">
            <div class="stat-label">Total Ibu</div>
            <div class="stat-value">{{ $totalIbu }}</div>
            <div class="stat-sub">terdaftar</div>
        </div>
    </div>
    <div class="stat-card purple fade-up">
        <div class="stat-icon purple">👶</div>
        <div class="stat-info">
            <div class="stat-label">Total Anak</div>
            <div class="stat-value">{{ $totalAnak }}</div>
            <div class="stat-sub">dipantau</div>
        </div>
    </div>
    <div class="stat-card amber fade-up">
        <div class="stat-icon amber">🔔</div>
        <div class="stat-info">
            <div class="stat-label">Reminder Aktif</div>
            <div class="stat-value">{{ $reminderAktif }}</div>
            <div class="stat-sub">pending</div>
        </div>
    </div>
</div>

{{-- CHARTS ROW --}}
<div class="chart-grid">
    <div class="card fade-up">
        <div class="card-header">
            <div class="card-title">📈 Tren Pertumbuhan (6 Bulan Terakhir)</div>
        </div>
        <div class="card-body">
            <div class="chart-wrapper">
                <canvas id="chartPertumbuhan"></canvas>
            </div>
        </div>
    </div>

    <div class="card fade-up">
        <div class="card-header">
            <div class="card-title">🥗 Distribusi Status Gizi</div>
        </div>
        <div class="card-body">
            <div class="chart-wrapper" style="height:220px;">
                <canvas id="chartStatusGizi"></canvas>
            </div>
            <div style="margin-top:12px;">
                @foreach($statusGizi as $status => $total)
                <div class="progress-bar-wrap">
                    <div class="progress-label">
                        <span>{{ ucfirst($status) }}</span>
                        <span>{{ $total }}</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill {{ $status === 'normal' ? 'green' : ($status === 'underweight' || $status === 'stunting' ? 'amber' : 'red') }}"
                             style="width:{{ $totalAnak > 0 ? round($total/$totalAnak*100) : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- BOTTOM ROW --}}
<div class="grid-2" style="gap:20px; margin-top:0;">
    {{-- Anak Terbaru --}}
    <div class="card fade-up">
        <div class="card-header">
            <div class="card-title">👶 Anak Terbaru</div>
            <a href="{{ route('anak.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="card-body" style="padding:0;">
            @forelse($anakTerbaru as $a)
            <div style="display:flex;align-items:center;gap:14px;padding:14px 22px;border-bottom:1px solid var(--border);">
                <div class="anak-avatar" style="width:40px;height:40px;font-size:18px;margin:0;">
                    {{ $a->jenis_kelamin === 'L' ? '👦' : '👧' }}
                </div>
                <div style="flex:1;">
                    <div style="font-weight:600;font-size:13.5px;">{{ $a->nama_anak }}</div>
                    <div style="font-size:12px;color:var(--text-muted);">{{ $a->ibu->nama_ibu }} • {{ $a->umur_label }}</div>
                </div>
                <a href="{{ route('anak.show', $a) }}" class="btn btn-outline btn-sm">Detail</a>
            </div>
            @empty
            <div class="empty-state" style="padding:30px;">
                <div class="empty-icon">👶</div>
                <div class="empty-title">Belum ada data anak</div>
                <a href="{{ route('anak.create') }}" class="btn btn-primary btn-sm">Tambah Anak</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Reminder Upcoming --}}
    <div class="card fade-up">
        <div class="card-header">
            <div class="card-title">🔔 Reminder Mendatang</div>
            <a href="{{ route('reminder.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="card-body" style="padding:0;">
            @forelse($upcomingReminders as $r)
            <div style="display:flex;align-items:center;gap:14px;padding:14px 22px;border-bottom:1px solid var(--border);">
                <div style="width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;"
                     class="badge {{ $r->tipe_label['class'] }}">
                    {{ $r->tipe_label['icon'] }}
                </div>
                <div style="flex:1;">
                    <div style="font-weight:600;font-size:13px;">{{ $r->judul }}</div>
                    <div style="font-size:12px;color:var(--text-muted);">
                        {{ $r->anak->nama_anak }} • {{ $r->tanggal_reminder->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state" style="padding:30px;">
                <div class="empty-icon">🔔</div>
                <div class="empty-title">Tidak ada reminder aktif</div>
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
window.addEventListener('load', function () {
    const bulanLabels = @json($bulanLabels);
    const avgBerat    = @json($avgBerat);
    const avgTinggi   = @json($avgTinggi);

    const statusData   = @json($statusGizi);
    const statusLabels = Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1));
    const statusValues = Object.values(statusData);

    const statusColorsMap = {
        'Normal': '#16a34a',
        'Stunting': '#f59e0b',
        'Underweight': '#f59e0b',
        'Wasting': '#ef4444'
    };
    const statusBgColors = statusLabels.map(label => statusColorsMap[label] || '#94a3b8');

    // Chart Pertumbuhan
    const ctx1 = document.getElementById('chartPertumbuhan').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: bulanLabels,
            datasets: [
                {
                    label: 'Berat Badan (kg)',
                    data: avgBerat,
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22,163,74,0.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#16a34a',
                    pointRadius: 4,
                    tension: 0.4,
                    fill: true,
                },
                {
                    label: 'Tinggi Badan (cm)',
                    data: avgTinggi,
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
            plugins: { legend: { position: 'top', labels: { font: { family: 'Plus Jakarta Sans', size: 12 } } } },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { color: '#f1f5f9' } }
            }
        }
    });

    // Chart Status Gizi
    const ctx2 = document.getElementById('chartStatusGizi').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: statusBgColors,
                borderWidth: 0,
                hoverOffset: 4,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: 'Plus Jakarta Sans', size: 11 }, padding: 12 } }
            },
            cutout: '65%',
        }
    });
});
</script>
@endpush
@endsection

