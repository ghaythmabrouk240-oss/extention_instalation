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
        Schema::create('installation_time_penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installation_id')->unique()->constrained()->onDelete('cascade');
            $table->date('date_limite_contractuelle')->nullable();
            $table->integer('jours_retard')->default(0);
            $table->decimal('penalite_par_jour', 15, 2)->default(0);
            $table->decimal('montant_penalite', 15, 2)->default(0);
            $table->string('payeur')->default('ST_IET');
            $table->text('raison_retard')->nullable();
            $table->boolean('applicable')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installation_time_penalties');
    }
};
