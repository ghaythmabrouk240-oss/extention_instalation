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

            $table->enum('type_profil', ['IRM', 'CATHETERISME']);

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

            $table->date('planned_start_date')->nullable();
            $table->date('planned_end_date')->nullable();
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();

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
