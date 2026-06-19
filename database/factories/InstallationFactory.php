<?php

namespace Database\Factories;

use App\Models\Installation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Installation>
 */
class InstallationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code_installation' => 'INS-' . fake()->unique()->randomNumber(4),
            'nom' => fake()->word(),
            'type_profil' => fake()->randomElement(['CATHETERISME', 'IRM']),
            'statut' => 'Brouillon',
            'criticite' => fake()->randomElement(['Critique', 'Haute', 'Moyenne', 'Basse']),
        ];
    }
}
