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
        Schema::create('sous_equipements', function (Blueprint $table) {
            $table->id();
            $table->string('identifiant');
            $table->string('designation');
            $table->string('marque');
            $table->string('modele');
            $table->string('description');
            $table->foreignId('equipement_id')->constrained('equipements')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sous_equipements');
    }
};
