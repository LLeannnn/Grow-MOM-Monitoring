<?php
namespace App\Http\Controllers;

use App\Models\Ibu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function show() {
        $user = Auth::user();
        if ($user->ibu) return redirect()->route('user.dashboard');
        return view('auth.onboarding');
    }

    public function store(Request $request) {
        $request->validate([
            'nik'               => 'required|string|size:16|unique:ibu,nik',
            'nama_ibu'          => 'required|string|max:100',
            'tanggal_lahir'     => 'required|date',
            'alamat'            => 'required|string|max:255',
            'pekerjaan'         => 'required|in:ibu_rumah_tangga,pns,swasta,wiraswasta,petani,lainnya',
            'pendidikan'        => 'required|in:sd,smp,sma,d3,s1,s2,s3',
            'status_pernikahan' => 'required|in:menikah,belum_menikah,cerai',
        ], [
            'nik.required'           => 'NIK KTP wajib diisi.',
            'nik.size'               => 'NIK KTP harus tepat 16 digit.',
            'nik.unique'             => 'NIK ini sudah terdaftar di sistem.',
            'nama_ibu.required'      => 'Nama lengkap wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'alamat.required'        => 'Alamat wajib diisi.',
            'pekerjaan.required'     => 'Pekerjaan wajib dipilih.',
            'pendidikan.required'    => 'Pendidikan wajib dipilih.',
            'status_pernikahan.required' => 'Status pernikahan wajib dipilih.',
        ]);

        Ibu::create([
            'user_id'           => Auth::user()->id,
            'nik'               => $request->nik,
            'nama_ibu'          => $request->nama_ibu,
            'tanggal_lahir'     => $request->tanggal_lahir,
            'no_telepon'        => Auth::user()->nomer,
            'alamat'            => $request->alamat,
            'pekerjaan'         => $request->pekerjaan,
            'pendidikan'        => $request->pendidikan,
            'status_pernikahan' => $request->status_pernikahan,
        ]);

        return redirect()->route('user.dashboard')
            ->with('success', 'Profil berhasil disimpan! Selamat datang di GROW-MOM 🎉');
    }
}