<?php

use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->prefix('super-admin')->name('super-admin.')->group(function () {



    // Dashboard
    Route::get('/', [SuperAdminController::class, 'dashboard'])->name('dashboard');

    // Changement de campus
    Route::post('/switch-campus', [SuperAdminController::class, 'switchCampus'])->name('switch-campus');

    // Gestion des utilisateurs
    Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
    Route::post('/users/create', [SuperAdminController::class, 'createUser'])->name('users.create');
    Route::post('/users/{id}/update', [SuperAdminController::class, 'updateUser'])->name('users.update');
    Route::get('/users/{id}/delete', [SuperAdminController::class, 'deleteUser'])->name('users.delete');

    // Gestion des campus
    Route::get('/campuses', [SuperAdminController::class, 'campuses'])->name('campuses');
    Route::post('/campuses/create', [SuperAdminController::class, 'createCampus'])->name('campuses.create');
    Route::post('/campuses/{id}/update', [SuperAdminController::class, 'updateCampus'])->name('campuses.update');
    Route::get('/campuses/{id}/delete', [SuperAdminController::class, 'deleteCampus'])->name('campuses.delete');

    // Comparatif
    Route::get('/comparatif', [SuperAdminController::class, 'comparatif'])->name('comparatif');
});
