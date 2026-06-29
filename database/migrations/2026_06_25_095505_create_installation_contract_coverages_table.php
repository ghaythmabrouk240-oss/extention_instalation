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
        Schema::create('installation_contract_coverages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installation_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipement_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('garantie_active')->default(true);
            $table->date('date_fin_garantie')->nullable();
            $table->boolean('contrat_renouvelable')->default(false);
            $table->string('contrat_reference')->nullable();
            $table->string('client_partie')->nullable();
            $table->string('stiet_partie')->nullable();
            $table->enum('statut_couverture', ['garantie', 'contrat_renouvelable', 'hors_contrat', 'a_verifier'])->default('garantie');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installation_contract_coverages');
    }
};
