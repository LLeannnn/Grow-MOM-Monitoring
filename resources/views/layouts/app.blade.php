<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — GROW-MOM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="app-wrapper">
    <!-- MOBILE OVERLAY -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-gradient"></div>
        <a href="{{ auth()->user()?->isAdmin() ? route('dashboard') : route('user.dashboard') }}" class="sidebar-brand">
            <div class="sidebar-logo">👩‍🍼</div>
            <div class="sidebar-brand-text">
                <div class="sidebar-brand-title">GROW-MOM</div>
                <div class="sidebar-brand-sub">Monitoring System</div>
            </div>
        </a>
        <nav class="sidebar-nav">
            @auth
            @if(auth()->user()->isAdmin())
            {{-- ── ADMIN MENU ── --}}
            <div class="nav-section-label">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">🏠</span> Dashboard Admin
            </a>
            <div class="nav-section-label">Data</div>
            <a href="{{ route('ibu.index') }}" class="nav-item {{ request()->routeIs('ibu.*') ? 'active' : '' }}">
                <span class="nav-icon">👩</span> Data Ibu
            </a>
            <a href="{{ route('anak.index') }}" class="nav-item {{ request()->routeIs('anak.*') ? 'active' : '' }}">
                <span class="nav-icon">👶</span> Data Anak
            </a>
            <div class="nav-section-label">Monitoring</div>
            <a href="{{ route('pertumbuhan.index') }}" class="nav-item {{ request()->routeIs('pertumbuhan.*') ? 'active' : '' }}">
                <span class="nav-icon">📈</span> Pertumbuhan
            </a>
            <a href="{{ route('recall.index') }}" class="nav-item {{ request()->routeIs('recall.*') ? 'active' : '' }}">
                <span class="nav-icon">📋</span> Recall Gizi
            </a>
            <div class="nav-section-label">Fitur</div>
            <a href="{{ route('edukasi.index') }}" class="nav-item {{ request()->routeIs('edukasi.*') ? 'active' : '' }}">
                <span class="nav-icon">🥕</span> Edukasi MPASI
            </a>
            <a href="{{ route('reminder.index') }}" class="nav-item {{ request()->routeIs('reminder.*') ? 'active' : '' }}">
                <span class="nav-icon">🔔</span> Reminder
            </a>
            <a href="{{ route('feedback.index') }}" class="nav-item {{ request()->routeIs('feedback.*') ? 'active' : '' }}">
                <span class="nav-icon">💬</span> Feedback
            </a>
            @else
            {{-- ── USER MENU (IBU) ── --}}
            <div class="nav-section-label">Menu Saya</div>
            <a href="{{ route('user.dashboard') }}" class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">🏠</span> Beranda
            </a>
            <div class="nav-section-label">Anak Saya</div>
            <a href="{{ route('anak.index') }}" class="nav-item {{ request()->routeIs('anak.*') ? 'active' : '' }}">
                <span class="nav-icon">👶</span> Data Anak
            </a>
            <a href="{{ route('pertumbuhan.index') }}" class="nav-item {{ request()->routeIs('pertumbuhan.*') ? 'active' : '' }}">
                <span class="nav-icon">📈</span> Pertumbuhan
            </a>
            <a href="{{ route('recall.index') }}" class="nav-item {{ request()->routeIs('recall.*') ? 'active' : '' }}">
                <span class="nav-icon">🍽️</span> Catat Makan
            </a>
            <div class="nav-section-label">Lainnya</div>
            <a href="{{ route('edukasi.index') }}" class="nav-item {{ request()->routeIs('edukasi.*') ? 'active' : '' }}">
                <span class="nav-icon">🥕</span> Tips MPASI
            </a>
            <a href="{{ route('reminder.index') }}" class="nav-item {{ request()->routeIs('reminder.*') ? 'active' : '' }}">
                <span class="nav-icon">🔔</span> Pengingat
            </a>
            <a href="{{ route('feedback.index') }}" class="nav-item {{ request()->routeIs('feedback.*') ? 'active' : '' }}">
                <span class="nav-icon">💬</span> Feedback / Rekomendasi
            </a>
            @endif
            @endauth
        </nav>
        <div class="sidebar-footer">
            <div class="sidebar-footer-info">
                <div class="avatar-sm">{{ auth()->user()?->isAdmin() ? '👑' : '👩' }}</div>
                <div>
                    <div class="footer-name">{{ Str::limit(auth()->user()?->name ?? 'Guest', 18) }}</div>
                    <div class="footer-role">{{ auth()->user()?->isAdmin() ? 'Administrator' : 'Ibu' }}</div>
                </div>
            </div>
            @auth
            <form method="POST" action="{{ route('logout') }}" style="margin-top:8px;">
                @csrf
                <button type="submit" style="width:100%;background:rgba(255,255,255,0.12);color:#fff;border:1px solid rgba(255,255,255,0.25);border-radius:8px;padding:8px;cursor:pointer;font-size:12.5px;font-weight:600;transition:background .2s;" onmouseover="this.style.background='rgba(255,255,255,0.22)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                    🚪 Keluar
                </button>
            </form>
            @endauth
        </div>
    </aside>
    <main class="main-content">
        <!-- MOBILE HEADER -->
        <div class="mobile-header">
            <button class="sidebar-toggle-btn" id="sidebarToggle">
                ☰
            </button>
            <div class="mobile-brand">
                👩‍🍼 GROW-MOM
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success fade-up">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error fade-up">❌ {{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
</div>
@stack('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mobileOverlay = document.getElementById('mobileOverlay');

    if (sidebarToggle && sidebar && mobileOverlay) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.add('show');
            mobileOverlay.classList.add('show');
            document.body.style.overflow = 'hidden'; // Prevent body scroll
        });

        mobileOverlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            mobileOverlay.classList.remove('show');
            document.body.style.overflow = '';
        });
    }
});
</script>
</body>
</html>