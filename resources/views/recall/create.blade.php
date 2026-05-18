@extends('layouts.app')
@section('title', 'Input Recall Gizi')
@section('content')
<div class="topbar">
    <div class="page-header">
        <h1>🍽️ Input Recall Gizi Harian</h1>
        <p>Cari makanan dan data gizi otomatis dari database TKPI & internet</p>
    </div>
    <a href="{{ route('recall.index') }}" class="btn btn-outline">← Kembali</a>
</div>
@php
$meals = [
    'pagi'  => ['label'=>'Makan Pagi',  'icon'=>'🌅','color'=>'#f59e0b'],
    'siang' => ['label'=>'Makan Siang', 'icon'=>'☀️', 'color'=>'#16a34a'],
    'malam' => ['label'=>'Makan Malam', 'icon'=>'🌙', 'color'=>'#7c3aed'],
    'snack' => ['label'=>'Snack',        'icon'=>'🍎','color'=>'#ef4444'],
];
@endphp
<form method="POST" action="{{ route('recall.store') }}" id="formRecall">
@csrf
<div class="card fade-up" style="margin-bottom:20px;">
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:end;">
            <div>
                <label class="form-label">Anak <span style="color:var(--danger)">*</span></label>
                <select name="anak_id" id="anakSelect" class="form-control" required onchange="updateAkg(this)">
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
                <label class="form-label">Tanggal <span style="color:var(--danger)">*</span></label>
                <input type="date" name="tanggal" class="form-control" value="{{ today()->format('Y-m-d') }}" required>
            </div>
        </div>
    </div>
