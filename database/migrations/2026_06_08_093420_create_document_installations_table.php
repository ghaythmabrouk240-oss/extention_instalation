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
            $table->unsignedBigInteger('installation_id');
            $table->string('categorie');
            $table->string('version');
            $table->string('statut');
            $table->boolean('est_bloquant')->default(false);
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
