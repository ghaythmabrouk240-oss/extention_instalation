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
        Schema::create('profil_irms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installation_id')->constrained('installations')->cascadeOnDelete();
            $table->string('champ_magnetique')->nullable();
            $table->string('blindage')->nullable();
            $table->string('atelier')->nullable();
            $table->boolean('confinement_ferromagnetique')->default(false);
            $table->boolean('arret_urgence')->default(false);
            $table->string('batiment')->nullable();
            $table->string('etage')->nullable();
            $table->string('zone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_irms');
    }
};
