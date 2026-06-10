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
        Schema::create('document_installations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installation_id')->constrained('installations')->cascadeOnDelete();
            $table->string('categorie');
            $table->string('version');
            $table->string('statut');
            $table->boolean('est_bloquant')->default(false);
            $table->string('reference_dms')->nullable();
            $table->string('reference_fichier')->nullable();
            $table->enum('profil_concerne', ['IRM', 'CATHETERISME', 'COMMUN'])->default('COMMUN');
            $table->boolean('est_version_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_installations');
    }
};
