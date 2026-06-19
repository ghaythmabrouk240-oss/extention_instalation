<?php

namespace Database\Factories;

use App\Models\DocumentInstallation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentInstallation>
 */
class DocumentInstallationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'installation_id' => null,
            'categorie' => fake()->randomElement(['Rapport de reception', 'Documents radioprotection', 'Plan de prevention', 'Controle qualite']),
            'version' => '1.0',
            'statut' => 'Actif',
            'est_bloquant' => false,
            'profil_concerne' => fake()->randomElement(['COMMUN', 'CATHETERISME', 'IRM']),
            'est_version_active' => true,
        ];
    }
}
