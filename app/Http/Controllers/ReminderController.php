<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Models\Anak;
use App\Models\Ibu;
use App\Services\FonnteService;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $query = Reminder::with(['ibu', 'anak']);

        if (!auth()->user()->isAdmin()) {
            $ibu = auth()->user()->ibu;
            if (!$ibu) return redirect()->route('onboarding');
            $query->where('ibu_id', $ibu->id);
        }

        if ($request->status) $query->where('status', $request->status);
        if ($request->tipe)   $query->where('tipe', $request->tipe);

        $reminders = $query->orderBy('tanggal_reminder')->paginate(15);
        if (!auth()->user()->isAdmin()) {
            return view('reminder.index_user', compact('reminders'));
        }
        return view('reminder.index', compact('reminders'));
    }

    public function create()
    {
        $ibuList  = Ibu::orderBy('nama_ibu')->get();
        $anakList = Anak::with('ibu')->orderBy('nama_anak')->get();
        return view('reminder.create', compact('ibuList', 'anakList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ibu_id'           => 'required|exists:ibu,id',
            'anak_id'          => 'required|exists:anak,id',
            'judul'            => 'required|string|max:255',
            'pesan'            => 'required|string',
            'tanggal_reminder' => 'required|date',
            'tipe'             => 'required|in:imunisasi,posyandu,mpasi,kontrol,lainnya',
        ]);

        $reminder = Reminder::create($validated);

        // Kirim pesan WhatsApp ke nomor ibu via Fonnte
        $ibu = Ibu::find($validated['ibu_id']);
        if ($ibu && $ibu->no_telepon) {
            (new FonnteService())->send($ibu->no_telepon, $validated['pesan']);
        }

        return redirect()->route('reminder.index')
            ->with('success', 'Reminder berhasil dibuat!');
    }

    public function selesai(Reminder $reminder)
    {
        if (!auth()->user()->isAdmin()) {
            $ibu = auth()->user()->ibu;
            if (!$ibu || $reminder->ibu_id !== $ibu->id) {
                abort(403, 'Anda tidak berhak mengubah status reminder ini.');
            }
        }

        $reminder->update(['status' => 'selesai']);
        return back()->with('success', 'Reminder ditandai selesai!');
    }

    public function destroy(Reminder $reminder)
    {
        $reminder->delete();
        return back()->with('success', 'Reminder berhasil dihapus!');
    }

}
