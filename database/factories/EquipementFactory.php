<?php

namespace Database\Factories;

use App\Models\Equipement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Equipement>
 */
class EquipementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'EQ-' . fake()->unique()->randomNumber(4),
            'numero_equipement' => fake()->unique()->randomNumber(6),
            'modele' => fake()->word(),
            'marque' => fake()->company(),
            'designation' => fake()->sentence(),
            'numero_serie' => fake()->unique()->randomNumber(8),
            'modalite_id' => null,
            'client_id' => null,
            'software' => fake()->word(),
            'date_installation' => fake()->date(),
            'date_debut_garantie' => fake()->date(),
            'plan_prev' => fake()->randomNumber(2),
            'garantie' => fake()->word(),
        ];
    }
}
