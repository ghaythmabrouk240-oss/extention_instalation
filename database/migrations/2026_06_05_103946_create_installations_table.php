<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('installations', function (Blueprint $table) {
            $table->id();

            $table->string('code_installation')->unique();
            $table->string('nom');

            // UML shows enum ProfilCatLab/ProfilIRM via relation, type_profil in Installation
            $table->enum('type_profil', ['IRM', 'CATHETERISME'])->nullable();

            $table->string('statut')->default('pending');
            $table->string('criticite')->nullable();

            // Foreign keys from UML
            $table->foreignId('proprietaire_interne_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->cascadeOnDelete();

            $table->foreignId('equipement_principal_id')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installations');
    }
};