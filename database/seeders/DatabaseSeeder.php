<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\DocumentInstallation;
use App\Models\Equipement;
use App\Models\Installation;
use App\Models\SousEquipement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Biomedical Demo',
            'email' => 'biomedical@example.com',
            'password' => Hash::make('password'),
        ]);

        $client = Client::create([
            'nom' => 'Clinique Demo Tunis',
            'adresse' => 'Avenue de la sante, Tunis',
            'email' => 'demo-clinic@example.com',
            'telephone' => '+216 70 000 000',
        ]);

        $mainEquipment = Equipement::create([
            'code' => 'EQ-CATH-001',
            'numero_equipement' => 'NE-CATH-001',
            'modele' => 'Azurion 7',
            'marque' => 'Philips',
            'designation' => 'Systeme angiographie principal',
            'numero_serie' => 'SN-CATH-001',
            'modalite_id' => 1,
            'client_id' => $client->id,
            'software' => 'R2.1',
            'date_installation' => now()->toDateString(),
            'date_debut_garantie' => now()->toDateString(),
            'plan_prev' => 12,
            'garantie' => '24 mois',
        ]);

        $secondaryEquipment = Equipement::create([
            'code' => 'EQ-INJ-001',
            'numero_equipement' => 'NE-INJ-001',
            'modele' => 'Mark 7',
            'marque' => 'Medrad',
            'designation' => 'Injecteur de contraste',
            'numero_serie' => 'SN-INJ-001',
            'modalite_id' => 1,
            'client_id' => $client->id,
            'software' => '1.0',
            'date_installation' => now()->toDateString(),
            'date_debut_garantie' => now()->toDateString(),
            'plan_prev' => 12,
            'garantie' => '12 mois',
        ]);

        SousEquipement::create([
            'identifiant' => 'SE-MON-001',
            'designation' => 'Moniteur salle',
            'marque' => 'Philips',
            'modele' => 'FlexVision',
            'description' => 'Moniteur rattache au systeme principal',
            'equipement_id' => $mainEquipment->id,
        ]);

        $installation = Installation::create([
            'code_installation' => 'INST-CATH-001',
            'nom' => 'Salle Catheterisme 1',
            'type_profil' => 'CATHETERISME',
            'statut' => 'Brouillon',
            'criticite' => 'Haute',
            'proprietaire_interne_id' => $user->id,
            'client_id' => $client->id,
            'equipement_principal_id' => $mainEquipment->id,
            'planned_start_date' => now()->startOfMonth()->addDays(8)->toDateString(),
            'planned_end_date' => now()->startOfMonth()->addDays(10)->toDateString(),
            'calendar_note' => 'Reception salle, controle radioprotection et tests qualite.',
        ]);

        $installation->profilCatLab()->create([
            'departement' => 'Cardiologie interventionnelle',
            'batiment' => 'Bloc B',
            'etage' => 'RDC',
            'systeme_angiographie' => 'Azurion 7 C20',
            'station_controle' => 'Console principale salle',
            'radioprotection' => 'Controle plombage conforme',
            'injecteur' => 'Injecteur Medrad Mark 7',
            'moniteurs' => '2 moniteurs salle + 1 console',
            'controle_acces' => true,
            'table_patient' => 'Table flottante motorisee',
            'alimentation' => 'Alimentation secourue conforme',
            'reseau' => 'Reseau DICOM local',
            'ventilation' => 'Ventilation salle conforme',
            'protection_murale' => 'Protection plombee validee',
            'stockage_consommables' => 'Armoire consommables salle',
            'signalisation_rayonnement' => 'Signalisation conforme',
            'conformite_salle_interventionnelle' => 'Salle interventionnelle validee',
            'dispositifs_securite' => 'Detecteurs et arret d urgence OK',
        ]);

        $installation->equipements()->attach($secondaryEquipment->id, ['role' => 'secondaire']);

        foreach ([
            'Rapport de reception',
            'Controle qualite',
            'Documents radioprotection',
            'Plan de prevention',
        ] as $category) {
            DocumentInstallation::create([
                'installation_id' => $installation->id,
                'categorie' => $category,
                'type_rapport' => 'document_requis',
                'version' => '1.0',
                'statut' => 'Valide',
                'description' => 'Document demo PRD pour ' . $category,
                'est_bloquant' => true,
                'reference_dms' => 'DMS-CATH-' . str_replace(' ', '-', strtoupper($category)),
                'profil_concerne' => 'CATHETERISME',
                'est_version_active' => true,
            ]);
        }

        DocumentInstallation::create([
            'installation_id' => $installation->id,
            'categorie' => 'Rapport installation generale',
            'type_rapport' => 'installation_generale',
            'version' => '1.0',
            'statut' => 'Valide',
            'description' => 'PV installation generale salle cath.',
            'est_bloquant' => true,
            'reference_dms' => 'DMS-CATH-PV',
            'profil_concerne' => 'CATHETERISME',
            'est_version_active' => true,
        ]);

        DocumentInstallation::create([
            'installation_id' => $installation->id,
            'categorie' => 'Rapport des tests',
            'type_rapport' => 'rapport_tests',
            'version' => '1.0',
            'statut' => 'Valide',
            'description' => 'Tests qualite, securite interventionnelle et validation technique de la salle.',
            'est_bloquant' => true,
            'reference_dms' => 'DMS-CATH-RAPPORT-TESTS',
            'profil_concerne' => 'CATHETERISME',
            'est_version_active' => true,
        ]);
    }
}
