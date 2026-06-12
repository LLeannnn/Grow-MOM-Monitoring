@extends('layouts.app')
@section('title', 'Analisis Pertumbuhan WHO — ' . $anak->nama_anak)

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1><i data-feather="bar-chart-2"></i> Analisis Pertumbuhan WHO</h1>
        <p>{{ $anak->nama_anak }} • {{ $anak->umur_label }} • {{ $anak->jenis_kelamin_label }}</p>
    </div>
    <div class="topbar-actions">
        <a href="{{ route('pertumbuhan.export-pdf') }}?anak_id={{ $anak->id }}" class="btn btn-outline btn-sm"><i data-feather="file-text"></i> PDF</a>
        <a href="{{ route('anak.show', $anak) }}" class="btn btn-outline btn-sm"><i data-feather="smile"></i> Info Anak</a>
        <a href="{{ route('pertumbuhan.index') }}?anak_id={{ $anak->id }}" class="btn btn-outline btn-sm"><i data-feather="arrow-left"></i> Kembali</a>
    </div>
</div>

{{-- STATUS WHO BADGES --}}
<div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
    @foreach($statusWho as $s)
    <div style="display:flex;align-items:center;gap:10px;background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:12px 18px;flex:1;min-width:220px;" class="fade-up">
        <span style="font-size:24px;">{{ $s['icon'] }}</span>
        <div>
            <span class="badge {{ $s['class'] }}" style="font-size:13px;padding:4px 12px;">{{ $s['label'] }}</span>
            <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">{{ $s['desc'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Z-SCORE & PENGUKURAN --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">
    {{-- Berat Badan --}}
    <div class="card fade-up">
        <div class="card-body" style="padding:18px;text-align:center;">
            <div style="font-size:28px;margin-bottom:6px;"><i data-feather="activity"></i></div>
            <div style="font-size:28px;font-weight:800;color:var(--primary);">{{ $pertumbuhan->berat_badan }}</div>
            <div style="font-size:12px;color:var(--text-muted);">kg • Berat Badan</div>
            @if($waz !== null)
            <div style="margin-top:8px;font-size:12px;font-weight:600;color:{{ $waz < -2 ? 'var(--danger)' : ($waz > 2 ? '#3b82f6' : 'var(--primary)') }};">
                WAZ: {{ $waz > 0 ? '+' : '' }}{{ $waz }}
            </div>
            @endif
        </div>
    </div>

    {{-- Tinggi Badan --}}
    <div class="card fade-up">
        <div class="card-body" style="padding:18px;text-align:center;">
            <div style="font-size:28px;margin-bottom:6px;"><i data-feather="bar-chart-2"></i></div>
            <div style="font-size:28px;font-weight:800;color:#7c3aed;">{{ $pertumbuhan->tinggi_badan }}</div>
            <div style="font-size:12px;color:var(--text-muted);">cm • Tinggi Badan</div>
            @if($haz !== null)
            <div style="margin-top:8px;font-size:12px;font-weight:600;color:{{ $haz < -2 ? 'var(--danger)' : ($haz > 2 ? '#3b82f6' : 'var(--primary)') }};">
                HAZ: {{ $haz > 0 ? '+' : '' }}{{ $haz }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- CHARTS WHO --}}
<div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:15px; flex-wrap:wrap; gap:10px;">
    <h2 style="font-size:16px; margin:0;">Kurva Pertumbuhan WHO</h2>
    <div style="display:flex;gap:6px;background:var(--card);padding:4px;border-radius:10px;border:1px solid var(--border);overflow-x:auto;">
        <button class="seg-btn active" data-s="0" data-e="24">0-24 Mgg</button>
        <button class="seg-btn" data-s="25" data-e="52">25-52 Mgg</button>
        <button class="seg-btn" data-s="53" data-e="78">53-78 Mgg</button>
        <button class="seg-btn" data-s="79" data-e="104">79-104 Mgg</button>
        <button class="seg-btn" data-s="0" data-e="104">Semua</button>
    </div>
