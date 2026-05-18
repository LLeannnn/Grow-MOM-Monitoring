<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class AdminMiddleware {
    public function handle(Request $request, Closure $next) {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return redirect()->route('user.dashboard')->with('error', 'Halaman ini khusus admin.');
        }
        return $next($request);
    }
}