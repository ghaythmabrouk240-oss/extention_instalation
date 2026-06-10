<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Equipement;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 2 test users
        User::factory()->count(2)->create();

        // Create 2 test clients
        Client::create([
            'nom' => 'Hôpital Central',
            'adresse' => '123 Rue de la Santé',
            'email' => 'contact@hopital.com',
            'telephone' => '0102030405'
        ]);

        Client::create([
            'nom' => 'Clinique du Sud',
            'adresse' => '456 Avenue des Soins'
        ]);

        // Create a test Equipment because equipement_principal_id is hitting errors too
        Equipement::create([
            'code' => 'EQ-12345',
            'numero_equipement' => 'NUM-001',
            'modele' => 'AZURION 7',
            'marque' => 'Philips',
            'numero_serie_id' => 123456
        ]);
        
        Equipement::create([
            'code' => 'EQ-67890',
            'numero_equipement' => 'NUM-002',
            'modele' => 'Ingenia Ambition',
            'marque' => 'Philips',
            'numero_serie_id' => 789012
        ]);
    }
}
