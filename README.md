# Grow-MOM Monitoring System

Grow-MOM Monitoring System adalah platform web inovatif yang dirancang untuk membantu ibu memantau tumbuh kembang anak mereka secara real-time, mendeteksi risiko status gizi buruk (Stunting, Wasting, Underweight) secara dini, memberikan rekomendasi gizi (MPASI), serta menjadwalkan pengingat (reminders) imunisasi dan pemeriksaan kesehatan.

## ✨ Fitur Utama

- **Pemantauan Pertumbuhan Anak**: Pencatatan tinggi badan, berat badan, serta perhitungan otomatis status gizi berdasarkan standar WHO (Normal, Stunting, Wasting, Underweight).
- **Dashboard Ibu & Anak**: Tampilan visual yang intuitif dan bersahabat bagi Ibu untuk memantau ringkasan gizi anak secara real-time.
- **Rekomendasi & Feedback Ahli**: Kombinasi feedback otomatis sistem dan feedback manual dari tenaga kesehatan/admin untuk gizi anak.
- **Pengingat Imunisasi & Kesehatan**: Pengingat otomatis untuk jadwal pemeriksaan, imunisasi, dan jadwal MPASI.
- **Edukasi Gizi & MPASI**: Panduan artikel edukatif untuk asupan nutrisi optimal balita.

## 🛠️ Tech Stack

- **Backend/Frontend Framework**: Laravel (PHP)
- **Database**: MySQL / MariaDB
- **Styling**: Tailwind CSS / Vanilla CSS dengan desain modern dan responsif
- **Exporting**: PDF Export untuk laporan riwayat tumbuh kembang anak

## 🚀 Memulai Project

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL / Laragon / XAMPP

### Instalasi

1. **Clone repositori**:
   ```bash
   git clone https://github.com/LLeannnn/Grow-MOM-Monitoring.git
   cd Grow-MOM-Monitoring-System
   ```

2. **Install dependensi**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**:
   Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database Anda.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Jalankan Migrasi & Seeder**:
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi**:
   ```bash
   php artisan serve
   npm run dev
   ```

