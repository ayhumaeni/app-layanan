<?php

use App\Livewire\Public\Auth\Login;
use App\Livewire\Public\Auth\Register;
use App\Livewire\Public\CertificateVerification;
use App\Livewire\Public\CitizenDashboard;
use App\Livewire\Public\ComplaintForm;
use App\Livewire\Public\DtsenApplication;
use App\Livewire\Public\Home;
use App\Livewire\Public\KisReactivationApplication;
use App\Livewire\Public\ServiceCatalog;
use App\Livewire\Public\ServiceDetail;
use App\Livewire\Public\TicketTracking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public Portal Routes (Livewire v4)
Route::get('/', Home::class)->name('home');
Route::get('/layanan', ServiceCatalog::class)->name('services.index');
Route::get('/layanan/{slug}', ServiceDetail::class)->name('services.show');
Route::get('/layanan-sk-dtsen/ajukan', DtsenApplication::class)->name('services.dtsen.apply');
Route::get('/layanan-reaktivasi-kis/ajukan', KisReactivationApplication::class)->name('services.kis.apply');
Route::get('/pengaduan', ComplaintForm::class)->name('complaints.create');
Route::get('/lacak', TicketTracking::class)->name('tracking');
Route::get('/verifikasi-surat/{code?}', CertificateVerification::class)->name('certificate.verify');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/masuk', Login::class)->name('login');
    Route::get('/daftar', Register::class)->name('register');
});

// Authenticated Citizen Routes
Route::middleware('auth')->group(function () {
    Route::get('/akun', CitizenDashboard::class)->name('citizen.dashboard');
    Route::post('/keluar', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('home');
    })->name('logout');
});
