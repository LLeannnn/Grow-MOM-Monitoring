<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ibu;
use App\Models\Anak;
use App\Models\Pertumbuhan;
use App\Models\EdukasiMpasi;
use App\Models\RecallGizi;
use App\Models\Reminder;
use App\Models\Feedback;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== ADMIN USER =====
        User::updateOrCreate(
            ['email' => 'admin@growmom.id'],
            ['name' => 'Administrator', 'password' => bcrypt('admin123'), 'role' => 'admin']
        );

        // ===== DATA IBU =====
        $ibuData = [
            ['nama_ibu' => 'Siti Rahayu', 'email' => 'siti@growmom.id', 'nik' => '3201010101850001', 'tanggal_lahir' => '1985-03-15', 'no_telepon' => '081234567890', 'pekerjaan' => 'ibu_rumah_tangga', 'pendidikan' => 'sma', 'status_pernikahan' => 'menikah', 'alamat' => 'Jl. Melati No. 12, Depok'],
            ['nama_ibu' => 'Dewi Kartika', 'email' => 'dewi@growmom.id', 'nik' => '3201010202900002', 'tanggal_lahir' => '1990-07-22', 'no_telepon' => '082345678901', 'pekerjaan' => 'swasta', 'pendidikan' => 's1', 'status_pernikahan' => 'menikah', 'alamat' => 'Jl. Anggrek No. 5, Bogor'],
            ['nama_ibu' => 'Nurul Hidayah', 'email' => 'nurul@growmom.id', 'nik' => '3201010303920003', 'tanggal_lahir' => '1992-11-08', 'no_telepon' => '083456789012', 'pekerjaan' => 'pns', 'pendidikan' => 's1', 'status_pernikahan' => 'menikah', 'alamat' => 'Jl. Kenanga No. 20, Bekasi'],
            ['nama_ibu' => 'Rina Susanti', 'email' => 'rina@growmom.id', 'nik' => '3201010404880004', 'tanggal_lahir' => '1988-05-30', 'no_telepon' => '084567890123', 'pekerjaan' => 'wiraswasta', 'pendidikan' => 'd3', 'status_pernikahan' => 'menikah', 'alamat' => 'Jl. Mawar No. 8, Tangerang'],
            ['nama_ibu' => 'Fitri Handayani', 'email' => 'fitri@growmom.id', 'nik' => '3201010505950005', 'tanggal_lahir' => '1995-02-14', 'no_telepon' => '085678901234', 'pekerjaan' => 'ibu_rumah_tangga', 'pendidikan' => 'sma', 'status_pernikahan' => 'menikah', 'alamat' => 'Jl. Dahlia No. 3, Jakarta Selatan'],
        ];

        $ibuList = [];
        foreach ($ibuData as $data) {
            // Create user first
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['nama_ibu'],
                    'password' => bcrypt('password123'),
                    'role' => 'user'
                ]
            );

            // Remove email from array so we can insert to ibu table safely
            unset($data['email']);
            
            // Add user_id to ibu data
            $data['user_id'] = $user->id;

            $ibuList[] = Ibu::updateOrCreate(
                ['nik' => $data['nik']],
                $data
            );
        }

        // ===== DATA ANAK =====
        $anakData = [
            ['ibu_id' => $ibuList[0]->id, 'nama_anak' => 'Budi Setiawan',  'tanggal_lahir' => '2024-02-10', 'jenis_kelamin' => 'L', 'berat_lahir' => 3.2, 'panjang_lahir' => 49, 'golongan_darah' => 'A'],
            ['ibu_id' => $ibuList[0]->id, 'nama_anak' => 'Putri Rahayu',   'tanggal_lahir' => '2022-06-15', 'jenis_kelamin' => 'P', 'berat_lahir' => 3.0, 'panjang_lahir' => 48, 'golongan_darah' => 'O'],
            ['ibu_id' => $ibuList[1]->id, 'nama_anak' => 'Rizki Kartika',  'tanggal_lahir' => '2024-08-20', 'jenis_kelamin' => 'L', 'berat_lahir' => 3.5, 'panjang_lahir' => 50, 'golongan_darah' => 'B'],
            ['ibu_id' => $ibuList[2]->id, 'nama_anak' => 'Zahra Hidayah',  'tanggal_lahir' => '2024-11-05', 'jenis_kelamin' => 'P', 'berat_lahir' => 2.9, 'panjang_lahir' => 47, 'golongan_darah' => 'AB'],
            ['ibu_id' => $ibuList[3]->id, 'nama_anak' => 'Arif Susanto',   'tanggal_lahir' => '2023-04-12', 'jenis_kelamin' => 'L', 'berat_lahir' => 3.3, 'panjang_lahir' => 50, 'golongan_darah' => 'O'],
            ['ibu_id' => $ibuList[4]->id, 'nama_anak' => 'Cinta Handayani','tanggal_lahir' => '2024-05-18', 'jenis_kelamin' => 'P', 'berat_lahir' => 3.1, 'panjang_lahir' => 49, 'golongan_darah' => 'A'],
        ];

        $anakList = [];
        foreach ($anakData as $data) {
            $anakList[] = Anak::create($data);
        }

        // ===== DATA PERTUMBUHAN =====
        foreach ($anakList as $anak) {
            $umurBulan = $anak->umur_bulan;
            $refBerat = $anak->jenis_kelamin === 'L'
                ? [0=>3.3,1=>4.5,2=>5.6,3=>6.4,4=>7.0,5=>7.5,6=>7.9,7=>8.3,8=>8.6,9=>8.9,10=>9.2,11=>9.4,12=>9.6]
                : [0=>3.2,1=>4.2,2=>5.1,3=>5.8,4=>6.4,5=>6.9,6=>7.3,7=>7.6,8=>7.9,9=>8.2,10=>8.5,11=>8.7,12=>8.9];

            // 3 pengukuran per anak
            for ($i = 0; $i < min(3, $umurBulan + 1); $i++) {
                $bulanLalu = $umurBulan - ($i * 2);
                $bulanRef  = max(0, min($bulanLalu, 12));
                $keys      = array_keys($refBerat);
                $closest   = $keys[0];
                foreach ($keys as $k) {
                    if (abs($k - $bulanRef) < abs($closest - $bulanRef)) $closest = $k;
                }
                $baseBerat = $refBerat[$closest];
                $berat     = round($baseBerat + rand(-5, 10) / 10, 2);
                $tinggi    = round(45 + ($bulanRef * 1.5) + rand(-5, 15) / 10, 1);

                $statusGizi = Pertumbuhan::hitungStatusGizi($berat, max(0, $bulanRef), $anak->jenis_kelamin);

                Pertumbuhan::create([
                    'anak_id'            => $anak->id,
                    'tanggal_pengukuran' => now()->subMonths($i * 2)->format('Y-m-d'),
                    'berat_badan'        => $berat,
                    'tinggi_badan'       => $tinggi,
                    'lingkar_kepala'     => round(34 + ($bulanRef * 0.5) + rand(-5, 5) / 10, 1),
                    'lingkar_lengan'     => round(11 + rand(-5, 15) / 10, 1),
                    'status_gizi'        => $statusGizi,
                    'catatan'            => $i === 0 ? 'Pengukuran rutin posyandu' : null,
                ]);
            }
        }

        $edukasi = [
            [
                'judul' => 'Memulai MPASI di Usia 6 Bulan: Panduan Lengkap', 
                'kategori_usia' => '6_bulan', 
                'tekstur_makanan' => 'Puree saring halus (encer hingga semi kental)',
                'bahan_makanan' => "- 2 sdm Karbohidrat (Beras putih/Kentang)\n- 1 sdm Protein Hewani (Ayam/Hati ayam)\n- 1 sdt Lemak Tambahan (Minyak kelapa/Santan)\n- Sedikit sayuran perkenalan (Wortel/Brokoli)",
                'konten' => "Pada usia 6 bulan, bayi sudah siap menerima makanan pendamping ASI (MPASI). Berikut panduan lengkapnya:\n\n1. TANDA BAYI SIAP MPASI\nBayi dapat duduk dengan bantuan, menunjukkan ketertarikan pada makanan, refleks menjulurkan lidah sudah berkurang, dan berat badan sudah dua kali lipat dari berat lahir.\n\n2. FREKUENSI DAN PORSI\nMulai dengan 2-3 sendok makan, 2 kali sehari. ASI tetap menjadi makanan utama.\n\n3. TIPS PENTING\n- Perkenalkan satu jenis makanan selama 3-5 hari\n- Amati reaksi alergi (kemerahan, muntah)\n- Jangan tambahkan garam atau gula\n- Tawarkan air putih sedikit setelah makan", 
                'tags' => 'mpasi awal, 6 bulan, bubur, puree'
            ],
            [
                'judul' => 'Menu MPASI Bergizi untuk Bayi 7-9 Bulan', 
                'kategori_usia' => '7_9_bulan', 
                'tekstur_makanan' => 'Lumat kental (mashed) atau cincang halus',
                'bahan_makanan' => "- 3 sdm Nasi lembek/tim\n- 1.5 sdm Daging sapi cincang atau ikan\n- 1 sdm Sayuran (Bayam/Labu siam)\n- 1 sdt Mentega/Minyak kelapa sawit",
                'konten' => "Pada usia 7-9 bulan, variasi dan tekstur makanan bayi semakin berkembang untuk melatih kemampuan mengunyah.\n\n1. KEMAMPUAN MOTORIK\nBayi mulai belajar menjepit makanan dengan jari (pincer grasp). Bisa dicoba memberikan finger food yang direbus sangat lunak.\n\n2. FREKUENSI\nBerikan 2-3 kali makan utama + 1-2 kali camilan bergizi per hari.\n\n3. NUTRISI PENTING\n- Zat besi sangat krusial di usia ini (hati ayam, daging merah)\n- Vitamin A (wortel, labu kuning)\n- Zinc dan Lemak untuk perkembangan otak", 
                'tags' => '7-9 bulan, protein, zat besi'
            ],
            [
                'judul' => 'MPASI 10-12 Bulan: Menuju Makanan Keluarga', 
                'kategori_usia' => '10_12_bulan', 
                'tekstur_makanan' => 'Cincang kasar (minced) atau potongan makanan seukuran jari (finger food)',
                'bahan_makanan' => "- Nasi lembek (tidak perlu disaring)\n- Potongan kecil ayam rebus / telur dadar potong kecil\n- Potongan sayur rebus (Wortel, Buncis)\n- Tahu kukus potong dadu",
                'konten' => "Di usia 10-12 bulan, bayi sudah mulai mahir mengunyah meskipun giginya belum tumbuh lengkap. Mereka mulai diperkenalkan dengan makanan yang mendekati makanan keluarga.\n\n1. KEMANDIRIAN MAKAN\nBerikan alat makan (sendok kecil berbahan aman) agar bayi belajar makan sendiri, meskipun masih berantakan.\n\n2. FREKUENSI\n3-4 kali makan utama + 1-2 kali camilan.\n\n3. HINDARI PEMBERIAN:\n- Madu (risiko botulisme)\n- Susu sapi segar/UHT sebagai minuman utama (tunggu hingga 1 tahun)\n- Makanan keras, bulat kecil, dan lengket (anggur utuh, permen, kacang) yang berisiko tersedak", 
                'tags' => '10-12 bulan, finger food, mandiri'
            ],
            [
                'judul' => 'Nutrisi Lengkap untuk Anak 1-2 Tahun', 
                'kategori_usia' => '12_24_bulan', 
                'tekstur_makanan' => 'Makanan keluarga biasa (tekstur normal)',
                'bahan_makanan' => "- Nasi biasa\n- Lauk pauk keluarga (Ikan goreng, Ayam bumbu kuning yang tidak pedas)\n- Sayur sop atau tumisan lunak\n- Buah potong segar sebagai camilan",
                'konten' => "Anak usia 1-2 tahun (batita) sudah bisa menikmati menu makanan keluarga dengan sedikit modifikasi (kurangi garam, gula, dan tanpa cabai).\n\n1. KEBUTUHAN KALORI\nMembutuhkan sekitar 1000-1400 kkal per hari, tergantung keaktifannya.\n\n2. MENGATASI GTM (Gerakan Tutup Mulut)\nAnak mungkin mulai memilih-milih makanan (picky eater). Jangan memaksa. Tawarkan porsi kecil namun sering, dan buat suasana makan menyenangkan.\n\n3. TIPS MAKAN SEHAT\n- Makan bersama keluarga di meja makan\n- Batasi waktu makan maksimal 30 menit\n- Hindari distraksi gadget (TV/HP) saat makan agar anak fokus dan mengenali rasa kenyangnya.", 
                'tags' => '1-2 tahun, nutrisi seimbang, keluarga'
            ],
            [
                'judul' => 'Makanan Kaya Zat Besi untuk Cegah Anemia', 
                'kategori_usia' => 'umum', 
                'tekstur_makanan' => 'Sesuaikan dengan usia anak (saring/cincang/utuh)',
                'bahan_makanan' => "- Hati ayam (9mg zat besi / 100g)\n- Daging sapi merah muda (2.7mg / 100g)\n- Bayam hijau segar\n- Kacang merah atau kacang polong\n- Tomat / Jeruk (Vitamin C pendamping)",
                'konten' => "Kekurangan zat besi adalah masalah gizi paling umum pada bayi dan anak yang dapat menyebabkan anemia dan menghambat perkembangan kognitif otak.\n\n1. SUMBER ZAT BESI HEME (DARI HEWAN)\nLebih mudah diserap oleh tubuh. Contoh: Hati ayam, hati sapi, daging sapi merah, ikan, dan kuning telur.\n\n2. SUMBER ZAT BESI NON-HEME (DARI TUMBUHAN)\nContoh: Bayam, brokoli, tahu, tempe, dan lentil/kacang merah.\n\n3. TIPS MEMAKSIMALKAN PENYERAPAN:\n- Selalu sajikan bahan makanan kaya zat besi bersamaan dengan makanan kaya Vitamin C (seperti jeruk, tomat, jambu biji) karena Vitamin C membantu usus menyerap zat besi berkali-kali lipat.\n- Jauhkan konsumsi teh atau susu kalsium tinggi berdekatan dengan jam makan utama, karena dapat menghambat penyerapan zat besi.", 
                'tags' => 'zat besi, anemia, nutrisi'
            ],
        ];

        foreach ($edukasi as $e) {
            EdukasiMpasi::create(array_merge($e, ['is_published' => true]));
        }

        // ===== RECALL GIZI =====
        $makananList = [
            ['nama_makanan' => 'Bubur beras merah', 'waktu_makan' => 'pagi',  'jumlah' => 1, 'satuan' => 'porsi', 'kalori' => 110, 'protein' => 2.5, 'karbohidrat' => 23, 'lemak' => 0.8],
            ['nama_makanan' => 'Puree wortel',      'waktu_makan' => 'siang', 'jumlah' => 100, 'satuan' => 'gram', 'kalori' => 41, 'protein' => 0.9, 'karbohidrat' => 10, 'lemak' => 0.2],
            ['nama_makanan' => 'Pisang',            'waktu_makan' => 'snack', 'jumlah' => 1, 'satuan' => 'buah', 'kalori' => 89, 'protein' => 1.1, 'karbohidrat' => 23, 'lemak' => 0.3],
            ['nama_makanan' => 'Nasi tim ayam',     'waktu_makan' => 'malam', 'jumlah' => 1, 'satuan' => 'porsi', 'kalori' => 180, 'protein' => 12, 'karbohidrat' => 25, 'lemak' => 4],
            ['nama_makanan' => 'Telur rebus',       'waktu_makan' => 'pagi',  'jumlah' => 1, 'satuan' => 'butir', 'kalori' => 155, 'protein' => 13, 'karbohidrat' => 1.1, 'lemak' => 11],
        ];

        foreach ($anakList as $anak) {
            foreach (array_slice($makananList, 0, 3) as $m) {
                RecallGizi::create(array_merge($m, [
                    'anak_id' => $anak->id,
                    'tanggal' => now()->format('Y-m-d'),
                    'catatan' => null,
                ]));
            }
        }

        // ===== REMINDERS =====
        $tipesReminder = ['imunisasi', 'posyandu', 'mpasi', 'kontrol'];
        foreach ($anakList as $idx => $anak) {
            Reminder::create([
                'ibu_id'           => $anak->ibu_id,
                'anak_id'          => $anak->id,
                'judul'            => 'Posyandu Rutin ' . $anak->nama_anak,
                'pesan'            => "Saatnya ke posyandu untuk penimbangan rutin {$anak->nama_anak}.",
                'tanggal_reminder' => now()->addDays($idx * 3 + 2),
                'tipe'             => $tipesReminder[$idx % count($tipesReminder)],
                'status'           => 'aktif',
                'kirim_sms'        => false,
            ]);
        }

        // ===== FEEDBACK =====
        $feedbackData = [
            ['nama' => 'Siti Rahayu',    'email' => 'siti@email.com',    'rating' => 5, 'kategori' => 'Fitur Aplikasi', 'pesan' => 'Aplikasi ini sangat membantu saya memantau tumbuh kembang anak. Grafik pertumbuhannya mudah dipahami!'],
            ['nama' => 'Dewi Kartika',   'email' => 'dewi@email.com',    'rating' => 5, 'kategori' => 'Konten Edukasi', 'pesan' => 'Konten edukasi MPASI sangat informatif dan mudah diikuti. Terima kasih!'],
            ['nama' => 'Nurul Hidayah',  'email' => 'nurul@email.com',   'rating' => 4, 'kategori' => 'Reminder',       'pesan' => 'Fitur reminder sangat berguna. Kalau bisa ditambahkan notifikasi push notification.'],
            ['nama' => 'Rina Susanti',   'email' => 'rina@email.com',    'rating' => 4, 'kategori' => 'Recall Gizi',    'pesan' => 'Recall gizi membantu saya memastikan anak makan dengan gizi seimbang setiap hari.'],
            ['nama' => 'Fitri Handayani','email' => 'fitri@email.com',   'rating' => 5, 'kategori' => 'Tampilan',       'pesan' => 'Tampilannya cantik dan modern. Mudah digunakan bahkan untuk yang tidak terbiasa teknologi.'],
        ];

        foreach ($feedbackData as $fb) {
            Feedback::create(array_merge($fb, ['status' => 'pending']));
        }

        $this->command->info('✅ Seeder selesai! Data dummy berhasil dimasukkan.');
    }
}
