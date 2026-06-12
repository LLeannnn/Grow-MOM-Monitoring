<?php

namespace App\Http\Middleware;

use App\Models\UserActivity;
use Closure;
use Illuminate\Http\Request;

class TrackUserActivity
{
    /**
     * Catat aktivitas browsing user (non-admin) ke database.
     * - Hanya request GET (halaman yang dibuka, bukan form submit)
     * - Throttle: skip jika URL yang sama dikunjungi ulang < 30 detik
     * - Skip AJAX/API requests
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Hanya catat untuk user yang sudah login, bukan admin, dan request GET
        if (
            !auth()->check() ||
            auth()->user()->isAdmin() ||
            !$request->isMethod('GET') ||
            $request->ajax() ||
            $request->wantsJson()
        ) {
            return $response;
        }

        $user = auth()->user();
        $now  = now();

        // Update last_activity_at (untuk status online)
        $user->timestamps = false;
        $user->update(['last_activity_at' => $now]);

        // Throttle: jangan catat jika URL sama dalam 30 detik terakhir
        $recentVisit = UserActivity::where('user_id', $user->id)
            ->where('url', $request->path())
            ->where('visited_at', '>=', $now->copy()->subSeconds(30))
            ->exists();

        if ($recentVisit) {
            return $response;
        }

        // Map route name ke label halaman
        $routeName = $request->route()?->getName();
        $labels    = UserActivity::pageLabels();
        $pageTitle = $labels[$routeName] ?? ucfirst(str_replace(['.', '-', '/'], ' ', $routeName ?? $request->path()));

        // Simpan record aktivitas
        UserActivity::create([
            'user_id'    => $user->id,
            'url'        => $request->path(),
            'page_title' => mb_substr($pageTitle, 0, 100),
            'method'     => 'GET',
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr($request->userAgent() ?? '', 0, 500),
            'session_id' => session()->getId(),
            'visited_at' => $now,
        ]);

        return $response;
    }
}
