@extends('layouts.user')
@php
    $isEdit = isset($anak) && $anak->id;
    $title = $isEdit ? 'Edit Data Anak' : 'Tambah Anak';
@endphp
@section('title', $title)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ $isEdit ? route('anak.show', $anak) : route('anak.index') }}" class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-on-surface-variant active:scale-95 transition-transform">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-xl font-bold text-on-surface">{{ $title }}</h1>
            <p class="text-xs text-on-surface-variant">{{ $isEdit ? 'Perbarui data buah hati Anda' : 'Daftarkan buah hati Anda untuk monitoring' }}</p>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ $isEdit ? route('anak.update', $anak) : route('anak.store') }}" enctype="multipart/form-data" class="bg-surface-container-lowest rounded-3xl p-5 sm:p-6 shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low space-y-5">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <!-- Nama Anak -->
        <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1.5 ml-1">Nama Lengkap <span class="text-error">*</span></label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant opacity-70 material-symbols-outlined text-lg">person</span>
                <input type="text" name="nama_anak" value="{{ old('nama_anak', $isEdit ? $anak->nama_anak : '') }}" placeholder="Contoh: Budi Santoso" class="w-full pl-11 pr-4 py-3 bg-surface-container-low border-transparent rounded-2xl text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold placeholder:font-normal" required>
            </div>
            @error('nama_anak')<span class="text-[10px] text-error font-bold mt-1 ml-1 block">{{ $message }}</span>@enderror
        </div>

        <!-- Ibu ID (Hidden for normal user if we can auto-fill, but we keep it based on old form logic, wait, in user context, ibu_id should be auto-filled or hidden if they are already ibu, but old form shows dropdown. I'll show it as a disabled/readonly field or hidden if the user is not admin. Actually, I will just replicate the dropdown to avoid logic errors, but pre-select their own ibu_id). -->
        @if(auth()->user()->isAdmin())
        <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1.5 ml-1">Data Ibu <span class="text-error">*</span></label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant opacity-70 material-symbols-outlined text-lg">pregnant_woman</span>
                <select name="ibu_id" class="w-full pl-11 pr-4 py-3 bg-surface-container-low border-transparent rounded-2xl text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold" required>
                    <option value="">-- Pilih Ibu --</option>
                    @foreach($ibuList as $ibu)
                    <option value="{{ $ibu->id }}" {{ old('ibu_id', $isEdit ? $anak->ibu_id : request('ibu_id')) == $ibu->id ? 'selected' : '' }}>{{ $ibu->nama_ibu }}</option>
                    @endforeach
                </select>
            </div>
            @error('ibu_id')<span class="text-[10px] text-error font-bold mt-1 ml-1 block">{{ $message }}</span>@enderror
        </div>
        @else
            <!-- Auto assign for User -->
            @php
                $myIbuId = auth()->user()->ibu->id ?? null;
            @endphp
            <input type="hidden" name="ibu_id" value="{{ $myIbuId }}">
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Tanggal Lahir -->
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 ml-1">Tanggal Lahir <span class="text-error">*</span></label>
                <div class="relative">
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $isEdit ? $anak->tanggal_lahir->format('Y-m-d') : '') }}" class="w-full px-4 py-3 bg-surface-container-low border-transparent rounded-2xl text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold" required>
                </div>
                @error('tanggal_lahir')<span class="text-[10px] text-error font-bold mt-1 ml-1 block">{{ $message }}</span>@enderror
            </div>

            <!-- Jenis Kelamin -->
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 ml-1">Jenis Kelamin <span class="text-error">*</span></label>
                <div class="relative">
                    <select name="jenis_kelamin" class="w-full px-4 py-3 bg-surface-container-low border-transparent rounded-2xl text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold" required>
                        <option value="">-- Pilih --</option>
                        <option value="L" {{ old('jenis_kelamin', $isEdit ? $anak->jenis_kelamin : '') == 'L' ? 'selected' : '' }}>👦 Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin', $isEdit ? $anak->jenis_kelamin : '') == 'P' ? 'selected' : '' }}>👧 Perempuan</option>
                    </select>
                </div>
                @error('jenis_kelamin')<span class="text-[10px] text-error font-bold mt-1 ml-1 block">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Berat Lahir -->
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 ml-1">Berat Lahir (kg)</label>
                <div class="relative">
                    <input type="number" name="berat_lahir" value="{{ old('berat_lahir', $isEdit ? $anak->berat_lahir : '') }}" step="0.01" placeholder="Cth: 3.2" class="w-full px-4 py-3 bg-surface-container-low border-transparent rounded-2xl text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold placeholder:font-normal">
                </div>
                @error('berat_lahir')<span class="text-[10px] text-error font-bold mt-1 ml-1 block">{{ $message }}</span>@enderror
            </div>

            <!-- Panjang Lahir -->
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 ml-1">Panjang Lahir (cm)</label>
                <div class="relative">
                    <input type="number" name="panjang_lahir" value="{{ old('panjang_lahir', $isEdit ? $anak->panjang_lahir : '') }}" step="0.1" placeholder="Cth: 49" class="w-full px-4 py-3 bg-surface-container-low border-transparent rounded-2xl text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold placeholder:font-normal">
                </div>
                @error('panjang_lahir')<span class="text-[10px] text-error font-bold mt-1 ml-1 block">{{ $message }}</span>@enderror
            </div>
        </div>

        <!-- Golongan Darah -->
        <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1.5 ml-1">Golongan Darah</label>
            <div class="relative">
                <select name="golongan_darah" class="w-full px-4 py-3 bg-surface-container-low border-transparent rounded-2xl text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold">
                    @foreach(['A'=>'A','B'=>'B','AB'=>'AB','O'=>'O','tidak_diketahui'=>'Tidak Diketahui'] as $val=>$label)
                    <option value="{{ $val }}" {{ old('golongan_darah', $isEdit ? $anak->golongan_darah : 'tidak_diketahui') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-primary text-on-primary py-3.5 rounded-2xl font-bold text-sm shadow-md shadow-primary/20 active:scale-[0.98] transition-transform">
                {{ $isEdit ? '💾 Simpan Perubahan' : '🚀 Tambah Data Anak' }}
            </button>
        </div>
    </form>
</div>
@endsection
