<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminCourtController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\ReservationController;
use App\Models\Court;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'courts' => Court::orderBy('number')->get(),
    ]);
})->name('home');

// Public read access - anyone can browse courts, reservations and check availability.
Route::get('courts', [CourtController::class, 'index'])->name('courts.index');

Route::prefix('reservations')->name('reservations.')->group(function () {
    Route::get('/', [ReservationController::class, 'index'])->name('index');
    Route::get('available-slots', [ReservationController::class, 'availableSlots'])->name('available-slots');
    Route::post('/', [ReservationController::class, 'store'])->name('store');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::post('courts', [AdminCourtController::class, 'store'])->name('courts.store');
        Route::put('courts/{id}', [AdminCourtController::class, 'update'])->name('courts.update');
        Route::delete('courts/{id}', [AdminCourtController::class, 'destroy'])->name('courts.destroy');

        Route::put('reservations/{id}', [ReservationController::class, 'update'])->name('reservations.update');
        Route::delete('reservations/{id}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
    });
});
