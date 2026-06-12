@extends('layouts.user')
@section('title', 'Catat Makan')

@section('content')
@php
$meals = [
    'pagi'  => ['label'=>'Pagi',  'icon'=>'wb_sunny',   'color'=>'text-amber-500', 'bg'=>'bg-amber-500/10', 'border'=>'border-amber-500'],
    'siang' => ['label'=>'Siang', 'icon'=>'light_mode',  'color'=>'text-green-500',  'bg'=>'bg-green-500/10',  'border'=>'border-green-500'],
    'malam' => ['label'=>'Malam', 'icon'=>'nights_stay', 'color'=>'text-purple-500', 'bg'=>'bg-purple-500/10', 'border'=>'border-purple-500'],
    'snack' => ['label'=>'Snack', 'icon'=>'cookie',      'color'=>'text-red-500',    'bg'=>'bg-red-500/10',    'border'=>'border-red-500'],
];
@endphp

<div class="space-y-6 pb-24">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('recall.index') }}" class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-on-surface-variant active:scale-95 transition-transform">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-xl font-bold text-on-surface">Catat Makan</h1>
            <p class="text-xs text-on-surface-variant">Cari makanan & hitung gizi otomatis</p>
        </div>
    </div>

    <form method="POST" action="{{ route('recall.store') }}" id="formRecall" class="space-y-6">
        @csrf

        <!-- Input Utama -->
        <div class="bg-surface-container-lowest rounded-3xl p-5 shadow-[0px_5px_15px_rgba(30,41,59,0.03)] border border-surface-container-low grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 ml-1">Anak <span class="text-error">*</span></label>
                <select name="anak_id" id="anakSelect" required onchange="updateAkg(this)" class="w-full px-4 py-3 bg-surface-container-low border-transparent rounded-2xl text-xs sm:text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold">
                    <option value="">-- Pilih Anak --</option>
                    @foreach($anakList as $a)
                    <option value="{{ $a->id }}" data-umur="{{ $a->umur_bulan }}"
                        {{ (request('anak_id')==$a->id||(isset($selectedAnak)&&$selectedAnak->id==$a->id))?'selected':'' }}>
                        {{ $a->nama_anak }} ({{ $a->umur_label }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1.5 ml-1">Tanggal <span class="text-error">*</span></label>
                <input type="date" name="tanggal" value="{{ today()->format('Y-m-d') }}" required class="w-full px-4 py-3 bg-surface-container-low border-transparent rounded-2xl text-xs sm:text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold">
            </div>
        </div>

        <!-- Sticky Progress Gizi -->
        <div class="bg-surface-container-lowest rounded-3xl p-5 shadow-[0px_10px_25px_rgba(30,41,59,0.05)] border border-surface-container-low space-y-4">
            <h2 class="text-sm font-bold text-on-surface flex items-center gap-1.5"><span class="material-symbols-outlined text-base">bar_chart</span> Estimasi Gizi Harian</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach([
                    ['id'=>'sum-kal','label'=>'⚡ Kalori','satuan'=>'kkal','color'=>'bg-amber-500'],
                    ['id'=>'sum-pro','label'=>'🥩 Protein','satuan'=>'g','color'=>'bg-green-500'],
                    ['id'=>'sum-kar','label'=>'🌾 Karbo','satuan'=>'g','color'=>'bg-purple-500'],
                    ['id'=>'sum-lem','label'=>'🫒 Lemak','satuan'=>'g','color'=>'bg-red-500']
                ] as $item)
                <div class="space-y-1">
                    <div class="flex justify-between text-[11px] font-bold text-on-surface-variant">
                        <span>{{ $item['label'] }}</span>
                        <span><strong id="{{ $item['id'] }}">0</strong>/<span id="{{ $item['id'] }}-akg">—</span>{{ $item['satuan'] }}</span>
                    </div>
                    <div class="h-2 bg-surface-container rounded-full overflow-hidden">
                        <div class="h-full {{ $item['color'] }} rounded-full transition-all duration-300" id="{{ $item['id'] }}-bar" style="width:0%"></div>
                    </div>
                    <div class="text-[9px] font-bold text-on-surface-variant/80" id="{{ $item['id'] }}-pct">0%</div>
                </div>
                @endforeach
            </div>
            
            <div id="akg-card" class="bg-surface-container p-3 rounded-2xl text-[10px] font-semibold text-on-surface-variant text-center" style="display:none;">
                <span id="akg-label">Usia: —</span> • Sumber: AKG Indonesia 2019
            </div>
        </div>

        <!-- Tab Makanan -->
        <div>
            <div class="flex bg-surface-container-low rounded-2xl p-1 mb-4">
                @foreach($meals as $key=>$m)
                <button type="button" onclick="switchTab('{{ $key }}')" id="tab-{{ $key }}" class="flex-1 py-3 text-xs font-bold rounded-xl flex flex-col items-center gap-1 transition-all {{ $loop->first ? 'bg-primary text-on-primary shadow-sm' : 'text-on-surface-variant hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-xl">{{ $m['icon'] }}</span>
                    <span class="flex items-center gap-1">
                        {{ $m['label'] }}
                        <span id="count-{{ $key }}" class="hidden text-[9px] bg-white/20 px-1.5 py-0.5 rounded-full">0</span>
                    </span>
                </button>
                @endforeach
            </div>

            @foreach($meals as $key=>$m)
            <div id="pane-{{ $key }}" class="space-y-4 {{ $loop->first ? 'block' : 'hidden' }}">
                <div class="flex justify-between items-center bg-surface-container-lowest px-4 py-3 rounded-2xl border border-surface-container-low">
                    <span class="text-xs font-bold flex items-center gap-1.5 {{ $m['color'] }}">
                        <span class="material-symbols-outlined text-lg">{{ $m['icon'] }}</span> Menu {{ $m['label'] }}
                    </span>
                    <button type="button" onclick="tambahBaris('{{ $key }}')" class="px-4 py-2 {{ $m['bg'] }} {{ $m['color'] }} rounded-xl text-xs font-extrabold active:scale-95 transition-transform flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm font-bold">add</span> Tambah
                    </button>
                </div>

                <!-- Container Baris Makanan -->
                <div id="rows-{{ $key }}" class="space-y-4"></div>

                <!-- Subtotal Gizi Per Waktu -->
                <div class="p-4 rounded-2xl {{ $m['bg'] }} {{ $m['color'] }} text-xs font-bold">
                    <div class="grid grid-cols-4 gap-2 text-center">
                        <div>
                            <span class="text-[9px] block opacity-80 uppercase mb-0.5">⚡ Kalori</span>
                            <span id="total-kal-{{ $key }}">0</span> <span class="text-[9px]">kkal</span>
                        </div>
                        <div>
                            <span class="text-[9px] block opacity-80 uppercase mb-0.5">🥩 Pro</span>
                            <span id="total-pro-{{ $key }}">0</span> <span class="text-[9px]">g</span>
                        </div>
                        <div>
                            <span class="text-[9px] block opacity-80 uppercase mb-0.5">🌾 Karbo</span>
                            <span id="total-kar-{{ $key }}">0</span> <span class="text-[9px]">g</span>
                        </div>
                        <div>
                            <span class="text-[9px] block opacity-80 uppercase mb-0.5">🫒 Lemak</span>
                            <span id="total-lem-{{ $key }}">0</span> <span class="text-[9px]">g</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Button Simpan -->
        <div class="fixed bottom-20 left-0 right-0 p-4 bg-surface-container/80 backdrop-blur-md z-40 max-w-md mx-auto border-t border-surface-container-high">
            <button type="submit" class="w-full py-4 rounded-2xl font-bold text-sm text-white shadow-lg active:scale-[0.98] transition-transform flex items-center justify-center gap-2" style="background:linear-gradient(135deg,#4648d4,#6063ee)">
                <span class="material-symbols-outlined text-lg">save</span> Simpan Recall Gizi
            </button>
        </div>
    </form>
</div>

<!-- ROW TEMPLATE -->
<template id="rowTemplate">
<div class="food-row bg-surface-container-lowest rounded-2xl border border-surface-container-low shadow-sm relative overflow-hidden">
    <!-- Header: Badge + Delete -->
    <div class="flex justify-between items-center px-4 pt-3 pb-1">
        <span class="src-badge text-[9px] px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider hidden"></span>
        <button type="button" onclick="hapusBaris(this)" class="w-7 h-7 rounded-full bg-error/10 text-error flex items-center justify-center active:scale-90 transition-all ml-auto">
            <span class="material-symbols-outlined text-sm">close</span>
        </button>
    </div>

    <!-- Input Fields -->
    <div class="px-4 pb-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Nama Makanan -->
            <div class="relative">
                <label class="block text-[10px] font-bold text-on-surface-variant mb-1 ml-0.5">Nama Makanan <span class="text-error">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/60 material-symbols-outlined text-base">search</span>
                    <input type="text" class="food-search-input w-full pl-9 pr-4 py-2.5 bg-surface-container-low border border-transparent rounded-xl text-xs focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-semibold placeholder:font-normal" name="nama_makanan[]" placeholder="Ketik min. 2 huruf..." autocomplete="off" oninput="onFoodSearch(this)" onblur="hideDropdown(this,300)" required>
                    <!-- Loading spinner -->
                    <span class="search-spinner hidden absolute right-3 top-1/2 -translate-y-1/2">
                        <svg class="animate-spin h-4 w-4 text-primary" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    </span>
                </div>
                <div class="food-dropdown hidden absolute top-full left-0 right-0 z-50 bg-white border border-surface-container-high rounded-xl max-h-52 overflow-y-auto shadow-xl mt-1"></div>
            </div>

            <!-- Porsi -->
            <div>
                <label class="block text-[10px] font-bold text-on-surface-variant mb-1 ml-0.5">Porsi / Takaran <span class="text-error">*</span></label>
                <select class="porsi-select w-full px-4 py-2.5 bg-surface-container-low border border-transparent rounded-xl text-xs focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-bold" onchange="recalcRow(this)">
                    <option value="100" data-satuan="gr">-- Pilih porsi --</option>
                </select>
                <input type="hidden" class="jumlah-input" name="jumlah[]" value="100">
                <input type="hidden" class="satuan-input" name="satuan[]" value="gr">
            </div>
        </div>
    </div>

    <!-- Nutrisi Ringkas (pill style) -->
    <div class="nutrisi-mini flex items-center gap-2 flex-wrap px-4 pb-3 pt-1 border-t border-surface-container-low mx-3 text-[10px] font-bold text-on-surface-variant/70">
        <span class="inline-flex items-center gap-0.5 bg-amber-500/8 text-amber-600 px-2 py-0.5 rounded-full">⚡ <span class="d-kal">0</span> kkal</span>
        <span class="inline-flex items-center gap-0.5 bg-green-500/8 text-green-600 px-2 py-0.5 rounded-full">🥩 <span class="d-pro">0</span>g</span>
        <span class="inline-flex items-center gap-0.5 bg-purple-500/8 text-purple-600 px-2 py-0.5 rounded-full">🌾 <span class="d-kar">0</span>g</span>
        <span class="inline-flex items-center gap-0.5 bg-red-500/8 text-red-600 px-2 py-0.5 rounded-full">🫒 <span class="d-lem">0</span>g</span>
    </div>

    <!-- Hidden Fields -->
    <input type="hidden" class="kal-field" name="kalori[]" value="0">
    <input type="hidden" class="pro-field" name="protein[]" value="0">
    <input type="hidden" class="kar-field" name="karbohidrat[]" value="0">
    <input type="hidden" class="lem-field" name="lemak[]" value="0">
    <input type="hidden" class="wkt-field" name="waktu_makan[]" value="">
</div>
</template>

@push('scripts')
<script>
const SEARCH_URL = "{{ route('recall.food-search') }}";
const AKG_DATA = [
    {max:5,   energi:550,  protein:9,  karbo:58,  lemak:31, label:'0-5 bulan'},
    {max:11,  energi:725,  protein:16, karbo:82,  lemak:36, label:'6-11 bulan'},
    {max:35,  energi:1125, protein:26, karbo:155, lemak:44, label:'1-2 tahun'},
    {max:59,  energi:1600, protein:35, karbo:220, lemak:62, label:'3-4 tahun'},
    {max:9999,energi:1600, protein:40, karbo:220, lemak:62, label:'5+ tahun'},
];
const PORSI = {
  cair: [
    {l:'1/4 gelas (±60ml)',  g:60,  s:'ml'},
    {l:'1/2 gelas (±100ml)', g:100, s:'ml'},
    {l:'1 gelas (±200ml)',   g:200, s:'ml'},
    {l:'2 gelas (±400ml)',   g:400, s:'ml'},
  ],
  butir: [
    {l:'1 butir',  g:55,  s:'butir'},
    {l:'2 butir',  g:110, s:'butir'},
    {l:'3 butir',  g:165, s:'butir'},
  ],
  buah: [
    {l:'1/2 buah kecil',   g:60,  s:'buah'},
    {l:'1 buah kecil',     g:80,  s:'buah'},
    {l:'1 buah sedang',    g:120, s:'buah'},
    {l:'1 buah besar',     g:180, s:'buah'},
  ],
  sendok: [
    {l:'1 sendok makan',   g:15, s:'sdm'},
    {l:'2 sendok makan',   g:30, sdm:'sdm'},
    {l:'3 sendok makan',   g:45, sdm:'sdm'},
    {l:'4 sendok makan',   g:60, sdm:'sdm'},
  ],
  padat: [
    {l:'1 sendok makan',   g:15,  s:'sdm'},
    {l:'3 sendok makan',   g:45,  s:'sdm'},
    {l:'1 potong kecil',   g:50,  s:'potong'},
    {l:'1 potong sedang',  g:100, s:'potong'},
    {l:'1 potong besar',   g:150, s:'potong'},
    {l:'1/2 mangkuk',      g:75,  s:'mangkuk'},
    {l:'1 mangkuk kecil',  g:100, s:'mangkuk'},
    {l:'1 mangkuk sedang', g:150, s:'mangkuk'},
    {l:'1/2 piring',       g:100, s:'piring'},
    {l:'1 piring kecil',   g:150, s:'piring'},
    {l:'1 piring sedang',  g:200, s:'piring'},
    {l:'1 piring besar',   g:300, s:'piring'},
  ],
};
const PORSI_MAP = [
  {kata:['asi','susu','air','jus','sari','kaldu','kuah','minum'],tipe:'cair',def:1},
  {kata:['telur'],                                               tipe:'butir',def:0},
  {kata:['pisang','apel','jeruk','mangga','pepaya','semangka','jambu','melon','alpukat','buah'],tipe:'buah',def:2},
  {kata:['minyak','kecap','saus','saos','sambal','bumbu','gula','garam'],tipe:'sendok',def:0},
  {kata:['nasi','bubur','tim','mie','kentang','roti','singkong','jagung','lauk',
          'ayam','sapi','ikan','udang','tahu','tempe','bayam','wortel','kangkung',
          'brokoli','labu','kacang','buncis','terong','tomat','biskuit','puree','yoghurt','keju'],
          tipe:'padat',def:3},
];
function getPorsiTipe(nama) {
  const n = nama.toLowerCase();
  for (const m of PORSI_MAP) {
    // Gunakan regex boundary \b agar "asi" tidak me-match "nasi"
    if (m.kata.some(k => new RegExp('\\b' + k + '\\b').test(n))) return {tipe:m.tipe, def:m.def};
  }
  // Fallback tambahan jika tidak ketemu dan mengandung kata tertentu
  if (n.includes('air') || n.includes('susu')) return {tipe:'cair', def:1};
  
  return {tipe:'padat', def:3};
}
let currentAkg = null;
const waktuList = ['pagi','siang','malam','snack'];
let searchTimers = {};

window.addEventListener('DOMContentLoaded', () => {
    waktuList.forEach(w => tambahBaris(w));
    const sel = document.getElementById('anakSelect');
    if (sel.value) updateAkg(sel);
});

function switchTab(key) {
    waktuList.forEach(w => {
        document.getElementById('pane-'+w).style.display = w===key?'block':'none';
        const btn = document.getElementById('tab-'+w);
        if (w===key) {
            btn.classList.add('bg-primary', 'text-on-primary', 'shadow-sm');
            btn.classList.remove('text-on-surface-variant');
        } else {
            btn.classList.remove('bg-primary', 'text-on-primary', 'shadow-sm');
            btn.classList.add('text-on-surface-variant');
        }
    });
}

function tambahBaris(waktu) {
    const tmpl = document.getElementById('rowTemplate').content.cloneNode(true);
    const row  = tmpl.querySelector('.food-row');
    row.querySelector('.wkt-field').value = waktu;
    document.getElementById('rows-'+waktu).appendChild(row);
    updateCount(waktu);
}

function hapusBaris(btn) {
    const row   = btn.closest('.food-row');
    const waktu = row.querySelector('.wkt-field').value;
    row.remove();
    updateTotalWaktu(waktu);
    updateCount(waktu);
    updateGrandTotal();
}

function onFoodSearch(input) {
    const q = input.value.trim();
    const row = input.closest('.food-row');
    const drop = row.querySelector('.food-dropdown');
    const spinner = row.querySelector('.search-spinner');

    if (q.length < 2) { drop.classList.add('hidden'); if(spinner) spinner.classList.add('hidden'); return; }

    const id = input.dataset.searchId || (input.dataset.searchId = Math.random());
    clearTimeout(searchTimers[id]);
    searchTimers[id] = setTimeout(() => {
        if(spinner) spinner.classList.remove('hidden');
        drop.innerHTML = '<div class="p-3 text-xs text-on-surface-variant/70">🔍 Mencari...</div>';
        drop.classList.remove('hidden');
        fetch(SEARCH_URL + '?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(results => {
                if(spinner) spinner.classList.add('hidden');
                renderDropdown(results, drop, input);
            })
            .catch(() => {
                if(spinner) spinner.classList.add('hidden');
                drop.innerHTML = '<div class="p-3 text-xs text-error">Gagal mengambil data</div>';
            });
    }, 300);
}

function renderDropdown(results, drop, input) {
    if (!results.length) {
        drop.innerHTML = '<div class="p-3 text-xs text-on-surface-variant/70">Tidak ditemukan. Masukkan data manual.</div>' +
            '<div class="p-3 pt-0"><button type="button" onclick="pilihManual(this.closest(\'.food-row\'))" class="text-xs px-3 py-1.5 border border-dashed border-outline rounded-xl font-bold text-on-surface">✏️ Isi manual</button></div>';
        return;
    }
    let html = '';
    results.forEach(r => {
        const badge = r.source_type==='online'
            ? '<span class="text-[9px] bg-primary/10 text-primary px-2 py-0.5 rounded-full font-bold ml-2">🌐 Internet</span>'
            : '<span class="text-[9px] bg-tertiary-container/10 text-tertiary-container px-2 py-0.5 rounded-full font-bold ml-2">📖 TKPI</span>';
        html += `<div class="drop-item p-3 cursor-pointer border-b border-surface-container-high hover:bg-surface-container transition-all" onclick="pilihMakanan(this)"
            data-nama="${r.nama.replace(/"/g,'&quot;')}"
            data-kal="${r.kalori}" data-pro="${r.protein}"
            data-kar="${r.karbohidrat}" data-lem="${r.lemak}"
            data-satuan="${r.satuan}" data-sumber="${r.sumber}" data-type="${r.source_type}">
            <div class="font-bold text-xs flex items-center">${r.nama}${badge}</div>
            <div class="text-[10px] text-on-surface-variant mt-1">
                ⚡${r.kalori} kkal &bull; 🥩${r.protein}g Pro &bull; 🌾${r.karbohidrat}g &bull; 🫒${r.lemak}g (per ${r.satuan})
            </div>
        </div>`;
    });
    html += '<div class="p-3"><button type="button" onclick="pilihManual(this.closest(\'.food-dropdown\').closest(\'.food-row\'))" class="text-xs px-3 py-1.5 border border-dashed border-outline rounded-xl font-bold text-on-surface w-full text-center">✏️ Isi manual</button></div>';
    drop.innerHTML = html;
    drop.classList.remove('hidden');
}

function pilihMakanan(item) {
    const row  = item.closest('.food-row');
    const drop = row.querySelector('.food-dropdown');
    const inp  = row.querySelector('.food-search-input');
    const nama = item.dataset.nama;
    const kal  = parseFloat(item.dataset.kal)||0;
    const pro  = parseFloat(item.dataset.pro)||0;
    const kar  = parseFloat(item.dataset.kar)||0;
    const lem  = parseFloat(item.dataset.lem)||0;

    inp.value = nama;
    row.dataset.baseKal = kal;
    row.dataset.basePro = pro;
    row.dataset.baseKar = kar;
    row.dataset.baseLem = lem;

    isiDropdownPorsi(row, nama);

    const badge = row.querySelector('.src-badge');
    badge.textContent = item.dataset.type==='online' ? '🌐 '+item.dataset.sumber : '📖 '+item.dataset.sumber;
    badge.classList.remove('hidden');
    badge.className = item.dataset.type==='online' 
        ? 'src-badge text-[9px] px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider bg-primary/10 text-primary' 
        : 'src-badge text-[9px] px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider bg-tertiary-container/10 text-tertiary-container';

    showManualFields(row, kal, pro, kar, lem, false);
    drop.classList.add('hidden');
    recalcRow(row.querySelector('.porsi-select'));
}

function isiDropdownPorsi(row, nama) {
    const sel = row.querySelector('.porsi-select');
    const {tipe, def} = getPorsiTipe(nama);
    const opts = PORSI[tipe];
    sel.innerHTML = '';
    opts.forEach((o,i) => {
        const op = document.createElement('option');
        op.value = o.g;
        op.dataset.satuan = o.s;
        op.textContent = o.l;
        if (i === def) op.selected = true;
        sel.appendChild(op);
    });
    const custom = document.createElement('option');
    custom.value = '__custom__';
    custom.textContent = '✏️ Isi sendiri (gram)...';
    sel.appendChild(custom);
    syncPorsiHidden(row);
}

function syncPorsiHidden(row) {
    const sel = row.querySelector('.porsi-select');
    const opt = sel.options[sel.selectedIndex];
    if (sel.value === '__custom__') {
        let gi = row.querySelector('.custom-gram');
        if (!gi) {
            gi = document.createElement('input');
            gi.type = 'number'; gi.min = 1; gi.step = 1; gi.value = 100;
            gi.className = 'custom-gram w-full px-4 py-2.5 bg-surface-container-low border-transparent rounded-2xl text-xs font-bold mt-2';
            gi.placeholder = 'Masukkan gram...';
            gi.oninput = () => {
                row.querySelector('.jumlah-input').value = gi.value || 100;
                row.querySelector('.satuan-input').value = 'gr';
                recalcRow(gi);
            };
            sel.after(gi);
        }
        gi.style.display = 'block';
        gi.focus();
    } else {
        const gi = row.querySelector('.custom-gram');
        if (gi) gi.style.display = 'none';
        row.querySelector('.jumlah-input').value = sel.value || 100;
        row.querySelector('.satuan-input').value = opt?.dataset?.satuan || 'gr';
    }
}

function pilihManual(row) {
    const drop = row.querySelector('.food-dropdown');
    if (drop) drop.classList.add('hidden');
    showManualFields(row, 0, 0, 0, 0, true);
    row.dataset.baseKal = 0; row.dataset.basePro = 0;
    row.dataset.baseKar = 0; row.dataset.baseLem = 0;
    const sel = row.querySelector('.porsi-select');
    if (sel.options.length <= 1) {
        isiDropdownPorsi(row, 'nasi');
    }
    const badge = row.querySelector('.src-badge');
    badge.textContent = '✏️ Manual'; 
    badge.classList.remove('hidden');
    badge.className = 'src-badge text-[9px] px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider bg-amber-500/10 text-amber-500';
}

function showManualFields(row, kal, pro, kar, lem, editable) {
    let mf = row.querySelector('.manual-fields');
    if (!mf) {
        mf = document.createElement('div');
        mf.className = 'manual-fields grid grid-cols-1 sm:grid-cols-2 gap-2 mt-3 pt-3 border-t border-surface-container';
        mf.innerHTML = `
            <div><label class="block text-[8px] font-bold text-on-surface-variant uppercase mb-1 ml-0.5">Kalori (per 100g)</label>
            <input type="number" class="mf-kal w-full px-3 py-2 bg-surface-container-low border-transparent rounded-xl text-xs font-bold" step="0.1" min="0" oninput="syncManual(this)"></div>
            <div><label class="block text-[8px] font-bold text-on-surface-variant uppercase mb-1 ml-0.5">Protein (per 100g)</label>
            <input type="number" class="mf-pro w-full px-3 py-2 bg-surface-container-low border-transparent rounded-xl text-xs font-bold" step="0.1" min="0" oninput="syncManual(this)"></div>
            <div><label class="block text-[8px] font-bold text-on-surface-variant uppercase mb-1 ml-0.5">Karbo (per 100g)</label>
            <input type="number" class="mf-kar w-full px-3 py-2 bg-surface-container-low border-transparent rounded-xl text-xs font-bold" step="0.1" min="0" oninput="syncManual(this)"></div>
            <div><label class="block text-[8px] font-bold text-on-surface-variant uppercase mb-1 ml-0.5">Lemak (per 100g)</label>
            <input type="number" class="mf-lem w-full px-3 py-2 bg-surface-container-low border-transparent rounded-xl text-xs font-bold" step="0.1" min="0" oninput="syncManual(this)"></div>`;
        row.querySelector('.nutrisi-mini').after(mf);
    }
    mf.querySelector('.mf-kal').value = kal; mf.querySelector('.mf-kal').readOnly = !editable;
    mf.querySelector('.mf-pro').value = pro; mf.querySelector('.mf-pro').readOnly = !editable;
    mf.querySelector('.mf-kar').value = kar; mf.querySelector('.mf-kar').readOnly = !editable;
    mf.querySelector('.mf-lem').value = lem; mf.querySelector('.mf-lem').readOnly = !editable;
    mf.style.display = 'grid';
}

function syncManual(input) {
    const row = input.closest('.food-row');
    const mf  = row.querySelector('.manual-fields');
    row.dataset.baseKal = parseFloat(mf.querySelector('.mf-kal').value)||0;
    row.dataset.basePro = parseFloat(mf.querySelector('.mf-pro').value)||0;
    row.dataset.baseKar = parseFloat(mf.querySelector('.mf-kar').value)||0;
    row.dataset.baseLem = parseFloat(mf.querySelector('.mf-lem').value)||0;
    recalcRow(row.querySelector('.jumlah-input'));
}

function hideDropdown(input, delay) {
    setTimeout(() => {
        const row = input.closest('.food-row');
        if (row) row.querySelector('.food-dropdown').classList.add('hidden');
    }, delay);
}

function recalcRow(input) {
    const row = input.closest('.food-row');
    if (input.classList.contains('porsi-select')) syncPorsiHidden(row);
    const jumlah = parseFloat(row.querySelector('.jumlah-input').value)||100;
    const factor = jumlah / 100;
    const kal = Math.round((parseFloat(row.dataset.baseKal)||0) * factor * 10)/10;
    const pro = Math.round((parseFloat(row.dataset.basePro)||0) * factor * 10)/10;
    const kar = Math.round((parseFloat(row.dataset.baseKar)||0) * factor * 10)/10;
    const lem = Math.round((parseFloat(row.dataset.baseLem)||0) * factor * 10)/10;
    row.querySelector('.kal-field').value = kal;
    row.querySelector('.pro-field').value = pro;
    row.querySelector('.kar-field').value = kar;
    row.querySelector('.lem-field').value = lem;
    row.querySelector('.d-kal').textContent = kal;
    row.querySelector('.d-pro').textContent = pro;
    row.querySelector('.d-kar').textContent = kar;
    row.querySelector('.d-lem').textContent = lem;
    updateTotalWaktu(row.querySelector('.wkt-field').value);
    updateGrandTotal();
}

function updateTotalWaktu(waktu) {
    let kal=0,pro=0,kar=0,lem=0;
    document.querySelectorAll(`#rows-${waktu} .food-row`).forEach(r=>{
        kal+=parseFloat(r.querySelector('.kal-field').value)||0;
        pro+=parseFloat(r.querySelector('.pro-field').value)||0;
        kar+=parseFloat(r.querySelector('.kar-field').value)||0;
        lem+=parseFloat(r.querySelector('.lem-field').value)||0;
    });
    document.getElementById('total-kal-'+waktu).textContent=Math.round(kal*10)/10;
    document.getElementById('total-pro-'+waktu).textContent=Math.round(pro*10)/10;
    document.getElementById('total-kar-'+waktu).textContent=Math.round(kar*10)/10;
    document.getElementById('total-lem-'+waktu).textContent=Math.round(lem*10)/10;
}

function updateGrandTotal() {
    let kal=0,pro=0,kar=0,lem=0;
    document.querySelectorAll('.kal-field').forEach(f=>kal+=parseFloat(f.value)||0);
    document.querySelectorAll('.pro-field').forEach(f=>pro+=parseFloat(f.value)||0);
    document.querySelectorAll('.kar-field').forEach(f=>kar+=parseFloat(f.value)||0);
    document.querySelectorAll('.lem-field').forEach(f=>lem+=parseFloat(f.value)||0);
    const vals = {
        'sum-kal':{val:Math.round(kal),   akg:currentAkg?.energi},
        'sum-pro':{val:Math.round(pro*10)/10, akg:currentAkg?.protein},
        'sum-kar':{val:Math.round(kar*10)/10, akg:currentAkg?.karbo},
        'sum-lem':{val:Math.round(lem*10)/10, akg:currentAkg?.lemak},
    };
    Object.entries(vals).forEach(([id,{val,akg}])=>{
        document.getElementById(id).textContent = val;
        if(akg){
            const pct = Math.min(100, Math.round(val/akg*100));
            document.getElementById(id+'-akg').textContent = akg;
            document.getElementById(id+'-bar').style.width = pct+'%';
            document.getElementById(id+'-pct').textContent = pct+'% dari AKG';
            const bar = document.getElementById(id+'-bar');
            bar.className = 'h-full rounded-full transition-all duration-300 ' + (pct>=90?'bg-green-500':pct>=60?'bg-amber-500':'bg-red-500');
        }
    });
}

function updateCount(waktu) {
    const count = document.querySelectorAll(`#rows-${waktu} .food-row`).length;
    const badge = document.getElementById('count-'+waktu);
    badge.textContent = count;
    if (count>0) {
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

function updateAkg(sel) {
    const umur = parseInt(sel.options[sel.selectedIndex]?.dataset?.umur)||0;
    currentAkg = AKG_DATA.find(a=>umur<=a.max);
    if(currentAkg){
        document.getElementById('akg-card').style.display='block';
        document.getElementById('akg-label').textContent='Usia: '+currentAkg.label;
        document.getElementById('sum-kal-akg').textContent=currentAkg.energi;
        document.getElementById('sum-pro-akg').textContent=currentAkg.protein;
        document.getElementById('sum-kar-akg').textContent=currentAkg.karbo;
        document.getElementById('sum-lem-akg').textContent=currentAkg.lemak;
        updateGrandTotal();
    }
}
</script>
@endpush
@endsection
