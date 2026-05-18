<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Laporan Pertumbuhan - {{ $anak->nama_anak }}</title>
<style>
body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a; }
h1 { color: #16a34a; font-size: 18px; margin-bottom: 4px; }
.sub { color: #666; font-size: 11px; margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 16px; }
th { background: #16a34a; color: white; padding: 8px 12px; text-align: left; font-size: 11px; }
td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
tr:nth-child(even) td { background: #f9fafb; }
.badge { padding: 2px 8px; border-radius: 100px; font-size: 10px; font-weight: bold; }
.normal      { background: #dcfce7; color: #166534; }
.stunting    { background: #fef3c7; color: #92400e; }
.underweight { background: #fef3c7; color: #92400e; }
.wasting     { background: #fee2e2; color: #991b1b; }
.footer  { margin-top: 24px; font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; }
.info-box { display: flex; gap: 20px; margin-bottom: 16px; }
.info-item { flex: 1; padding: 10px; background: #f9fafb; border-radius: 6px; }
.info-label { font-size: 10px; color: #6b7280; }
.info-value { font-size: 14px; font-weight: bold; color: #111827; }
</style>
</head>
<body>
<h1>📈 Laporan Pertumbuhan Anak</h1>
<div class="sub">GROW-MOM Monitoring System — Dicetak: {{ now()->format('d F Y, H:i') }}</div>

<div class="info-box">
    <div class="info-item">
        <div class="info-label">Nama Anak</div>
        <div class="info-value">{{ $anak->nama_anak }}</div>
    </div>
    <div class="info-item">
        <div class="info-label">Umur</div>
        <div class="info-value">{{ $anak->umur_label }}</div>
    </div>
    <div class="info-item">
        <div class="info-label">Jenis Kelamin</div>
        <div class="info-value">{{ $anak->jenis_kelamin_label }}</div>
    </div>
    <div class="info-item">
        <div class="info-label">Nama Ibu</div>
        <div class="info-value">{{ $anak->ibu->nama_ibu }}</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Berat Badan (kg)</th>
            <th>Tinggi Badan (cm)</th>
            <th>Status Gizi</th>
            <th>Catatan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pertumbuhan as $i => $p)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $p->tanggal_pengukuran->format('d M Y') }}</td>
            <td><strong>{{ $p->berat_badan }}</strong></td>
            <td>{{ $p->tinggi_badan }}</td>
            <td><span class="badge {{ $p->status_gizi }}">{{ ucfirst($p->status_gizi) }}</span></td>
            <td>{{ $p->catatan ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:#9ca3af;">Belum ada data pengukuran</td></tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    Laporan ini digenerate otomatis oleh GROW-MOM Monitoring System. Total {{ $pertumbuhan->count() }} data pengukuran.
</div>
</body>
</html>
