<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IbuController;
use App\Http\Controllers\AnakController;
use App\Http\Controllers\PertumbuhanController;
use App\Http\Controllers\EdukasiMpasiController;
use App\Http\Controllers\RecallGiziController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\UserMonitoringController;

// ── Auth (tamu) ────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Onboarding (user baru isi profil) ────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/mulai',  [OnboardingController::class, 'show'])->name('onboarding');
    Route::post('/mulai', [OnboardingController::class, 'store'])->name('onboarding.store');

    // Dashboard user (ibu)
    Route::get('/beranda', [DashboardController::class, 'userDashboard'])->name('user.dashboard');

    // Recall Gizi (user dapat input untuk anak sendiri)
    Route::get('recall/food-search', [RecallGiziController::class, 'searchFood'])->name('recall.food-search');
    Route::get('recall/export-pdf',  [RecallGiziController::class, 'exportPdf'])->name('recall.export-pdf');
    Route::resource('recall', RecallGiziController::class)->except(['edit','update','show']);

    // Data Anak (user dapat tambah anak sendiri)
    Route::resource('anak', AnakController::class);

    // Pertumbuhan (user dapat input untuk anak sendiri)
    Route::get('pertumbuhan/export-pdf', [PertumbuhanController::class, 'exportPdf'])->name('pertumbuhan.export-pdf');
    Route::resource('pertumbuhan', PertumbuhanController::class)->except(['edit','update']);

    // Reminder (user dapat lihat reminder sendiri)
    Route::get('reminder', [ReminderController::class, 'index'])->name('reminder.index');
    Route::patch('reminder/{reminder}/selesai', [ReminderController::class, 'selesai'])->name('reminder.selesai');

    // Edukasi MPASI (semua bisa baca)
    Route::get('edukasi', [EdukasiMpasiController::class, 'index'])->name('edukasi.index');
    
    // Rute admin untuk edukasi MPASI (harus di atas edukasi/{edukasi} agar create tidak dianggap sebagai ID)
    Route::middleware('admin')->group(function () {
        Route::get('edukasi/create', [EdukasiMpasiController::class, 'create'])->name('edukasi.create');
        Route::post('edukasi', [EdukasiMpasiController::class, 'store'])->name('edukasi.store');
        Route::get('edukasi/{edukasi}/edit', [EdukasiMpasiController::class, 'edit'])->name('edukasi.edit');
        Route::put('edukasi/{edukasi}', [EdukasiMpasiController::class, 'update'])->name('edukasi.update');
        Route::delete('edukasi/{edukasi}', [EdukasiMpasiController::class, 'destroy'])->name('edukasi.destroy');
    });

    Route::get('edukasi/{edukasi}', [EdukasiMpasiController::class, 'show'])->name('edukasi.show');

    // Feedback (User & Admin)
    Route::get('feedback',       [FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('feedback/{anak}',[FeedbackController::class, 'show'])->name('feedback.show');
});

// ── Admin only ─────────────────────────────────────────────────
Route::middleware(['auth','admin'])->group(function () {
    // Dashboard admin
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Monitoring Aktivitas User
    Route::get('monitoring', [UserMonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('monitoring/api/online', [UserMonitoringController::class, 'apiOnlineUsers'])->name('monitoring.api.online');
    Route::get('monitoring/{user}', [UserMonitoringController::class, 'show'])->name('monitoring.show');

    // Data Ibu (admin kelola semua)
    Route::resource('ibu', IbuController::class);

    // Edukasi MPASI routes are now handled above in the auth group with admin middleware


    // Reminder (admin create/delete)
    Route::get('reminder/create', [ReminderController::class, 'create'])->name('reminder.create');
    Route::post('reminder', [ReminderController::class, 'store'])->name('reminder.store');
    Route::delete('reminder/{reminder}', [ReminderController::class, 'destroy'])->name('reminder.destroy');

    // Feedback (Admin only actions)
    Route::post('feedback/{anak}/manual', [FeedbackController::class, 'storeManualFeedback'])->name('feedback.manual');
});