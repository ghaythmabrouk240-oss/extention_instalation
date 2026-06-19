<?php

namespace Database\Factories;

use App\Models\ProfilCatLab;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProfilCatLab>
 */
class ProfilCatLabFactory extends Factory
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
            'systeme_angiographie' => fake()->word(),
            'radioprotection' => fake()->word(),
            'injecteur' => fake()->word(),
            'moniteurs' => fake()->word(),
            'controle_acces' => false,
            'table_patient' => fake()->word(),
            'radiation_shielding_status' => fake()->randomElement(['conforme', 'a_verifier', 'non_conforme']),
            'lead_glass_status' => fake()->randomElement(['conforme', 'a_verifier', 'non_conforme']),
            'ceiling_support_status' => fake()->randomElement(['conforme', 'a_verifier', 'non_conforme']),
            'emergency_equipment_status' => fake()->randomElement(['conforme', 'a_verifier', 'non_conforme']),
            'access_control_status' => fake()->randomElement(['conforme', 'a_verifier', 'non_conforme']),
            'dose_monitoring_available' => fake()->boolean(),
            'acceptance_test_status' => fake()->randomElement(['conforme', 'a_verifier', 'non_conforme']),
        ];
    }
}
