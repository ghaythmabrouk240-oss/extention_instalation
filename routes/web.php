<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallationController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\DocumentInstallationController;
use App\Http\Controllers\SousEquipementController;
use App\Http\Controllers\ProfilCatLabController;
use App\Http\Controllers\HistoriqueStatutInstallationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallationGraphController;

Route::get('/', function () {
    return redirect()->route('installations.index');
});

Route::get('dashboard', DashboardController::class)->name('dashboard');
Route::get('/dashboard/installation-graph', [InstallationGraphController::class, 'show'])->name('dashboard.installation-graph');
Route::get('/installations/graph', [InstallationGraphController::class, 'index'])->name('installations.graph');

Route::get('installations-calendar', [InstallationController::class, 'calendar'])->name('installations.calendar');
Route::get('installations/{installation}/export', [InstallationController::class, 'export'])->name('installations.export');
Route::resource('installations', InstallationController::class);
Route::resource('equipements', EquipementController::class);
Route::resource('documents', DocumentInstallationController::class);
Route::resource('sous-equipements', SousEquipementController::class);
Route::resource('profil-cat-labs', ProfilCatLabController::class);
Route::get('historiques', [HistoriqueStatutInstallationController::class, 'index'])->name('historiques.index');
