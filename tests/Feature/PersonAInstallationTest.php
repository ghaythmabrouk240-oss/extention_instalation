<?php

namespace Tests\Feature;

use App\Models\Installation;
use App\Models\User;
use App\Services\InstallationStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonAInstallationTest extends TestCase
{
    use RefreshDatabase;

    public function test_biomedical_can_create_mri_installation_with_child_profile(): void
    {
        $user = User::factory()->biomedical()->create();

        $response = $this->actingAs($user)->post(route('installations.store'), $this->mriPayload());

        $response->assertRedirect(route('installations.index'));
        $this->assertDatabaseHas('installations', [
            'code_installation' => 'INS-MRI-001',
            'type_profil' => Installation::TYPE_IRM,
            'statut' => InstallationStatusService::BROUILLON,
        ]);
        $this->assertDatabaseHas('profil_irms', [
            'champ_magnetique' => '3T',
            'blindage' => 'RF conforme',
            'batiment' => 'B1',
            'zone' => 'Zone 4',
            'zone_controlee' => 1,
        ]);
    }

    public function test_manager_cannot_edit_installation(): void
    {
        $manager = User::factory()->manager()->create();
        $installation = $this->createMriInstallation();

        $response = $this->actingAs($manager)->put(
            route('installations.update', $installation),
            $this->mriPayload(['nom' => 'Salle refusée'])
        );

        $response->assertForbidden();
        $this->assertDatabaseMissing('installations', ['nom' => 'Salle refusée']);
    }

    public function test_biomedical_cannot_archive_installation(): void
    {
        $user = User::factory()->biomedical()->create();
        $installation = $this->createMriInstallation();

        $response = $this->actingAs($user)->delete(route('installations.destroy', $installation));

        $response->assertForbidden();
        $this->assertNotSoftDeleted('installations', ['id' => $installation->id]);
    }

    public function test_invalid_status_transition_is_rejected(): void
    {
        $user = User::factory()->biomedical()->create();
        $installation = $this->createMriInstallation();

        $response = $this
            ->actingAs($user)
            ->from(route('installations.edit', $installation))
            ->put(route('installations.update', $installation), $this->mriPayload([
                'statut' => InstallationStatusService::OPERATIONNEL,
            ]));

        $response
            ->assertRedirect(route('installations.edit', $installation))
            ->assertSessionHasErrors('statut');

        $this->assertSame(
            InstallationStatusService::BROUILLON,
            $installation->fresh()->statut
        );
    }

    public function test_biomedical_dashboard_hides_strategic_kpis_and_shows_operational_kpis(): void
    {
        $user = User::factory()->biomedical()->create();

        $this->createMriInstallation([
            'code_installation' => 'INS-MRI-OP',
            'nom' => 'IRM opérationnelle',
            'statut' => InstallationStatusService::OPERATIONNEL,
        ]);
        Installation::create([
            'code_installation' => 'INS-CATH-MAINT',
            'nom' => 'Cath maintenance',
            'type_profil' => Installation::TYPE_CATHETERISME,
            'statut' => InstallationStatusService::EN_MAINTENANCE,
            'criticite' => 'Haute',
        ]);
        Installation::create([
            'code_installation' => 'INS-ARCH',
            'nom' => 'Archive',
            'type_profil' => Installation::TYPE_IRM,
            'statut' => InstallationStatusService::ARCHIVE,
            'criticite' => 'Basse',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('canViewStrategicKpis', false);
        $response->assertDontSee('Installations actives', false);
        $response->assertSee('KPIs opérationnels', false);
        $response->assertSee('En maintenance', false);
        $response->assertSee('Les KPIs stratégiques globaux sont masqués', false);
        $response->assertViewHas('operationalKpis', function (array $operationalKpis) {
            $maintenance = collect($operationalKpis)->firstWhere('label', 'En maintenance');

            return $maintenance && $maintenance['value'] === 1;
        });
    }

    public function test_admin_and_manager_can_see_strategic_dashboard_kpis(): void
    {
        $admin = User::factory()->admin()->create();
        $manager = User::factory()->manager()->create();

        $this->createMriInstallation([
            'code_installation' => 'INS-MRI-ADMIN',
            'nom' => 'IRM admin',
            'statut' => InstallationStatusService::OPERATIONNEL,
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('canViewStrategicKpis', true)
            ->assertSee('KPIs stratégiques', false)
            ->assertSee('Installations actives', false);

        $this->actingAs($manager)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('canViewStrategicKpis', true)
            ->assertSee('KPIs stratégiques', false)
            ->assertSee('Installations actives', false);
    }

    public function test_installation_list_sorts_by_criticality_descending(): void
    {
        $user = User::factory()->biomedical()->create();

        Installation::create([
            'code_installation' => 'INS-BASSE',
            'nom' => 'Basse',
            'type_profil' => Installation::TYPE_IRM,
            'statut' => InstallationStatusService::BROUILLON,
            'criticite' => 'Basse',
        ]);
        Installation::create([
            'code_installation' => 'INS-CRITIQUE',
            'nom' => 'Critique',
            'type_profil' => Installation::TYPE_IRM,
            'statut' => InstallationStatusService::BROUILLON,
            'criticite' => 'Critique',
        ]);
        Installation::create([
            'code_installation' => 'INS-HAUTE',
            'nom' => 'Haute',
            'type_profil' => Installation::TYPE_IRM,
            'statut' => InstallationStatusService::BROUILLON,
            'criticite' => 'Haute',
        ]);

        $response = $this->actingAs($user)->get(route('installations.index', ['sort' => 'criticite_desc']));

        $response->assertOk();
        $this->assertSame(
            ['INS-CRITIQUE', 'INS-HAUTE', 'INS-BASSE'],
            $response->viewData('installations')->pluck('code_installation')->all()
        );
    }

    private function createMriInstallation(array $overrides = []): Installation
    {
        $installation = Installation::create(array_merge([
            'code_installation' => 'INS-MRI-001',
            'nom' => 'Salle IRM 1',
            'type_profil' => Installation::TYPE_IRM,
            'statut' => InstallationStatusService::BROUILLON,
            'criticite' => 'Moyenne',
        ], $overrides));

        $installation->profilIrm()->create([
            'champ_magnetique' => '3T',
            'zone_controlee' => true,
            'blindage' => 'RF conforme',
            'atelier' => 'Local technique',
            'confinement_ferromagnetique' => true,
            'arret_urgence' => true,
            'batiment' => 'B1',
            'etage' => '2',
            'zone' => 'Zone 4',
        ]);

        return $installation;
    }

    private function mriPayload(array $overrides = []): array
    {
        return array_merge([
            'code_installation' => 'INS-MRI-001',
            'nom' => 'Salle IRM 1',
            'type_profil' => Installation::TYPE_IRM,
            'statut' => InstallationStatusService::BROUILLON,
            'criticite' => 'Moyenne',
            'client_id' => null,
            'equipement_principal_id' => null,
            'proprietaire_interne_id' => null,
            'profil_irm' => [
                'champ_magnetique' => '3T',
                'zone_controlee' => '1',
                'blindage' => 'RF conforme',
                'atelier' => 'Local technique',
                'confinement_ferromagnetique' => '1',
                'arret_urgence' => '1',
                'batiment' => 'B1',
                'etage' => '2',
                'zone' => 'Zone 4',
            ],
        ], $overrides);
    }
}
