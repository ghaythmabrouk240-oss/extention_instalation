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
        Schema::create('lien_equipement_installation', function (Blueprint $table) {
            $table->foreignId('installation_id')->constrained('installations')->cascadeOnDelete();
            $table->foreignId('equipement_id')->constrained('equipements')->cascadeOnDelete();
            $table->string('role');
            $table->timestamps();
            $table->unique(['installation_id', 'equipement_id', 'role'], 'lien_equipement_installation_unique');
        });

        Schema::table('installations', function (Blueprint $table) {
            $table->foreign('equipement_principal_id')
                ->references('id')
                ->on('equipements')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->dropForeign(['equipement_principal_id']);
        });

        Schema::dropIfExists('lien_equipement_installation');
    }
};
