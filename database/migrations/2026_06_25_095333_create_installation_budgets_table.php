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
        Schema::create('installation_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installation_id')->unique()->constrained()->onDelete('cascade');
            $table->enum('regime_prise_en_charge', ['garantie', 'contrat_renouvelable', 'hors_contrat'])->default('garantie');
            $table->string('devise', 10)->default('EUR');
            $table->decimal('budget_prevu', 15, 2)->nullable();
            $table->decimal('total_frais', 15, 2)->default(0);
            $table->decimal('total_penalites', 15, 2)->default(0);
            $table->decimal('total_final', 15, 2)->default(0);
            $table->enum('statut_validation', ['brouillon', 'en_cours', 'valide', 'rejete'])->default('brouillon');
            $table->string('reference_contrat')->nullable();
            $table->text('notes_finance')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installation_budgets');
    }
};