</div>
<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">
<div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0;background:var(--card);border:1px solid var(--border);border-radius:var(--radius) var(--radius) 0 0;overflow:hidden;">
        @foreach($meals as $key=>$m)
        <button type="button" onclick="switchTab('{{ $key }}')" id="tab-{{ $key }}"
            style="padding:14px 10px;border:none;border-right:1px solid var(--border);cursor:pointer;font-size:13px;font-weight:600;transition:all 0.2s;background:{{ $loop->first?'var(--primary)':'var(--card)' }};color:{{ $loop->first?'#fff':'var(--text-muted)' }};display:flex;flex-direction:column;align-items:center;gap:4px;">
            <span style="font-size:20px;">{{ $m['icon'] }}</span>
            <span>{{ $m['label'] }}</span>
            <span id="count-{{ $key }}" style="font-size:11px;background:rgba(255,255,255,0.3);border-radius:100px;padding:1px 8px;display:none;">0</span>
        </button>
        @endforeach
    </div>
    @foreach($meals as $key=>$m)
    <div id="pane-{{ $key }}" style="display:{{ $loop->first?'block':'none' }};background:var(--card);border:1px solid var(--border);border-top:none;border-radius:0 0 var(--radius) var(--radius);padding:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div style="font-weight:700;font-size:14px;color:{{ $m['color'] }};">{{ $m['icon'] }} {{ $m['label'] }}</div>
            <button type="button" onclick="tambahBaris('{{ $key }}')"
                style="background:{{ $m['color'] }};color:#fff;border:none;border-radius:8px;padding:7px 14px;font-size:12.5px;font-weight:600;cursor:pointer;">
                + Tambah Makanan
            </button>
        </div>
        <div id="rows-{{ $key }}"></div>
        <div style="background:{{ $m['color'] }}15;border-radius:8px;padding:10px 14px;margin-top:10px;font-size:12.5px;">
            <div style="display:flex;gap:20px;flex-wrap:wrap;">
                <span>🔥 <strong id="total-kal-{{ $key }}">0</strong> kkal</span>
                <span>🥩 <strong id="total-pro-{{ $key }}">0</strong> g protein</span>
                <span>🌾 <strong id="total-kar-{{ $key }}">0</strong> g karbo</span>
                <span>🧈 <strong id="total-lem-{{ $key }}">0</strong> g lemak</span>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div style="position:sticky;top:20px;display:flex;flex-direction:column;gap:16px;">
    <div class="card fade-up">
        <div class="card-header"><div class="card-title">📊 Total Gizi Hari Ini</div></div>
        <div class="card-body">
            @foreach([['id'=>'sum-kal','label'=>'🔥 Energi','satuan'=>'kkal','color'=>'amber'],['id'=>'sum-pro','label'=>'🥩 Protein','satuan'=>'g','color'=>'green'],['id'=>'sum-kar','label'=>'🌾 Karbohidrat','satuan'=>'g','color'=>'purple'],['id'=>'sum-lem','label'=>'🧈 Lemak','satuan'=>'g','color'=>'red']] as $item)
            <div style="margin-bottom:12px;">
                <div style="display:flex;justify-content:space-between;font-size:12.5px;margin-bottom:4px;">
                    <span>{{ $item['label'] }}</span>
                    <span><strong id="{{ $item['id'] }}">0</strong> / <span id="{{ $item['id'] }}-akg" style="color:var(--text-muted);">—</span> {{ $item['satuan'] }}</span>
                </div>
                <div class="progress-track"><div class="progress-fill {{ $item['color'] }}" id="{{ $item['id'] }}-bar" style="width:0%;transition:width 0.4s;"></div></div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:2px;" id="{{ $item['id'] }}-pct">0%</div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="card fade-up" id="akg-card" style="display:{{ $akg?'block':'none' }};">
        <div class="card-header"><div class="card-title">📋 AKG Referensi</div></div>
        <div class="card-body" style="font-size:12.5px;">
            <div id="akg-label" style="color:var(--text-muted);margin-bottom:8px;">Usia: —</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                <div style="background:var(--bg);border-radius:6px;padding:8px;text-align:center;">
                    <div style="font-size:16px;font-weight:800;color:var(--primary);" id="akg-kal">{{ $akg['energi']??'—' }}</div>
                    <div style="color:var(--text-muted);font-size:11px;">kkal/hari</div>
                </div>
                <div style="background:var(--bg);border-radius:6px;padding:8px;text-align:center;">
                    <div style="font-size:16px;font-weight:800;color:#7c3aed;" id="akg-pro">{{ $akg['protein']??'—' }}</div>
                    <div style="color:var(--text-muted);font-size:11px;">g protein/hari</div>
                </div>
                <div style="background:var(--bg);border-radius:6px;padding:8px;text-align:center;">
                    <div style="font-size:16px;font-weight:800;color:#f59e0b;" id="akg-kar">{{ $akg['karbo']??'—' }}</div>
                    <div style="color:var(--text-muted);font-size:11px;">g karbo/hari</div>
                </div>
                <div style="background:var(--bg);border-radius:6px;padding:8px;text-align:center;">
                    <div style="font-size:16px;font-weight:800;color:#ef4444;" id="akg-lem">{{ $akg['lemak']??'—' }}</div>
                    <div style="color:var(--text-muted);font-size:11px;">g lemak/hari</div>
                </div>
            </div>
            <div style="margin-top:10px;font-size:11px;color:var(--text-muted);">Sumber: AKG Indonesia 2019 (Permenkes No.28/2019)</div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary" style="width:100%;padding:14px;font-size:14px;font-weight:700;">💾 Simpan Recall Gizi</button>
</div>
</div>
</form>

