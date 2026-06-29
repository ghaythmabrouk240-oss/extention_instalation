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
        Schema::create('installation_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installation_id')->constrained()->onDelete('cascade');
            $table->enum('type_depense', ['transport_aller', 'transport_retour', 'hotel', 'repas', 'piece_equipement', 'autre_frais']);
            $table->date('date_depense');
            $table->string('description');
            $table->string('fournisseur')->nullable();
            $table->decimal('quantite', 10, 2)->default(1);
            $table->decimal('montant_unitaire', 15, 2);
            $table->decimal('montant_total', 15, 2);
            $table->decimal('tva', 15, 2)->default(0);
            $table->foreignId('document_id')->nullable()->constrained('document_installations')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installation_expenses');
    }
};
