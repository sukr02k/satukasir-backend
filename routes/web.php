<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminController::class, 'login']);
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/owners/create', [AdminController::class, 'createOwner'])->name('owners.create');
        Route::post('/owners', [AdminController::class, 'storeOwner'])->name('owners.store');
        Route::get('/owners/{id}/edit', [AdminController::class, 'editOwner'])->name('owners.edit');
        Route::put('/owners/{id}', [AdminController::class, 'updateOwner'])->name('owners.update');
        Route::delete('/owners/{id}', [AdminController::class, 'deleteOwner'])->name('owners.delete');
    });
});
