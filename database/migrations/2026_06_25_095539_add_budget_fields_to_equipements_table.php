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
        Schema::table('equipements', function (Blueprint $table) {
            $table->date('date_fin_garantie')->nullable();
            $table->enum('statut_couverture', ['garantie', 'contrat_renouvelable', 'hors_contrat', 'a_verifier'])->default('garantie');
            $table->string('contrat_reference')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipements', function (Blueprint $table) {
            $table->dropColumn(['date_fin_garantie', 'statut_couverture', 'contrat_reference']);
        });
    }
};
