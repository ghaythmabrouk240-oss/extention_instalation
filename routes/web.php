<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallationController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\DocumentInstallationController;
use App\Http\Controllers\SousEquipementController;
use App\Http\Controllers\ProfilCatLabController;
use App\Http\Controllers\HistoriqueStatutInstallationController;

Route::get('/', function () {
    return redirect()->route('installations.index');
});

Route::resource('installations', InstallationController::class);
Route::resource('equipements', EquipementController::class);
Route::resource('documents', DocumentInstallationController::class);
Route::resource('sous-equipements', SousEquipementController::class);
Route::resource('profil-cat-labs', ProfilCatLabController::class);
Route::get('historiques', [HistoriqueStatutInstallationController::class, 'index'])->name('historiques.index');
