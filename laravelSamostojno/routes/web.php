<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ReservationController;

Route::get('/', [CourtController::class, 'loadDataHome'])->name('loadDataHome');
Route::post('/home/reservation/add', [ReservationController::class, 'addReservationHome'])->name('addReservationHome');

Route::get('/reservation/available/{id}', [ReservationController::class, 'vseMoznosti'])->name('vseMoznosti');

// AUTH
Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login']);
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

// ADMIN
Route::middleware('adminAuth')->group(function () {
    Route::get('/adminPanel', [AdminController::class, 'loadData'])->name('adminPanel');

    Route::post('/adminPanel/court/add', [AdminController::class, 'addCourt'])->name('addCourt');
    Route::delete('/adminPanel/court/delete/{id}', [AdminController::class, 'deleteCourt'])->name('deleteCourt');
    Route::put('/adminPanel/court/edit/{id}', [AdminController::class, 'editCourt'])->name('editCourt');

    Route::delete('/adminPanel/reservation/delete/{id}', [AdminController::class, 'deleteReservation'])->name('deleteReservation');
    Route::post('/adminPanel/reservation/add', [AdminController::class, 'addReservation'])->name('addReservation');
    Route::put('/adminPanel/reservation/edit/{id}', [AdminController::class, 'editReservation'])->name('editReservation');
});