{{-- ROW TEMPLATE --}}
<template id="rowTemplate">
<div class="food-row" style="margin-bottom:12px;padding:12px;background:var(--bg);border-radius:10px;border:1px solid var(--border);">
    <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:8px;align-items:end;">
        <div style="position:relative;">
            <label style="font-size:11.5px;color:var(--text-muted);margin-bottom:3px;display:block;">Nama Makanan</label>
            <div style="position:relative;">
                <input type="text" class="form-control food-search-input" name="nama_makanan[]"
                    placeholder="🔍 Ketik nama makanan..." autocomplete="off"
                    oninput="onFoodSearch(this)" onblur="hideDropdown(this,300)" required>
                <div class="food-dropdown" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:999;background:#fff;border:1.5px solid var(--primary);border-radius:0 0 10px 10px;max-height:220px;overflow-y:auto;box-shadow:0 8px 24px rgba(0,0,0,0.12);"></div>
            </div>
        </div>
        <div>
            <label style="font-size:11.5px;color:var(--text-muted);margin-bottom:3px;display:block;">🍽️ Berapa banyak?</label>
            <select class="form-control porsi-select" onchange="recalcRow(this)">
                <option value="100" data-satuan="gr">-- Pilih porsi --</option>
            </select>
            <input type="hidden" class="jumlah-input" name="jumlah[]" value="100">
            <input type="hidden" class="satuan-input" name="satuan[]" value="gr">
        </div>
        <div>
            <button type="button" onclick="hapusBaris(this)" style="background:var(--danger);color:#fff;border:none;border-radius:8px;padding:9px 10px;cursor:pointer;font-size:14px;" title="Hapus">🗑</button>
        </div>
        <input type="hidden" class="kal-field" name="kalori[]" value="0">
        <input type="hidden" class="pro-field" name="protein[]" value="0">
        <input type="hidden" class="kar-field" name="karbohidrat[]" value="0">
        <input type="hidden" class="lem-field" name="lemak[]" value="0">
        <input type="hidden" class="wkt-field" name="waktu_makan[]" value="">
    </div>
    <div class="nutrisi-mini" style="margin-top:8px;font-size:11.5px;color:var(--text-muted);display:flex;gap:14px;align-items:center;flex-wrap:wrap;">
        <span>🔥 <span class="d-kal">0</span> kkal</span>
        <span>🥩 <span class="d-pro">0</span>g</span>
        <span>🌾 <span class="d-kar">0</span>g</span>
        <span>🧈 <span class="d-lem">0</span>g</span>
        <span class="src-badge" style="font-size:10px;padding:2px 7px;border-radius:100px;background:#e0f2fe;color:#0369a1;display:none;"></span>
    </div>
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
// Porsi rumah tangga → gram
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
    {l:'2 sendok makan',   g:30, s:'sdm'},
    {l:'3 sendok makan',   g:45, s:'sdm'},
    {l:'4 sendok makan',   g:60, s:'sdm'},
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
// Kata kunci → tipe porsi + default index
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
    if (m.kata.some(k => n.includes(k))) return {tipe:m.tipe, def:m.def};
  }
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
        btn.style.background = w===key?'var(--primary)':'var(--card)';
        btn.style.color = w===key?'#fff':'var(--text-muted)';
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

