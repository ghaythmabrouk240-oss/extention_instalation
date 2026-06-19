<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Models\DocumentInstallation;
use App\Models\Equipement;
use App\Models\Installation;
use App\Models\SousEquipement;
use App\Services\Graph\MriReadinessStrategy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MriReadinessStrategyTest extends TestCase
{
    use RefreshDatabase;

    private MriReadinessStrategy $strategy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->strategy = new MriReadinessStrategy();
    }

    public function test_complete_mri_installation_returns_green_readiness_graph(): void
    {
        $installation = $this->mriInstallation();
        $this->completeProfile($installation);
        $this->addDocuments($installation, [
            'Rapport installation generale',
            'Rapport de reception',
            'Plan de prevention',
            'Rapport des tests',
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $this->assertSame('vert', $this->node($graph, 'equip_principal')['state']);
        $this->assertSame('vert', $this->node($graph, 'champ_magnetique')['state']);
        $this->assertSame('vert', $this->node($graph, 'securite_irm')['state']);
        $this->assertSame(0, $graph['summary']['blockers']);
        $this->assertSame(0, $graph['summary']['warnings']);
        $this->assertEquals(100, $graph['summary']['completion_rate']);
    }

    public function test_missing_mri_safety_fields_create_red_blocking_nodes(): void
    {
        $installation = $this->mriInstallation();
        $installation->profilIrm()->create([
            'champ_magnetique' => '3T',
            'blindage' => null,
            'zone_controlee' => false,
            'confinement_ferromagnetique' => false,
            'arret_urgence' => false,
            'batiment' => 'B1',
            'etage' => '2',
            'zone' => 'Zone 4',
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $this->assertSame('rouge', $this->node($graph, 'blindage_rf')['state']);
        $this->assertSame('rouge', $this->node($graph, 'zone_controlee')['state']);
        $this->assertSame('rouge', $this->node($graph, 'confinement_ferromagnetique')['state']);
        $this->assertSame('rouge', $this->node($graph, 'arret_urgence')['state']);
        $this->assertSame('rouge', $this->node($graph, 'securite_irm')['state']);
        $this->assertGreaterThan(0, $graph['summary']['blockers']);
    }

    public function test_missing_required_mri_documents_are_red_and_optional_tests_are_yellow(): void
    {
        $installation = $this->mriInstallation();
        $this->completeProfile($installation);

        $graph = $this->strategy->buildGraph($installation);

        $this->assertSame('rouge', $this->node($graph, 'rapport_installation_generale')['state']);
        $this->assertSame('rouge', $this->node($graph, 'rapport_reception')['state']);
        $this->assertSame('rouge', $this->node($graph, 'plan_prevention')['state']);
        $this->assertSame('jaune', $this->node($graph, 'rapport_tests')['state']);
        $this->assertSame(3, $graph['summary']['blockers']);
        $this->assertSame(1, $graph['summary']['warnings']);
    }

    public function test_mri_graph_includes_secondary_and_sub_equipment_nodes(): void
    {
        $installation = $this->mriInstallation();
        $this->completeProfile($installation);
        $secondary = Equipement::factory()->create([
            'client_id' => $installation->client_id,
            'designation' => 'Refroidisseur IRM',
        ]);
        $installation->equipements()->attach($secondary->id, ['role' => 'secondaire']);

        $subEquipment = SousEquipement::create([
            'identifiant' => 'SE-IRM-001',
            'designation' => 'Console operateur',
            'marque' => 'Philips',
            'modele' => 'Console MRI',
            'description' => 'Console de pilotage IRM',
            'equipement_id' => $installation->equipement_principal_id,
        ]);

        $graph = $this->strategy->buildGraph($installation);

        $this->assertSame(
            'equipement_secondaire',
            $this->node($graph, 'equipement_secondaire_'.$secondary->id)['type']
        );
        $this->assertSame(
            'sous_equipement',
            $this->node($graph, 'sous_equipement_'.$subEquipment->id)['type']
        );
        $this->assertTrue(collect($graph['edges'])->contains(
            fn (array $edge) => $edge['source'] === 'sous_equipement_'.$subEquipment->id
                && $edge['target'] === 'equip_principal'
                && $edge['relation'] === 'compose'
        ));
    }

    private function mriInstallation(): Installation
    {
        $client = Client::factory()->create();
        $equipment = Equipement::factory()->create([
            'client_id' => $client->id,
            'designation' => 'Systeme IRM Ingenia',
            'marque' => 'Philips',
            'modele' => 'Ingenia 3T',
            'numero_serie' => 'MRI-3T-001',
        ]);

        return Installation::factory()->create([
            'type_profil' => Installation::TYPE_IRM,
            'client_id' => $client->id,
            'equipement_principal_id' => $equipment->id,
            'code_installation' => 'INS-IRM-TEST',
        ]);
    }

    private function completeProfile(Installation $installation): void
    {
        $installation->profilIrm()->create([
            'champ_magnetique' => '3T',
            'blindage' => 'Blindage RF conforme',
            'zone_controlee' => true,
            'confinement_ferromagnetique' => true,
            'arret_urgence' => true,
            'batiment' => 'B1',
            'etage' => '2',
            'zone' => 'Zone 4',
            'atelier' => 'Local technique IRM',
        ]);
    }

    private function addDocuments(Installation $installation, array $categories): void
    {
        foreach ($categories as $category) {
            DocumentInstallation::factory()->create([
                'installation_id' => $installation->id,
                'categorie' => $category,
                'profil_concerne' => Installation::TYPE_IRM,
                'est_version_active' => true,
            ]);
        }
    }

    private function node(array $graph, string $id): array
    {
        $node = collect($graph['nodes'])->firstWhere('id', $id);

        $this->assertNotNull($node, "Node {$id} was not found.");

        return $node;
    }
}
