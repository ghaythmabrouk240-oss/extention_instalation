<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profil_cat_labs', function (Blueprint $table) {
            $table->string('departement')->nullable()->after('installation_id');
            $table->string('batiment')->nullable()->after('departement');
            $table->string('etage')->nullable()->after('batiment');
            $table->string('station_controle')->nullable()->after('systeme_angiographie');
            $table->string('alimentation')->nullable()->after('radioprotection');
            $table->string('reseau')->nullable()->after('alimentation');
            $table->string('ventilation')->nullable()->after('reseau');
            $table->string('protection_murale')->nullable()->after('ventilation');
            $table->string('stockage_consommables')->nullable()->after('protection_murale');
            $table->string('signalisation_rayonnement')->nullable()->after('controle_acces');
            $table->string('conformite_salle_interventionnelle')->nullable()->after('signalisation_rayonnement');
            $table->string('dispositifs_securite')->nullable()->after('conformite_salle_interventionnelle');
        });
    }

    public function down(): void
    {
        Schema::table('profil_cat_labs', function (Blueprint $table) {
            $table->dropColumn([
                'departement',
                'batiment',
                'etage',
                'station_controle',
                'alimentation',
                'reseau',
                'ventilation',
                'protection_murale',
                'stockage_consommables',
                'signalisation_rayonnement',
                'conformite_salle_interventionnelle',
                'dispositifs_securite',
            ]);
        });
    }
};
