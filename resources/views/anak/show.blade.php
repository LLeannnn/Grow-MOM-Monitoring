@extends('layouts.app')
@section('title', $anak->nama_anak)

@section('content')
<div class="topbar">
    <div class="page-header">
        <h1>{{ $anak->jenis_kelamin === 'L' ? '👦' : '👧' }} {{ $anak->nama_anak }}</h1>
        <p>{{ $anak->umur_label }} • {{ $anak->jenis_kelamin_label }}</p>
    </div>
    <div class="topbar-actions">
        <a href="{{ route('pertumbuhan.create') }}?anak_id={{ $anak->id }}" class="btn btn-primary">📏 Input Pengukuran</a>
        <a href="{{ route('recall.create') }}?anak_id={{ $anak->id }}" class="btn btn-primary">🍽️ Input Recall</a>
        <a href="{{ route('anak.edit', $anak) }}" class="btn btn-outline">✏️ Edit</a>
        <a href="{{ route('anak.index') }}" class="btn btn-outline">← Kembali</a>
    </div>
</div>

<div class="grid-2" style="gap:20px;align-items:start;margin-bottom:20px;">
    {{-- Info Anak --}}
    <div class="card fade-up">
        <div class="card-header"><div class="card-title">📋 Profil Anak</div></div>
        <div class="card-body">
            <div style="text-align:center;margin-bottom:18px;">
                <div class="anak-avatar" style="width:80px;height:80px;font-size:40px;margin:0 auto 10px;">
                    {{ $anak->jenis_kelamin === 'L' ? '👦' : '👧' }}
                </div>
                <div style="font-weight:800;font-size:18px;">{{ $anak->nama_anak }}</div>
                <div style="color:var(--text-muted);font-size:13px;">{{ $anak->umur_label }}</div>
                @if($anak->pertumbuhan_terakhir)
                    @php $badge = $anak->pertumbuhan_terakhir->status_gizi_badge; @endphp
                    <span class="badge {{ $badge['class'] }}" style="margin-top:6px;">{{ $badge['label'] }}</span>
                @endif
            </div>
            <div class="detail-row"><span class="detail-label">Ibu</span><span class="detail-value">{{ $anak->ibu->nama_ibu }}</span></div>
            <div class="detail-row"><span class="detail-label">Tanggal Lahir</span><span class="detail-value">{{ $anak->tanggal_lahir->format('d F Y') }}</span></div>
            <div class="detail-row"><span class="detail-label">Jenis Kelamin</span><span class="detail-value">{{ $anak->jenis_kelamin_label }}</span></div>
            <div class="detail-row"><span class="detail-label">Berat Lahir</span><span class="detail-value">{{ $anak->berat_lahir ? $anak->berat_lahir.' kg' : '-' }}</span></div>
            <div class="detail-row"><span class="detail-label">Panjang Lahir</span><span class="detail-value">{{ $anak->panjang_lahir ? $anak->panjang_lahir.' cm' : '-' }}</span></div>
            <div class="detail-row"><span class="detail-label">Golongan Darah</span><span class="detail-value">{{ strtoupper(str_replace('_',' ',$anak->golongan_darah)) }}</span></div>
        </div>
    </div>

    {{-- Ringkasan Gizi Hari Ini --}}
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="card fade-up">
            <div class="card-header">
                <div class="card-title">🍽️ Ringkasan Gizi Hari Ini</div>
                <span style="font-size:12px;color:var(--text-muted);">{{ today()->format('d F Y') }}</span>
            </div>
            <div class="card-body">
                @if($ringkasanHariIni && $ringkasanHariIni->total_kalori > 0)
                <div class="grid-2" style="gap:16px;">
                    <div>
                        <div class="progress-bar-wrap">
                            <div class="progress-label"><span>🔥 Kalori</span><span>{{ number_format($ringkasanHariIni->total_kalori,1) }} / {{ $akg['energi'] ?? 1000 }} kkal</span></div>
                            <div class="progress-track"><div class="progress-fill amber" style="width:{{ min(round($ringkasanHariIni->total_kalori/($akg['energi']??1000)*100),100) }}%"></div></div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ round($ringkasanHariIni->total_kalori/($akg['energi']??1000)*100) }}% dari AKG</div>
                        </div>
                        <div class="progress-bar-wrap">
                            <div class="progress-label"><span>🥩 Protein</span><span>{{ number_format($ringkasanHariIni->total_protein,1) }} / {{ $akg['protein'] ?? 20 }} g</span></div>
                            <div class="progress-track"><div class="progress-fill green" style="width:{{ min(round($ringkasanHariIni->total_protein/($akg['protein']??20)*100),100) }}%"></div></div>
                        </div>
                    </div>
                    <div>
                        <div class="progress-bar-wrap">
                            <div class="progress-label"><span>🌾 Karbohidrat</span><span>{{ number_format($ringkasanHariIni->total_karbo,1) }} / {{ $akg['karbo'] ?? 130 }} g</span></div>
                            <div class="progress-track"><div class="progress-fill purple" style="width:{{ min(round($ringkasanHariIni->total_karbo/($akg['karbo']??130)*100),100) }}%"></div></div>
                        </div>
                        <div class="progress-bar-wrap">
                            <div class="progress-label"><span>🧈 Lemak</span><span>{{ number_format($ringkasanHariIni->total_lemak,1) }} / {{ $akg['lemak'] ?? 30 }} g</span></div>
                            <div class="progress-track"><div class="progress-fill red" style="width:{{ min(round($ringkasanHariIni->total_lemak/($akg['lemak']??30)*100),100) }}%"></div></div>
                        </div>
                    </div>
                </div>
                <div style="margin-top:14px;text-align:center;">
                    <a href="{{ route('recall.index') }}?anak_id={{ $anak->id }}" class="btn btn-outline btn-sm">Lihat Riwayat Asupan</a>
                </div>
                @else
                <div class="empty-state" style="padding:10px;">
                    <div class="empty-icon" style="font-size:24px;">🍽️</div>
                    <div class="empty-title" style="font-size:13px;">Belum ada asupan makanan hari ini</div>
                    <a href="{{ route('recall.create') }}?anak_id={{ $anak->id }}" class="btn btn-primary btn-sm" style="margin-top:10px;">Catat Sekarang</a>
                </div>
                @endif
            </div>
        </div>

        {{-- Pengukuran Terakhir --}}
        @if($anak->pertumbuhan_terakhir)
        <div class="card fade-up">
            <div class="card-header"><div class="card-title">📊 Pengukuran Terakhir</div></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    @foreach(['berat_badan'=>['⚖️','Berat Badan','kg'],'tinggi_badan'=>['📏','Tinggi Badan','cm']] as $key=>[$icon,$label,$satuan])
                    <div style="background:var(--bg);border-radius:10px;padding:14px;text-align:center;">
                        <div style="font-size:22px;">{{ $icon }}</div>
                        <div style="font-size:20px;font-weight:800;color:var(--primary);">{{ $anak->pertumbuhan_terakhir->$key ?? '-' }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $label }} ({{ $satuan }})</div>
                    </div>
                    @endforeach
                </div>
                <div style="margin-top:12px;text-align:center;font-size:12px;color:var(--text-muted);">
                    Diukur: {{ $anak->pertumbuhan_terakhir->tanggal_pengukuran->format('d F Y') }}
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Catatan Khusus dari Admin --}}
    @if($anak->feedbackAnak->isNotEmpty())
    <div class="card fade-up full" style="grid-column: 1 / -1; margin-bottom: 0px;">
        <div class="card-header"><div class="card-title">📝 Catatan Khusus dari Bidan / Admin</div></div>
        <div class="card-body">
            <div style="display: flex; flex-direction: column; gap: 14px;">
                @foreach($anak->feedbackAnak as $fbManual)
                <div style="background:#fff;border:1px solid #e2e8f0;border-left:4px solid var(--secondary);border-radius:var(--radius);padding:16px 20px;">
                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        <div style="width:38px;height:38px;background:var(--secondary-light);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                            👩‍⚕️
                        </div>
                        <div style="flex:1;">
                            <div style="display: flex; justify-content: space-between; margin-bottom:6px;">
                                <div style="font-weight:700;font-size:14px;color:var(--secondary);">Pesan untuk Ibu</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $fbManual->created_at->format('d M Y, H:i') }}</div>
                            </div>
                            <div style="font-size:13px;line-height:1.65;color:#374151;white-space: pre-wrap;">{{ $fbManual->pesan }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

{{-- CHARTS WHO --}}
<div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:15px;">
    <h2 style="font-size:16px; margin:0;">Kurva Pertumbuhan WHO</h2>
    <div style="display:flex; gap:8px;">
        <a href="{{ request()->fullUrlWithQuery(['interval' => 1]) }}" class="btn btn-sm {{ $interval == 1 ? 'btn-primary' : 'btn-outline' }}">0-20 Bulan</a>
        <a href="{{ request()->fullUrlWithQuery(['interval' => 2]) }}" class="btn btn-sm {{ $interval == 2 ? 'btn-primary' : 'btn-outline' }}">21-40 Bulan</a>
        <a href="{{ request()->fullUrlWithQuery(['interval' => 3]) }}" class="btn btn-sm {{ $interval == 3 ? 'btn-primary' : 'btn-outline' }}">41-60 Bulan</a>
    </div>
</div>

<div class="grid-2" style="gap:20px;margin-bottom:20px;">
    {{-- Chart Berat Badan vs WHO --}}
    <div class="card fade-up">
        <div class="card-header">
            <div class="card-title">⚖️ Berat Badan vs Kurva WHO ({{ $minUmur }} - {{ $maxUmur }} Bulan)</div>
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
            <div class="card-title">📏 Tinggi Badan vs Kurva WHO ({{ $minUmur }} - {{ $maxUmur }} Bulan)</div>
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

{{-- Riwayat Pertumbuhan --}}
<div class="card fade-up">
    <div class="card-header">
        <div class="card-title">📋 Riwayat Pertumbuhan</div>
        <a href="{{ route('pertumbuhan.export-pdf') }}?anak_id={{ $anak->id }}" class="btn btn-outline btn-sm">📄 Export PDF</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr>
                <th>Tanggal</th><th>Berat (kg)</th><th>Tinggi (cm)</th>
                <th>Status Gizi</th><th>Catatan</th>
            </tr></thead>
            <tbody>
                @forelse($anak->pertumbuhan as $p)
                <tr>
                    <td>{{ $p->tanggal_pengukuran->format('d M Y') }}</td>
                    <td><strong>{{ $p->berat_badan }}</strong></td>
                    <td>{{ $p->tinggi_badan }}</td>
                    <td><span class="badge {{ $p->status_gizi_badge['class'] }}">{{ $p->status_gizi_badge['label'] }}</span></td>
                    <td style="color:var(--text-muted);font-size:12px;">{{ $p->catatan ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5"><div class="empty-state" style="padding:20px;">
                    <div class="empty-icon">📏</div>
                    <div class="empty-title">Belum ada data pengukuran</div>
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

    // ── Chart Berat Badan vs WHO ────────────────────────────
    const ctxBerat = document.getElementById('chartBeratWHO')?.getContext('2d');
    if (ctxBerat) {
        new Chart(ctxBerat, {
            type: 'line',
            data: {
                labels: whoWeightLabels,
                datasets: [
                    { label: 'Median', data: whoWeightMed, ...dashedLine('#94a3b8') },
                    { label: '-2SD',   data: whoWeightM2,  ...dashedLine('#f59e0b') },
                    { label: '-3SD',   data: whoWeightM3,  ...dashedLine('#ef4444') },
                    { label: '+2SD',   data: whoWeightP2,  ...dashedLine('#3b82f6') },
                    {
                        label: 'Berat Anak (kg)',
                        data: anakBerat, 
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
        new Chart(ctxTinggi, {
            type: 'line',
            data: {
                labels: whoHeightLabels,
                datasets: [
                    { label: 'Median', data: whoHeightMed, ...dashedLine('#94a3b8') },
                    { label: '-2SD',   data: whoHeightM2,  ...dashedLine('#f59e0b') },
                    { label: '-3SD',   data: whoHeightM3,  ...dashedLine('#ef4444') },
                    { label: '+2SD',   data: whoHeightP2,  ...dashedLine('#3b82f6') },
                    {
                        label: 'Tinggi Anak (cm)',
                        data: anakTinggi, 
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
});
</script>
@endpush
@endsection
