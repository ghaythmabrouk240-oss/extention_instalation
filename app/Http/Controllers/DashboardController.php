<?php

namespace App\Http\Controllers;

use App\Models\Installation;
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

        return view('dashboard', compact(
            'strategicKpis',
            'operationalKpis',
            'canViewStrategicKpis',
            'statusCounts',
            'recentInstallations',
            'currentUser'
        ));
    }
}
