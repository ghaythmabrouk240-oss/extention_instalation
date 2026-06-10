<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\DocumentInstallation;
use App\Models\Equipement;
use App\Models\Installation;
use App\Models\SousEquipement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PersonBInstallationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cath_installation_saves_child_profile_and_secondary_equipment(): void
    {
        $user = User::factory()->create();
        $client = Client::create(['nom' => 'Clinique Test']);
        $mainEquipment = $this->equipment($client, 'EQ-MAIN');
        $secondaryEquipment = $this->equipment($client, 'EQ-SEC');

        $response = $this->actingAs($user)->post(route('installations.store'), array_merge([
            'code_installation' => 'INST-CATH-T',
            'nom' => 'Salle Cath Test',
            'type_profil' => 'CATHETERISME',
            'statut' => 'Brouillon',
            'criticite' => 'Haute',
            'client_id' => $client->id,
            'equipement_principal_id' => $mainEquipment->id,
            'planned_start_date' => '2026-06-15',
            'planned_end_date' => '2026-06-16',
            'equipements_secondaires' => [$secondaryEquipment->id],
        ], $this->cathProfilePayload()));

        $installation = Installation::where('code_installation', 'INST-CATH-T')->first();

        $response->assertRedirect(route('installations.show', $installation));
        $this->assertDatabaseHas('profil_cat_labs', [
            'installation_id' => $installation->id,
            'systeme_angiographie' => 'Azurion',
            'controle_acces' => true,
        ]);
        $this->assertDatabaseHas('lien_equipement_installation', [
            'installation_id' => $installation->id,
            'equipement_id' => $secondaryEquipment->id,
            'role' => 'secondaire',
        ]);
    }

    public function test_irm_installation_does_not_create_cath_profile_from_cath_fields(): void
    {
        $user = User::factory()->create();
        $client = Client::create(['nom' => 'Clinique Test']);

        $this->actingAs($user)->post(route('installations.store'), [
            'code_installation' => 'INST-IRM-T',
            'nom' => 'Salle IRM Test',
            'type_profil' => 'IRM',
            'statut' => 'Brouillon',
            'client_id' => $client->id,
            'systeme_angiographie' => 'Should not attach',
            'radioprotection' => 'Should not attach',
            'injecteur' => 'Should not attach',
            'moniteurs' => 'Should not attach',
            'table_patient' => 'Should not attach',
        ])->assertRedirect();

        $installation = Installation::where('code_installation', 'INST-IRM-T')->firstOrFail();

        $this->assertDatabaseMissing('profil_cat_labs', [
            'installation_id' => $installation->id,
        ]);
    }

    public function test_active_document_version_replaces_previous_active_version(): void
    {
        $installation = $this->installation();

        $this->post(route('documents.store'), [
            'installation_id' => $installation->id,
            'categorie' => 'Documents radioprotection',
            'version' => '1.0',
            'statut' => 'Valide',
            'profil_concerne' => 'CATHETERISME',
            'est_version_active' => '1',
        ])->assertRedirect(route('documents.index'));

        $this->post(route('documents.store'), [
            'installation_id' => $installation->id,
            'categorie' => 'Documents radioprotection',
            'version' => '2.0',
            'statut' => 'Valide',
            'profil_concerne' => 'CATHETERISME',
            'est_version_active' => '1',
        ])->assertRedirect(route('documents.index'));

        $this->assertDatabaseHas('document_installations', [
            'installation_id' => $installation->id,
            'categorie' => 'Documents radioprotection',
            'version' => '1.0',
            'est_version_active' => false,
        ]);
        $this->assertDatabaseHas('document_installations', [
            'installation_id' => $installation->id,
            'categorie' => 'Documents radioprotection',
            'version' => '2.0',
            'est_version_active' => true,
        ]);
    }

    public function test_missing_required_document_logic_for_cath_profile(): void
    {
        $installation = $this->installation();

        DocumentInstallation::create([
            'installation_id' => $installation->id,
            'categorie' => 'Rapport de reception',
            'version' => '1.0',
            'statut' => 'Valide',
            'profil_concerne' => 'CATHETERISME',
            'est_version_active' => true,
        ]);

        $installation->load('documents');

        $this->assertEquals([
            'Controle qualite',
            'Documents radioprotection',
            'Plan de prevention',
            'Rapports techniques',
        ], $installation->missingRequiredDocumentCategories());
    }

    public function test_calendar_shows_installations_planned_in_selected_month(): void
    {
        $installation = $this->installation([
            'code_installation' => 'INST-CAL-1',
            'planned_start_date' => '2026-06-20',
            'calendar_note' => 'Controle qualite et rapport des tests',
        ]);

        $this->get(route('installations.calendar', ['month' => '2026-06']))
            ->assertOk()
            ->assertSee('INST-CAL-1')
            ->assertSee($installation->nom)
            ->assertSee('Controle qualite et rapport des tests');
    }

    public function test_sous_equipement_show_view_exists(): void
    {
        $client = Client::create(['nom' => 'Clinique Test']);
        $equipment = $this->equipment($client, 'EQ-PARENT');
        $sousEquipement = SousEquipement::create([
            'identifiant' => 'SE-001',
            'designation' => 'Station de controle',
            'marque' => 'Philips',
            'modele' => 'CTRL',
            'description' => 'Station rattachee a l equipement principal',
            'equipement_id' => $equipment->id,
        ]);

        $this->get(route('sous-equipements.show', $sousEquipement))
            ->assertOk()
            ->assertSee('Station de controle');
    }

    public function test_report_can_store_pdf_or_scan_metadata(): void
    {
        Storage::fake('public');
        $installation = $this->installation();

        $this->post(route('documents.store'), [
            'installation_id' => $installation->id,
            'categorie' => 'Rapport des tests',
            'type_rapport' => 'rapport_tests',
            'version' => '1.0',
            'statut' => 'Valide',
            'description' => 'Tests qualite et securite',
            'profil_concerne' => 'CATHETERISME',
            'est_version_active' => '1',
            'fichier' => UploadedFile::fake()->create('rapport-tests.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('documents.index'));

        $document = DocumentInstallation::where('categorie', 'Rapport des tests')->firstOrFail();

        $this->assertEquals('rapport_tests', $document->type_rapport);
        $this->assertEquals('rapport-tests.pdf', $document->fichier_original_name);
        Storage::disk('public')->assertExists($document->fichier_path);
    }

    public function test_required_report_upload_redirects_back_to_installation_with_file(): void
    {
        Storage::fake('public');
        $installation = $this->installation();

        $this->post(route('documents.store'), [
            'installation_id' => $installation->id,
            'categorie' => 'Rapport de reception',
            'type_rapport' => 'document_requis',
            'version' => '1.0',
            'statut' => 'Valide',
            'profil_concerne' => 'CATHETERISME',
            'est_version_active' => '1',
            'redirect_to' => 'installation',
            'requires_file' => '1',
            'fichier' => UploadedFile::fake()->create('reception-scan.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('installations.show', $installation));

        $this->assertNotNull($installation->fresh()->activeDocumentByCategorie('Rapport de reception'));
    }

    public function test_installation_export_downloads_csv_with_details(): void
    {
        $installation = $this->installation([
            'code_installation' => 'INST-EXPORT',
            'nom' => 'Salle Export Test',
        ]);

        $response = $this->get(route('installations.export', $installation));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('INST-EXPORT', $content);
        $this->assertStringContainsString('Salle Export Test', $content);
        $this->assertStringContainsString('IDENTITE', $content);
    }

    private function cathProfilePayload(): array
    {
        return [
            'departement' => 'Cardiologie',
            'batiment' => 'Bloc A',
            'etage' => '1',
            'systeme_angiographie' => 'Azurion',
            'station_controle' => 'Console A',
            'radioprotection' => 'Conforme',
            'injecteur' => 'Injecteur A',
            'moniteurs' => '2 moniteurs',
            'controle_acces' => '1',
            'table_patient' => 'Table A',
            'alimentation' => 'Secourue',
            'reseau' => 'DICOM',
            'ventilation' => 'Conforme',
            'protection_murale' => 'Plomb OK',
            'stockage_consommables' => 'Armoire A',
            'signalisation_rayonnement' => 'Conforme',
            'conformite_salle_interventionnelle' => 'Validee',
            'dispositifs_securite' => 'Detecteurs OK',
        ];
    }

    private function installation(array $overrides = []): Installation
    {
        $client = Client::first() ?? Client::create(['nom' => 'Clinique Test']);

        return Installation::create(array_merge([
            'code_installation' => 'INST-CATH-BASE',
            'nom' => 'Salle Cath Base',
            'type_profil' => 'CATHETERISME',
            'statut' => 'Brouillon',
            'criticite' => 'Haute',
            'client_id' => $client->id,
            'planned_start_date' => '2026-06-15',
        ], $overrides));
    }

    private function equipment(Client $client, string $code): Equipement
    {
        return Equipement::create([
            'code' => $code,
            'numero_equipement' => $code . '-NUM',
            'modele' => 'Modele Test',
            'marque' => 'Philips',
            'designation' => 'Equipement ' . $code,
            'numero_serie' => $code . '-SN',
            'modalite_id' => 1,
            'client_id' => $client->id,
            'software' => '1.0',
            'date_installation' => '2026-06-01',
            'date_debut_garantie' => '2026-06-01',
            'plan_prev' => 12,
            'garantie' => '12 mois',
        ]);
    }
}
