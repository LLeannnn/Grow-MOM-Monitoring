<?php

namespace App\Http\Controllers;

use App\Models\Ibu;
use Illuminate\Http\Request;

class IbuController extends Controller
{
    public function index(Request $request)
    {
        $query = Ibu::withCount('anak');

        if ($request->search) {
            $query->where('nama_ibu', 'like', "%{$request->search}%")
                  ->orWhere('nik', 'like', "%{$request->search}%");
        }

        $ibu = $query->latest()->paginate(10);
        return view('ibu.index', compact('ibu'));
    }

    public function create()
    {
        return view('ibu.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ibu'         => 'required|string|max:255',
            'nik'              => 'required|string|size:16|unique:ibu',
            'tanggal_lahir'    => 'required|date',
            'alamat'           => 'required|string',
            'no_telepon'       => 'required|string|max:20',
            'pekerjaan'        => 'required|in:ibu_rumah_tangga,pns,swasta,wiraswasta,petani,lainnya',
            'pendidikan'       => 'required|in:sd,smp,sma,d3,s1,s2,s3',
            'status_pernikahan' => 'required|in:menikah,belum_menikah,cerai',
            'foto'             => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('ibu', 'public');
        }

        Ibu::create($validated);

        return redirect()->route('ibu.index')
            ->with('success', 'Data ibu berhasil ditambahkan!');
    }

    public function show(Ibu $ibu)
    {
        $ibu->load(['anak.pertumbuhan', 'reminders']);
        return view('ibu.show', compact('ibu'));
    }

    public function edit(Ibu $ibu)
    {
        return view('ibu.edit', compact('ibu'));
    }

    public function update(Request $request, Ibu $ibu)
    {
        $validated = $request->validate([
            'nama_ibu'         => 'required|string|max:255',
            'nik'              => 'required|string|size:16|unique:ibu,nik,' . $ibu->id,
            'tanggal_lahir'    => 'required|date',
            'alamat'           => 'required|string',
            'no_telepon'       => 'required|string|max:20',
            'pekerjaan'        => 'required|in:ibu_rumah_tangga,pns,swasta,wiraswasta,petani,lainnya',
            'pendidikan'       => 'required|in:sd,smp,sma,d3,s1,s2,s3',
            'status_pernikahan' => 'required|in:menikah,belum_menikah,cerai',
            'foto'             => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('ibu', 'public');
        }

        $ibu->update($validated);

        return redirect()->route('ibu.show', $ibu)
            ->with('success', 'Data ibu berhasil diperbarui!');
    }

    public function destroy(Ibu $ibu)
    {
        $ibu->delete();
        return redirect()->route('ibu.index')
            ->with('success', 'Data ibu berhasil dihapus!');
    }
}
