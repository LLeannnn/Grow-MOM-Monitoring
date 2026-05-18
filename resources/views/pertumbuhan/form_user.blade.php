@extends('layouts.user')
@section('title', 'Input Pengukuran')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('pertumbuhan.index') }}" class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-on-surface-variant active:scale-95 transition-transform">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-xl font-bold text-on-surface">Input Pengukuran</h1>
            <p class="text-xs text-on-surface-variant">Catat berat dan tinggi badan anak hari ini</p>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('pertumbuhan.store') }}" class="bg-surface-container-lowest rounded-3xl p-5 sm:p-6 shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low space-y-5">
        @csrf

        <!-- Anak -->
        <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1.5 ml-1">Pilih Anak <span class="text-error">*</span></label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant opacity-70 material-symbols-outlined text-lg">child_care</span>
                <select name="anak_id" required class="w-full pl-11 pr-4 py-3 bg-surface-container-low border-transparent rounded-2xl text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold">
                    <option value="">-- Pilih Anak --</option>
                    @foreach($anakList as $a)
                    <option value="{{ $a->id }}" {{ old('anak_id', request('anak_id')) == $a->id ? 'selected' : '' }}>
                        {{ $a->nama_anak }}
                    </option>
                    @endforeach
                </select>
            </div>
            @error('anak_id')<span class="text-[10px] text-error font-bold mt-1 ml-1 block">{{ $message }}</span>@enderror
        </div>

        <!-- Tanggal Pengukuran -->
        <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1.5 ml-1">Tanggal Pengukuran <span class="text-error">*</span></label>
            <div class="relative">
                <input type="date" name="tanggal_pengukuran" value="{{ old('tanggal_pengukuran', today()->format('Y-m-d')) }}" required class="w-full px-4 py-3 bg-surface-container-low border-transparent rounded-2xl text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold">
            </div>
            @error('tanggal_pengukuran')<span class="text-[10px] text-error font-bold mt-1 ml-1 block">{{ $message }}</span>@enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Berat Badan -->
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 ml-1">Berat Badan <span class="text-error">*</span></label>
                <div class="relative flex items-center">
                    <input type="number" name="berat_badan" value="{{ old('berat_badan') }}" step="0.01" placeholder="Cth: 8.5" required class="w-full pl-4 pr-10 py-3 bg-surface-container-low border-transparent rounded-2xl text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold placeholder:font-normal">
                    <span class="absolute right-4 text-xs font-bold text-on-surface-variant">kg</span>
                </div>
                @error('berat_badan')<span class="text-[10px] text-error font-bold mt-1 ml-1 block">{{ $message }}</span>@enderror
            </div>

            <!-- Tinggi Badan -->
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 ml-1">Tinggi Badan <span class="text-error">*</span></label>
                <div class="relative flex items-center">
                    <input type="number" name="tinggi_badan" value="{{ old('tinggi_badan') }}" step="0.1" placeholder="Cth: 72.5" required class="w-full pl-4 pr-10 py-3 bg-surface-container-low border-transparent rounded-2xl text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold placeholder:font-normal">
                    <span class="absolute right-4 text-xs font-bold text-on-surface-variant">cm</span>
                </div>
                @error('tinggi_badan')<span class="text-[10px] text-error font-bold mt-1 ml-1 block">{{ $message }}</span>@enderror
            </div>
        </div>

        <!-- Catatan -->
        <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1.5 ml-1">Catatan Khusus <span class="text-[10px] font-normal text-on-surface-variant opacity-70">(Opsional)</span></label>
            <textarea name="catatan" rows="3" placeholder="Misal: Anak sedang flu ringan..." class="w-full px-4 py-3 bg-surface-container-low border-transparent rounded-2xl text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all placeholder:font-normal">{{ old('catatan') }}</textarea>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-primary text-on-primary py-3.5 rounded-2xl font-bold text-sm shadow-md shadow-primary/20 active:scale-[0.98] transition-transform flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-xl">save</span> Simpan & Analisis
            </button>
        </div>
    </form>

    <!-- Info WHO -->
    <div class="bg-tertiary-container/5 rounded-3xl p-5 border border-tertiary-container/20 flex gap-4 items-start">
        <div class="w-10 h-10 rounded-full bg-tertiary-container/10 flex items-center justify-center text-tertiary-container shrink-0">
            <span class="material-symbols-outlined">info</span>
        </div>
        <div>
            <h3 class="text-sm font-bold text-on-surface mb-1">Dihitung Secara Otomatis</h3>
            <p class="text-xs text-on-surface-variant leading-relaxed">Status gizi anak Anda akan dihitung secara otomatis berdasarkan standar grafik pertumbuhan WHO (World Health Organization).</p>
        </div>
    </div>
</div>
@endsection
