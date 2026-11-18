<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicAppointmentController;
use App\Http\Controllers\PublicPrescriptionController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
});

Route::get('/verify', [AuthController::class, 'showVerify'])->name('verify');
Route::post('/verify', [AuthController::class, 'verify'])->middleware('throttle:10,1');
Route::post('/verify/resend', [AuthController::class, 'resend'])->middleware('throttle:10,1')->name('verify.resend');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/appointment/{slug}', [PublicAppointmentController::class, 'show'])->name('appointment.public');
Route::post('/appointment/{slug}', [PublicAppointmentController::class, 'store'])->middleware('throttle:30,1')->name('appointment.store');
Route::get('/book/{slug}', [PublicAppointmentController::class, 'show'])->name('appointment.book');
Route::get('/rx/{token}', [PublicPrescriptionController::class, 'show'])->name('rx.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::patch('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');

    Route::get('/appointment-config', [AvailabilityController::class, 'index'])->name('availability.index');
    Route::post('/availability/rules', [AvailabilityController::class, 'storeRule'])->name('availability.rules.store');
    Route::delete('/availability/rules/{rule}', [AvailabilityController::class, 'destroyRule'])->name('availability.rules.destroy');
    Route::post('/availability/exceptions', [AvailabilityController::class, 'storeException'])->name('availability.exceptions.store');
    Route::delete('/availability/exceptions/{exception}', [AvailabilityController::class, 'destroyException'])->name('availability.exceptions.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
    Route::post('/profile/logo', [ProfileController::class, 'uploadLogo'])->name('profile.logo');
    Route::delete('/profile/logo', [ProfileController::class, 'destroyLogo'])->name('profile.logo.destroy');

    Route::get('/prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::get('/prescriptions/new', [PrescriptionController::class, 'create'])->name('prescriptions.create');
    Route::post('/prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');
    Route::get('/prescriptions/{prescription}', [PrescriptionController::class, 'edit'])->name('prescriptions.edit')->whereNumber('prescription');
    Route::put('/prescriptions/{prescription}', [PrescriptionController::class, 'update'])->name('prescriptions.update')->whereNumber('prescription');
    Route::post('/prescriptions/{prescription}/share-email', [PrescriptionController::class, 'shareEmail'])->name('prescriptions.share-email')->whereNumber('prescription');
});
