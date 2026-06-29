<?php

namespace App\Http\Controllers;

use App\Models\Installation;
use App\Models\InstallationBudget;
use App\Services\InstallationStatusService;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $this->authorizeInstallation('viewDashboard', Installation::class);
        $currentUser = $this->effectiveUser();
        $canViewStrategicKpis = $currentUser->canViewStrategicInstallationKpis();

        $activeInstallations = Installation::query()
            ->where('statut', '!=', InstallationStatusService::ARCHIVE);

        $totalActive = (clone $activeInstallations)->count();
        $operationalCount = (clone $activeInstallations)
            ->where('statut', InstallationStatusService::OPERATIONNEL)
            ->count();

        $plannedThisMonth = 0;
        if (Schema::hasColumn('installations', 'planned_start_date')) {
            $plannedThisMonth = (clone $activeInstallations)
                ->whereBetween('planned_start_date', [now()->startOfMonth(), now()->endOfMonth()])
                ->count();
        }

        $strategicKpis = [
            [
                'label' => 'Installations actives',
                'value' => $totalActive,
                'icon' => 'fa-layer-group',
            ],
            [
                'label' => 'Profils IRM',
                'value' => (clone $activeInstallations)->where('type_profil', Installation::TYPE_IRM)->count(),
                'icon' => 'fa-magnet',
            ],
            [
                'label' => 'Profils Cath',
                'value' => (clone $activeInstallations)->where('type_profil', Installation::TYPE_CATHETERISME)->count(),
                'icon' => 'fa-heart-pulse',
            ],
            [
                'label' => 'Taux opérationnel',
                'value' => ($totalActive > 0 ? round(($operationalCount / $totalActive) * 100, 1) : 0).'%',
                'icon' => 'fa-chart-line',
            ],
        ];

        $operationalKpis = [
            [
                'label' => 'En maintenance',
                'value' => (clone $activeInstallations)->where('statut', InstallationStatusService::EN_MAINTENANCE)->count(),
                'icon' => 'fa-screwdriver-wrench',
            ],
            [
                'label' => 'Sans équipement principal',
                'value' => (clone $activeInstallations)->whereNull('equipement_principal_id')->count(),
                'icon' => 'fa-microchip',
            ],
            [
                'label' => 'Docs bloquants absents',
                'value' => (clone $activeInstallations)
                    ->whereDoesntHave('documents', fn ($query) => $query->where('est_bloquant', true))
                    ->count(),
                'icon' => 'fa-file-circle-exclamation',
            ],
            [
                'label' => 'Planifiées ce mois',
                'value' => $plannedThisMonth,
                'icon' => 'fa-calendar-days',
            ],
        ];

        $statusCounts = Installation::query()
            ->selectRaw('statut, count(*) as total')
            ->groupBy('statut')
            ->orderBy('statut')
            ->pluck('total', 'statut');

        $recentInstallations = Installation::query()
            ->latest()
            ->limit(6)
            ->get();

        $budgetByCurrency = collect(['EUR', 'TND'])->mapWithKeys(fn ($currency) => [
            $currency => [
                'budget_prevu' => 0,
                'total_frais' => 0,
                'total_penalites' => 0,
                'total_final' => 0,
            ],
        ]);

        InstallationBudget::query()
            ->selectRaw('devise, COALESCE(SUM(budget_prevu), 0) as budget_prevu')
            ->selectRaw('COALESCE(SUM(total_frais), 0) as total_frais')
            ->selectRaw('COALESCE(SUM(total_penalites), 0) as total_penalites')
            ->selectRaw('COALESCE(SUM(total_final), 0) as total_final')
            ->whereIn('devise', ['EUR', 'TND'])
            ->groupBy('devise')
            ->get()
            ->each(function ($budget) use ($budgetByCurrency) {
                $budgetByCurrency[$budget->devise] = [
                    'budget_prevu' => (float) $budget->budget_prevu,
                    'total_frais' => (float) $budget->total_frais,
                    'total_penalites' => (float) $budget->total_penalites,
                    'total_final' => (float) $budget->total_final,
                ];
            });

        return view('dashboard', compact(
            'strategicKpis',
            'operationalKpis',
            'budgetByCurrency',
            'canViewStrategicKpis',
            'statusCounts',
            'recentInstallations',
            'currentUser'
        ));
    }
}
