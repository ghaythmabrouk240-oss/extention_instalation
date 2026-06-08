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
        Schema::create('profil_cat_labs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('installation_id');
            $table->string('systeme_angiographie');
            $table->string('radioprotection');
            $table->string('injecteur');
            $table->string('moniteurs');
            $table->boolean('controle_acces')->default(false);
            $table->string('table_patient');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_cat_labs');
    }
};
