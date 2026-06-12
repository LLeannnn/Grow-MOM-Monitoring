<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Recall Gizi - {{ $anak->nama_anak }}</title>
<style>
body { font-family: Arial, sans-serif; font-size: 12px; }
h1 { color: #16a34a; font-size: 18px; }
table { width: 100%; border-collapse: collapse; margin-top: 16px; }
th { background: #16a34a; color: white; padding: 8px 10px; font-size: 11px; }
td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
tr:nth-child(even) td { background: #f9fafb; }
.totals { margin-top: 16px; background: #f0fdf4; padding: 12px; border-radius: 6px; }
</style>
</head>
<body>
<h1><i data-feather="clipboard"></i> Recall Gizi — {{ $anak->nama_anak }}</h1>
<p style="color:#666;font-size:11px;">Dicetak: {{ now()->format('d F Y, H:i') }} | Ibu: {{ $anak->ibu->nama_ibu }}</p>

<table>
    <thead><tr><th>Tgl</th><th>Waktu</th><th>Makanan</th><th>Jml</th><th>Satuan</th><th>Kalori</th><th>Protein</th><th>Karbo</th><th>Lemak</th></tr></thead>
    <tbody>
        @php $totKal=0; $totPro=0; $totKarbo=0; $totLem=0; @endphp
        @foreach($recalls as $r)
        @php $totKal+=$r->kalori; $totPro+=$r->protein; $totKarbo+=$r->karbohidrat; $totLem+=$r->lemak; @endphp
        <tr>
            <td>{{ $r->tanggal->format('d/m/Y') }}</td>
            <td>{{ $r->waktu_makan }}</td>
            <td>{{ $r->nama_makanan }}</td>
            <td>{{ $r->jumlah }}</td>
            <td>{{ $r->satuan }}</td>
            <td>{{ $r->kalori }}</td>
            <td>{{ $r->protein }}</td>
            <td>{{ $r->karbohidrat }}</td>
            <td>{{ $r->lemak }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="totals">
    <strong>Total Asupan:</strong>
    Kalori: {{ round($totKal,1) }} kkal |
    Protein: {{ round($totPro,1) }} g |
    Karbohidrat: {{ round($totKarbo,1) }} g |
    Lemak: {{ round($totLem,1) }} g
</div>
</body>
</html>
