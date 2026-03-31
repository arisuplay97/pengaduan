<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\NotulenController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\WorkerController;

use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\UploadController;

// ===== PUBLIC PORTAL (Tanpa Login) =====
Route::get('/', [PublicController::class, 'landing'])->name('public.landing');
Route::get('/lapor', [PublicController::class, 'reportForm'])->name('public.report');
Route::post('/lapor', [PublicController::class, 'storeReport'])->name('public.report.store')->middleware('throttle:5,1');
Route::get('/lacak', [PublicController::class, 'trackTicket'])->name('public.track');

// ===== TELEGRAM WEBHOOK (No CSRF, No Auth) =====
Route::post('/api/telegram/webhook', [TelegramWebhookController::class, 'handle'])->name('telegram.webhook');

// ===== SIGNED URL UPLOAD (Require Valid Signature) =====
Route::get('/upload/{ticketCode}', [UploadController::class, 'showForm'])->name('upload.form')->middleware('signed');
Route::post('/upload/{ticketCode}', [UploadController::class, 'store'])->name('upload.store')->middleware('signed');

// ===== INTERNAL LOGIN (URL Rahasia) =====
Route::middleware('guest')->group(function () {
    Route::get('/portal-internal', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/portal-internal', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
});

// Magic Link Login (no auth required)
Route::get('/akses-petugas/{token}', [WorkerController::class, 'magicLogin'])->name('worker.magic-login');
Route::get('/claim-job/{job}/{worker}', [WorkerController::class, 'claimViaWa'])->name('worker.claim_wa')->middleware('signed');

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes (Require Authentication)
Route::middleware('auth')->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/notify', [DashboardController::class, 'sendNotificationNow'])->name('dashboard.notify');
    Route::get('/calendar', [DashboardController::class, 'calendar'])->name('calendar');
    Route::get('/monitor/calendar', [AgendaController::class, 'monitor'])->name('monitor.calendar');
    // Settings & Profile
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

    // Resource Routes for CRUD
    Route::resource('agenda', AgendaController::class);
    Route::resource('dokumen', DocumentController::class)->parameter('dokumen', 'document');
    Route::resource('reminder', ReminderController::class);
    Route::resource('notulen', NotulenController::class);

    // Custom document download route
    Route::get('/dokumen/{dokumen}/download', [DocumentController::class, 'download'])->name('dokumen.download');
    
    // API for Notifications
    Route::get('/api/upcoming-agenda', [AgendaController::class, 'upcoming'])->name('api.upcoming');

    // ===== COMMAND CENTER (Admin/Direksi - Monitoring Only) =====
    Route::get('/layanan-gangguan', [JobController::class, 'commandCenter'])->name('command-center');
    Route::get('/api/map-data', [JobController::class, 'getMapData'])->name('api.map-data');

    // ===== STATISTICS DASHBOARD =====
    Route::get('/statistik-gangguan', [StatisticsController::class, 'index'])->name('statistics');
    Route::get('/api/statistics-data', [StatisticsController::class, 'getData'])->name('api.statistics');
    Route::get('/api/statistics-export/csv', [StatisticsController::class, 'exportCsv'])->name('statistics.export.csv');
    Route::get('/api/statistics-export/excel', [StatisticsController::class, 'exportExcel'])->name('statistics.export.excel');

    // ===== WORKER (Petugas - Self-Dispatch) =====
    Route::get('/worker', [JobController::class, 'workerDashboard'])->name('worker.dashboard');
    Route::redirect('/worker/jobs', '/worker');
    Route::post('/worker/jobs', [JobController::class, 'store'])->name('worker.jobs.store');
    Route::post('/worker/jobs/{id}/start', [JobController::class, 'startJob'])->name('worker.jobs.start');
    Route::post('/worker/jobs/{id}/finish', [JobController::class, 'update'])->name('worker.jobs.finish');
    Route::put('/worker/jobs/{id}', [JobController::class, 'updateReport'])->name('worker.jobs.update');
    Route::delete('/worker/jobs/{id}', [JobController::class, 'destroy'])->name('worker.jobs.destroy');
    Route::post('/worker/profile', [JobController::class, 'updateProfile'])->name('worker.profile.update');

    // ===== ASSIGNMENT MANAGEMENT (Admin) =====
    Route::get('/manajemen-tugas', [JobController::class, 'assignmentIndex'])->name('assignment.index');
    Route::post('/manajemen-tugas/{id}/status', [JobController::class, 'updateAssignmentStatus'])->name('assignment.update-status');
    Route::post('/manajemen-tugas/{id}/assign', [JobController::class, 'assignWorker'])->name('assignment.assign-worker');
    Route::delete('/manajemen-tugas/{id}', [JobController::class, 'destroyAssignment'])->name('assignment.destroy');
    Route::get('/manajemen-tugas/{id}/export/pdf', [JobController::class, 'exportPdf'])->name('assignment.export.pdf');
    Route::get('/manajemen-tugas/{id}/export/excel', [JobController::class, 'exportExcel'])->name('assignment.export.excel');

    // ===== DISPATCH (Admin Lapang) =====
    Route::get('/worker/dispatch', [JobController::class, 'dispatchIndex'])->name('worker.dispatch');
    Route::post('/worker/dispatch', [JobController::class, 'dispatchStore'])->name('worker.dispatch.store');
    Route::post('/worker/dispatch/{id}/status', [JobController::class, 'dispatchUpdateStatus'])->name('worker.dispatch.update-status');

    // ===== SUPERADMIN — User Management =====
    Route::get('/kelola-pengguna', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/kelola-pengguna', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/kelola-pengguna/{id}', [UserManagementController::class, 'update'])->name('users.update');
    Route::post('/kelola-pengguna/{id}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
    Route::delete('/kelola-pengguna/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
});