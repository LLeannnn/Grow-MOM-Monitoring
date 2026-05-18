@extends('layouts.user')
@section('title','Beranda Saya')
@section('content')

    <!-- Hero Section -->
        <section class="space-y-1">
            <h1 class="text-2xl font-bold text-on-surface">Halo, {{ $ibu->nama_ibu }}! 👋</h1>
            <p class="text-base text-on-surface-variant">Pantau kesehatan dan gizi buah hati Anda hari ini</p>
        </section>

        @if($anakList->isEmpty())
        <!-- Empty State -->
        <section class="bg-surface-container-lowest rounded-3xl p-8 text-center shadow-[0px_10px_30px_rgba(30,41,59,0.04)]">
            <div class="w-20 h-20 mx-auto bg-primary/10 rounded-full flex items-center justify-center text-4xl mb-4">👶</div>
            <h2 class="text-xl font-bold text-on-surface mb-2">Belum ada data anak</h2>
            <p class="text-on-surface-variant mb-6 text-sm">Yuk tambahkan data anak pertama Anda untuk mulai memantau tumbuh kembangnya!</p>
            <a href="{{ route('anak.create') }}" class="w-full py-4 px-6 bg-primary text-on-primary rounded-xl font-semibold flex items-center justify-center gap-2 shadow-lg hover:bg-primary-container active:scale-95 transition-all duration-200">
                <span class="material-symbols-outlined">person_add</span>
                Tambah Data Anak Sekarang
            </a>
        </section>
        @else

        <!-- Child Cards Grid (Bento Style) -->
        <section class="space-y-4">
            <h2 class="text-lg font-bold">Data Anak</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 md:gap-6">
                @foreach($anakList as $anak)
                @php
                    $recall = $recallHariIni[$anak->id] ?? null;
                    $umurBulan = $anak->umur_bulan ?? 0;
                    $akg = \App\Http\Controllers\RecallGiziController::getAkg($umurBulan);
                    $pctKal = $recall ? min(100, round($recall->total_kal / $akg['energi'] * 100)) : 0;
                    $status = $anak->pertumbuhan->sortByDesc('tanggal_pengukuran')->first()?->status_gizi ?? null;
                    
                    // Set colors based on status
                    $statusColor = 'tertiary-container'; // default green
                    $statusBg = 'bg-tertiary-container/10 text-tertiary-container';
                    if($status === 'stunting' || $status === 'wasting' || $status === 'underweight') {
                        $statusColor = 'secondary-container'; // red
                        $statusBg = 'bg-secondary-container/10 text-secondary-container';
                    }
                @endphp
                <div onclick="window.location.href='{{ route('anak.show',$anak->id) }}'" class="cursor-pointer hover:bg-surface-container transition-colors bg-surface-container-lowest rounded-[20px] p-4 shadow-[0px_10px_30px_rgba(30,41,59,0.04)] border-l-4 border-[color:var(--tw-colors-{{$statusColor}})] relative overflow-hidden flex flex-col h-full">
                    <div class="flex justify-between items-start mb-4 gap-2">
                        <div class="flex flex-col gap-2">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary/30 to-primary/80 flex items-center justify-center text-white text-lg shadow-inner shrink-0">
                                {{ $anak->jenis_kelamin === 'laki-laki' ? '👦' : '👧' }}
                            </div>
                            <div>
                                <h3 class="text-sm font-bold leading-tight">{{ Str::limit($anak->nama_anak, 12) }}</h3>
                                <p class="text-[10px] text-on-surface-variant font-medium mt-0.5">{{ $anak->umur_label ?? '-' }}</p>
                            </div>
                        </div>
                        @if($status)
                        <span class="{{ $statusBg }} px-2 py-1 rounded-full text-[9px] font-bold uppercase tracking-wide text-right leading-tight max-w-[50%]">{{ ucfirst($status) }}</span>
                        @endif
                    </div>
                    
                    <div class="space-y-2 mt-auto">
                        <div class="flex justify-between items-end">
                            <span class="text-xs font-semibold text-on-surface">Kalori</span>
                            <span class="text-[10px] font-bold text-on-surface-variant">{{ $pctKal }}%</span>
                        </div>
                        <div class="w-full bg-surface-container h-2 rounded-full overflow-hidden">
                            <div class="bg-{{$statusColor}} h-full rounded-full transition-all duration-1000 ease-out" style="width: {{ $pctKal }}%;"></div>
                        </div>
                        <div class="flex justify-between items-center text-on-surface-variant text-[9px]">
                            <span class="font-bold text-on-surface">{{ $recall ? round($recall->total_kal) : 0 }} kkal</span>
                            <span class="truncate ml-1">Target: {{ $akg['energi'] }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-t border-surface-container-high flex gap-2">
                        <a href="{{ route('recall.create', ['anak_id'=>$anak->id]) }}" onclick="event.stopPropagation();" class="w-full bg-primary text-on-primary py-2 rounded-xl text-xs font-semibold flex items-center justify-center gap-1.5 active:scale-95 hover:bg-primary-container transition-all shadow-md">
                            <span class="material-symbols-outlined text-[14px]">assignment</span>
                            Input Gizi
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Pengingat Aktif -->
        @if($reminderAktif->isNotEmpty())
        <section class="space-y-4 pb-6">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold">Pengingat Aktif</h2>
                <a href="{{ route('reminder.index') }}" class="text-primary text-sm font-semibold hover:underline">Lihat Semua</a>
            </div>
            <div class="space-y-3">
                @foreach($reminderAktif as $r)
                <div class="bg-surface-container-lowest rounded-2xl p-4 flex items-center justify-between group shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-surface rounded-xl flex flex-col items-center justify-center border border-outline-variant/30">
                            <span class="text-[10px] font-bold text-error uppercase">{{ \Carbon\Carbon::parse($r->tanggal_reminder)->translatedFormat('M') }}</span>
                            <span class="text-lg font-bold leading-none text-on-surface">{{ \Carbon\Carbon::parse($r->tanggal_reminder)->format('d') }}</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-on-surface">{{ $r->judul }}</h4>
                            <p class="text-[11px] font-medium text-on-surface-variant mt-0.5">📅 {{ \Carbon\Carbon::parse($r->tanggal_reminder)->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('reminder.selesai', $r->id) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="w-10 h-10 rounded-full border-2 border-outline-variant flex items-center justify-center text-outline-variant hover:border-tertiary-container hover:text-tertiary-container hover:bg-tertiary-container/10 transition-colors active:scale-90">
                            <span class="material-symbols-outlined">check</span>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </section>
        @endif
        @endif
@endsection