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
        Schema::create('equipements', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('numero_equipement');
            $table->string('modele');
            $table->string('marque');
            $table->string('designation');
            $table->string('numero_serie');
            $table->unsignedBigInteger('modalite_id');
            $table->unsignedBigInteger('client_id');
            $table->string('software');
            $table->date('date_installation');
            $table->date('date_debut_garantie');
            $table->integer('plan_prev');
            $table->string('garantie');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipements');
    }
};
