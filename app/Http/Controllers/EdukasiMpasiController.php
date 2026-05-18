<?php

namespace App\Http\Controllers;

use App\Models\EdukasiMpasi;
use Illuminate\Http\Request;

class EdukasiMpasiController extends Controller
{
    public function index(Request $request)
    {
        $query = EdukasiMpasi::where('is_published', true);

        if ($request->kategori) {
            $query->where('kategori_usia', $request->kategori);
        }
        if ($request->search) {
            $query->where('judul', 'like', "%{$request->search}%");
        }

        $edukasi = $query->latest()->paginate(9);
        $kategoriList = [
            '6_bulan'     => '6 Bulan',
            '7_9_bulan'   => '7-9 Bulan',
            '10_12_bulan' => '10-12 Bulan',
            '12_24_bulan' => '12-24 Bulan',
            'umum'        => 'Umum',
        ];

        if (!auth()->user()->isAdmin()) {
            return view('edukasi.index_user', compact('edukasi', 'kategoriList'));
        }

        return view('edukasi.index', compact('edukasi', 'kategoriList'));
    }

    public function show(EdukasiMpasi $edukasi)
    {
        $related = EdukasiMpasi::where('kategori_usia', $edukasi->kategori_usia)
            ->where('id', '!=', $edukasi->id)
            ->take(3)
            ->get();

        if (!auth()->user()->isAdmin()) {
            return view('edukasi.show_user', compact('edukasi', 'related'));
        }

        return view('edukasi.show', compact('edukasi', 'related'));
    }

    public function create()
    {
        return view('edukasi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'        => 'required|string|max:255',
            'kategori_usia' => 'required|in:6_bulan,7_9_bulan,10_12_bulan,12_24_bulan,umum',
            'bahan_makanan'=> 'nullable|string',
            'tekstur_makanan'=> 'nullable|string',
            'konten'       => 'required|string',
            'gambar'       => 'nullable|image|max:4096',
            'tags'         => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('edukasi', 'public');
        }

        EdukasiMpasi::create($validated);

        return redirect()->route('edukasi.index')
            ->with('success', 'Konten edukasi berhasil ditambahkan!');
    }

    public function edit(EdukasiMpasi $edukasi)
    {
        return view('edukasi.edit', compact('edukasi'));
    }

    public function update(Request $request, EdukasiMpasi $edukasi)
    {
        $validated = $request->validate([
            'judul'        => 'required|string|max:255',
            'kategori_usia' => 'required|in:6_bulan,7_9_bulan,10_12_bulan,12_24_bulan,umum',
            'bahan_makanan'=> 'nullable|string',
            'tekstur_makanan'=> 'nullable|string',
            'konten'       => 'required|string',
            'gambar'       => 'nullable|image|max:4096',
            'tags'         => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($edukasi->gambar && \Storage::disk('public')->exists($edukasi->gambar)) {
                \Storage::disk('public')->delete($edukasi->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('edukasi', 'public');
        }

        $edukasi->update($validated);

        return redirect()->route('edukasi.show', $edukasi)
            ->with('success', 'Konten edukasi berhasil diperbarui!');
    }

    public function destroy(EdukasiMpasi $edukasi)
    {
        $edukasi->delete();
        return redirect()->route('edukasi.index')
            ->with('success', 'Konten edukasi berhasil dihapus!');
    }
}
