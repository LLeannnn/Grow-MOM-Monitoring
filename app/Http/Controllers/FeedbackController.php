<?php

namespace App\Http\Controllers;

use App\Models\Anak;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    private function checkAccess(Anak $anak)
    {
        if (!auth()->user()->isAdmin()) {
            $ibu = auth()->user()->ibu;
            if (!$ibu || $anak->ibu_id !== $ibu->id) {
                abort(403, 'Akses ditolak. Anda tidak berhak melihat data anak ini.');
            }
        }
    }

    /**
     * Halaman utama — daftar semua anak (bagi admin) atau anak sendiri (bagi ibu).
     */
    public function index(Request $request)
    {
        $query = Anak::with([
            'ibu',
            'pertumbuhan' => fn($q) => $q->orderBy('tanggal_pengukuran', 'desc')->limit(1),
            'recallGizi'  => fn($q) => $q->where('tanggal', '>=', now()->subDays(7)),
        ]);

        if (!auth()->user()->isAdmin()) {
            $ibu = auth()->user()->ibu;
            if (!$ibu) return redirect()->route('onboarding');
            $query->where('ibu_id', $ibu->id);
        }

        if ($request->search) {
            $query->where('nama_anak', 'like', "%{$request->search}%");
        }

        $anakList = $query->latest()->get();

        // Generate ringkasan feedback per anak (untuk card di index)
        $ringkasanFeedback = [];
        foreach ($anakList as $anak) {
            $ringkasanFeedback[$anak->id] = $this->getRingkasan($anak);
        }

        if (!auth()->user()->isAdmin()) {
            return view('feedback.index_user', compact('anakList', 'ringkasanFeedback'));
        }

        return view('feedback.index', compact('anakList', 'ringkasanFeedback'));
    }

    /**
     * Detail feedback otomatis untuk satu anak.
     */
    public function show(Anak $anak)
    {
        $this->checkAccess($anak);

        $anak->load([
            'ibu',
            'pertumbuhan',
            'recallGizi' => fn($q) => $q->where('tanggal', '>=', now()->subDays(7))->orderBy('tanggal'),
            'feedbackAnak', // Load manual feedbacks
        ]);

        $feedbacks = $this->generateFeedback($anak);

        // Hitung statistik nutrisi 7 hari
        $recalls     = $anak->recallGizi;
        $hariCount   = max(1, $recalls->groupBy('tanggal')->count());
        $nutrisiStats = [
            'kalori'      => ['nilai' => round($recalls->sum('kalori') / $hariCount),     'target' => $this->targetKalori($anak->umur_bulan)],
            'protein'     => ['nilai' => round($recalls->sum('protein') / $hariCount, 1),  'target' => $this->targetProtein($anak->umur_bulan)],
            'karbohidrat' => ['nilai' => round($recalls->sum('karbohidrat') / $hariCount, 1), 'target' => $this->targetKarbo($anak->umur_bulan)],
            'lemak'       => ['nilai' => round($recalls->sum('lemak') / $hariCount, 1),    'target' => $this->targetLemak($anak->umur_bulan)],
        ];

        if (!auth()->user()->isAdmin()) {
            return view('feedback.show_user', compact('anak', 'feedbacks', 'nutrisiStats', 'recalls', 'hariCount'));
        }

        return view('feedback.show', compact('anak', 'feedbacks', 'nutrisiStats', 'recalls', 'hariCount'));
    }

    /**
     * Menyimpan feedback manual dari admin untuk anak tertentu.
     */
    public function storeManualFeedback(Request $request, Anak $anak)
    {
        $request->validate([
            'pesan' => 'required|string|max:1000',
        ]);

        $anak->feedbackAnak()->create([
            'pesan' => $request->pesan,
        ]);

        return redirect()->route('feedback.show', $anak)
            ->with('success', 'Catatan / feedback manual berhasil ditambahkan!');
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────

    private function getRingkasan(Anak $anak): array
    {
        $pertumbuhanTerakhir = $anak->pertumbuhan->first();
        $hasRecall           = $anak->recallGizi->isNotEmpty();

        $status  = $pertumbuhanTerakhir?->status_gizi ?? 'belum_diukur';
        $badge   = match($status) {
            'normal'       => ['label' => 'Normal',        'class' => 'badge-success'],
            'kurang'       => ['label' => 'Kurang',        'class' => 'badge-warning'],
            'buruk'        => ['label' => 'Gizi Buruk',    'class' => 'badge-danger'],
            'lebih'        => ['label' => 'Lebih',         'class' => 'badge-info'],
            'obesitas'     => ['label' => 'Obesitas',      'class' => 'badge-danger'],
            default        => ['label' => 'Belum Diukur',  'class' => 'badge-neutral'],
        };

        // Hitung skor kelengkapan data (0-100)
        $skor = 0;
        if ($pertumbuhanTerakhir) $skor += 50;
        if ($hasRecall) $skor += 50;

        return [
            'status_gizi' => $status,
            'badge'       => $badge,
            'has_recall'  => $hasRecall,
            'skor'        => $skor,
        ];
    }

    private function generateFeedback(Anak $anak): array
    {
        $feedbacks  = [];
        $umurBulan  = $anak->umur_bulan;
        $pertTerakhir = $anak->pertumbuhan->first();
        $recalls      = $anak->recallGizi;

        // ── 1. ANALISIS STATUS GIZI ──────────────────────────────
        if ($pertTerakhir) {
            $feedbacks[] = match($pertTerakhir->status_gizi) {
                'buruk'   => [
                    'tipe'  => 'danger', 'icon' => '🚨',
                    'judul' => 'Status Gizi Buruk — Tindakan Segera Diperlukan',
                    'pesan' => 'Berat badan anak jauh di bawah standar WHO. Kondisi ini memerlukan penanganan medis segera. Konsultasikan ke dokter atau puskesmas dan berikan MPASI berkalori tinggi (nasi lembek + minyak + telur + daging).',
                    'saran' => ['🏥 Segera konsultasi ke dokter/puskesmas', '🍳 Tambahkan 1 sdt minyak ke setiap porsi makan', '🥚 Berikan telur setiap hari', '📅 Pantau kenaikan berat badan setiap minggu'],
                ],
                'kurang'  => [
                    'tipe'  => 'warning', 'icon' => '⚠️',
                    'judul' => 'Berat Badan Kurang dari Standar WHO',
                    'pesan' => 'Berat badan anak berada di bawah kurva pertumbuhan WHO. Tingkatkan asupan makanan bergizi tinggi dan frekuensi makan untuk mengejar ketertinggalan.',
                    'saran' => ['🥑 Tambahkan alpukat, minyak, atau mentega ke makanan', '🍗 Perbanyak protein: telur, ikan, ayam, tahu, tempe', '⏰ Tingkatkan frekuensi makan 1-2x dari biasanya', '📏 Ukur berat badan setiap 2 minggu'],
                ],
                'lebih'   => [
                    'tipe'  => 'info', 'icon' => 'ℹ️',
                    'judul' => 'Berat Badan Sedikit di Atas Normal',
                    'pesan' => 'Berat badan anak sedikit melebihi standar. Pertahankan pola makan sehat dengan mengurangi makanan manis dan tinggi lemak.',
                    'saran' => ['🥦 Perbanyak sayuran dan buah segar', '🚫 Kurangi makanan manis, gorengan, dan camilan tidak sehat', '🏃 Tingkatkan aktivitas fisik & bermain aktif', '✅ Lanjutkan pemantauan rutin di posyandu'],
                ],
                'obesitas' => [
                    'tipe'  => 'danger', 'icon' => '🚨',
                    'judul' => 'Obesitas — Perlu Intervensi Gizi',
                    'pesan' => 'Berat badan anak jauh melebihi standar WHO. Konsultasikan ke dokter atau ahli gizi untuk program diet yang aman bagi anak.',
                    'saran' => ['🏥 Konsultasi ke dokter/ahli gizi', '🚫 Hentikan makanan tinggi gula dan lemak jenuh', '🥗 Perbanyak sayuran, buah, dan protein tanpa lemak', '🏃 Perbanyak aktivitas fisik aktif'],
                ],
                default => [
                    'tipe'  => 'success', 'icon' => '✅',
                    'judul' => 'Status Gizi Normal — Pertahankan!',
                    'pesan' => 'Selamat! Berat badan anak berada dalam kisaran normal WHO. Pola makan dan pertumbuhan anak sudah berjalan dengan baik.',
                    'saran' => ['🥗 Lanjutkan pola makan bergizi seimbang', '📅 Rutin ke posyandu setiap bulan', '📏 Pantau pertumbuhan secara berkala', '🎯 Variasikan menu agar anak tidak bosan'],
                ],
            };
        } else {
            $feedbacks[] = [
                'tipe'  => 'neutral', 'icon' => '📏',
                'judul' => 'Belum Ada Data Pengukuran Pertumbuhan',
                'pesan' => 'Data berat dan tinggi badan anak belum tersedia. Segera input pengukuran untuk mendapatkan analisis status gizi.',
                'saran' => ['📥 Input data pengukuran di menu Pertumbuhan'],
            ];
        }

        // ── 2. ANALISIS ASUPAN NUTRISI (7 hari) ─────────────────
        if ($recalls->isNotEmpty()) {
            $hariCount        = max(1, $recalls->groupBy('tanggal')->count());
            $avgKalori        = $recalls->sum('kalori') / $hariCount;
            $avgProtein       = $recalls->sum('protein') / $hariCount;
            $avgKarbo         = $recalls->sum('karbohidrat') / $hariCount;
            $avgLemak         = $recalls->sum('lemak') / $hariCount;

            $targetKal  = $this->targetKalori($umurBulan);
            $targetPro  = $this->targetProtein($umurBulan);
            $pctKalori  = $targetKal > 0 ? ($avgKalori / $targetKal) * 100 : 0;
            $pctProtein = $targetPro > 0 ? ($avgProtein / $targetPro) * 100 : 0;

            // Kalori
            if ($pctKalori < 70) {
                $feedbacks[] = [
                    'tipe'  => 'warning', 'icon' => '🔥',
                    'judul' => 'Asupan Kalori Harian Kurang',
                    'pesan' => sprintf(
                        'Rata-rata asupan kalori anak hanya %.0f kkal/hari (%.0f%% dari kebutuhan %d kkal). Perlu peningkatan segera.',
                        $avgKalori, $pctKalori, $targetKal
                    ),
                    'saran' => ['🍚 Tambahkan porsi nasi/kentang/roti', '🫒 Tambahkan 1 sdt minyak zaitun/minyak kelapa ke makanan', '🍌 Berikan snack buah atau biskuit bayi 2x sehari'],
                ];
            } elseif ($pctKalori < 90) {
                $feedbacks[] = [
                    'tipe'  => 'info', 'icon' => '🔥',
                    'judul' => 'Asupan Kalori Hampir Mencukupi',
                    'pesan' => sprintf(
                        'Asupan kalori sudah mencapai %.0f%% dari kebutuhan (%.0f / %d kkal). Tambahkan sedikit lagi!',
                        $pctKalori, $avgKalori, $targetKal
                    ),
                    'saran' => ['➕ Tambahkan 1 porsi snack sehat per hari', '🥜 Selai kacang atau alpukat sebagai topping makanan'],
                ];
            } else {
                $feedbacks[] = [
                    'tipe'  => 'success', 'icon' => '🔥',
                    'judul' => 'Asupan Kalori Sudah Mencukupi',
                    'pesan' => sprintf('Rata-rata %.0f kkal/hari — memenuhi %.0f%% dari kebutuhan. Bagus sekali!', $avgKalori, min($pctKalori, 100)),
                    'saran' => ['✅ Pertahankan pola makan yang sudah baik'],
                ];
            }

            // Protein
            if ($pctProtein < 80) {
                $feedbacks[] = [
                    'tipe'  => 'warning', 'icon' => '🥩',
                    'judul' => 'Asupan Protein Kurang',
                    'pesan' => sprintf(
                        'Rata-rata protein %.1f g/hari, di bawah kebutuhan %d g/hari. Protein penting untuk pertumbuhan otak dan otot.',
                        $avgProtein, $targetPro
                    ),
                    'saran' => ['🥚 Berikan telur 1 butir setiap hari', '🐟 Variasikan ikan (salmon, tuna, lele, kembung)', '🌱 Tahu & tempe sebagai sumber protein nabati terjangkau', '🍗 Daging ayam atau sapi 2-3x seminggu'],
                ];
            }

            // Frekuensi makan
            $frekPerHari    = $recalls->groupBy('tanggal')->map(fn($g) => $g->unique('waktu_makan')->count())->avg();
            $minFrekuensi   = $umurBulan <= 8 ? 2 : ($umurBulan <= 12 ? 3 : 4);
            $labelFrekuensi = $umurBulan <= 8 ? '2' : ($umurBulan <= 12 ? '3' : '4-5');

            if ($frekPerHari < $minFrekuensi) {
                $feedbacks[] = [
                    'tipe'  => 'warning', 'icon' => '⏰',
                    'judul' => 'Frekuensi Makan Masih Kurang',
                    'pesan' => sprintf(
                        'Rata-rata anak makan %.1fx/hari. Usia %d bulan membutuhkan minimal %s kali makan per hari untuk memenuhi kebutuhan energi.',
                        $frekPerHari, $umurBulan, $labelFrekuensi
                    ),
                    'saran' => ['📅 Buat jadwal makan yang konsisten (pagi, siang, sore, malam)', '🍎 Sisipkan snack sehat di antara waktu makan utama', '⏰ Tawarkan makan setiap 2-3 jam sekali'],
                ];
            } else {
                $feedbacks[] = [
                    'tipe'  => 'success', 'icon' => '⏰',
                    'judul' => 'Frekuensi Makan Sudah Sesuai',
                    'pesan' => sprintf('Rata-rata %.1fx makan per hari — sesuai rekomendasi untuk usia %d bulan. Pertahankan jadwal makan yang teratur!', $frekPerHari, $umurBulan),
                    'saran' => ['✅ Pertahankan jadwal makan yang sudah konsisten'],
                ];
            }

        } else {
            $feedbacks[] = [
                'tipe'  => 'neutral', 'icon' => '📋',
                'judul' => 'Belum Ada Data Recall Gizi (7 Hari Terakhir)',
                'pesan' => 'Catat asupan makanan anak setiap hari di menu Recall Gizi. Data ini digunakan untuk menganalisis kecukupan kalori, protein, dan pola makan anak.',
                'saran' => ['📥 Input recall gizi di menu Recall Gizi', '🗓️ Catat setiap waktu makan: pagi, siang, snack, malam'],
            ];
        }

        // ── 3. REKOMENDASI MPASI BERDASARKAN USIA ───────────────
        if ($umurBulan >= 6) {
            $feedbacks[] = $this->rekomendasiMpasi($umurBulan);
        }

        return $feedbacks;
    }

    private function rekomendasiMpasi(int $umurBulan): array
    {
        if ($umurBulan < 7) {
            return [
                'tipe' => 'info', 'icon' => '🥣',
                'judul' => 'MPASI Awal (6 Bulan) — Mulai Perlahan',
                'pesan' => 'Anak baru memulai MPASI. Perkenalkan satu jenis makanan puree halus selama 3-5 hari untuk mendeteksi alergi.',
                'saran' => ['🥣 Tekstur: puree sangat halus (seperti yogurt)', '🍵 Porsi awal: 1-2 sendok makan, 1-2x/hari', '🧪 Satu jenis makanan per 3-5 hari', '🚫 Jangan tambahkan garam, gula, atau madu'],
            ];
        } elseif ($umurBulan < 10) {
            return [
                'tipe' => 'info', 'icon' => '🥣',
                'judul' => 'MPASI 7-9 Bulan — Tingkatkan Variasi',
                'pesan' => 'Saatnya memperkaya variasi makanan. Perkenalkan protein hewani dan nabati, serta mulai tekstur yang lebih kental.',
                'saran' => ['🍲 Tekstur: bubur kental atau makanan lembut', '🍽️ Frekuensi: 2-3 kali makan utama + 1-2 snack', '🥩 Variasikan protein: daging, ikan, tahu, telur', '🥦 Tambahkan sayuran warna-warni untuk vitamin'],
            ];
        } elseif ($umurBulan < 13) {
            return [
                'tipe' => 'info', 'icon' => '🥣',
                'judul' => 'MPASI 10-12 Bulan — Menuju Makanan Keluarga',
                'pesan' => 'Anak sudah bisa mencoba makanan yang lebih padat dan finger food. Latih kemampuan mengunyah dan makan mandiri.',
                'saran' => ['✋ Tekstur: cincang kasar atau finger food lembut', '🍽️ Frekuensi: 3-4x makan utama + 2 snack', '🥕 Coba potongan kecil wortel kukus, pisang, roti', '🚫 Hindari madu, susu sapi segar, kacang utuh'],
            ];
        } else {
            return [
                'tipe' => 'info', 'icon' => '🥣',
                'judul' => 'Usia 12-24 Bulan — Makanan Keluarga',
                'pesan' => 'Anak sudah bisa makan makanan keluarga dengan modifikasi (kurangi garam & gula). Libatkan dalam suasana makan bersama.',
                'saran' => ['👨‍👩‍👧 Makan bersama keluarga untuk meningkatkan nafsu makan', '🍚 Nasi, lauk, sayur, buah setiap hari', '🧂 Batasi garam <1g/hari dan gula <5g/hari', '🚫 Hindari makanan ultra-proses dan fast food'],
            ];
        }
    }

    // ── Target nutrisi harian berdasarkan usia (WHO/Kemenkes) ───
    private function targetKalori(int $umurBulan): int
    {
        if ($umurBulan <= 8)  return 615;
        if ($umurBulan <= 12) return 686;
        if ($umurBulan <= 24) return 894;
        return 1046;
    }

    private function targetProtein(int $umurBulan): int
    {
        return $umurBulan <= 12 ? 11 : 15;
    }

    private function targetKarbo(int $umurBulan): int
    {
        if ($umurBulan <= 12) return 95;
        return 130;
    }

    private function targetLemak(int $umurBulan): int
    {
        if ($umurBulan <= 12) return 31;
        return 35;
    }
}
