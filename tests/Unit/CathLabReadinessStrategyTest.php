<?php

namespace Tests\Unit;

use App\Models\Installation;
use App\Models\ProfilCatLab;
use App\Models\Equipement;
use App\Models\Client;
use App\Models\DocumentInstallation;
use App\Services\Graph\CathLabReadinessStrategy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CathLabReadinessStrategyTest extends TestCase
{
    use RefreshDatabase;

    private CathLabReadinessStrategy $strategy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->strategy = new CathLabReadinessStrategy();
    }

    public function test_equip_principal_vert_with_conforme_status(): void
    {
        $client = Client::factory()->create();
        $equipment = Equipement::factory()->create(['client_id' => $client->id]);
        
        $installation = Installation::factory()->create([
            'type_profil' => 'CATHETERISME',
            'equipement_principal_id' => $equipment->id,
        ]);

        $profile = ProfilCatLab::factory()->create([
            'installation_id' => $installation->id,
            'acceptance_test_status' => 'conforme',
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $equipNode = collect($graph['nodes'])->firstWhere('id', 'equip_principal');
        $this->assertEquals('vert', $equipNode['state']);
    }

    public function test_equip_principal_jaune_with_a_verifier_status(): void
    {
        $client = Client::factory()->create();
        $equipment = Equipement::factory()->create(['client_id' => $client->id]);
        
        $installation = Installation::factory()->create([
            'type_profil' => 'CATHETERISME',
            'equipement_principal_id' => $equipment->id,
        ]);

        $profile = ProfilCatLab::factory()->create([
            'installation_id' => $installation->id,
            'acceptance_test_status' => 'a_verifier',
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $equipNode = collect($graph['nodes'])->firstWhere('id', 'equip_principal');
        $this->assertEquals('jaune', $equipNode['state']);
    }

    public function test_equip_principal_rouge_without_equipment(): void
    {
        $installation = Installation::factory()->create([
            'type_profil' => 'CATHETERISME',
            'equipement_principal_id' => null,
        ]);

        $profile = ProfilCatLab::factory()->create([
            'installation_id' => $installation->id,
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $equipNode = collect($graph['nodes'])->firstWhere('id', 'equip_principal');
        $this->assertEquals('rouge', $equipNode['state']);
    }

    public function test_table_patient_vert_when_filled(): void
    {
        $installation = Installation::factory()->create(['type_profil' => 'CATHETERISME']);
        $profile = ProfilCatLab::factory()->create([
            'installation_id' => $installation->id,
            'table_patient' => 'Table XYZ-123',
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $node = collect($graph['nodes'])->firstWhere('id', 'table_patient');
        $this->assertEquals('vert', $node['state']);
    }

    public function test_table_patient_rouge_when_empty(): void
    {
        $installation = Installation::factory()->create(['type_profil' => 'CATHETERISME']);
        $profile = ProfilCatLab::factory()->create([
            'installation_id' => $installation->id,
            'table_patient' => null,
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $node = collect($graph['nodes'])->firstWhere('id', 'table_patient');
        $this->assertEquals('rouge', $node['state']);
    }

    public function test_injecteur_vert_when_filled(): void
    {
        $installation = Installation::factory()->create(['type_profil' => 'CATHETERISME']);
        $profile = ProfilCatLab::factory()->create([
            'installation_id' => $installation->id,
            'injecteur' => 'Injecteur Medrad',
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $node = collect($graph['nodes'])->firstWhere('id', 'injecteur');
        $this->assertEquals('vert', $node['state']);
    }

    public function test_injecteur_rouge_when_empty(): void
    {
        $installation = Installation::factory()->create(['type_profil' => 'CATHETERISME']);
        $profile = ProfilCatLab::factory()->create([
            'installation_id' => $installation->id,
            'injecteur' => null,
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $node = collect($graph['nodes'])->firstWhere('id', 'injecteur');
        $this->assertEquals('rouge', $node['state']);
    }

    public function test_moniteurs_vert_when_filled(): void
    {
        $installation = Installation::factory()->create(['type_profil' => 'CATHETERISME']);
        $profile = ProfilCatLab::factory()->create([
            'installation_id' => $installation->id,
            'moniteurs' => 'Philips monitors',
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $node = collect($graph['nodes'])->firstWhere('id', 'moniteurs');
        $this->assertEquals('vert', $node['state']);
    }

    public function test_moniteurs_jaune_when_empty(): void
    {
        $installation = Installation::factory()->create(['type_profil' => 'CATHETERISME']);
        $profile = ProfilCatLab::factory()->create([
            'installation_id' => $installation->id,
            'moniteurs' => null,
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $node = collect($graph['nodes'])->firstWhere('id', 'moniteurs');
        $this->assertEquals('jaune', $node['state']);
    }

    public function test_radioprotection_vert_when_both_conforme(): void
    {
        $installation = Installation::factory()->create(['type_profil' => 'CATHETERISME']);
        $profile = ProfilCatLab::factory()->create([
            'installation_id' => $installation->id,
            'radiation_shielding_status' => 'conforme',
            'lead_glass_status' => 'conforme',
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $node = collect($graph['nodes'])->firstWhere('id', 'radioprotection');
        $this->assertEquals('vert', $node['state']);
    }

    public function test_radioprotection_jaune_when_a_verifier(): void
    {
        $installation = Installation::factory()->create(['type_profil' => 'CATHETERISME']);
        $profile = ProfilCatLab::factory()->create([
            'installation_id' => $installation->id,
            'radiation_shielding_status' => 'a_verifier',
            'lead_glass_status' => 'conforme',
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $node = collect($graph['nodes'])->firstWhere('id', 'radioprotection');
        $this->assertEquals('jaune', $node['state']);
    }

    public function test_radioprotection_rouge_when_non_conforme(): void
    {
        $installation = Installation::factory()->create(['type_profil' => 'CATHETERISME']);
        $profile = ProfilCatLab::factory()->create([
            'installation_id' => $installation->id,
            'radiation_shielding_status' => 'non_conforme',
            'lead_glass_status' => 'conforme',
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $node = collect($graph['nodes'])->firstWhere('id', 'radioprotection');
        $this->assertEquals('rouge', $node['state']);
    }

    public function test_ceiling_support_mapping(): void
    {
        $installation = Installation::factory()->create(['type_profil' => 'CATHETERISME']);
        
        // Test conforme
        $profile = ProfilCatLab::factory()->create([
            'installation_id' => $installation->id,
            'ceiling_support_status' => 'conforme',
        ]);
        $graph = $this->strategy->buildGraph($installation);
        $node = collect($graph['nodes'])->firstWhere('id', 'ceiling_support');
        $this->assertEquals('vert', $node['state']);
    }

    public function test_dose_monitoring_vert_when_available(): void
    {
        $installation = Installation::factory()->create(['type_profil' => 'CATHETERISME']);
        $profile = ProfilCatLab::factory()->create([
            'installation_id' => $installation->id,
            'dose_monitoring_available' => true,
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $node = collect($graph['nodes'])->firstWhere('id', 'dose_monitoring');
        $this->assertEquals('vert', $node['state']);
    }

    public function test_dose_monitoring_jaune_when_not_available(): void
    {
        $installation = Installation::factory()->create(['type_profil' => 'CATHETERISME']);
        $profile = ProfilCatLab::factory()->create([
            'installation_id' => $installation->id,
            'dose_monitoring_available' => false,
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $node = collect($graph['nodes'])->firstWhere('id', 'dose_monitoring');
        $this->assertEquals('jaune', $node['state']);
    }

    public function test_salle_interventionnelle_worst_state_rule(): void
    {
        $installation = Installation::factory()->create(['type_profil' => 'CATHETERISME']);
        $profile = ProfilCatLab::factory()->create([
            'installation_id' => $installation->id,
            'radiation_shielding_status' => 'conforme',
            'lead_glass_status' => 'conforme',
            'ceiling_support_status' => 'conforme',
            'dose_monitoring_available' => true,
            'emergency_equipment_status' => 'non_conforme', // This should make it rouge
            'access_control_status' => 'conforme',
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $node = collect($graph['nodes'])->firstWhere('id', 'salle_interventionnelle');
        $this->assertEquals('rouge', $node['state']);
    }

    public function test_rapport_reception_vert_when_present(): void
    {
        $installation = Installation::factory()->create(['type_profil' => 'CATHETERISME']);
        DocumentInstallation::factory()->create([
            'installation_id' => $installation->id,
            'categorie' => 'Rapport de reception',
            'est_version_active' => true,
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $node = collect($graph['nodes'])->firstWhere('id', 'rapport_reception');
        $this->assertEquals('vert', $node['state']);
    }

    public function test_rapport_reception_rouge_when_absent(): void
    {
        $installation = Installation::factory()->create(['type_profil' => 'CATHETERISME']);

        $graph = $this->strategy->buildGraph($installation);

        $node = collect($graph['nodes'])->firstWhere('id', 'rapport_reception');
        $this->assertEquals('rouge', $node['state']);
    }

    public function test_rapport_reception_blocking_edge(): void
    {
        $installation = Installation::factory()->create(['type_profil' => 'CATHETERISME']);

        $graph = $this->strategy->buildGraph($installation);

        $edge = collect($graph['edges'])->firstWhere('relation', 'document_requis');
        $this->assertTrue($edge['blocking']);
    }

    public function test_recommended_document_jaune_when_absent(): void
    {
        $installation = Installation::factory()->create(['type_profil' => 'CATHETERISME']);

        $graph = $this->strategy->buildGraph($installation);

        $node = collect($graph['nodes'])->firstWhere('id', 'rapport_radioprotection');
        $this->assertEquals('jaune', $node['state']);
    }

    public function test_summary_calculation(): void
    {
        $client = Client::factory()->create();
        $equipment = Equipement::factory()->create(['client_id' => $client->id]);
        
        $installation = Installation::factory()->create([
            'type_profil' => 'CATHETERISME',
            'equipement_principal_id' => $equipment->id,
            'code_installation' => 'INS-CATH-0001',
        ]);

        $profile = ProfilCatLab::factory()->create([
            'installation_id' => $installation->id,
            'acceptance_test_status' => 'conforme',
            'table_patient' => 'Table XYZ',
            'injecteur' => 'Injecteur Medrad',
            'moniteurs' => 'Philips monitors',
            'radiation_shielding_status' => 'conforme',
            'lead_glass_status' => 'conforme',
            'ceiling_support_status' => 'conforme',
            'dose_monitoring_available' => true,
            'emergency_equipment_status' => 'conforme',
            'access_control_status' => 'conforme',
        ]);

        DocumentInstallation::factory()->create([
            'installation_id' => $installation->id,
            'categorie' => 'Rapport de reception',
            'est_version_active' => true,
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $this->assertEquals('INS-CATH-0001', $graph['summary']['installation']);
        $this->assertEquals('CATHETERISME', $graph['summary']['profile']);
        $this->assertGreaterThan(0, $graph['summary']['total_nodes']);
        $this->assertEquals(0, $graph['summary']['blockers']);
    }
}
