<?php

namespace App\Services;

use App\Models\Installation;
use App\Models\InstallationBudget;
use App\Models\InstallationExpense;
use App\Models\InstallationTimePenalty;
use Carbon\Carbon;

class InstallationBudgetService
{
    /**
     * Calculate total expenses for an installation
     *
     * @param int $installationId
     * @return float
     */
    public function calculateTotalExpenses(int $installationId): float
    {
        return InstallationExpense::where('installation_id', $installationId)
            ->sum('montant_total');
    }

    /**
     * Calculate time penalties for an installation
     *
     * @param int $installationId
     * @return float
     */
    public function calculateTimePenalties(int $installationId): float
    {
        $penalty = InstallationTimePenalty::where('installation_id', $installationId)->first();
        
        if (!$penalty || !$penalty->applicable) {
            return 0;
        }

        return $penalty->montant_penalite;
    }

    /**
     * Calculate total final amount (expenses + penalties)
     *
     * @param int $installationId
     * @return float
     */
    public function calculateTotalFinal(int $installationId): float
    {
        $totalExpenses = $this->calculateTotalExpenses($installationId);
        $totalPenalties = $this->calculateTimePenalties($installationId);
        
        return $totalExpenses + $totalPenalties;
    }

    /**
     * Calculate budget variance (planned vs actual)
     *
     * @param int $installationId
     * @return array
     */
    public function calculateBudgetVariance(int $installationId): array
    {
        $budget = InstallationBudget::where('installation_id', $installationId)->first();
        
        if (!$budget || !$budget->budget_prevu) {
            return [
                'budget_prevu' => 0,
                'total_final' => $this->calculateTotalFinal($installationId),
                'variance' => 0,
                'variance_percentage' => 0,
            ];
        }

        $totalFinal = $this->calculateTotalFinal($installationId);
        $variance = $totalFinal - $budget->budget_prevu;
        $variancePercentage = $budget->budget_prevu > 0 
            ? ($variance / $budget->budget_prevu) * 100 
            : 0;

        return [
            'budget_prevu' => $budget->budget_prevu,
            'total_final' => $totalFinal,
            'variance' => $variance,
            'variance_percentage' => $variancePercentage,
        ];
    }

    /**
     * Update budget totals based on current expenses and penalties
     *
     * @param int $installationId
     * @return void
     */
    public function updateBudgetTotals(int $installationId): void
    {
        $budget = InstallationBudget::where('installation_id', $installationId)->first();
        
        if (!$budget) {
            return;
        }

        $budget->total_frais = $this->calculateTotalExpenses($installationId);
        $budget->total_penalites = $this->calculateTimePenalties($installationId);
        $budget->total_final = $budget->total_frais + $budget->total_penalites;
        
        $budget->save();
    }

    /**
     * Calculate time penalty based on delay
     *
     * @param int $installationId
     * @param Carbon $actualEndDate
     * @param float $penaltyPerDay
     * @return array
     */
    public function calculateDelayPenalty(int $installationId, Carbon $actualEndDate, float $penaltyPerDay = 0): array
    {
        $installation = Installation::find($installationId);
        
        if (!$installation || !$installation->planned_end_date) {
            return [
                'jours_retard' => 0,
                'montant_penalite' => 0,
            ];
        }

        $plannedEndDate = Carbon::parse($installation->planned_end_date);
        
        if ($actualEndDate <= $plannedEndDate) {
            return [
                'jours_retard' => 0,
                'montant_penalite' => 0,
            ];
        }

        $joursRetard = abs($actualEndDate->diffInDays($plannedEndDate));
        $montantPenalite = $joursRetard * $penaltyPerDay;

        return [
            'jours_retard' => $joursRetard,
            'montant_penalite' => $montantPenalite,
        ];
    }

    /**
     * Determine coverage status based on warranty and contract
     *
     * @param int $installationId
     * @return string
     */
    public function determineCoverageStatus(int $installationId): string
    {
        $installation = Installation::with('equipementPrincipal')->find($installationId);
        
        if (!$installation || !$installation->equipementPrincipal) {
            return 'a_verifier';
        }

        $equipment = $installation->equipementPrincipal;
        
        // Check if warranty is still active
        if ($equipment->date_fin_garantie && Carbon::parse($equipment->date_fin_garantie)->isFuture()) {
            return 'garantie';
        }

        // Check if there's a renewable contract
        if ($equipment->contrat_reference) {
            return 'contrat_renouvelable';
        }

        return 'hors_contrat';
    }

    /**
     * Get budget summary for an installation
     *
     * @param int $installationId
     * @return array
     */
    public function getBudgetSummary(int $installationId): array
    {
        $budget = InstallationBudget::where('installation_id', $installationId)->first();
        
        if (!$budget) {
            return [
                'regime_prise_en_charge' => 'garantie',
                'budget_prevu' => 0,
                'total_frais' => $this->calculateTotalExpenses($installationId),
                'total_penalites' => $this->calculateTimePenalties($installationId),
                'total_final' => $this->calculateTotalFinal($installationId),
                'statut_validation' => 'brouillon',
                'reference_contrat' => null,
                'statut_couverture' => $this->determineCoverageStatus($installationId),
            ];
        }

        $variance = $this->calculateBudgetVariance($installationId);

        return [
            'regime_prise_en_charge' => $budget->regime_prise_en_charge,
            'budget_prevu' => $budget->budget_prevu,
            'total_frais' => $budget->total_frais,
            'total_penalites' => $budget->total_penalites,
            'total_final' => $budget->total_final,
            'statut_validation' => $budget->statut_validation,
            'reference_contrat' => $budget->reference_contrat,
            'statut_couverture' => $this->determineCoverageStatus($installationId),
            'variance' => $variance,
        ];
    }
}
