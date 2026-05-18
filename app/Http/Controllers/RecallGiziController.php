<?php

namespace App\Http\Controllers;

use App\Models\RecallGizi;
use App\Models\Anak;
use App\Services\FoodNutritionService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RecallGiziController extends Controller
{
    private function checkAccess(Anak $anak)
    {
        if (!auth()->user()->isAdmin()) {
            $ibu = auth()->user()->ibu;
            if (!$ibu || $anak->ibu_id !== $ibu->id) {
                abort(403, 'Akses ditolak. Anda tidak berhak mengakses data recall gizi anak ini.');
            }
        }
    }

    // AKG (Angka Kecukupan Gizi Indonesia 2019) by usia
    public static function getAkg(int $umurBulan): array
    {
        if ($umurBulan <= 5)  return ['energi' => 550,  'protein' => 9,  'karbo' => 58,  'lemak' => 31, 'label' => '0-5 bulan'];
        if ($umurBulan <= 11) return ['energi' => 725,  'protein' => 16, 'karbo' => 82,  'lemak' => 36, 'label' => '6-11 bulan'];
        if ($umurBulan <= 35) return ['energi' => 1125, 'protein' => 26, 'karbo' => 155, 'lemak' => 44, 'label' => '1-2 tahun'];
        if ($umurBulan <= 59) return ['energi' => 1600, 'protein' => 35, 'karbo' => 220, 'lemak' => 62, 'label' => '3-4 tahun'];
        return                       ['energi' => 1600, 'protein' => 40, 'karbo' => 220, 'lemak' => 62, 'label' => '5+ tahun'];
    }

    /**
     * Endpoint AJAX: cari makanan dari database lokal + Open Food Facts
     * GET /recall/food-search?q=...
     */
    public function searchFood(Request $request)
    {
        $query = trim($request->get('q', ''));
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        $results = FoodNutritionService::search($query);
        return response()->json($results);
    }

    /**
     * @deprecated Gunakan FoodNutritionService::getLocalDatabase()
     */
    public static function getDaftarMakanan(): array
    {
        return FoodNutritionService::getLocalDatabase();
    }

    public function index(Request $request)
    {
        $query = RecallGizi::with('anak.ibu');

        if (!auth()->user()->isAdmin()) {
            $ibu = auth()->user()->ibu;
            if (!$ibu) return redirect()->route('onboarding');
            // Filter global recall list to only their children
            $query->whereHas('anak', function($q) use ($ibu) {
                $q->where('ibu_id', $ibu->id);
            });
        }

        if ($request->anak_id) {
            $anak = Anak::findOrFail($request->anak_id);
            $this->checkAccess($anak);
            $query->where('anak_id', $anak->id);
        }
        if ($request->tanggal) $query->whereDate('tanggal', $request->tanggal);

        $recalls  = $query->orderBy('tanggal', 'desc')->paginate(20);

        if (auth()->user()->isAdmin()) {
            $anakList = Anak::orderBy('nama_anak')->get();
        } else {
            $anakList = Anak::where('ibu_id', auth()->user()->ibu->id)->orderBy('nama_anak')->get();
        }

        // Ringkasan vs AKG untuk anak yang difilter
        $ringkasanHariIni = null;
        $akg              = null;
        if ($request->anak_id) {
            $anak = Anak::find($request->anak_id);
            $akg  = $anak ? self::getAkg($anak->umur_bulan) : null;

            $ringkasanHariIni = RecallGizi::where('anak_id', $request->anak_id)
                ->whereDate('tanggal', today())
                ->selectRaw('SUM(kalori) as total_kalori, SUM(protein) as total_protein, SUM(karbohidrat) as total_karbo, SUM(lemak) as total_lemak')
                ->first();
        } elseif (!auth()->user()->isAdmin()) {
            $firstAnak = $anakList->first();
            if ($firstAnak) {
                $akg = self::getAkg($firstAnak->umur_bulan);
                $ringkasanHariIni = RecallGizi::where('anak_id', $firstAnak->id)
                    ->whereDate('tanggal', today())
                    ->selectRaw('SUM(kalori) as total_kalori, SUM(protein) as total_protein, SUM(karbohidrat) as total_karbo, SUM(lemak) as total_lemak')
                    ->first();
            }
        }

        if (!auth()->user()->isAdmin()) {
            return view('recall.index_user', compact('recalls', 'anakList', 'ringkasanHariIni', 'akg'));
        }

        return view('recall.index', compact('recalls', 'anakList', 'ringkasanHariIni', 'akg'));
    }

    public function create(Request $request)
    {
        if (auth()->user()->isAdmin()) {
            $anakList = Anak::with('ibu')->orderBy('nama_anak')->get();
        } else {
            $ibu = auth()->user()->ibu;
            if (!$ibu) return redirect()->route('onboarding');
            $anakList = Anak::where('ibu_id', $ibu->id)->orderBy('nama_anak')->get();
        }
        $daftarMakanan = FoodNutritionService::getLocalDatabase();
        $selectedAnak  = $request->anak_id ? Anak::find($request->anak_id) : null;
        $akg           = $selectedAnak ? self::getAkg($selectedAnak->umur_bulan) : null;

        if (!auth()->user()->isAdmin()) {
            return view('recall.form_user', compact('anakList', 'daftarMakanan', 'selectedAnak', 'akg'));
        }

        return view('recall.create', compact('anakList', 'daftarMakanan', 'selectedAnak', 'akg'));
    }

    /**
     * Batch store — accepts arrays of food items per waktu makan
     */
    public function store(Request $request)
    {
        $request->validate([
            'anak_id'         => 'required|exists:anak,id',
            'tanggal'         => 'required|date',
            'nama_makanan'    => 'required|array|min:1',
            'nama_makanan.*'  => 'required|string|max:255',
            'waktu_makan'     => 'required|array',
            'waktu_makan.*'   => 'required|in:pagi,siang,malam,snack',
            'jumlah'          => 'required|array',
            'jumlah.*'        => 'required|numeric|min:0',
            'satuan'          => 'required|array',
            'satuan.*'        => 'required|string|max:50',
            'kalori'          => 'nullable|array',
            'protein'         => 'nullable|array',
            'karbohidrat'     => 'nullable|array',
            'lemak'           => 'nullable|array',
        ]);

        $anak = Anak::findOrFail($request->anak_id);
        $this->checkAccess($anak);

        $saved = 0;
        foreach ($request->nama_makanan as $i => $nama) {
            if (empty($nama)) continue;
            RecallGizi::create([
                'anak_id'      => $request->anak_id,
                'tanggal'      => $request->tanggal,
                'waktu_makan'  => $request->waktu_makan[$i],
                'nama_makanan' => $nama,
                'jumlah'       => $request->jumlah[$i] ?? 1,
                'satuan'       => $request->satuan[$i] ?? 'porsi',
                'kalori'       => $request->kalori[$i] ?? 0,
                'protein'      => $request->protein[$i] ?? 0,
                'karbohidrat'  => $request->karbohidrat[$i] ?? 0,
                'lemak'        => $request->lemak[$i] ?? 0,
            ]);
            $saved++;
        }

        return redirect()->route('recall.index', ['anak_id' => $request->anak_id])
            ->with('success', "{$saved} asupan berhasil disimpan!");
    }

    public function destroy(RecallGizi $recall)
    {
        $this->checkAccess($recall->anak);
        $recall->delete();
        return back()->with('success', 'Data asupan berhasil dihapus!');
    }

    public function exportPdf(Request $request)
    {
        if (!$request->anak_id) {
            return back()->with('error', 'Silakan pilih anak terlebih dahulu untuk mengekspor PDF.');
        }
        $anak    = Anak::with('ibu')->findOrFail($request->anak_id);
        $this->checkAccess($anak);
        $recalls = RecallGizi::where('anak_id', $request->anak_id)->orderBy('tanggal')->get();
        $akg     = self::getAkg($anak->umur_bulan);
        $pdf     = Pdf::loadView('recall.pdf', compact('anak', 'recalls', 'akg'));
        return $pdf->download("recall-gizi-{$anak->nama_anak}.pdf");
    }
}
