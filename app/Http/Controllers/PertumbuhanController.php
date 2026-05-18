<?php

namespace App\Http\Controllers;

use App\Models\Pertumbuhan;
use App\Models\Anak;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PertumbuhanController extends Controller
{
    private function checkAccess(Anak $anak)
    {
        if (!auth()->user()->isAdmin()) {
            $ibu = auth()->user()->ibu;
            if (!$ibu || $anak->ibu_id !== $ibu->id) {
                abort(403, 'Akses ditolak. Anda tidak berhak mengakses data pertumbuhan anak ini.');
            }
        }
    }

    public function index(Request $request)
    {
        $query = Pertumbuhan::with('anak.ibu');

        if (!auth()->user()->isAdmin()) {
            $ibu = auth()->user()->ibu;
            if (!$ibu) return redirect()->route('onboarding');
            
            $query->whereHas('anak', function($q) use ($ibu) {
                $q->where('ibu_id', $ibu->id);
            });
            $anakList = Anak::where('ibu_id', $ibu->id)->orderBy('nama_anak')->get();
        } else {
            $anakList = Anak::orderBy('nama_anak')->get();
        }

        if ($request->anak_id) {
            // Verifikasi akses jika difilter berdasarkan anak tertentu
            if (!auth()->user()->isAdmin()) {
                $anak = Anak::findOrFail($request->anak_id);
                $this->checkAccess($anak);
            }
            $query->where('anak_id', $request->anak_id);
        }
        if ($request->status_gizi) {
            $query->where('status_gizi', $request->status_gizi);
        }

        $pertumbuhan = $query->orderBy('tanggal_pengukuran', 'desc')->paginate(15);

        if (!auth()->user()->isAdmin()) {
            return view('pertumbuhan.index_user', compact('pertumbuhan', 'anakList'));
        }
        return view('pertumbuhan.index', compact('pertumbuhan', 'anakList'));
    }

    public function create()
    {
        if (auth()->user()->isAdmin()) {
            $anakList = Anak::with('ibu')->orderBy('nama_anak')->get();
        } else {
            $ibu = auth()->user()->ibu;
            if (!$ibu) return redirect()->route('onboarding');
            $anakList = Anak::where('ibu_id', $ibu->id)->orderBy('nama_anak')->get();
        }
        if (!auth()->user()->isAdmin()) {
            return view('pertumbuhan.form_user', compact('anakList'));
        }
        return view('pertumbuhan.create', compact('anakList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'anak_id'            => 'required|exists:anak,id',
            'tanggal_pengukuran' => 'required|date',
            'berat_badan'        => 'required|numeric|min:0.5|max:100',
            'tinggi_badan'       => 'required|numeric|min:20|max:250',
            'catatan'            => 'nullable|string',
        ]);

        $anak = Anak::findOrFail($validated['anak_id']);
        $this->checkAccess($anak);

        $umurBulanSaatPengukuran = (int) \Carbon\Carbon::parse($anak->tanggal_lahir)
            ->diffInMonths(\Carbon\Carbon::parse($validated['tanggal_pengukuran']));

        // WHO-based classification (pass tinggiBadan for HAZ/stunting check)
        $validated['status_gizi'] = Pertumbuhan::hitungStatusGizi(
            (float) $validated['berat_badan'],
            $umurBulanSaatPengukuran,
            $anak->jenis_kelamin,
            (float) $validated['tinggi_badan']
        );

        Pertumbuhan::create($validated);

        return redirect()->route('pertumbuhan.index')
            ->with('success', 'Data pertumbuhan berhasil disimpan! Status: ' . ucfirst($validated['status_gizi']));
    }

    public function show(Pertumbuhan $pertumbuhan)
    {
        $pertumbuhan->load('anak.ibu');
        $anak       = $pertumbuhan->anak;
        $this->checkAccess($anak);
        
        $umurBulan = (int) \Carbon\Carbon::parse($anak->tanggal_lahir)
            ->diffInMonths(\Carbon\Carbon::parse($pertumbuhan->tanggal_pengukuran));
            
        $jk = $anak->jenis_kelamin;

        // Semua riwayat pertumbuhan anak ini (untuk chart actual)
        $riwayat = Pertumbuhan::where('anak_id', $anak->id)
            ->orderBy('tanggal_pengukuran')
            ->get(['tanggal_pengukuran', 'berat_badan', 'tinggi_badan', 'status_gizi']);

        // Z-Scores untuk pengukuran ini
        $waz = Pertumbuhan::hitungZScore((float) $pertumbuhan->berat_badan, $umurBulan, $jk, 'weight');
        $haz = Pertumbuhan::hitungZScore((float) $pertumbuhan->tinggi_badan, $umurBulan, $jk, 'height');

        // ── WHO Chart Data Intervals (based on this measurement)
        if ($umurBulan <= 20) {
            $minUmur = 0; $maxUmur = 20; $interval = 1;
        } elseif ($umurBulan <= 40) {
            $minUmur = 21; $maxUmur = 40; $interval = 2;
        } else {
            $minUmur = 41; $maxUmur = 60; $interval = 3;
        }

        $whoWeight = Pertumbuhan::getWhoReferenceForChart($minUmur, $maxUmur, $jk, 'weight');
        $whoHeight = Pertumbuhan::getWhoReferenceForChart($minUmur, $maxUmur, $jk, 'height');

        // Chart actual anak (null-padded array)
        $chartBerat  = array_fill(0, $maxUmur - $minUmur + 1, null);
        $chartTinggi = array_fill(0, $maxUmur - $minUmur + 1, null);
        $tglLahir    = \Carbon\Carbon::parse($anak->tanggal_lahir);

        foreach ($riwayat as $r) {
            $uB = $tglLahir->diffInMonths($r->tanggal_pengukuran);
            if ($uB >= $minUmur && $uB <= $maxUmur) {
                $idx = $uB - $minUmur;
                $chartBerat[$idx]  = (float) $r->berat_badan;
                $chartTinggi[$idx] = (float) $r->tinggi_badan;
            }
        }
        $chartLabels = [];

        // Deteksi status WHO lengkap
        $statusWho = $this->getStatusWhoDetail($waz, $haz);

        return view('pertumbuhan.show', compact(
            'pertumbuhan', 'anak', 'riwayat',
            'waz', 'haz', 'statusWho',
            'whoWeight', 'whoHeight',
            'chartLabels', 'chartBerat', 'chartTinggi',
            'minUmur', 'maxUmur', 'interval'
        ));
    }

    public function destroy(Pertumbuhan $pertumbuhan)
    {
        $this->checkAccess($pertumbuhan->anak);
        $pertumbuhan->delete();
        return redirect()->route('pertumbuhan.index')
            ->with('success', 'Data pertumbuhan berhasil dihapus!');
    }

    public function exportPdf(Request $request)
    {
        $anakId      = $request->anak_id;
        if (!$anakId) {
            return back()->with('error', 'Silakan pilih anak terlebih dahulu untuk mengekspor PDF.');
        }
        $anak        = Anak::with('ibu')->findOrFail($anakId);
        $this->checkAccess($anak);
        $pertumbuhan = Pertumbuhan::where('anak_id', $anakId)
            ->orderBy('tanggal_pengukuran')
            ->get();

        $pdf = Pdf::loadView('pertumbuhan.pdf', compact('anak', 'pertumbuhan'));
        return $pdf->download("laporan-pertumbuhan-{$anak->nama_anak}.pdf");
    }

    // ─────────────────────────────────────────────────────────────
    private function getStatusWhoDetail(?float $waz, ?float $haz): array
    {
        $items = [];

        // Stunting (HAZ)
        if ($haz !== null) {
            if ($haz < -2)  $items[] = ['label' => 'Stunting',        'class' => 'badge-warning', 'desc' => 'Tinggi badan rendah untuk usia (HAZ < -2SD)', 'icon' => '⚠️'];
        }

        // Wasting / Underweight (WAZ)
        if ($waz !== null) {
            if ($waz < -3)      $items[] = ['label' => 'Wasting',      'class' => 'badge-danger',  'desc' => 'Berat badan sangat rendah untuk usia (WAZ < -3SD)', 'icon' => '🚨'];
            elseif ($waz < -2)  $items[] = ['label' => 'Underweight',     'class' => 'badge-warning', 'desc' => 'Berat badan rendah untuk usia (WAZ < -2SD)', 'icon' => '⚠️'];
        }

        if (empty($items)) {
            $items[] = ['label' => 'Normal', 'class' => 'badge-success', 'desc' => 'Berat dan tinggi badan sesuai standar WHO', 'icon' => '✅'];
        }

        return $items;
    }
}
