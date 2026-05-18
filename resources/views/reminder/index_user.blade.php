@extends('layouts.user')
@section('title', 'Reminder')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-on-surface">🔔 Reminder</h1>
        <p class="text-sm text-on-surface-variant mt-1">Jadwal imunisasi, posyandu & kontrol penting</p>
    </div>

    <!-- Filter -->
    <form method="GET" class="bg-surface-container-lowest p-4 rounded-3xl shadow-[0px_5px_15px_rgba(30,41,59,0.03)] space-y-3 border border-surface-container-low">
        <div class="flex gap-2">
            <select name="status" class="w-1/2 bg-surface-container-low border-transparent rounded-xl text-sm px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
                <option value="selesai" {{ request('status')=='selesai'?'selected':'' }}>Selesai</option>
            </select>
            <select name="tipe" class="w-1/2 bg-surface-container-low border-transparent rounded-xl text-sm px-3 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold">
                <option value="">Semua Tipe</option>
                @foreach(['imunisasi','posyandu','mpasi','kontrol','lainnya'] as $t)
                <option value="{{ $t }}" {{ request('tipe')==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-primary text-on-primary py-2.5 rounded-xl text-sm font-bold active:scale-[0.98] transition-transform">Filter</button>
            @if(request()->hasAny(['status','tipe']))
                <a href="{{ route('reminder.index') }}" class="px-5 py-2.5 bg-surface-container-high text-on-surface rounded-xl text-sm font-bold flex items-center justify-center active:scale-[0.98] transition-transform">Reset</a>
            @endif
        </div>
    </form>

    <!-- List Reminder -->
    <div class="space-y-4">
        @forelse($reminders as $r)
        @php
            $isSelesai = $r->status === 'selesai';
            $isTerlambat = !$isSelesai && $r->is_expired;
            
            $bgType = 'bg-[#f59e0b]/10 text-[#f59e0b]';
            if ($r->tipe === 'imunisasi') $bgType = 'bg-[#4648d4]/10 text-[#4648d4]';
            elseif ($r->tipe === 'posyandu') $bgType = 'bg-[#00885d]/10 text-[#00885d]';
            elseif ($r->tipe === 'mpasi') $bgType = 'bg-[#e11d48]/10 text-[#e11d48]';
            elseif ($r->tipe === 'kontrol') $bgType = 'bg-purple-600/10 text-purple-600';
        @endphp
        <div class="bg-surface-container-lowest rounded-[24px] p-5 shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low flex gap-4 items-start relative overflow-hidden">
            <!-- Icon / Type -->
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0 {{ $bgType }}">
                {{ $r->tipe_label['icon'] }}
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0 space-y-1">
                <div class="flex flex-wrap gap-2 items-center">
                    <h3 class="font-bold text-sm text-on-surface leading-tight">{{ $r->judul }}</h3>
                    
                    @if($isSelesai)
                        <span class="bg-green-500/10 text-green-600 px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider">Selesai</span>
                    @elseif($isTerlambat)
                        <span class="bg-red-500/10 text-red-600 px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider">Terlambat</span>
                    @else
                        <span class="bg-primary/10 text-primary px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider">Aktif</span>
                    @endif
                </div>

                <p class="text-[10px] font-bold text-on-surface-variant/80">👶 {{ $r->anak->nama_anak }}</p>
                
                <p class="text-[10px] font-semibold text-on-surface-variant opacity-75 flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">calendar_today</span>
                    {{ $r->tanggal_reminder->format('d M Y, H:i') }}
                </p>

                <p class="text-xs text-on-surface leading-relaxed mt-2 pt-2 border-t border-surface-container font-medium">{{ $r->pesan }}</p>
            </div>

            <!-- Action -->
            @if($r->status === 'aktif')
            <div class="self-center">
                <form method="POST" action="{{ route('reminder.selesai', $r) }}" class="m-0">
                    @csrf @method('PATCH')
                    <button type="submit" class="w-10 h-10 rounded-full bg-green-500/10 text-green-600 flex items-center justify-center shadow-sm active:scale-95 transition-transform" title="Tandai Selesai">
                        <span class="material-symbols-outlined text-xl font-bold">check</span>
                    </button>
                </form>
            </div>
            @endif
        </div>
        @empty
        <div class="bg-surface-container-lowest rounded-3xl p-8 text-center shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low">
            <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center text-3xl mb-4">🔔</div>
            <div class="font-bold text-on-surface mb-2">Belum ada reminder</div>
            <div class="text-xs text-on-surface-variant">Jadwal penting atau imunisasi anak Anda akan muncul di sini</div>
        </div>
        @endforelse
    </div>

    @if($reminders->hasPages())
    <div class="mt-6">
        <div class="flex justify-between items-center bg-surface-container-lowest rounded-xl p-2 shadow-sm">
            @if ($reminders->onFirstPage())
                <span class="px-4 py-2 text-outline-variant font-semibold text-sm">Sebelumnya</span>
            @else
                <a href="{{ $reminders->previousPageUrl() }}" class="px-4 py-2 text-primary font-bold text-sm active:scale-95 transition-all">Sebelumnya</a>
            @endif

            <span class="text-xs font-bold text-on-surface-variant">Hal {{ $reminders->currentPage() }}</span>

            @if ($reminders->hasMorePages())
                <a href="{{ $reminders->nextPageUrl() }}" class="px-4 py-2 text-primary font-bold text-sm active:scale-95 transition-all">Selanjutnya</a>
            @else
                <span class="px-4 py-2 text-outline-variant font-semibold text-sm">Selanjutnya</span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
