<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin POC',
            'email' => 'admin@example.com',
        ]);

        User::factory()->biomedical()->create([
            'name' => 'Biomedical POC',
            'email' => 'biomedical@example.com',
        ]);

        User::factory()->manager()->create([
            'name' => 'Manager POC',
            'email' => 'manager@example.com',
        ]);
    }
}
