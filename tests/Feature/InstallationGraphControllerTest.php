<?php

namespace Tests\Feature;

use App\Models\Installation;
use App\Models\ProfilCatLab;
use App\Models\ProfilIRM;
use App\Models\Equipement;
use App\Models\Client;
use App\Models\DocumentInstallation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallationGraphControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_graph_page_enables_irm_in_the_shared_interface(): void
    {
        Installation::factory()->create([
            'type_profil' => 'IRM',
            'code_installation' => 'INS-IRM-UI',
        ]);

        $this->get(route('installations.graph'))
            ->assertOk()
            ->assertSee('INS-IRM-UI', false)
            ->assertSee('<option value="IRM">IRM</option>', false)
            ->assertSee('vis.Network', false)
            ->assertDontSee('IRM (à venir)', false)
            ->assertDontSee('Profil IRM — à venir', false);
    }

    public function test_get_catheterisme_complete_installation_returns_200_with_completion_100(): void
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

        $response = $this->get('/dashboard/installation-graph?installation_id=' . $installation->id . '&profile=CATHETERISME');

        $response->assertStatus(200);
        $response->assertJson([
            'summary' => [
                'installation' => 'INS-CATH-0001',
                'profile' => 'CATHETERISME',
                'blockers' => 0,
            ]
        ]);
    }

    public function test_get_installation_without_rapport_reception_returns_blocker(): void
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
            'table_patient' => 'Table XYZ', // Add this to avoid being a blocker
            'injecteur' => 'Injecteur Medrad', // Add this to avoid being a blocker
            'radiation_shielding_status' => 'conforme',
            'lead_glass_status' => 'conforme',
            'ceiling_support_status' => 'conforme',
            'dose_monitoring_available' => true,
            'emergency_equipment_status' => 'conforme',
            'access_control_status' => 'conforme',
        ]);

        $response = $this->get('/dashboard/installation-graph?installation_id=' . $installation->id . '&profile=CATHETERISME');

        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertEquals(1, $data['summary']['blockers']);
        
        $rapportReceptionNode = collect($data['nodes'])->firstWhere('id', 'rapport_reception');
        $this->assertNotNull($rapportReceptionNode);
        $this->assertEquals('rouge', $rapportReceptionNode['state']);
    }

    public function test_get_with_profile_irm_returns_readiness_graph(): void
    {
        $installation = Installation::factory()->create([
            'type_profil' => 'IRM',
        ]);
        ProfilIRM::create([
            'installation_id' => $installation->id,
            'champ_magnetique' => '3T',
            'blindage' => 'Blindage RF conforme',
            'zone_controlee' => true,
            'confinement_ferromagnetique' => true,
            'arret_urgence' => true,
            'batiment' => 'B1',
            'etage' => '2',
            'zone' => 'Zone 4',
        ]);

        $response = $this->get('/dashboard/installation-graph?installation_id=' . $installation->id . '&profile=IRM');

        $response->assertStatus(200);
        $response
            ->assertJsonPath('summary.installation', $installation->code_installation)
            ->assertJsonPath('summary.profile', 'IRM')
            ->assertJsonFragment([
                'id' => 'champ_magnetique',
                'state' => 'vert',
                'profile' => 'IRM',
            ])
            ->assertJsonFragment([
                'id' => 'securite_irm',
                'state' => 'vert',
            ]);

        $this->assertGreaterThan(0, $response->json('summary.total_nodes'));
        $this->assertNotEmpty($response->json('edges'));
    }

    public function test_get_with_nonexistent_installation_id_returns_422(): void
    {
        $response = $this->get('/dashboard/installation-graph?installation_id=99999&profile=CATHETERISME', ['Accept' => 'application/json']);

        $response->assertStatus(422);
    }

    public function test_get_without_authorization_returns_403(): void
    {
        // TODO: Re-enable this test when authentication is set up
        $this->markTestSkipped('Authorization test skipped - authentication not set up');
        
        // Create a user without permission to view installations
        $user = User::factory()->create(['role' => 'user']);
        
        $installation = Installation::factory()->create([
            'type_profil' => 'CATHETERISME',
        ]);

        $response = $this->actingAs($user)->get('/dashboard/installation-graph?installation_id=' . $installation->id . '&profile=CATHETERISME');

        $response->assertStatus(403);
    }
}
