<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;

class UserMonitoringController extends Controller
{
    /**
     * Halaman utama monitoring: daftar semua ibu + statistik.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');

        // ── Stat cards ─────────────────────────────────────────
        $onlineCount = User::where('role', '!=', 'admin')
            ->where('last_activity_at', '>=', now()->subMinutes(5))
            ->count();

        $totalVisitsQuery = UserActivity::period($period);
        $totalVisits = (clone $totalVisitsQuery)->count();

        // Halaman terpopuler
        $topPages = UserActivity::period($period)
            ->selectRaw('page_title, COUNT(*) as total')
            ->groupBy('page_title')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // ── Grafik tren kunjungan 7 hari terakhir ─────────────
        $trendData = UserActivity::where('visited_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(visited_at) as tanggal, COUNT(*) as total, COUNT(DISTINCT user_id) as unique_users')
            ->groupByRaw('DATE(visited_at)')
            ->orderBy('tanggal')
            ->get();

        $trendLabels  = $trendData->pluck('tanggal')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'));
        $trendVisits  = $trendData->pluck('total');
        $trendUsers   = $trendData->pluck('unique_users');

        // ── Daftar user (ibu) dengan statistik ────────────────
        $users = User::where('role', '!=', 'admin')
            ->with('ibu')
            ->withCount(['activities as visit_count' => function ($q) use ($period) {
                $q->period($period);
            }])
            ->orderByDesc('last_activity_at')
            ->paginate(15);

        // Hitung durasi sesi per user (untuk yang ada kunjungan)
        $userIds = $users->pluck('id');
        $sessionDurations = [];
        if ($userIds->isNotEmpty()) {
            $activitiesByUser = UserActivity::whereIn('user_id', $userIds)
                ->period($period)
                ->orderBy('visited_at')
                ->get()
                ->groupBy('user_id');

            foreach ($activitiesByUser as $userId => $acts) {
                $sessionDurations[$userId] = UserActivity::calculateSessionDuration($acts);
            }
        }

        // Rata-rata durasi sesi global
        $avgDuration = count($sessionDurations) > 0
            ? round(array_sum($sessionDurations) / count($sessionDurations))
            : 0;

        return view('monitoring.index', compact(
            'period', 'onlineCount', 'totalVisits', 'topPages',
            'trendLabels', 'trendVisits', 'trendUsers',
            'users', 'sessionDurations', 'avgDuration'
        ));
    }

    /**
     * Detail aktivitas per-user.
     */
    public function show(Request $request, User $user)
    {
        $period = $request->get('period', 'today');

        // Info ibu
        $user->load('ibu');

        // Aktivitas dengan pagination
        $activities = $user->activities()
            ->period($period)
            ->orderByDesc('visited_at')
            ->paginate(30);

        // Statistik ringkasan
        $totalVisits = $user->activities()->period($period)->count();

        $topPages = $user->activities()
            ->period($period)
            ->selectRaw('page_title, COUNT(*) as total')
            ->groupBy('page_title')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Durasi sesi
        $allActivities = $user->activities()->period($period)->orderBy('visited_at')->get();
        $sessionDuration = UserActivity::calculateSessionDuration($allActivities);

        // Heatmap: distribusi jam aktif (0-23)
        $hourlyData = $user->activities()
            ->period($period)
            ->selectRaw('HOUR(visited_at) as jam, COUNT(*) as total')
            ->groupByRaw('HOUR(visited_at)')
            ->pluck('total', 'jam')
            ->toArray();

        // Pad semua jam (0-23)
        $heatmap = [];
        for ($h = 0; $h < 24; $h++) {
            $heatmap[$h] = $hourlyData[$h] ?? 0;
        }

        // Tren harian 7 hari terakhir untuk user ini
        $userTrend = $user->activities()
            ->where('visited_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(visited_at) as tanggal, COUNT(*) as total')
            ->groupByRaw('DATE(visited_at)')
            ->orderBy('tanggal')
            ->get();

        return view('monitoring.show', compact(
            'user', 'period', 'activities', 'totalVisits',
            'topPages', 'sessionDuration', 'heatmap', 'userTrend'
        ));
    }

    /**
     * API: jumlah user online (untuk auto-refresh via JS).
     */
    public function apiOnlineUsers()
    {
        $count = User::where('role', '!=', 'admin')
            ->where('last_activity_at', '>=', now()->subMinutes(5))
            ->count();

        return response()->json(['online' => $count]);
    }
}