// ── Live search makanan ────────────────────────────────────────
function onFoodSearch(input) {
    const q = input.value.trim();
    const row = input.closest('.food-row');
    const drop = row.querySelector('.food-dropdown');

    // Reset nutrisi jika input dikosongkan
    if (q.length < 2) { drop.style.display='none'; return; }

    // Debounce 400ms
    const id = input.dataset.searchId || (input.dataset.searchId = Math.random());
    clearTimeout(searchTimers[id]);
    searchTimers[id] = setTimeout(() => {
        drop.innerHTML = '<div style="padding:10px 14px;font-size:12px;color:#888;">🔍 Mencari...</div>';
        drop.style.display = 'block';
        fetch(SEARCH_URL + '?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(results => renderDropdown(results, drop, input))
            .catch(() => {
                drop.innerHTML = '<div style="padding:10px 14px;font-size:12px;color:#f87171;">Gagal mengambil data</div>';
            });
    }, 400);
}

function renderDropdown(results, drop, input) {
    if (!results.length) {
        drop.innerHTML = '<div style="padding:10px 14px;font-size:12px;color:#888;">Tidak ditemukan. Masukkan data manual di bawah.</div>' +
            '<div style="padding:6px 14px 12px;"><button type="button" onclick="pilihManual(this.closest(\'.food-row\'))" style="font-size:12px;padding:5px 12px;border:1.5px dashed #ccc;background:transparent;border-radius:6px;cursor:pointer;color:#555;">✏️ Isi manual</button></div>';
        return;
    }
    let html = '';
    results.forEach(r => {
        const badge = r.source_type==='online'
            ? '<span style="font-size:10px;background:#dbeafe;color:#1d4ed8;padding:1px 6px;border-radius:10px;margin-left:4px;">🌐 Internet</span>'
            : '<span style="font-size:10px;background:#dcfce7;color:#166534;padding:1px 6px;border-radius:10px;margin-left:4px;">📚 TKPI</span>';
        html += `<div class="drop-item" onclick="pilihMakanan(this)"
            data-nama="${r.nama.replace(/"/g,'&quot;')}"
            data-kal="${r.kalori}" data-pro="${r.protein}"
            data-kar="${r.karbohidrat}" data-lem="${r.lemak}"
            data-satuan="${r.satuan}" data-sumber="${r.sumber}" data-type="${r.source_type}"
            style="padding:9px 14px;cursor:pointer;border-bottom:1px solid #f0f0f0;transition:background 0.15s;">
            <div style="font-weight:600;font-size:13px;">${r.nama}${badge}</div>
            <div style="font-size:11px;color:#888;margin-top:2px;">
                🔥${r.kalori} kkal &nbsp;🥩${r.protein}g &nbsp;🌾${r.karbohidrat}g &nbsp;🧈${r.lemak}g
                <span style="margin-left:6px;font-style:italic;">(per ${r.satuan})</span>
            </div>
        </div>`;
    });
    html += '<div style="padding:8px 14px;border-top:1px solid #f0f0f0;"><button type="button" onclick="pilihManual(this.closest(\'.food-dropdown\').closest(\'.food-row\'))" style="font-size:12px;padding:4px 10px;border:1.5px dashed #ccc;background:transparent;border-radius:6px;cursor:pointer;color:#555;">✏️ Isi manual</button></div>';
    drop.innerHTML = html;
    drop.style.display = 'block';
    // Hover effect
    drop.querySelectorAll('.drop-item').forEach(el => {
        el.addEventListener('mouseenter', ()=>el.style.background='#f0f9ff');
        el.addEventListener('mouseleave', ()=>el.style.background='transparent');
    });
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

    // Set dropdown porsi sesuai jenis makanan
    isiDropdownPorsi(row, nama);

    // Badge sumber
    const badge = row.querySelector('.src-badge');
    badge.textContent = item.dataset.type==='online' ? '🌐 '+item.dataset.sumber : '📚 '+item.dataset.sumber;
    badge.style.display = 'inline-block';
    badge.style.background = item.dataset.type==='online' ? '#dbeafe' : '#dcfce7';
    badge.style.color = item.dataset.type==='online' ? '#1d4ed8' : '#166534';

    showManualFields(row, kal, pro, kar, lem, false);
    drop.style.display = 'none';
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
    // Opsi "lainnya"
    const custom = document.createElement('option');
    custom.value = '__custom__';
    custom.textContent = '✏️ Isi sendiri (gram)...';
    sel.appendChild(custom);
    // Sync hidden fields
    syncPorsiHidden(row);
}

function syncPorsiHidden(row) {
    const sel = row.querySelector('.porsi-select');
    const opt = sel.options[sel.selectedIndex];
    if (sel.value === '__custom__') {
        // Tampilkan input manual gram
        let gi = row.querySelector('.custom-gram');
        if (!gi) {
            gi = document.createElement('input');
            gi.type = 'number'; gi.min = 1; gi.step = 1; gi.value = 100;
            gi.className = 'form-control custom-gram';
            gi.placeholder = 'Masukkan gram...';
            gi.style.marginTop = '4px';
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
    if (drop) drop.style.display = 'none';
    showManualFields(row, 0, 0, 0, 0, true);
    row.dataset.baseKal = 0; row.dataset.basePro = 0;
    row.dataset.baseKar = 0; row.dataset.baseLem = 0;
    // Tampilkan dropdown porsi generik
    const sel = row.querySelector('.porsi-select');
    if (sel.options.length <= 1) {
        isiDropdownPorsi(row, 'nasi'); // default: padat
    }
    const badge = row.querySelector('.src-badge');
    badge.textContent = '✏️ Manual'; badge.style.display='inline-block';
    badge.style.background='#fef3c7'; badge.style.color='#92400e';
}

function showManualFields(row, kal, pro, kar, lem, editable) {
    let mf = row.querySelector('.manual-fields');
    if (!mf) {
        mf = document.createElement('div');
        mf.className = 'manual-fields';
        mf.style.cssText = 'display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-top:8px;';
        mf.innerHTML = `
            <div><label style="font-size:10.5px;color:var(--text-muted);">Kalori (kkal/100g)</label>
            <input type="number" class="form-control mf-kal" step="0.1" min="0" oninput="syncManual(this)"></div>
            <div><label style="font-size:10.5px;color:var(--text-muted);">Protein (g/100g)</label>
            <input type="number" class="form-control mf-pro" step="0.1" min="0" oninput="syncManual(this)"></div>
            <div><label style="font-size:10.5px;color:var(--text-muted);">Karbo (g/100g)</label>
            <input type="number" class="form-control mf-kar" step="0.1" min="0" oninput="syncManual(this)"></div>
            <div><label style="font-size:10.5px;color:var(--text-muted);">Lemak (g/100g)</label>
            <input type="number" class="form-control mf-lem" step="0.1" min="0" oninput="syncManual(this)"></div>`;
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
        if (row) row.querySelector('.food-dropdown').style.display='none';
    }, delay);
}

function recalcRow(input) {
    const row = input.closest('.food-row');
    // Sync hidden fields dulu jika trigger dari porsi-select
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
            bar.style.background = pct>=90?'#16a34a':pct>=60?'#f59e0b':'#ef4444';
        }
    });
}

