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
use App\Http\Controllers\InstallationBudgetController;

Route::get('/', function () {
    return redirect()->route('installations.index');
});

Route::get('dashboard', DashboardController::class)->name('dashboard');
Route::get('/dashboard/installation-graph', [InstallationGraphController::class, 'show'])->name('dashboard.installation-graph');
Route::get('/installations/graph', [InstallationGraphController::class, 'index'])->name('installations.graph');

Route::get('installations-calendar', [InstallationController::class, 'calendar'])->name('installations.calendar');
Route::get('installations/{installation}/export', [InstallationController::class, 'export'])->name('installations.export');
Route::resource('installations', InstallationController::class);
Route::get('installations/{installation}/budget', [InstallationBudgetController::class, 'show'])->name('installations.budget');
Route::get('installations/{installation}/budget/export', [InstallationBudgetController::class, 'export'])->name('installations.budget.export');
Route::post('installations/{installation}/budget', [InstallationBudgetController::class, 'updateBudget'])->name('installations.budget.update');
Route::post('installations/{installation}/expenses', [InstallationBudgetController::class, 'storeExpense'])->name('installations.expenses.store');
Route::post('installations/{installation}/time-penalty', [InstallationBudgetController::class, 'updateTimePenalty'])->name('installations.time-penalty.update');
Route::delete('installations/{installation}/expenses/{expense}', [InstallationBudgetController::class, 'destroyExpense'])->name('installations.expenses.destroy');
Route::get('equipements/{equipement}/scan', [EquipementController::class, 'scan'])->name('equipements.scan');
Route::resource('equipements', EquipementController::class);
Route::resource('documents', DocumentInstallationController::class);
Route::resource('sous-equipements', SousEquipementController::class);
Route::resource('profil-cat-labs', ProfilCatLabController::class);
Route::get('historiques', [HistoriqueStatutInstallationController::class, 'index'])->name('historiques.index');
