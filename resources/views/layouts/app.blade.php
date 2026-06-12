<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — GROW-MOM</title>
    
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="app-wrapper">
    <!-- MOBILE OVERLAY -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <a href="{{ auth()->user()?->isAdmin() ? route('dashboard') : route('user.dashboard') }}" class="sidebar-brand">
            <div class="sidebar-logo">
                <i data-feather="heart"></i>
            </div>
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
                <span class="nav-icon"><i data-feather="grid"></i></span> Dashboard
            </a>
            
            <div class="nav-section-label">Data Master</div>
            <a href="{{ route('ibu.index') }}" class="nav-item {{ request()->routeIs('ibu.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-feather="users"></i></span> Data Ibu
            </a>
            <a href="{{ route('anak.index') }}" class="nav-item {{ request()->routeIs('anak.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-feather="smile"></i></span> Data Anak
            </a>
            
            <div class="nav-section-label">Monitoring</div>
            <a href="{{ route('pertumbuhan.index') }}" class="nav-item {{ request()->routeIs('pertumbuhan.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-feather="trending-up"></i></span> Pertumbuhan
            </a>
            <a href="{{ route('recall.index') }}" class="nav-item {{ request()->routeIs('recall.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-feather="clipboard"></i></span> Recall Gizi
            </a>
            <a href="{{ route('monitoring.index') }}" class="nav-item {{ request()->routeIs('monitoring.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-feather="eye"></i></span> Monitoring User
            </a>
            
            <div class="nav-section-label">Fitur & Layanan</div>
            <a href="{{ route('edukasi.index') }}" class="nav-item {{ request()->routeIs('edukasi.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-feather="book-open"></i></span> Edukasi MPASI
            </a>
            <a href="{{ route('reminder.index') }}" class="nav-item {{ request()->routeIs('reminder.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-feather="bell"></i></span> Reminder
            </a>
            <a href="{{ route('feedback.index') }}" class="nav-item {{ request()->routeIs('feedback.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-feather="message-square"></i></span> Feedback
            </a>
            @else
            {{-- ── USER MENU (IBU) ── --}}
            <div class="nav-section-label">Menu Saya</div>
            <a href="{{ route('user.dashboard') }}" class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <span class="nav-icon"><i data-feather="home"></i></span> Beranda
            </a>
            <div class="nav-section-label">Anak Saya</div>
            <a href="{{ route('anak.index') }}" class="nav-item {{ request()->routeIs('anak.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-feather="smile"></i></span> Data Anak
            </a>
            <a href="{{ route('pertumbuhan.index') }}" class="nav-item {{ request()->routeIs('pertumbuhan.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-feather="trending-up"></i></span> Pertumbuhan
            </a>
            <a href="{{ route('recall.index') }}" class="nav-item {{ request()->routeIs('recall.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-feather="pie-chart"></i></span> Catat Makan
            </a>
            <div class="nav-section-label">Lainnya</div>
            <a href="{{ route('edukasi.index') }}" class="nav-item {{ request()->routeIs('edukasi.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-feather="book-open"></i></span> Tips MPASI
            </a>
            <a href="{{ route('reminder.index') }}" class="nav-item {{ request()->routeIs('reminder.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-feather="bell"></i></span> Pengingat
            </a>
            <a href="{{ route('feedback.index') }}" class="nav-item {{ request()->routeIs('feedback.*') ? 'active' : '' }}">
                <span class="nav-icon"><i data-feather="message-square"></i></span> Feedback
            </a>
            @endif
            @endauth
        </nav>
        <div class="sidebar-footer">
            <div class="sidebar-footer-info">
                <div class="avatar-sm">
                    {{ auth()->user()?->isAdmin() ? 'A' : 'U' }}
                </div>
                <div>
                    <div class="footer-name">{{ Str::limit(auth()->user()?->name ?? 'Guest', 18) }}</div>
                    <div class="footer-role">{{ auth()->user()?->isAdmin() ? 'Administrator' : 'Ibu' }}</div>
                </div>
            </div>
            @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <i data-feather="log-out"></i> Keluar
                </button>
            </form>
            @endauth
        </div>
    </aside>

    <main class="main-content">
        <!-- TOPBAR STICKY (Refine Style) -->
        <div class="topbar-sticky">
            <div class="breadcrumb">
                <a href="#"><i data-feather="home" style="width:14px;height:14px;"></i></a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">@yield('title')</span>
            </div>
            @if(!auth()->user()?->isAdmin())
            <div class="topbar-right">
                <a href="{{ route('reminder.index') }}" class="topbar-action-icon" title="Notifikasi">
                    <i data-feather="bell"></i>
                </a>
                <div class="avatar-sm" style="width: 34px; height: 34px;">
                    U
                </div>
            </div>
            @endif
        </div>

        <!-- MOBILE HEADER -->
        <div class="mobile-header">
            <div style="display:flex; align-items:center; gap:12px;">
                @if(!auth()->user()?->isAdmin())
                <button class="sidebar-toggle-btn" id="sidebarToggle">
                    <i data-feather="menu"></i>
                </button>
                @endif
                <div class="mobile-brand">
                    <i data-feather="heart"></i> GROW-MOM
                </div>
            </div>
            @if(!auth()->user()?->isAdmin())
            <div class="topbar-right">
                <a href="{{ route('reminder.index') }}" class="topbar-action-icon" title="Notifikasi">
                    <i data-feather="bell"></i>
                </a>
                <div class="avatar-sm" style="width: 34px; height: 34px;">
                    U
                </div>
            </div>
            @endif
        </div>

        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success fade-up">
                    <i data-feather="check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error fade-up">
                    <i data-feather="alert-circle"></i> {{ session('error') }}
                </div>
            @endif
            
            @yield('content')
        </div>
    </main>
</div>

@stack('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Feather Icons
    feather.replace();

    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mobileOverlay = document.getElementById('mobileOverlay');

    if (sidebarToggle && sidebar && mobileOverlay) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.add('show');
            mobileOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
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