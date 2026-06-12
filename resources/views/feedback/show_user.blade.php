@extends('layouts.user')
@section('title', 'Rekomendasi — ' . $anak->nama_anak)

@section('content')
<div class="space-y-6 pb-24">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('feedback.index') }}" class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-on-surface-variant active:scale-95 transition-transform">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-xl font-bold text-on-surface">Rekomendasi Gizi</h1>
            <p class="text-xs text-on-surface-variant">{{ $anak->nama_anak }} • {{ $anak->umur_label }}</p>
        </div>
    </div>

    <!-- Profil Anak Ringkas -->
    <div class="bg-surface-container-lowest rounded-3xl p-5 shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low flex gap-4 items-center">
        <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-3xl shrink-0">
            {!! $anak->jenis_kelamin === 'L' ? '<i data-feather="user"></i>' : '<i data-feather="user"></i>' !!}
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="font-extrabold text-sm sm:text-base text-on-surface leading-tight">{{ $anak->nama_anak }}</h2>
            <p class="text-xs text-on-surface-variant font-medium mt-0.5">{{ $anak->umur_label }} • {{ $anak->jenis_kelamin_label }}</p>
            @if($anak->pertumbuhan->isNotEmpty())
                @php $badge = $anak->pertumbuhan->first()->status_gizi_badge; @endphp
                <span class="inline-block mt-2 px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider {{ $badge['class'] === 'badge-success' ? 'bg-green-500/10 text-green-600' : ($badge['class'] === 'badge-warning' ? 'bg-amber-500/10 text-amber-600' : ($badge['class'] === 'badge-danger' ? 'bg-red-500/10 text-red-600' : 'bg-surface-container text-on-surface-variant')) }}">
                    {{ $badge['label'] }}
                </span>
            @endif
        </div>
    </div>

    <!-- Pemenuhan Gizi Harian (Rata-rata 7 hari) -->
    <div class="space-y-3">
        <h3 class="text-sm font-bold text-on-surface flex items-center gap-1.5 ml-1">
            <span class="material-symbols-outlined text-base text-primary">analytics</span> Rata-rata 7 Hari Terakhir
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @php
                $nutrisiConfig = [
                    'kalori'      => ['icon' => '<i data-feather="zap"></i>', 'label' => 'Kalori', 'satuan' => 'kkal', 'color' => 'bg-amber-500', 'textColor' => 'text-amber-600'],
                    'protein'     => ['icon' => '<i data-feather="hash"></i>', 'label' => 'Protein', 'satuan' => 'g', 'color' => 'bg-green-500', 'textColor' => 'text-green-600'],
                    'karbohidrat' => ['icon' => '<i data-feather="hash"></i>', 'label' => 'Karbo', 'satuan' => 'g', 'color' => 'bg-purple-500', 'textColor' => 'text-purple-600'],
                    'lemak'       => ['icon' => '<i data-feather="hash"></i>', 'label' => 'Lemak', 'satuan' => 'g', 'color' => 'bg-red-500', 'textColor' => 'text-red-600'],
                ];
            @endphp
            @foreach($nutrisiConfig as $key => $cfg)
            @php
                $stat   = $nutrisiStats[$key];
                $pct    = $stat['target'] > 0 ? min(100, round($stat['nilai'] / $stat['target'] * 100)) : 0;
            @endphp
            <div class="bg-surface-container-lowest rounded-3xl p-4 shadow-sm border border-surface-container-low space-y-2">
                <div class="flex justify-between items-center text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">
                    <span>{{ $cfg['icon'] }} {{ $cfg['label'] }}</span>
                    <span class="{{ $cfg['textColor'] }}">{{ $pct }}%</span>
                </div>
                <div class="space-y-1">
                    <div class="text-base font-extrabold text-on-surface">
                        {{ $stat['nilai'] }} <span class="text-[10px] text-on-surface-variant font-semibold">{{ $cfg['satuan'] }}</span>
                    </div>
                    <div class="h-2 bg-surface-container rounded-full overflow-hidden">
                        <div class="h-full {{ $cfg['color'] }} rounded-full" style="width:{{ $pct }}%"></div>
                    </div>
                    <div class="text-[9px] font-bold text-on-surface-variant/80">Target: {{ $stat['target'] }}{{ $cfg['satuan'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Catatan Bidan / Admin (Manual) -->
    <div class="space-y-3">
        <h3 class="text-sm font-bold text-on-surface flex items-center gap-1.5 ml-1">
            <span class="material-symbols-outlined text-base text-primary">assignment_ind</span> Catatan Khusus Bidan
        </h3>

        @forelse($anak->feedbackAnak as $fbManual)
        <div class="bg-surface-container-lowest rounded-[24px] p-5 border-l-4 border-primary shadow-sm border-y border-r border-surface-container-low space-y-3">
            <div class="flex justify-between items-start">
                <div class="flex items-center gap-2">
                    <span class="text-2xl"><i data-feather="user"></i><i data-feather="activity"></i></span>
                    <div>
                        <h4 class="font-bold text-xs text-on-surface leading-tight">Saran Bidan</h4>
                        <p class="text-[9px] text-on-surface-variant mt-0.5">Petugas Kesehatan</p>
                    </div>
                </div>
                <span class="text-[9px] font-bold text-on-surface-variant/80 bg-surface-container px-2 py-0.5 rounded">{{ $fbManual->created_at->format('d M Y') }}</span>
            </div>
            <p class="text-xs text-on-surface font-medium leading-relaxed whitespace-pre-wrap">{{ $fbManual->pesan }}</p>
        </div>
        @empty
        <div class="bg-surface-container-lowest rounded-3xl p-6 text-center border border-surface-container-low shadow-sm">
            <p class="text-xs text-on-surface-variant">Belum ada catatan khusus dari bidan.</p>
        </div>
        @endforelse
    </div>

    <!-- Analisis & Rekomendasi Otomatis (Sistem) -->
    <div class="space-y-3">
        <h3 class="text-sm font-bold text-on-surface flex items-center gap-1.5 ml-1">
            <span class="material-symbols-outlined text-base text-primary">smart_toy</span> Rekomendasi Otomatis
        </h3>

        @foreach($feedbacks as $fb)
        @php
            $colors = [
                'success' => ['bg' => 'bg-green-500/5', 'border' => 'border-green-500', 'icon_bg' => 'bg-green-500/10', 'text' => 'text-green-600'],
                'warning' => ['bg' => 'bg-amber-500/5', 'border' => 'border-amber-500', 'icon_bg' => 'bg-amber-500/10', 'text' => 'text-amber-600'],
                'danger'  => ['bg' => 'bg-red-500/5', 'border' => 'border-red-500', 'icon_bg' => 'bg-red-500/10', 'text' => 'text-red-600'],
                'info'    => ['bg' => 'bg-primary/5', 'border' => 'border-primary', 'icon_bg' => 'bg-primary/10', 'text' => 'text-primary'],
                'neutral' => ['bg' => 'bg-surface-container/20',  'border' => 'border-outline', 'icon_bg' => 'bg-surface-container', 'text' => 'text-on-surface-variant'],
            ];
            $c = $colors[$fb['tipe']] ?? $colors['neutral'];
        @endphp
        <div class="bg-surface-container-lowest rounded-[24px] p-5 border-l-4 {{ $c['border'] }} shadow-sm border-y border-r border-surface-container-low space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xl {{ $c['icon_bg'] }}">
                    {{ $fb['icon'] }}
                </div>
                <h4 class="font-extrabold text-xs {{ $c['text'] }} leading-tight">{{ $fb['judul'] }}</h4>
            </div>
            
            <p class="text-xs text-on-surface font-medium leading-relaxed">{{ $fb['pesan'] }}</p>

            @if(!empty($fb['saran']))
            <div class="bg-surface-container/60 p-3 rounded-2xl space-y-1.5">
                <span class="text-[9px] font-bold {{ $c['text'] }} uppercase tracking-wider block mb-1">Saran Tindakan:</span>
                @foreach($fb['saran'] as $saran)
                <div class="text-[11px] font-semibold text-on-surface leading-normal flex gap-1.5 items-start">
                    <span class="{{ $c['text'] }} font-black">•</span>
                    <span>{{ $saran }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Riwayat Pengukuran (Mini) -->
    @if($anak->pertumbuhan->isNotEmpty())
    <div class="bg-surface-container-lowest rounded-3xl p-5 shadow-sm border border-surface-container-low space-y-4">
        <h3 class="text-xs font-bold text-on-surface-variant uppercase tracking-wider"><i data-feather="bar-chart-2"></i> Data Antropometri Terakhir</h3>
        @php $p = $anak->pertumbuhan->first(); @endphp
        <div class="grid grid-cols-3 gap-2 text-center text-xs font-semibold text-on-surface">
            <div>
                <span class="text-[9px] block text-on-surface-variant/80 uppercase font-bold">Berat</span>
                {{ $p->berat_badan }} kg
            </div>
            <div>
                <span class="text-[9px] block text-on-surface-variant/80 uppercase font-bold">Tinggi</span>
                {{ $p->tinggi_badan }} cm
            </div>
            <div>
                <span class="text-[9px] block text-on-surface-variant/80 uppercase font-bold">Tgl Ukur</span>
                {{ $p->tanggal_pengukuran->format('d M Y') }}
            </div>
        </div>
    </div>
    @endif

    <!-- Disclaimer -->
    <div class="bg-amber-500/10 p-4 rounded-3xl border border-amber-500/20 text-[10px] sm:text-xs font-semibold text-amber-600/90 leading-relaxed text-center">
        <i data-feather="alert-triangle"></i> <strong>PENTING:</strong> Rekomendasi ini merupakan analisis sistem berdasarkan data yang diinput. Jika anak mengalami gejala medis serius, selalu konsultasikan langsung ke dokter atau puskesmas.
    </div>

    <!-- Bottom Actions -->
    <div class="fixed bottom-20 left-0 right-0 p-4 bg-surface-container/80 backdrop-blur-md z-40 max-w-md mx-auto border-t border-surface-container-high flex gap-3">
        <a href="{{ route('pertumbuhan.create') }}?anak_id={{ $anak->id }}" class="flex-1 bg-surface-container-high text-on-surface py-3.5 rounded-2xl text-center text-xs font-bold active:scale-95 transition-transform flex items-center justify-center gap-1">
            <span class="material-symbols-outlined text-base">straighten</span> Ukur
        </a>
        <a href="{{ route('recall.create') }}?anak_id={{ $anak->id }}" class="flex-1 bg-primary text-on-primary py-3.5 rounded-2xl text-center text-xs font-bold active:scale-95 transition-transform flex items-center justify-center gap-1 shadow-md shadow-primary/20">
            <span class="material-symbols-outlined text-base">restaurant</span> Catat Makan
        </a>
    </div>
</div>
@endsection
