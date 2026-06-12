<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() {
        if (Auth::check()) return $this->redirectByRole();
        return view('auth.login');
    }

    public function login(Request $request) {
        $request->validate([
            'nomer'    => 'required|string',
            'password' => 'required',
        ], [
            'nomer.required'    => 'No. WhatsApp wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt(['nomer' => $request->nomer, 'password' => $request->password], $request->remember)) {
            $request->session()->regenerate();
            return $this->redirectByRole();
        }
        return back()->withErrors(['nomer' => 'No. WhatsApp atau password salah.'])->withInput($request->only('nomer'));
    }

    public function showRegister() {
        if (Auth::check()) return $this->redirectByRole();
        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'name'     => 'required|string|max:100',
            'nomer'    => 'required|string|unique:users,nomer',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'nomer.required'     => 'No. WhatsApp wajib diisi.',
            'nomer.unique'       => 'No. WhatsApp ini sudah terdaftar. Silakan login.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $plainPassword = $request->password;

        $user = User::create([
            'name'     => $request->name,
            'nomer'    => $request->nomer,
            'password' => Hash::make($plainPassword),
            'role'     => 'user',
        ]);

        // Kirim pesan sambutan via WhatsApp (Fonnte)
        try {
            $fonnte  = new FonnteService();
            $pesan   = "Halo Ibu {$user->name} 👋\n\n"
                     . "Selamat datang di *GROW-MOM* 🌱\n"
                     . "Akun Anda telah berhasil dibuat.\n\n"
                     . "Untuk Login masukkan:\n"
                     . "📱 No. WhatsApp : {$user->nomer}\n"
                     . "🔑 Password      : {$plainPassword}\n\n"
                     . "Simpan informasi ini dan jangan bagikan kepada siapapun ya, Bu!";
            $fonnte->send($user->nomer, $pesan);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal kirim WA registrasi: ' . $e->getMessage());
        }

        Auth::login($user);
        return redirect()->route('onboarding')->with('success', 'Selamat datang! Yuk lengkapi profil Anda dulu 😊');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda berhasil keluar.');
    }

    private function redirectByRole() {
        return Auth::user()->isAdmin()
            ? redirect()->route('dashboard')
            : redirect()->route('user.dashboard');
    }
}