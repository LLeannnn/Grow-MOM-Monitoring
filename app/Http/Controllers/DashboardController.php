<?php

namespace App\Http\Controllers;

use App\Models\Ibu;
use App\Models\Anak;
use App\Models\Pertumbuhan;
use App\Models\RecallGizi;
use App\Models\Reminder;
use App\Models\Feedback;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalIbu      = Ibu::count();
        $totalAnak     = Anak::count();
        $reminderAktif = Reminder::where('status', 'aktif')->count();
        $totalFeedback = Feedback::count();

        // Status Gizi Distribution
        $statusGizi = Pertumbuhan::selectRaw('status_gizi, COUNT(*) as total')
            ->groupBy('status_gizi')
            ->pluck('total', 'status_gizi');

        // Growth data for chart (last 6 months, average berat)
        $pertumbuhanBulanan = Pertumbuhan::selectRaw('MONTH(tanggal_pengukuran) as bulan, YEAR(tanggal_pengukuran) as tahun, AVG(berat_badan) as avg_berat, AVG(tinggi_badan) as avg_tinggi')
            ->where('tanggal_pengukuran', '>=', now()->subMonths(6))
            ->groupByRaw('YEAR(tanggal_pengukuran), MONTH(tanggal_pengukuran)')
            ->orderByRaw('tahun ASC, bulan ASC')
            ->get();

        // Pre-process chart labels (avoids arrow-function issue inside Blade @json)
        $bulanLabels = $pertumbuhanBulanan->map(function ($p) {
            return date('M Y', mktime(0, 0, 0, $p->bulan, 1, $p->tahun));
        })->values();
        $avgBerat    = $pertumbuhanBulanan->pluck('avg_berat')->map(fn($v) => round((float)$v, 2))->values();
        $avgTinggi   = $pertumbuhanBulanan->pluck('avg_tinggi')->map(fn($v) => round((float)$v, 2))->values();

        // Recent recall gizi
        $recentRecall = RecallGizi::with('anak')
            ->latest()
            ->take(5)
            ->get();

        // Upcoming reminders
        $upcomingReminders = Reminder::with(['anak', 'ibu'])
            ->where('status', 'aktif')
            ->where('tanggal_reminder', '>=', now())
            ->orderBy('tanggal_reminder')
            ->take(5)
            ->get();

        // Anak terbaru
        $anakTerbaru = Anak::with('ibu')->latest()->take(5)->get();

        // Feedback terbaru
        // avgRating removed

        return view('dashboard.index', compact(
            'totalIbu', 'totalAnak', 'reminderAktif', 'totalFeedback',
            'statusGizi', 'bulanLabels', 'avgBerat', 'avgTinggi',
            'recentRecall', 'upcomingReminders', 'anakTerbaru'
        ));
    }

    /** Dashboard untuk user (ibu) — hanya data miliknya sendiri */
    public function userDashboard()
    {
        $user = auth()->user();
        $ibu  = $user->ibu;

        // Jika belum isi profil → paksa onboarding
        if (!$ibu) return redirect()->route('onboarding');

        $anakList = $ibu->anak()->with('pertumbuhan')->get();

        // Recall gizi hari ini untuk semua anak user
        $anakIds = $anakList->pluck('id');
        $recallHariIni = \App\Models\RecallGizi::whereIn('anak_id', $anakIds)
            ->whereDate('tanggal', today())
            ->selectRaw('anak_id, SUM(kalori) as total_kal, SUM(protein) as total_pro, SUM(karbohidrat) as total_kar, SUM(lemak) as total_lem')
            ->groupBy('anak_id')
            ->get()
            ->keyBy('anak_id');

        $reminderAktif = $ibu->reminders()->where('status','aktif')
            ->where('tanggal_reminder','>=', today())->orderBy('tanggal_reminder')->take(5)->get();

        return view('dashboard.user', compact('ibu','anakList','recallHariIni','reminderAktif'));
    }
}
