@extends('layouts.user')
@section('title', $anak->nama_anak)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('anak.index') }}" class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-on-surface-variant active:scale-95 transition-transform">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div class="flex gap-2">
            <a href="{{ route('anak.edit', $anak) }}" class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary active:scale-95 transition-transform">
                <span class="material-symbols-outlined">edit</span>
            </a>
        </div>
    </div>

    <!-- Profil Anak -->
    <div class="bg-surface-container-lowest rounded-3xl p-6 shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-b from-primary/10 to-transparent"></div>
        <div class="relative">
            <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-primary/20 to-primary/60 flex items-center justify-center text-white text-5xl shadow-inner mb-4 border-4 border-white">
                {{ $anak->jenis_kelamin === 'L' ? '👦' : '👧' }}
            </div>
            <h1 class="text-2xl font-bold text-on-surface mb-1">{{ $anak->nama_anak }}</h1>
            <p class="text-sm font-medium text-on-surface-variant">{{ $anak->umur_label }} • {{ $anak->jenis_kelamin_label }}</p>
            
            @if($anak->pertumbuhan_terakhir)
                @php 
                    $badge = $anak->pertumbuhan_terakhir->status_gizi_badge; 
                    $status = $anak->pertumbuhan_terakhir->status_gizi;
                    $statusBg = 'bg-tertiary-container/10 text-tertiary-container';
                    if(in_array($status, ['stunting', 'wasting', 'underweight', 'kurang', 'buruk', 'obesitas'])) {
                        $statusBg = 'bg-secondary-container/10 text-secondary-container';
                    }
                @endphp
                <div class="mt-4">
                    <span class="{{ $statusBg }} px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide inline-flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full {{ str_contains($statusBg, 'tertiary') ? 'bg-tertiary-container' : 'bg-secondary-container' }}"></span>
                        {{ $badge['label'] }}
                    </span>
                </div>
            @endif
        </div>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4 text-left border-t border-surface-container-high pt-6">
            <div>
                <p class="text-[10px] text-on-surface-variant font-medium uppercase tracking-wider mb-0.5">Tanggal Lahir</p>
                <p class="text-sm font-bold text-on-surface">{{ $anak->tanggal_lahir->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-[10px] text-on-surface-variant font-medium uppercase tracking-wider mb-0.5">Gol. Darah</p>
                <p class="text-sm font-bold text-on-surface">{{ strtoupper(str_replace('_',' ',$anak->golongan_darah)) }}</p>
            </div>
            <div>
                <p class="text-[10px] text-on-surface-variant font-medium uppercase tracking-wider mb-0.5">Berat Lahir</p>
                <p class="text-sm font-bold text-on-surface">{{ $anak->berat_lahir ? $anak->berat_lahir.' kg' : '-' }}</p>
            </div>
            <div>
                <p class="text-[10px] text-on-surface-variant font-medium uppercase tracking-wider mb-0.5">Panjang Lahir</p>
                <p class="text-sm font-bold text-on-surface">{{ $anak->panjang_lahir ? $anak->panjang_lahir.' cm' : '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('pertumbuhan.create') }}?anak_id={{ $anak->id }}" class="bg-primary text-on-primary rounded-2xl p-4 flex flex-col items-center justify-center gap-2 shadow-md active:scale-95 transition-transform">
            <span class="material-symbols-outlined text-3xl">straighten</span>
            <span class="text-xs font-bold text-center">Input<br>Pengukuran</span>
        </a>
        <a href="{{ route('recall.create') }}?anak_id={{ $anak->id }}" class="bg-tertiary-container text-white rounded-2xl p-4 flex flex-col items-center justify-center gap-2 shadow-md active:scale-95 transition-transform">
            <span class="material-symbols-outlined text-3xl">restaurant</span>
            <span class="text-xs font-bold text-center">Input<br>Recall Gizi</span>
        </a>
    </div>

    <!-- Ringkasan Gizi Hari Ini -->
    <div class="bg-surface-container-lowest rounded-3xl p-6 shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-base font-bold text-on-surface flex items-center gap-2">
                <span class="text-xl">🍽️</span> Gizi Hari Ini
            </h2>
            <span class="text-[11px] font-semibold text-on-surface-variant bg-surface-container py-1 px-3 rounded-full">{{ today()->format('d M Y') }}</span>
        </div>

        @if($ringkasanHariIni && $ringkasanHariIni->total_kalori > 0)
            <div class="space-y-5">
                <!-- Kalori -->
                <div>
                    <div class="flex justify-between text-sm mb-1.5 font-semibold">
                        <span class="text-on-surface">🔥 Kalori</span>
                        <span class="text-primary">{{ number_format($ringkasanHariIni->total_kalori,0) }} / {{ $akg['energi'] ?? 1000 }} kkal</span>
                    </div>
                    <div class="h-2.5 bg-surface-container-low rounded-full overflow-hidden">
                        @php $pctKal = min(round($ringkasanHariIni->total_kalori/($akg['energi']??1000)*100),100); @endphp
                        <div class="h-full bg-[#f59e0b] rounded-full" style="width:{{ $pctKal }}%"></div>
                    </div>
                </div>

                <!-- Makro Nutrien -->
                <div class="grid grid-cols-3 gap-4">
                    <!-- Protein -->
                    <div>
                        <div class="text-[10px] font-bold text-on-surface-variant mb-1 flex justify-between">
                            <span>🥩 Pro</span>
                            <span>{{ number_format($ringkasanHariIni->total_protein,1) }}g</span>
                        </div>
                        <div class="h-1.5 bg-surface-container-low rounded-full overflow-hidden">
                            @php $pctPro = min(round($ringkasanHariIni->total_protein/($akg['protein']??20)*100),100); @endphp
                            <div class="h-full bg-[#16a34a] rounded-full" style="width:{{ $pctPro }}%"></div>
                        </div>
                    </div>
                    <!-- Karbo -->
                    <div>
                        <div class="text-[10px] font-bold text-on-surface-variant mb-1 flex justify-between">
                            <span>🌾 Karbo</span>
                            <span>{{ number_format($ringkasanHariIni->total_karbo,1) }}g</span>
                        </div>
                        <div class="h-1.5 bg-surface-container-low rounded-full overflow-hidden">
                            @php $pctKar = min(round($ringkasanHariIni->total_karbo/($akg['karbo']??130)*100),100); @endphp
                            <div class="h-full bg-[#9333ea] rounded-full" style="width:{{ $pctKar }}%"></div>
                        </div>
                    </div>
                    <!-- Lemak -->
                    <div>
                        <div class="text-[10px] font-bold text-on-surface-variant mb-1 flex justify-between">
                            <span>🧈 Lemak</span>
                            <span>{{ number_format($ringkasanHariIni->total_lemak,1) }}g</span>
                        </div>
                        <div class="h-1.5 bg-surface-container-low rounded-full overflow-hidden">
                            @php $pctLem = min(round($ringkasanHariIni->total_lemak/($akg['lemak']??30)*100),100); @endphp
                            <div class="h-full bg-[#ef4444] rounded-full" style="width:{{ $pctLem }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('recall.index') }}?anak_id={{ $anak->id }}" class="mt-6 block w-full py-2.5 text-center bg-primary/10 text-primary font-bold text-sm rounded-xl active:scale-95 transition-transform">
                Lihat Riwayat Asupan
            </a>
        @else
            <div class="text-center py-6">
                <div class="text-4xl mb-3 opacity-50">🍽️</div>
                <p class="text-sm font-bold text-on-surface mb-1">Belum ada asupan</p>
                <p class="text-xs text-on-surface-variant mb-4">Catat makanan anak hari ini</p>
                <a href="{{ route('recall.create') }}?anak_id={{ $anak->id }}" class="inline-block px-6 py-2.5 bg-primary text-on-primary font-bold text-sm rounded-xl shadow-md active:scale-95 transition-transform">
                    Catat Sekarang
                </a>
            </div>
        @endif
    </div>

    <!-- Catatan Khusus dari Admin -->
    @if($anak->feedbackAnak->isNotEmpty())
    <div class="bg-secondary-container/5 rounded-3xl p-6 shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-secondary-container/20">
        <h2 class="text-base font-bold text-secondary-container flex items-center gap-2 mb-4">
            <span class="text-xl">👩‍⚕️</span> Pesan Bidan / Admin
        </h2>
        <div class="space-y-4">
            @foreach($anak->feedbackAnak as $fbManual)
            <div class="bg-white p-4 rounded-2xl border-l-4 border-secondary-container shadow-sm">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-[10px] font-bold text-secondary-container bg-secondary-container/10 px-2 py-0.5 rounded-md">Pesan</span>
                    <span class="text-[10px] text-on-surface-variant">{{ $fbManual->created_at->format('d M Y, H:i') }}</span>
                </div>
                <p class="text-sm text-on-surface whitespace-pre-wrap leading-relaxed">{{ $fbManual->pesan }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Kurva Pertumbuhan WHO -->
    <div class="bg-surface-container-lowest rounded-3xl p-6 shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-base font-bold text-on-surface flex items-center gap-2">
                <span class="text-xl">📈</span> Kurva WHO
            </h2>
        </div>
        
        <div class="flex gap-2 mb-6 overflow-x-auto pb-2 scrollbar-hide">
            <a href="{{ request()->fullUrlWithQuery(['interval' => 1]) }}" class="whitespace-nowrap px-4 py-2 rounded-xl text-xs font-bold transition-colors {{ $interval == 1 ? 'bg-primary text-on-primary shadow-md' : 'bg-surface-container-low text-on-surface-variant' }}">0-20 Bulan</a>
            <a href="{{ request()->fullUrlWithQuery(['interval' => 2]) }}" class="whitespace-nowrap px-4 py-2 rounded-xl text-xs font-bold transition-colors {{ $interval == 2 ? 'bg-primary text-on-primary shadow-md' : 'bg-surface-container-low text-on-surface-variant' }}">21-40 Bulan</a>
            <a href="{{ request()->fullUrlWithQuery(['interval' => 3]) }}" class="whitespace-nowrap px-4 py-2 rounded-xl text-xs font-bold transition-colors {{ $interval == 3 ? 'bg-primary text-on-primary shadow-md' : 'bg-surface-container-low text-on-surface-variant' }}">41-60 Bulan</a>
        </div>

        <div class="space-y-8">
            <!-- Chart BB -->
            <div>
                <h3 class="text-sm font-bold text-on-surface mb-1">Berat Badan (WAZ)</h3>
                <p class="text-[10px] text-on-surface-variant mb-4">{{ $minUmur }} - {{ $maxUmur }} Bulan</p>
                <div class="h-56 relative w-full">
                    <canvas id="chartBeratWHO"></canvas>
                </div>
            </div>
            
            <div class="border-t border-surface-container-high"></div>

            <!-- Chart TB -->
            <div>
                <h3 class="text-sm font-bold text-on-surface mb-1">Tinggi Badan (HAZ)</h3>
                <p class="text-[10px] text-on-surface-variant mb-4">{{ $minUmur }} - {{ $maxUmur }} Bulan</p>
                <div class="h-56 relative w-full">
                    <canvas id="chartTinggiWHO"></canvas>
                </div>
            </div>
            
            <!-- Legend -->
            <div class="flex flex-wrap gap-x-4 gap-y-2 justify-center mt-2 border-t border-surface-container-high pt-4">
                <div class="flex items-center gap-1.5"><div class="w-4 h-1 bg-primary rounded-full"></div><span class="text-[10px] font-medium">Anak</span></div>
                <div class="flex items-center gap-1.5"><div class="w-4 h-0.5 border-t-2 border-dashed border-slate-400"></div><span class="text-[10px] font-medium">Median</span></div>
                <div class="flex items-center gap-1.5"><div class="w-4 h-0.5 border-t-2 border-dashed border-amber-500"></div><span class="text-[10px] font-medium">-2SD</span></div>
                <div class="flex items-center gap-1.5"><div class="w-4 h-0.5 border-t-2 border-dashed border-red-500"></div><span class="text-[10px] font-medium">-3SD</span></div>
            </div>
        </div>
    </div>

    <!-- Riwayat Pertumbuhan -->
    <div class="bg-surface-container-lowest rounded-3xl p-6 shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-base font-bold text-on-surface flex items-center gap-2">
                <span class="text-xl">📋</span> Riwayat Ukur
            </h2>
            <a href="{{ route('pertumbuhan.export-pdf') }}?anak_id={{ $anak->id }}" class="text-primary text-xs font-bold px-3 py-1.5 bg-primary/10 rounded-lg">📄 PDF</a>
        </div>
        
        <div class="space-y-3">
            @forelse($anak->pertumbuhan as $p)
            <div class="p-4 rounded-2xl border border-surface-container-low hover:border-primary/20 transition-colors">
                <div class="flex justify-between items-start mb-2">
                    <div class="font-bold text-sm">{{ $p->tanggal_pengukuran->format('d M Y') }}</div>
                    <span class="bg-surface-container-high px-2 py-0.5 rounded text-[10px] font-bold">{{ $p->status_gizi_badge['label'] }}</span>
                </div>
                <div class="flex gap-4 text-xs font-medium text-on-surface-variant">
                    <span>⚖️ {{ $p->berat_badan }} kg</span>
                    <span>📏 {{ $p->tinggi_badan }} cm</span>
                </div>
            </div>
            @empty
            <div class="text-center py-6">
                <span class="text-2xl opacity-50 block mb-2">📏</span>
                <p class="text-xs text-on-surface-variant">Belum ada data pengukuran</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.addEventListener('load', function () {
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

    const anakLabels = @json($chartLabels);
    const anakBerat  = @json($chartBerat);
    const anakTinggi = @json($chartTinggi);

    const dashedLine = (color) => ({
        borderColor: color, backgroundColor: 'transparent',
        borderWidth: 1.5, borderDash: [6, 3],
        pointRadius: 0, fill: false, tension: 0.3,
    });

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
                        borderColor: '#4648d4', // primary
                        backgroundColor: 'rgba(70, 72, 212, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#4648d4',
                        pointRadius: 4,
                        fill: false,
                        tension: 0.4,
                        spanGaps: true
                    },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 9, family: "'Manrope', sans-serif" } } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 9, family: "'Manrope', sans-serif" } } }
                }
            }
        });
    }

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
                        borderColor: '#00885d', // tertiary
                        backgroundColor: 'rgba(0, 136, 93, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#00885d',
                        pointRadius: 4,
                        fill: false,
                        tension: 0.4,
                        spanGaps: true
                    },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 9, family: "'Manrope', sans-serif" } } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 9, family: "'Manrope', sans-serif" } } }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
