<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GROW-MOM')</title>
    
    <!-- Inject Tailwind and Fonts -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#4648d4",
                        "primary-container": "#6063ee",
                        "on-primary": "#ffffff",
                        "on-primary-container": "#fffbff",
                        "secondary-container": "#dc2c4f",
                        "tertiary-container": "#00885d",
                        "surface": "#f7f9fb",
                        "surface-container": "#eceef0",
                        "surface-container-low": "#f2f4f6",
                        "surface-container-high": "#e6e8ea",
                        "surface-container-lowest": "#ffffff",
                        "on-surface": "#191c1e",
                        "on-surface-variant": "#464554",
                        "outline-variant": "#c7c4d7",
                        "error": "#ba1a1a",
                    },
                    fontFamily: {
                        "sans": ["Manrope", "sans-serif"],
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #f7f9fb; font-family: 'Manrope', sans-serif; min-height: 100vh; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        
        /* Animation for bottom sheet */
        .bottom-sheet {
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .bottom-sheet.show {
            transform: translateY(0);
        }
        .backdrop {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .backdrop.show {
            opacity: 1;
            pointer-events: auto;
        }

        /* Hide scrollbar for clean look */
        ::-webkit-scrollbar { width: 0px; background: transparent; }
    </style>
    @stack('styles')
</head>
<body class="bg-surface text-on-surface pb-28 min-h-screen">
    
    <!-- Top App Bar -->
    <header class="bg-surface sticky top-0 z-40 shadow-sm">
        <div class="flex justify-between items-center w-full px-6 h-16 max-w-md mx-auto">
            <div class="flex items-center gap-3">
                <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/20 overflow-hidden flex items-center justify-center text-primary text-xl">
                        <span class="material-symbols-outlined text-xl">person</span>
                    </div>
                    <span class="text-xl font-bold text-primary tracking-tight">GROW-MOM</span>
                </a>
            </div>
            <a href="{{ route('anak.create') }}" class="text-primary active:scale-95 transition-transform duration-200 bg-primary/10 p-1.5 rounded-full">
                <span class="material-symbols-outlined text-2xl">add</span>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="px-4 pt-6 space-y-6 max-w-md mx-auto">
        @if(session('success'))
            <div class="bg-tertiary-container/10 border-l-4 border-tertiary-container text-tertiary-container p-4 rounded-xl shadow-sm font-semibold mb-4 text-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">check_circle</span> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-secondary-container/10 border-l-4 border-secondary-container text-secondary-container p-4 rounded-xl shadow-sm font-semibold mb-4 text-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">cancel</span> {{ session('error') }}
            </div>
        @endif
        
        @yield('content')
    </main>

    <!-- Bottom Navigation Bar -->
    <nav class="fixed bottom-0 left-0 w-full z-40 bg-surface/90 backdrop-blur-md shadow-[0_-4px_20px_rgba(30,41,59,0.08)] rounded-t-3xl px-2 py-2 pb-safe flex justify-around items-center border-t border-white/50">
        <!-- Beranda -->
        <a href="{{ route('user.dashboard') }}" class="flex flex-col items-center justify-center {{ request()->routeIs('user.dashboard') ? 'bg-primary-container/20 text-primary' : 'text-on-surface-variant hover:text-primary' }} rounded-xl px-4 py-2 active:scale-95 transition-all">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('user.dashboard') ? "font-variation-settings: 'FILL' 1;" : "" }}">home</span>
            <span class="text-[10px] font-bold mt-1">Beranda</span>
        </a>
        
        <!-- Kategori Anak -->
        <button onclick="openBottomSheet('sheet-anak')" class="flex flex-col items-center justify-center {{ request()->routeIs('anak.*') || request()->routeIs('pertumbuhan.*') || request()->routeIs('recall.*') ? 'bg-primary-container/20 text-primary' : 'text-on-surface-variant hover:text-primary' }} rounded-xl px-4 py-2 active:scale-95 transition-all">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('anak.*') || request()->routeIs('pertumbuhan.*') || request()->routeIs('recall.*') ? "font-variation-settings: 'FILL' 1;" : "" }}">child_care</span>
            <span class="text-[10px] font-bold mt-1">Anak Saya</span>
        </button>
        
        <!-- Kategori Fitur -->
        <button onclick="openBottomSheet('sheet-fitur')" class="flex flex-col items-center justify-center {{ request()->routeIs('edukasi.*') || request()->routeIs('reminder.*') || request()->routeIs('feedback.*') ? 'bg-primary-container/20 text-primary' : 'text-on-surface-variant hover:text-primary' }} rounded-xl px-4 py-2 active:scale-95 transition-all">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('edukasi.*') || request()->routeIs('reminder.*') || request()->routeIs('feedback.*') ? "font-variation-settings: 'FILL' 1;" : "" }}">grid_view</span>
            <span class="text-[10px] font-bold mt-1">Fitur</span>
        </button>
        
        <!-- Kategori Akun -->
        <button onclick="openBottomSheet('sheet-akun')" class="flex flex-col items-center justify-center text-on-surface-variant px-4 py-2 hover:text-primary active:scale-95 transition-all">
            <span class="material-symbols-outlined">person</span>
            <span class="text-[10px] font-bold mt-1">Akun</span>
        </button>
    </nav>

    <!-- Backdrop for Bottom Sheets -->
    <div id="sheet-backdrop" class="backdrop fixed inset-0 bg-on-surface/40 backdrop-blur-sm z-50" onclick="closeAllSheets()"></div>

    <!-- Bottom Sheet: Anak Saya -->
    <div id="sheet-anak" class="bottom-sheet fixed bottom-0 left-0 w-full bg-surface z-50 rounded-t-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] px-6 py-6 pb-12">
        <div class="w-12 h-1.5 bg-outline-variant/40 rounded-full mx-auto mb-6"></div>
        <h3 class="text-xl font-bold mb-6 text-center">Menu Anak Saya</h3>
        <div class="space-y-3">
            <a href="{{ route('anak.index') }}" class="flex items-center p-4 rounded-2xl bg-surface-container-lowest shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-surface-container-low hover:border-primary/30 transition-colors active:scale-[0.98]">
                <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary mr-4"><span class="material-symbols-outlined text-2xl">child_care</span></div>
                <div class="flex-1">
                    <div class="font-bold text-base text-on-surface">Data Anak</div>
                    <div class="text-xs text-on-surface-variant font-medium mt-0.5">Kelola profil & data diri anak</div>
                </div>
                <span class="material-symbols-outlined text-outline-variant">chevron_right</span>
            </a>
            <a href="{{ route('pertumbuhan.index') }}" class="flex items-center p-4 rounded-2xl bg-surface-container-lowest shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-surface-container-low hover:border-primary/30 transition-colors active:scale-[0.98]">
                <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary mr-4"><span class="material-symbols-outlined text-2xl">monitoring</span></div>
                <div class="flex-1">
                    <div class="font-bold text-base text-on-surface">Pertumbuhan</div>
                    <div class="text-xs text-on-surface-variant font-medium mt-0.5">Pantau grafik BB & TB (Z-Score)</div>
                </div>
                <span class="material-symbols-outlined text-outline-variant">chevron_right</span>
            </a>
            <a href="{{ route('recall.index') }}" class="flex items-center p-4 rounded-2xl bg-surface-container-lowest shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-surface-container-low hover:border-primary/30 transition-colors active:scale-[0.98]">
                <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary mr-4"><span class="material-symbols-outlined text-2xl">restaurant</span></div>
                <div class="flex-1">
                    <div class="font-bold text-base text-on-surface">Catat Makan</div>
                    <div class="text-xs text-on-surface-variant font-medium mt-0.5">Input asupan gizi & kalori harian</div>
                </div>
                <span class="material-symbols-outlined text-outline-variant">chevron_right</span>
            </a>
        </div>
    </div>

    <!-- Bottom Sheet: Fitur -->
    <div id="sheet-fitur" class="bottom-sheet fixed bottom-0 left-0 w-full bg-surface z-50 rounded-t-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] px-6 py-6 pb-12">
        <div class="w-12 h-1.5 bg-outline-variant/40 rounded-full mx-auto mb-6"></div>
        <h3 class="text-xl font-bold mb-6 text-center">Fitur Lainnya</h3>
        <div class="space-y-3">
            <a href="{{ route('edukasi.index') }}" class="flex items-center p-4 rounded-2xl bg-surface-container-lowest shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-surface-container-low hover:border-primary/30 transition-colors active:scale-[0.98]">
                <div class="w-12 h-12 rounded-full bg-tertiary-container/10 flex items-center justify-center text-tertiary-container mr-4"><span class="material-symbols-outlined text-2xl">school</span></div>
                <div class="flex-1">
                    <div class="font-bold text-base text-on-surface">Tips MPASI</div>
                    <div class="text-xs text-on-surface-variant font-medium mt-0.5">Artikel & resep gizi seimbang</div>
                </div>
                <span class="material-symbols-outlined text-outline-variant">chevron_right</span>
            </a>
            <a href="{{ route('reminder.index') }}" class="flex items-center p-4 rounded-2xl bg-surface-container-lowest shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-surface-container-low hover:border-primary/30 transition-colors active:scale-[0.98]">
                <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary mr-4"><span class="material-symbols-outlined text-2xl">notifications_active</span></div>
                <div class="flex-1">
                    <div class="font-bold text-base text-on-surface">Pengingat</div>
                    <div class="text-xs text-on-surface-variant font-medium mt-0.5">Jadwal imunisasi & kontrol rutin</div>
                </div>
                <span class="material-symbols-outlined text-outline-variant">chevron_right</span>
            </a>
            <a href="{{ route('feedback.index') }}" class="flex items-center p-4 rounded-2xl bg-surface-container-lowest shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-surface-container-low hover:border-primary/30 transition-colors active:scale-[0.98]">
                <div class="w-12 h-12 rounded-full bg-secondary-container/10 flex items-center justify-center text-secondary-container mr-4"><span class="material-symbols-outlined text-2xl">forum</span></div>
                <div class="flex-1">
                    <div class="font-bold text-base text-on-surface">Feedback</div>
                    <div class="text-xs text-on-surface-variant font-medium mt-0.5">Rekomendasi dari admin/ahli gizi</div>
                </div>
                <span class="material-symbols-outlined text-outline-variant">chevron_right</span>
            </a>
        </div>
    </div>

    <!-- Bottom Sheet: Akun -->
    <div id="sheet-akun" class="bottom-sheet fixed bottom-0 left-0 w-full bg-surface z-50 rounded-t-[32px] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] px-6 py-6 pb-12">
        <div class="w-12 h-1.5 bg-outline-variant/40 rounded-full mx-auto mb-6"></div>
        <h3 class="text-xl font-bold mb-6 text-center">Profil Saya</h3>
        
        <div class="flex items-center mb-6 p-4 bg-primary/5 rounded-2xl border border-primary/10">
            <div class="w-14 h-14 rounded-full bg-primary/20 flex items-center justify-center text-primary mr-4"><span class="material-symbols-outlined text-3xl">person</span></div>
            <div>
                <div class="font-bold text-lg text-on-surface">{{ auth()->user()->ibu->nama_ibu ?? auth()->user()->name }}</div>
                <div class="text-sm font-medium text-primary">Ibu</div>
            </div>
        </div>
        
        <div class="space-y-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center p-4 rounded-2xl bg-error/10 text-error hover:bg-error/20 transition-colors active:scale-[0.98]">
                    <div class="w-12 h-12 rounded-full bg-white/50 flex items-center justify-center mr-4"><span class="material-symbols-outlined text-2xl">logout</span></div>
                    <div class="font-bold text-base text-left">Keluar (Logout)</div>
                </button>
            </form>
        </div>
    </div>

    <script>
        function openBottomSheet(id) {
            document.getElementById('sheet-backdrop').classList.add('show');
            document.getElementById(id).classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeAllSheets() {
            document.getElementById('sheet-backdrop').classList.remove('show');
            const sheets = document.querySelectorAll('.bottom-sheet');
            sheets.forEach(sheet => sheet.classList.remove('show'));
            document.body.style.overflow = '';
        }
    </script>
    @stack('scripts')
</body>
</html>
