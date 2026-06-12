<?php

namespace App\Http\Controllers;

use App\Models\Anak;
use App\Models\Ibu;
use Illuminate\Http\Request;

class AnakController extends Controller
{
    private function checkAccess(Anak $anak)
    {
        if (!auth()->user()->isAdmin()) {
            $ibu = auth()->user()->ibu;
            if (!$ibu || $anak->ibu_id !== $ibu->id) {
                abort(403, 'Akses ditolak. Anda tidak berhak melihat atau mengubah data anak ini.');
            }
        }
    }

    public function index(Request $request)
    {
        $query = Anak::with('ibu');

        // User hanya melihat anak sendiri
        if (!auth()->user()->isAdmin()) {
            $ibu = auth()->user()->ibu;
            if (!$ibu) return redirect()->route('onboarding');
            $query->where('ibu_id', $ibu->id);
        }

        if ($request->search) {
            $query->where('nama_anak', 'like', "%{$request->search}%");
        }
        if ($request->jenis_kelamin) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        $anak = $query->latest()->paginate(12);
        if (!auth()->user()->isAdmin()) {
            return view('anak.index_user', compact('anak'));
        }
        return view('anak.index', compact('anak'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            $ibuList = Ibu::orderBy('nama_ibu')->get();
            return view('anak.create', compact('ibuList'));
        }
        // User: auto-set ibu mereka sendiri
        $ibu = $user->ibu;
        if (!$ibu) return redirect()->route('onboarding');
        $ibuList = collect([$ibu]);
        return view('anak.form_user', compact('ibuList'));
    }

    public function store(Request $request)
    {
        // Pastikan user tidak bisa memilih ibu_id milik orang lain
        if (!auth()->user()->isAdmin()) {
            $ibu = auth()->user()->ibu;
            if (!$ibu) return redirect()->route('onboarding');
            $request->merge(['ibu_id' => $ibu->id]);
        }

        $validated = $request->validate([
            'ibu_id'        => 'required|exists:ibu,id',
            'nama_anak'     => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'berat_lahir'   => 'nullable|numeric|min:0.5|max:10',
            'panjang_lahir' => 'nullable|numeric|min:20|max:80',
            'golongan_darah' => 'required|in:A,B,AB,O,tidak_diketahui',
            'foto'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('anak', 'public');
        }

        $anak = Anak::create($validated);

        return redirect()->route('anak.show', $anak)
            ->with('success', 'Data anak berhasil ditambahkan!');
    }

    public function show(Anak $anak)
    {
        $this->checkAccess($anak);
        $anak->load(['ibu', 'pertumbuhan', 'recallGizi', 'reminders', 'feedbackAnak']);

        // reorder() clears relasi default DESC, lalu set ASC agar grafik kiri→kanan
        $pertumbuhanChart = $anak->pertumbuhan()
            ->reorder('tanggal_pengukuran', 'asc')
            ->get(['tanggal_pengukuran', 'berat_badan', 'tinggi_badan']);

        // ── WHO Chart Data Intervals ──────────────────────────────
        $minUmur = 0;
        $maxUmur = 104; // 104 minggu (~24 bulan)

        // Pre-process chart actual anak (null-padded array for Chart.js category scale)
        $chartBerat  = array_fill(0, $maxUmur - $minUmur + 1, null);
        $chartTinggi = array_fill(0, $maxUmur - $minUmur + 1, null);
        $tglLahir    = \Carbon\Carbon::parse($anak->tanggal_lahir);
        
        foreach ($pertumbuhanChart as $p) {
            $umurMingguAtMeasure = $tglLahir->diffInWeeks($p->tanggal_pengukuran);
            
            if ($umurMingguAtMeasure >= $minUmur && $umurMingguAtMeasure <= $maxUmur) {
                $index = $umurMingguAtMeasure - $minUmur;
                // Assign to index so it perfectly aligns with WHO labels
                $chartBerat[$index]  = (float) $p->berat_badan;
                $chartTinggi[$index] = (float) $p->tinggi_badan;
            }
        }
        $chartLabels = []; 

        $whoWeight = \App\Models\Pertumbuhan::getWhoReferenceForChart($minUmur, $maxUmur, $anak->jenis_kelamin, 'weight', true);
        $whoHeight = \App\Models\Pertumbuhan::getWhoReferenceForChart($minUmur, $maxUmur, $anak->jenis_kelamin, 'height', true);

        // ── Nutrition Summary (Hari Ini) ─────────────────────────
        $ringkasanHariIni = \App\Models\RecallGizi::where('anak_id', $anak->id)
            ->whereDate('tanggal', today())
            ->selectRaw('SUM(kalori) as total_kalori, SUM(protein) as total_protein, SUM(karbohidrat) as total_karbo, SUM(lemak) as total_lemak')
            ->first();
        $akg = \App\Http\Controllers\RecallGiziController::getAkg($anak->umur_bulan);

        if (!auth()->user()->isAdmin()) {
            return view('anak.show_user', compact(
                'anak',
                'pertumbuhanChart',
                'chartLabels',
                'chartBerat',
                'chartTinggi',
                'whoWeight',
                'whoHeight',
                'ringkasanHariIni',
                'akg',
                'minUmur',
                'maxUmur'
            ));
        }

        return view('anak.show', compact(
            'anak',
            'pertumbuhanChart',
            'chartLabels',
            'chartBerat',
            'chartTinggi',
            'whoWeight',
            'whoHeight',
            'ringkasanHariIni',
            'akg',
            'minUmur',
            'maxUmur'
        ));
    }

    public function edit(Anak $anak)
    {
        $this->checkAccess($anak);
        $ibuList = Ibu::orderBy('nama_ibu')->get();
        if (!auth()->user()->isAdmin()) {
            return view('anak.form_user', compact('anak', 'ibuList'));
        }
        return view('anak.edit', compact('anak', 'ibuList'));
    }

    public function update(Request $request, Anak $anak)
    {
        $this->checkAccess($anak);
        $validated = $request->validate([
            'ibu_id'        => 'required|exists:ibu,id',
            'nama_anak'     => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'berat_lahir'   => 'nullable|numeric|min:0.5|max:10',
            'panjang_lahir' => 'nullable|numeric|min:20|max:80',
            'golongan_darah' => 'required|in:A,B,AB,O,tidak_diketahui',
            'foto'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('anak', 'public');
        }

        $anak->update($validated);

        return redirect()->route('anak.show', $anak)
            ->with('success', 'Data anak berhasil diperbarui!');
    }

    public function destroy(Anak $anak)
    {
        $this->checkAccess($anak);
        $anak->delete();
        return redirect()->route('anak.index')
            ->with('success', 'Data anak berhasil dihapus!');
    }
}
