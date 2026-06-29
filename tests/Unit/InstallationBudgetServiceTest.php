<?php

namespace Tests\Unit;

use App\Models\Installation;
use App\Models\InstallationBudget;
use App\Models\InstallationExpense;
use App\Models\InstallationTimePenalty;
use App\Services\InstallationBudgetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallationBudgetServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InstallationBudgetService $budgetService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->budgetService = app(InstallationBudgetService::class);
    }

    public function test_calculate_total_expenses()
    {
        $installation = Installation::factory()->create();
        
        InstallationExpense::create([
            'installation_id' => $installation->id,
            'type_depense' => 'transport_aller',
            'date_depense' => now(),
            'description' => 'Test expense',
            'quantite' => 2,
            'montant_unitaire' => 100,
            'montant_total' => 200,
            'tva' => 20,
        ]);

        InstallationExpense::create([
            'installation_id' => $installation->id,
            'type_depense' => 'hotel',
            'date_depense' => now(),
            'description' => 'Hotel expense',
            'quantite' => 1,
            'montant_unitaire' => 150,
            'montant_total' => 150,
            'tva' => 30,
        ]);

        $total = $this->budgetService->calculateTotalExpenses($installation->id);

        $this->assertEquals(350, $total);
    }

    public function test_calculate_time_penalties()
    {
        $installation = Installation::factory()->create();
        
        InstallationTimePenalty::create([
            'installation_id' => $installation->id,
            'jours_retard' => 5,
            'penalite_par_jour' => 100,
            'montant_penalite' => 500,
            'applicable' => true,
        ]);

        $penalty = $this->budgetService->calculateTimePenalties($installation->id);

        $this->assertEquals(500, $penalty);
    }

    public function test_calculate_time_penalties_not_applicable()
    {
        $installation = Installation::factory()->create();
        
        InstallationTimePenalty::create([
            'installation_id' => $installation->id,
            'jours_retard' => 5,
            'penalite_par_jour' => 100,
            'montant_penalite' => 500,
            'applicable' => false,
        ]);

        $penalty = $this->budgetService->calculateTimePenalties($installation->id);

        $this->assertEquals(0, $penalty);
    }

    public function test_calculate_total_final()
    {
        $installation = Installation::factory()->create();
        
        InstallationExpense::create([
            'installation_id' => $installation->id,
            'type_depense' => 'transport_aller',
            'date_depense' => now(),
            'description' => 'Test expense',
            'quantite' => 1,
            'montant_unitaire' => 200,
            'montant_total' => 200,
            'tva' => 0,
        ]);

        InstallationTimePenalty::create([
            'installation_id' => $installation->id,
            'jours_retard' => 2,
            'penalite_par_jour' => 50,
            'montant_penalite' => 100,
            'applicable' => true,
        ]);

        $total = $this->budgetService->calculateTotalFinal($installation->id);

        $this->assertEquals(300, $total);
    }

    public function test_calculate_budget_variance()
    {
        $installation = Installation::factory()->create();
        
        InstallationBudget::create([
            'installation_id' => $installation->id,
            'budget_prevu' => 1000,
            'total_frais' => 800,
            'total_penalites' => 100,
            'total_final' => 900,
        ]);

        InstallationExpense::create([
            'installation_id' => $installation->id,
            'type_depense' => 'transport_aller',
            'date_depense' => now(),
            'description' => 'Test expense',
            'quantite' => 1,
            'montant_unitaire' => 800,
            'montant_total' => 800,
            'tva' => 0,
        ]);

        InstallationTimePenalty::create([
            'installation_id' => $installation->id,
            'montant_penalite' => 100,
            'applicable' => true,
        ]);

        $variance = $this->budgetService->calculateBudgetVariance($installation->id);

        $this->assertEquals(1000, $variance['budget_prevu']);
        $this->assertEquals(900, $variance['total_final']);
        $this->assertEquals(-100, $variance['variance']);
        $this->assertEquals(-10, $variance['variance_percentage']);
    }

    public function test_update_budget_totals()
    {
        $installation = Installation::factory()->create();
        
        InstallationBudget::create([
            'installation_id' => $installation->id,
            'total_frais' => 0,
            'total_penalites' => 0,
            'total_final' => 0,
        ]);

        InstallationExpense::create([
            'installation_id' => $installation->id,
            'type_depense' => 'transport_aller',
            'date_depense' => now(),
            'description' => 'Test expense',
            'quantite' => 1,
            'montant_unitaire' => 500,
            'montant_total' => 500,
            'tva' => 0,
        ]);

        InstallationTimePenalty::create([
            'installation_id' => $installation->id,
            'montant_penalite' => 50,
            'applicable' => true,
        ]);

        $this->budgetService->updateBudgetTotals($installation->id);

        $budget = InstallationBudget::where('installation_id', $installation->id)->first();

        $this->assertEquals(500, $budget->total_frais);
        $this->assertEquals(50, $budget->total_penalites);
        $this->assertEquals(550, $budget->total_final);
    }

    public function test_calculate_delay_penalty()
    {
        $installation = Installation::factory()->create([
            'planned_end_date' => '2026-06-20',
        ]);

        $actualEndDate = \Carbon\Carbon::parse('2026-06-25');
        $penaltyPerDay = 100;

        $penalty = $this->budgetService->calculateDelayPenalty(
            $installation->id,
            $actualEndDate,
            $penaltyPerDay
        );

        $this->assertEquals(5, abs($penalty['jours_retard']));
        $this->assertEquals(500, $penalty['montant_penalite']);
    }

    public function test_calculate_delay_penalty_no_delay()
    {
        $installation = Installation::factory()->create([
            'planned_end_date' => '2026-06-20',
        ]);

        $actualEndDate = \Carbon\Carbon::parse('2026-06-18');
        $penaltyPerDay = 100;

        $penalty = $this->budgetService->calculateDelayPenalty(
            $installation->id,
            $actualEndDate,
            $penaltyPerDay
        );

        $this->assertEquals(0, $penalty['jours_retard']);
        $this->assertEquals(0, $penalty['montant_penalite']);
    }

    public function test_get_budget_summary()
    {
        $installation = Installation::factory()->create();
        
        InstallationBudget::create([
            'installation_id' => $installation->id,
            'regime_prise_en_charge' => 'garantie',
            'budget_prevu' => 5000,
            'total_frais' => 3000,
            'total_penalites' => 200,
            'total_final' => 3200,
            'statut_validation' => 'valide',
        ]);

        $summary = $this->budgetService->getBudgetSummary($installation->id);

        $this->assertEquals('garantie', $summary['regime_prise_en_charge']);
        $this->assertEquals(5000, $summary['budget_prevu']);
        $this->assertEquals(3000, $summary['total_frais']);
        $this->assertEquals(200, $summary['total_penalites']);
        $this->assertEquals(3200, $summary['total_final']);
        $this->assertEquals('valide', $summary['statut_validation']);
    }
}