</div>
<style>
.seg-btn { background:transparent; border:none; border-radius:6px; padding:6px 12px; font-size:12px; font-weight:600; color:var(--text-muted); cursor:pointer; transition:all 0.2s; white-space:nowrap; }
.seg-btn:hover { color:var(--text-main); }
.seg-btn.active { background:var(--primary); color:#fff; }
</style>

<div class="grid-2" style="gap:20px;margin-bottom:20px;">

    {{-- Chart Berat Badan vs WHO --}}
    <div class="card fade-up">
        <div class="card-header">
            <div class="card-title"><i data-feather="activity"></i> Berat Badan vs Kurva WHO</div>
            <span style="font-size:11px;color:var(--text-muted);">Weight-for-Age (WAZ)</span>
        </div>
        <div class="card-body">
            <div class="chart-wrapper" style="height:260px;">
                <canvas id="chartBeratWHO"></canvas>
            </div>
            {{-- Legend --}}
            <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:10px;font-size:11px;">
                <span style="display:flex;align-items:center;gap:4px;"><span style="display:inline-block;width:18px;height:3px;background:#16a34a;border-radius:2px;"></span> Anak</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="display:inline-block;width:18px;height:2px;background:#94a3b8;border-radius:2px;border-top:2px dashed #94a3b8;"></span> Median</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="display:inline-block;width:18px;height:2px;background:#f59e0b;border-radius:2px;border-top:2px dashed #f59e0b;"></span> -2SD</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="display:inline-block;width:18px;height:2px;background:#ef4444;border-radius:2px;border-top:2px dashed #ef4444;"></span> -3SD</span>
            </div>
        </div>
    </div>

    {{-- Chart Tinggi Badan vs WHO --}}
    <div class="card fade-up">
        <div class="card-header">
            <div class="card-title"><i data-feather="bar-chart-2"></i> Tinggi Badan vs Kurva WHO</div>
            <span style="font-size:11px;color:var(--text-muted);">Height-for-Age (HAZ)</span>
        </div>
        <div class="card-body">
            <div class="chart-wrapper" style="height:260px;">
                <canvas id="chartTinggiWHO"></canvas>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:10px;font-size:11px;">
                <span style="display:flex;align-items:center;gap:4px;"><span style="display:inline-block;width:18px;height:3px;background:#7c3aed;border-radius:2px;"></span> Anak</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="display:inline-block;width:18px;height:2px;background:#94a3b8;"></span> Median</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="display:inline-block;width:18px;height:2px;background:#f59e0b;"></span> -2SD</span>
                <span style="display:flex;align-items:center;gap:4px;"><span style="display:inline-block;width:18px;height:2px;background:#ef4444;"></span> -3SD</span>
            </div>
        </div>
    </div>
</div>

{{-- KETERANGAN STATUS --}}
<div class="card fade-up" style="margin-bottom:20px;">
    <div class="card-header"><div class="card-title"><i data-feather="book"></i> Panduan Status WHO</div></div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
            @foreach([
                ['Normal',      'badge-success', 'Tumbuh sesuai standar WHO.'],
                ['Stunting',    'badge-warning', 'HAZ < −2SD. Tinggi rendah untuk usia.'],
                ['Wasting',     'badge-danger',  'WAZ < −3SD. Berat sangat rendah untuk usia.'],
                ['Underweight', 'badge-warning', 'WAZ < −2SD. Berat rendah untuk usia.'],
            ] as [$lbl, $cls, $desc])
            <div style="background:var(--bg);border-radius:8px;padding:12px;">
                <span class="badge {{ $cls }}" style="margin-bottom:6px;display:inline-block;">{{ $lbl }}</span>
                <div style="font-size:12px;color:var(--text-muted);line-height:1.5;">{{ $desc }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- RIWAYAT TABEL --}}
<div class="card fade-up">
    <div class="card-header">
        <div class="card-title"><i data-feather="clipboard"></i> Riwayat Pengukuran</div>
        <a href="{{ route('pertumbuhan.create') }}?anak_id={{ $anak->id }}" class="btn btn-primary btn-sm">+ Input</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr>
                <th>Tanggal</th><th>BB (kg)</th><th>TB (cm)</th>
                <th>Status</th><th>Catatan</th>
            </tr></thead>
            <tbody>
                @forelse($riwayat as $r)
                <tr>
                    <td>{{ $r->tanggal_pengukuran->format('d M Y') }}</td>
                    <td><strong>{{ $r->berat_badan }}</strong></td>
                    <td>{{ $r->tinggi_badan }}</td>
                    <td><span class="badge {{ $r->status_gizi_badge['class'] }}">{{ $r->status_gizi_badge['label'] }}</span></td>
                    <td style="font-size:12px;color:var(--text-muted);">-</td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state" style="padding:20px;">
                    <div class="empty-icon"><i data-feather="bar-chart-2"></i></div>
                    <div class="empty-title">Belum ada riwayat</div>
                </div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