function updateCount(waktu) {
    const count = document.querySelectorAll(`#rows-${waktu} .food-row`).length;
    const badge = document.getElementById('count-'+waktu);
    badge.textContent = count;
    badge.style.display = count>0?'inline-block':'none';
}

function updateAkg(sel) {
    const umur = parseInt(sel.options[sel.selectedIndex]?.dataset?.umur)||0;
    currentAkg = AKG_DATA.find(a=>umur<=a.max);
    if(currentAkg){
        document.getElementById('akg-card').style.display='block';
        document.getElementById('akg-label').textContent='Usia: '+currentAkg.label;
        document.getElementById('akg-kal').textContent=currentAkg.energi;
        document.getElementById('akg-pro').textContent=currentAkg.protein;
        document.getElementById('akg-kar').textContent=currentAkg.karbo;
        document.getElementById('akg-lem').textContent=currentAkg.lemak;
        document.getElementById('sum-kal-akg').textContent=currentAkg.energi;
        document.getElementById('sum-pro-akg').textContent=currentAkg.protein;
        document.getElementById('sum-kar-akg').textContent=currentAkg.karbo;
        document.getElementById('sum-lem-akg').textContent=currentAkg.lemak;
        updateGrandTotal();
    }
}
</script>
<style>
.form-control{width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--card);color:var(--text-main);box-sizing:border-box;}
.form-control:focus{outline:none;border-color:var(--primary);}
.form-control[readonly]{background:#f8fafc;color:#888;}
.form-label{font-size:13px;font-weight:600;color:var(--text-main);margin-bottom:5px;display:block;}
.food-search-input{padding-left:12px;}
</style>
@endpush
@endsection