window.addEventListener('load', function () {
    // ── Data WHO reference ──────────────────────────────────
    const whoWeightLabels = @json($whoWeight['labels']);
    const whoWeightM3     = @json($whoWeight['m3']);
    const whoWeightM2     = @json($whoWeight['m2']);
    const whoWeightMed    = @json($whoWeight['med']);
    const whoWeightP2     = @json($whoWeight['p2']);

    const whoHeightLabels = @json($whoHeight['labels']);
    const whoHeightM3     = @json($whoHeight['m3']);
    const whoHeightM2     = @json($whoHeight['m2']);
    const whoHeightMed    = @json($whoHeight['med']);
    const whoHeightP2     = @json($whoHeight['p2']);

    // ── Actual anak data ────────────────────────────────────
    const anakLabels = @json($chartLabels);
    const anakBerat  = @json($chartBerat);
    const anakTinggi = @json($chartTinggi);

    const dashedLine = (color) => ({
        borderColor: color, backgroundColor: 'transparent',
        borderWidth: 1.5, borderDash: [6, 3],
        pointRadius: 0, fill: false, tension: 0.3,
    });

    let chartBerat = null;
    let chartTinggi = null;

    // ── Chart Berat Badan vs WHO ────────────────────────────
    const ctxBerat = document.getElementById('chartBeratWHO')?.getContext('2d');
    if (ctxBerat) {
        chartBerat = new Chart(ctxBerat, {
            type: 'line',
            data: {
                labels: whoWeightLabels.slice(0, 25),
                datasets: [
                    { label: 'Median', data: whoWeightMed.slice(0, 25), ...dashedLine('#94a3b8') },
                    { label: '-2SD',   data: whoWeightM2.slice(0, 25),  ...dashedLine('#f59e0b') },
                    { label: '-3SD',   data: whoWeightM3.slice(0, 25),  ...dashedLine('#ef4444') },
                    { label: '+2SD',   data: whoWeightP2.slice(0, 25),  ...dashedLine('#3b82f6') },
                    {
                        label: 'Berat Anak (kg)',
                        data: anakBerat.slice(0, 25),
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22,163,74,0.12)',
                        borderWidth: 3,
                        pointBackgroundColor: '#16a34a',
                        pointRadius: 5,
                        fill: false,
                        tension: 0.4,
                        spanGaps: true
                    },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }
                }
            }
        });
    }

    // ── Chart Tinggi Badan vs WHO ───────────────────────────
    const ctxTinggi = document.getElementById('chartTinggiWHO')?.getContext('2d');
    if (ctxTinggi) {
        chartTinggi = new Chart(ctxTinggi, {
            type: 'line',
            data: {
                labels: whoHeightLabels.slice(0, 25),
                datasets: [
                    { label: 'Median', data: whoHeightMed.slice(0, 25), ...dashedLine('#94a3b8') },
                    { label: '-2SD',   data: whoHeightM2.slice(0, 25),  ...dashedLine('#f59e0b') },
                    { label: '-3SD',   data: whoHeightM3.slice(0, 25),  ...dashedLine('#ef4444') },
                    { label: '+2SD',   data: whoHeightP2.slice(0, 25),  ...dashedLine('#3b82f6') },
                    {
                        label: 'Tinggi Anak (cm)',
                        data: anakTinggi.slice(0, 25),
                        borderColor: '#7c3aed',
                        backgroundColor: 'rgba(124,58,237,0.10)',
                        borderWidth: 3,
                        pointBackgroundColor: '#7c3aed',
                        pointRadius: 5,
                        fill: false,
                        tension: 0.4,
                        spanGaps: true
                    },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }
                }
            }
        });
    }

    // ── Segment Logic ───────────────────────────────────────
    document.querySelectorAll('.seg-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.seg-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const s = parseInt(this.dataset.s);
            const e = parseInt(this.dataset.e) + 1;
            
            if (chartBerat) {
                chartBerat.data.labels = whoWeightLabels.slice(s, e);
                chartBerat.data.datasets[0].data = whoWeightMed.slice(s, e);
                chartBerat.data.datasets[1].data = whoWeightM2.slice(s, e);
                chartBerat.data.datasets[2].data = whoWeightM3.slice(s, e);
                chartBerat.data.datasets[3].data = whoWeightP2.slice(s, e);
                chartBerat.data.datasets[4].data = anakBerat.slice(s, e);
                chartBerat.update();
            }
            if (chartTinggi) {
                chartTinggi.data.labels = whoHeightLabels.slice(s, e);
                chartTinggi.data.datasets[0].data = whoHeightMed.slice(s, e);
                chartTinggi.data.datasets[1].data = whoHeightM2.slice(s, e);
                chartTinggi.data.datasets[2].data = whoHeightM3.slice(s, e);
                chartTinggi.data.datasets[3].data = whoHeightP2.slice(s, e);
                chartTinggi.data.datasets[4].data = anakTinggi.slice(s, e);
                chartTinggi.update();
            }
        });
    });
});
</script>
@endpush
@endsection
